<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Device;
use App\Models\Image;
use App\Models\InferenceResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $dashboardData = $this->resolveDashboardData($request);

        return view('admin.admin', array_merge([
            'admin_email' => session('admin_email'),
        ], $dashboardData));
    }

    public function filterPanels(Request $request)
    {
        $dashboardData = $this->resolveDashboardData($request);

        return response()->json([
            'devices_html' => view('admin.partials.devices-table', $dashboardData)->render(),
            'inference_html' => view('admin.partials.inference-table', $dashboardData)->render(),
            'gallery_html' => view('admin.partials.gallery-grid', $dashboardData)->render(),
        ]);
    }

    private function resolveDashboardData(Request $request): array
    {
        $deviceFilters = [
            'search' => trim((string) $request->query('devices_search', '')),
            'status' => (string) $request->query('devices_status', ''),
        ];

        $inferenceFilters = [
            'device_code' => trim((string) $request->query('inference_device', '')),
            'status' => trim((string) $request->query('inference_status', '')),
            'date_from' => trim((string) $request->query('inference_date_from', '')),
            'date_to' => trim((string) $request->query('inference_date_to', '')),
        ];

        $galleryFilters = [
            'device_code' => trim((string) $request->query('gallery_device', '')),
            'date_from' => trim((string) $request->query('gallery_date_from', '')),
            'date_to' => trim((string) $request->query('gallery_date_to', '')),
        ];

        $stats = [
            'device_count' => Device::count(),
            'inference_count' => InferenceResult::count(),
            'gallery_count' => Image::count(),
        ];

        $deviceOptions = Device::query()
            ->whereNotNull('device_code')
            ->orderBy('device_code')
            ->pluck('device_code');

        $inferenceStatusOptions = InferenceResult::query()
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $devicesQuery = Device::query();

        if ($deviceFilters['search'] !== '') {
            $keyword = $deviceFilters['search'];
            $devicesQuery->where(function ($query) use ($keyword) {
                $query
                    ->where('device_code', 'like', "%{$keyword}%")
                    ->orWhere('location', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if (in_array($deviceFilters['status'], ['active', 'inactive'], true)) {
            $devicesQuery->where('is_active', $deviceFilters['status'] === 'active');
        }

        $devices = $devicesQuery->latest('created_at')->get();

        $inferenceDateFrom = $this->parseDateInput($inferenceFilters['date_from']);
        $inferenceDateTo = $this->parseDateInput($inferenceFilters['date_to'], true);
        if ($inferenceDateFrom !== null && $inferenceDateTo !== null && $inferenceDateFrom->greaterThan($inferenceDateTo)) {
            [$inferenceDateFrom, $inferenceDateTo] = [$inferenceDateTo->copy()->startOfDay(), $inferenceDateFrom->copy()->endOfDay()];
        }

        $inferenceQuery = InferenceResult::query()->with('device');

        if ($inferenceFilters['device_code'] !== '') {
            $selectedDeviceCode = $inferenceFilters['device_code'];
            $inferenceQuery->where(function ($query) use ($selectedDeviceCode) {
                $query
                    ->where('device_code', $selectedDeviceCode)
                    ->orWhereHas('device', function ($deviceQuery) use ($selectedDeviceCode) {
                        $deviceQuery->where('device_code', $selectedDeviceCode);
                    });
            });
        }

        if ($inferenceFilters['status'] !== '') {
            $inferenceQuery->where('status', $inferenceFilters['status']);
        }

        if ($inferenceDateFrom !== null) {
            $inferenceQuery->where('inference_at', '>=', $inferenceDateFrom);
        }

        if ($inferenceDateTo !== null) {
            $inferenceQuery->where('inference_at', '<=', $inferenceDateTo);
        }

        $inferenceResults = $inferenceQuery
            ->latest('inference_at')
            ->take(100)
            ->get()
            ->map(function (InferenceResult $result) {
                $confidence = $result->avg_confidence;
                $score = $confidence === null ? null : ($confidence <= 1 ? $confidence * 100 : $confidence);

                return [
                    'id' => $result->id,
                    'timestamp' => optional($result->inference_at)->format('d M Y H:i:s') ?? '-',
                    'device_code' => $result->device_code ?? optional($result->device)->device_code ?? '-',
                    'label' => $result->status ?? 'N/A',
                    'score' => $score === null ? null : number_format($score, 1),
                    'total_jentik' => $result->total_jentik,
                    'raw_score' => $confidence,
                ];
            });

        $galleryDateFrom = $this->parseDateInput($galleryFilters['date_from']);
        $galleryDateTo = $this->parseDateInput($galleryFilters['date_to'], true);
        if ($galleryDateFrom !== null && $galleryDateTo !== null && $galleryDateFrom->greaterThan($galleryDateTo)) {
            [$galleryDateFrom, $galleryDateTo] = [$galleryDateTo->copy()->startOfDay(), $galleryDateFrom->copy()->endOfDay()];
        }

        $galleryQuery = Image::query()
            ->with(['device', 'inferenceResult'])
            ->where('image_type', 'original')
            ->when($galleryFilters['device_code'] !== '', function ($query) use ($galleryFilters) {
                $selectedDeviceCode = $galleryFilters['device_code'];

                $query->where(function ($innerQuery) use ($selectedDeviceCode) {
                    $innerQuery
                        ->where('device_code', $selectedDeviceCode)
                        ->orWhereHas('device', function ($deviceQuery) use ($selectedDeviceCode) {
                            $deviceQuery->where('device_code', $selectedDeviceCode);
                        });
                });
            })
            ->when($galleryDateFrom !== null, function ($query) use ($galleryDateFrom) {
                $query->where('captured_at', '>=', $galleryDateFrom);
            })
            ->when($galleryDateTo !== null, function ($query) use ($galleryDateTo) {
                $query->where('captured_at', '<=', $galleryDateTo);
            });

        $galleryImages = $galleryQuery
            ->latest('captured_at')
            ->take(120)
            ->get()
            ->map(function (Image $image) {
                $confidence = optional($image->inferenceResult)->avg_confidence;
                $score = $confidence === null ? null : ($confidence <= 1 ? $confidence * 100 : $confidence);
                $normalizedPath = $this->normalizeImagePath($image->image_path);

                $imageUrl = null;
                if (!empty($image->image_blob)) {
                    $imageUrl = 'data:image/jpeg;base64,' . base64_encode($image->image_blob);
                } elseif ($normalizedPath) {
                    $imageUrl = Storage::url($normalizedPath);
                }

                return [
                    'id' => $image->id,
                    'device_code' => $image->device_code ?? optional($image->device)->device_code ?? '-',
                    'captured_at' => optional($image->captured_at)->format('d M Y H:i'),
                    'image_url' => $imageUrl,
                    'label' => optional($image->inferenceResult)->status ?? 'Deteksi',
                    'score' => $score === null ? null : number_format($score, 1),
                ];
            });

        return [
            'stats' => $stats,
            'devices' => $devices,
            'inferenceResults' => $inferenceResults,
            'galleryImages' => $galleryImages,
            'deviceOptions' => $deviceOptions,
            'inferenceStatusOptions' => $inferenceStatusOptions,
            'deviceFilters' => $deviceFilters,
            'inferenceFilters' => $inferenceFilters,
            'galleryFilters' => $galleryFilters,
        ];
    }

    public function chartData(Request $request)
    {
        $allowedModes = ['date_range', 'week_in_month'];
        $mode = (string) $request->query('mode', 'date_range');
        if (!in_array($mode, $allowedModes, true)) {
            $mode = 'date_range';
        }

        if ($mode === 'week_in_month') {
            return $this->buildWeekInMonthChartResponse($request);
        }

        return $this->buildDateRangeChartResponse($request);
    }

    private function parseDateInput(?string $value, bool $endOfDay = false): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();

            return $endOfDay ? $parsed->endOfDay() : $parsed;
        } catch (Throwable) {
            return null;
        }
    }

    private function buildDateRangeChartResponse(Request $request)
    {
        $dateFrom = $this->parseDateInput((string) $request->query('date_from', ''));
        $dateTo = $this->parseDateInput((string) $request->query('date_to', ''), true);

        if ($dateFrom === null && $dateTo === null) {
            $dateFrom = now()->subDays(6)->startOfDay();
            $dateTo = now()->endOfDay();
        } elseif ($dateFrom === null && $dateTo !== null) {
            $dateFrom = $dateTo->copy()->subDays(6)->startOfDay();
        } elseif ($dateFrom !== null && $dateTo === null) {
            $dateTo = $dateFrom->copy()->addDays(6)->endOfDay();
        }

        if ($dateFrom !== null && $dateTo !== null && $dateFrom->greaterThan($dateTo)) {
            [$dateFrom, $dateTo] = [$dateTo->copy()->startOfDay(), $dateFrom->copy()->endOfDay()];
        }

        [$labels, $values] = $this->buildDailySeries($dateFrom, $dateTo);

        return response()->json([
            'mode' => 'date_range',
            'labels' => $labels,
            'values' => $values,
            'meta' => [
                'title' => 'Deteksi Jentik per Hari',
                'range_text' => $dateFrom->format('d M Y') . ' - ' . $dateTo->format('d M Y'),
                'date_from' => $dateFrom->toDateString(),
                'date_to' => $dateTo->toDateString(),
            ],
        ]);
    }

    private function buildWeekInMonthChartResponse(Request $request)
    {
        $monthInput = (int) $request->query('month', now()->month);
        $yearInput = (int) $request->query('year', now()->year);
        $weekInput = (int) $request->query('week', 1);

        $month = min(max($monthInput, 1), 12);
        $year = min(max($yearInput, 2000), 2100);

        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();

        $firstWeekStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $lastWeekEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);
        $weeksInMonth = $firstWeekStart->diffInWeeks($lastWeekEnd) + 1;

        $week = min(max($weekInput, 1), $weeksInMonth);

        $selectedWeekStart = $firstWeekStart->copy()->addWeeks($week - 1);
        $selectedWeekEnd = $selectedWeekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $rangeStart = $selectedWeekStart->copy()->startOfDay();
        if ($rangeStart->lessThan($monthStart)) {
            $rangeStart = $monthStart->copy();
        }

        $rangeEnd = $selectedWeekEnd->copy()->endOfDay();
        if ($rangeEnd->greaterThan($monthEnd)) {
            $rangeEnd = $monthEnd->copy();
        }

        [$labels, $values] = $this->buildDailySeries($rangeStart, $rangeEnd);

        return response()->json([
            'mode' => 'week_in_month',
            'labels' => $labels,
            'values' => $values,
            'meta' => [
                'title' => 'Deteksi Jentik per Hari',
                'range_text' => 'Minggu ke-' . $week . ' ' . $monthStart->locale('id')->isoFormat('MMMM Y'),
                'week' => $week,
                'month' => $month,
                'year' => $year,
                'weeks_in_month' => $weeksInMonth,
                'date_from' => $rangeStart->toDateString(),
                'date_to' => $rangeEnd->toDateString(),
            ],
        ]);
    }

    private function buildDailySeries(Carbon $rangeStart, Carbon $rangeEnd): array
    {
        $totals = DB::table('inference_results')
            ->selectRaw('DATE(inference_at) as day, COALESCE(SUM(total_jentik), 0) as total')
            ->whereBetween('inference_at', [$rangeStart, $rangeEnd])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $values = [];

        $cursor = $rangeStart->copy()->startOfDay();
        $end = $rangeEnd->copy()->startOfDay();

        while ($cursor->lessThanOrEqualTo($end)) {
            $dayKey = $cursor->toDateString();
            $labels[] = $cursor->locale('id')->isoFormat('ddd, D MMM');
            $values[] = (int) ($totals[$dayKey] ?? 0);
            $cursor->addDay();
        }

        return [$labels, $values];
    }

    private function normalizeImagePath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $normalized = str_replace('\\', '/', $path);
        $normalized = ltrim($normalized, './');

        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        $normalized = ltrim($normalized, '/');

        return $normalized !== '' ? $normalized : null;
    }
}

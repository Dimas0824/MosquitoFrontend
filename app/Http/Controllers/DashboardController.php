<?php

namespace App\Http\Controllers;

use App\Models\InferenceResult;
use App\Models\Image;
use App\Models\DeviceControl;
use App\Services\HistoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    private HistoryService $historyService;

    public function __construct(HistoryService $historyService)
    {
        $this->historyService = $historyService;
    }

    /**
     * Display the dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $deviceCode = session('device_code');
        $deviceId = session('device_id');
        $deviceLocation = session('device_location', 'Unknown Location');
        $deviceInfo = session('device_info', []);

        $perPage = 5;
        $historyPage = max(1, (int) $request->input('history_page', 1));

        $historyData = $this->historyService->fetchHistoryPage($deviceId, $perPage, $historyPage);

        $historyPaginator = new LengthAwarePaginator(
            $historyData['records']->values(),
            $historyData['total'],
            $perPage,
            $historyPage,
            [
                'path' => url()->current(),
                'query' => Arr::except($request->query(), 'history_page'),
                'pageName' => 'history_page',
            ]
        );

        // Gallery: latest images (original) for the device
        $galleryImages = Image::query()
            ->select(['id', 'device_id', 'image_type', 'image_path', 'image_blob', 'captured_at'])
            ->with(['inferenceResult:id,image_id,total_jentik'])
            ->where('device_id', $deviceId)
            ->where('image_type', 'original')
            ->orderByDesc('captured_at')
            ->limit(12)
            ->get()
            ->map(function (Image $img) {
                $capturedAt = $img->captured_at;

                $status = 'Aman';
                $count = optional($img->inferenceResult)->total_jentik;
                if ($count > 5) {
                    $status = 'Bahaya';
                } elseif ($count > 0) {
                    $status = 'Waspada';
                }

                // Build inline data URI if blob exists
                $imageSrc = null;
                if (!empty($img->image_blob)) {
                    $imageSrc = 'data:image/jpeg;base64,' . base64_encode($img->image_blob);
                } elseif (!empty($img->image_path)) {
                    $imageSrc = $img->image_path;
                }

                return [
                    'id' => $img->id,
                    'time' => $capturedAt?->timezone(config('app.timezone'))->format('H:i') . ' WIB',
                    'date' => $capturedAt?->isToday()
                        ? 'Hari Ini'
                        : ($capturedAt?->isYesterday() ? 'Kemarin' : $capturedAt?->format('d M Y')),
                    'count' => $count ?? 0,
                    'status' => $status,
                    'image_path' => $img->image_path,
                    'image_src' => $imageSrc,
                    'captured_at' => $capturedAt?->toIso8601String(),
                ];
            })->toArray();

        $today = now();
        $chartDateFromInput = trim((string) $request->query('chart_date_from', ''));
        $chartDateToInput = trim((string) $request->query('chart_date_to', ''));

        $chartDateFrom = null;
        $chartDateTo = null;
        try {
            if ($chartDateFromInput !== '') {
                $chartDateFrom = Carbon::createFromFormat('Y-m-d', $chartDateFromInput)->startOfDay();
            }
            if ($chartDateToInput !== '') {
                $chartDateTo = Carbon::createFromFormat('Y-m-d', $chartDateToInput)->endOfDay();
            }
        } catch (\Throwable) {
            $chartDateFrom = null;
            $chartDateTo = null;
        }

        if ($chartDateFrom === null && $chartDateTo === null) {
            $chartDateFrom = $today->copy()->subDays(6)->startOfDay();
            $chartDateTo = $today->copy()->endOfDay();
        } elseif ($chartDateFrom === null && $chartDateTo !== null) {
            $chartDateFrom = $chartDateTo->copy()->subDays(6)->startOfDay();
        } elseif ($chartDateFrom !== null && $chartDateTo === null) {
            $chartDateTo = $chartDateFrom->copy()->addDays(6)->endOfDay();
        }

        if ($chartDateFrom !== null && $chartDateTo !== null && $chartDateFrom->greaterThan($chartDateTo)) {
            [$chartDateFrom, $chartDateTo] = [$chartDateTo->copy()->startOfDay(), $chartDateFrom->copy()->endOfDay()];
        }

        // Chart by selected date range from inference_results
        $rawCounts = DB::table('inference_results')
            ->selectRaw('DATE(inference_at) as d, COALESCE(SUM(total_jentik), 0) as total')
            ->where('device_id', $deviceId)
            ->whereBetween('inference_at', [$chartDateFrom, $chartDateTo])
            ->groupBy('d')
            ->pluck('total', 'd');

        $chartLabels = [];
        $chartValues = [];

        $cursor = $chartDateFrom->copy()->startOfDay();
        $chartEnd = $chartDateTo->copy()->startOfDay();
        while ($cursor->lessThanOrEqualTo($chartEnd)) {
            $chartLabels[] = $cursor->locale('id')->isoFormat('ddd, D MMM');
            $chartValues[] = (int) ($rawCounts[$cursor->toDateString()] ?? 0);
            $cursor->addDay();
        }

        if (count($chartLabels) === 0) {
            $chartLabels[] = $today->locale('id')->isoFormat('ddd, D MMM');
            $chartValues[] = 0;
        }

        // KPI sources from inference_results table
        $latestInference = InferenceResult::where('device_id', $deviceId)
            ->orderByDesc('inference_at')
            ->first();

        $latestDetectionCount = $latestInference?->total_jentik;

        $todayDetectionTotal = InferenceResult::where('device_id', $deviceId)
            ->whereDate('inference_at', now()->toDateString())
            ->count();

        // Debug KPI sources to verify data matches DB
        try {
            $imageCount = DB::table('images')->where('device_id', $deviceId)->count();
            $inferenceCount = DB::table('inference_results')->where('device_id', $deviceId)->count();
            $latestInference = DB::table('inference_results')
                ->where('device_id', $deviceId)
                ->orderByDesc('inference_at')
                ->first();

            Log::info('KPI debug snapshot', [
                'device_id' => $deviceId,
                'device_code' => $deviceCode,
                'image_count' => $imageCount,
                'inference_count' => $inferenceCount,
                'latest_inference_at' => $latestInference->inference_at ?? null,
                'latest_total_jentik' => $latestInference->total_jentik ?? null,
                'latest_image_id' => $latestInference->image_id ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('KPI debug snapshot failed', [
                'device_id' => $deviceId,
                'error' => $e->getMessage(),
            ]);
        }

        // Get latest servo control status
        $servoControl = DeviceControl::where('device_id', $deviceId)
            ->orderByDesc('created_at')
            ->first();

        $servoStatus = [
            'is_active' => false,
            'command' => null,
            'status' => null,
            'last_activation' => null,
            'updated_at' => null,
        ];

        if ($servoControl) {
            $isActivateServo = $servoControl->control_command === 'ACTIVATE_SERVO';
            $isPending = $servoControl->status === 'PENDING';

            $servoStatus = [
                'is_active' => $isActivateServo && $isPending,
                'command' => $servoControl->control_command,
                'status' => $servoControl->status,
                'last_activation' => $servoControl->updated_at,
                'updated_at' => $servoControl->updated_at,
            ];
        }

        return view('dashboard2', [
            'device_code' => $deviceCode,
            'device_location' => $deviceLocation,
            'device_info' => $deviceInfo,
            'latest_detection_count' => $latestDetectionCount,
            'today_detection_total' => $todayDetectionTotal,
            'history' => $historyPaginator,
            'gallery' => $galleryImages,
            'chart_labels' => $chartLabels,
            'chart_values' => $chartValues,
            'chart_date_from' => $chartDateFrom->toDateString(),
            'chart_date_to' => $chartDateTo->toDateString(),
            'chart_range_text' => $chartDateFrom->format('d M Y') . ' - ' . $chartDateTo->format('d M Y'),
            'servo_status' => $servoStatus,
        ]);
    }

}

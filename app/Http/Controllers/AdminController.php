<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Device;
use App\Models\Image;
use App\Models\InferenceResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AdminController extends Controller
{
    public function index(Request $request)
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
            ->where('image_type', 'preprocessed')
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

        return view('admin.admin', [
            'admin_email' => session('admin_email'),
            'stats' => $stats,
            'devices' => $devices,
            'inferenceResults' => $inferenceResults,
            'galleryImages' => $galleryImages,
            'deviceOptions' => $deviceOptions,
            'inferenceStatusOptions' => $inferenceStatusOptions,
            'deviceFilters' => $deviceFilters,
            'inferenceFilters' => $inferenceFilters,
            'galleryFilters' => $galleryFilters,
        ]);
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

<?php

namespace App\Http\Controllers;

use App\Models\InferenceResult;
use App\Models\Image;
use App\Services\HistoryService;
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
        $galleryImages = Image::with('inferenceResult')
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

        // Weekly chart: last 7 days counts from inference_results
        $startDate = now()->subDays(6)->startOfDay();
        $rawCounts = DB::table('inference_results')
            ->selectRaw('DATE(inference_at) as d, COALESCE(SUM(total_jentik), 0) as total')
            ->where('device_id', $deviceId)
            ->where('inference_at', '>=', $startDate)
            ->groupBy('d')
            ->pluck('total', 'd');

        $chartLabels = [];
        $chartValues = [];
        for ($i = 0; $i < 7; $i++) {
            $day = now()->subDays(6 - $i)->startOfDay();
            $chartLabels[] = $day->locale('id')->isoFormat('ddd');
            $chartValues[] = (int) ($rawCounts[$day->toDateString()] ?? 0);
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
        ]);
    }

}

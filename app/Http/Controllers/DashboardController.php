<?php

namespace App\Http\Controllers;

use App\Services\MosquitoApiService;
use App\Models\InferenceResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    private MosquitoApiService $apiService;

    public function __construct(MosquitoApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display the dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $deviceCode = session('device_code');
        $deviceId = session('device_id');
        $deviceLocation = session('device_location', 'Unknown Location');
        $deviceInfo = session('device_info', []);

        // Fetch detection history from shared database (same DB as FastAPI)
        $detectionHistory = $this->apiService->getDetectionHistory($deviceCode, 10);

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
            'images' => $detectionHistory,
        ]);
    }

    /**
     * Get detection history (for AJAX requests)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistory(Request $request)
    {
        $deviceCode = session('device_code');
        $limit = $request->input('limit', 10);

        $detectionHistory = $this->apiService->getDetectionHistory($deviceCode, $limit);

        return response()->json([
            'status' => 'success',
            'data' => $detectionHistory,
        ]);
    }

    /**
     * Export detection history as CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportHistory(Request $request)
    {
        $deviceCode = session('device_code');
        $detectionHistory = $this->apiService->getDetectionHistory($deviceCode, 1000);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"detection_history_{$deviceCode}.csv\"",
        ];

        $callback = function () use ($detectionHistory) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Timestamp', 'Time', 'Date', 'Larvae Count', 'Status']);

            foreach ($detectionHistory as $record) {
                fputcsv($file, [
                    $record['captured_at'] ?? '',
                    $record['time'] ?? '',
                    $record['date'] ?? '',
                    $record['count'] ?? 0,
                    $record['status'] ?? 'Unknown',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

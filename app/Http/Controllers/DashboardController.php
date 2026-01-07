<?php

namespace App\Http\Controllers;

use App\Services\MosquitoApiService;
use Illuminate\Http\Request;

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
        $deviceLocation = session('device_location', 'Unknown Location');
        $deviceInfo = session('device_info', []);

        // Fetch detection history from shared database (same DB as FastAPI)
        $detectionHistory = $this->apiService->getDetectionHistory($deviceCode, 10);

        return view('dashboard2', [
            'device_code' => $deviceCode,
            'device_location' => $deviceLocation,
            'device_info' => $deviceInfo,
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

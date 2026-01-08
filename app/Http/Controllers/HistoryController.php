<?php

namespace App\Http\Controllers;

use App\Services\HistoryService;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    private HistoryService $historyService;

    public function __construct(HistoryService $historyService)
    {
        $this->historyService = $historyService;
    }

    /**
     * Return paginated history for AJAX/SPA consumption.
     */
    public function index(Request $request)
    {
        $deviceId = session('device_id');
        $limit = max(1, (int) $request->input('limit', 10));

        $history = $this->historyService->getRecentRecords($deviceId, $limit);

        return response()->json([
            'status' => 'success',
            'data' => $history,
        ]);
    }

    /**
     * Stream the detection history CSV based on database records.
     */
    public function export(Request $request)
    {
        $deviceCode = session('device_code');
        $deviceId = session('device_id');
        $records = $this->historyService->getAllRecords($deviceId);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"detection_history_{$deviceCode}.csv\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Timestamp', 'Time', 'Date', 'Larvae Count', 'Status']);

            foreach ($records as $record) {
                fputcsv($file, [
                    $record['captured_at'] ?? '',
                    $record['time'],
                    $record['date'],
                    $record['count'],
                    $record['status'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

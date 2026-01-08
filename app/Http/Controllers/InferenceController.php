<?php

namespace App\Http\Controllers;

use App\Models\InferenceResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InferenceController extends Controller
{
    /**
     * Return inference results for the authenticated device.
     */
    public function index(Request $request)
    {
        $deviceId = session('device_id');
        $deviceCode = session('device_code');

        if (!$deviceId || !$deviceCode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Device not authenticated',
            ], 401);
        }

        $limit = (int) $request->query('limit', 20);

        try {
            $results = InferenceResult::with([
                'image' => function ($query) {
                    $query->select('id', 'device_code', 'image_path', 'captured_at');
                }
            ])
                ->where('device_id', $deviceId)
                ->orderByDesc('inference_at')
                ->limit($limit)
                ->get()
                ->map(function (InferenceResult $row) {
                    return [
                        'id' => $row->id,
                        'device_code' => $row->device_code,
                        'inference_at' => optional($row->inference_at)->toIso8601String(),
                        'total_jentik' => $row->total_jentik,
                        'total_objects' => $row->total_objects,
                        'avg_confidence' => $row->avg_confidence,
                        'status' => $row->status,
                        'image_path' => optional($row->image)->image_path,
                        'captured_at' => optional(optional($row->image)->captured_at)->toIso8601String(),
                    ];
                });

            return response()->json([
                'status' => 'success',
                'count' => $results->count(),
                'data' => $results,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch inference results', [
                'device_id' => $deviceId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data inference',
            ], 500);
        }
    }
}

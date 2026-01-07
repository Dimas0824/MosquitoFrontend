<?php

namespace App\Http\Controllers;

use App\Services\MosquitoApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActuatorController extends Controller
{
    private MosquitoApiService $apiService;

    public function __construct(MosquitoApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Activate the actuator (pump/larvasida) via FastAPI backend
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(Request $request)
    {
        $deviceCode = session('device_code');
        $credentials = session('api_credentials');

        if (!$deviceCode || !$credentials) {
            return response()->json([
                'success' => false,
                'message' => 'Device not authenticated',
            ], 401);
        }

        // Call FastAPI backend to activate servo
        $result = $this->apiService->activateServo(
            $credentials['device_code'],
            $credentials['password']
        );

        if ($result) {
            Log::info('Manual actuator activation requested via FastAPI', [
                'device_code' => $deviceCode,
                'result' => $result,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Aktuator diaktifkan secara manual',
                'data' => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengaktifkan aktuator',
        ], 500);
    }
}

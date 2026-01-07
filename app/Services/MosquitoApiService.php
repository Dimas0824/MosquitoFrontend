<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MosquitoApiService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('MOSQUITO_API_URL', 'http://localhost:8080');
    }

    /**
     * Get device info from backend API
     *
     * @param string $deviceCode
     * @param string $password
     * @return array|null
     */
    public function getDeviceInfo(string $deviceCode, string $password): ?array
    {
        try {
            $response = Http::withBasicAuth($deviceCode, $password)
                ->timeout(10)
                ->get("{$this->baseUrl}/api/device/info");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Failed to get device info', [
                'device_code' => $deviceCode,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting device info', [
                'device_code' => $deviceCode,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Check API health
     *
     * @return bool
     */
    public function checkHealth(): bool
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/api/health");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('API health check failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Validate device credentials against backend API
     *
     * @param string $deviceCode
     * @param string $password
     * @return bool
     */
    public function validateCredentials(string $deviceCode, string $password): bool
    {
        $deviceInfo = $this->getDeviceInfo($deviceCode, $password);

        return $deviceInfo !== null && isset($deviceInfo['device_code']);
    }

    /**
     * Get device control status from backend API
     *
     * @param string $deviceCode
     * @param string $password
     * @return array|null
     */
    public function getDeviceControl(string $deviceCode, string $password): ?array
    {
        try {
            $response = Http::withBasicAuth($deviceCode, $password)
                ->timeout(10)
                ->get("{$this->baseUrl}/api/device/{$deviceCode}/control");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting device control', [
                'device_code' => $deviceCode,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Activate servo/actuator
     *
     * @param string $deviceCode
     * @param string $password
     * @return array|null
     */
    public function activateServo(string $deviceCode, string $password): ?array
    {
        try {
            $response = Http::withBasicAuth($deviceCode, $password)
                ->timeout(10)
                ->post("{$this->baseUrl}/api/device/{$deviceCode}/activate_servo");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error activating servo', [
                'device_code' => $deviceCode,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get detection history with images and inference results
     * This method fetches data from the shared database that FastAPI uses
     *
     * @param string $deviceCode
     * @param int $limit
     * @return array
     */
    public function getDetectionHistory(string $deviceCode, int $limit = 10): array
    {
        try {
            // Query local database (same DB that FastAPI uses)
            $images = \App\Models\Image::where('device_code', $deviceCode)
                ->where('image_type', 'original')
                ->with('inferenceResult')
                ->orderBy('captured_at', 'desc')
                ->limit($limit)
                ->get();

            $history = [];
            foreach ($images as $image) {
                $inference = $image->inferenceResult;
                $larvaeCount = $inference ? $inference->total_jentik : 0;

                $status = 'Aman';
                if ($larvaeCount > 5) {
                    $status = 'Bahaya';
                } elseif ($larvaeCount > 0) {
                    $status = 'Waspada';
                }

                $capturedAt = $image->captured_at;
                $history[] = [
                    'id' => $image->id,
                    'time' => $capturedAt->format('H:i') . ' WIB',
                    'date' => $capturedAt->isToday() ? 'Hari Ini' :
                        ($capturedAt->isYesterday() ? 'Kemarin' : $capturedAt->format('d M Y')),
                    'count' => $larvaeCount,
                    'status' => $status,
                    'image_path' => $image->image_path,
                    'captured_at' => $capturedAt->toIso8601String(),
                    'inference_id' => $inference ? $inference->id : null,
                ];
            }

            return $history;
        } catch (\Exception $e) {
            Log::error('Error getting detection history', [
                'device_code' => $deviceCode,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get active alerts for device
     *
     * @param string $deviceCode
     * @return array
     */
    public function getActiveAlerts(string $deviceCode): array
    {
        try {
            $alerts = \App\Models\Alert::where('device_code', $deviceCode)
                ->whereNull('resolved_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return $alerts->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting alerts', [
                'device_code' => $deviceCode,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}

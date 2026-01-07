<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\MosquitoApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private MosquitoApiService $apiService;

    public function __construct(MosquitoApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Handle device login
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $deviceCode = $request->input('device_id');
        $password = $request->input('password');

        // Validate credentials with backend API
        $deviceInfo = $this->apiService->getDeviceInfo($deviceCode, $password);

        if (!$deviceInfo) {
            return back()
                ->withInput()
                ->withErrors(['device_id' => 'Device ID atau kata sandi tidak valid.']);
        }

        // Check if device exists in local database, if not create it
        $device = Device::firstOrCreate(
            ['device_code' => $deviceCode],
            [
                'password' => Hash::make($password),
                'location' => $deviceInfo['location'] ?? null,
                'description' => $deviceInfo['description'] ?? null,
                'is_active' => $deviceInfo['is_active'] ?? true,
            ]
        );

        // Store device info in session
        session([
            'device_id' => $device->id,
            'device_code' => $device->device_code,
            'device_location' => $deviceInfo['location'] ?? $device->location,
            'device_info' => $deviceInfo,
            'api_credentials' => [
                'device_code' => $deviceCode,
                'password' => $password,
            ],
        ]);

        Log::info('Device logged in', [
            'device_code' => $deviceCode,
            'device_id' => $device->id,
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Handle logout
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $deviceCode = session('device_code');

        $request->session()->flush();

        Log::info('Device logged out', [
            'device_code' => $deviceCode,
        ]);

        return redirect()->route('login')
            ->with('success', 'Berhasil keluar dari sistem.');
    }
}

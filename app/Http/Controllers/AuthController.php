<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

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

        // Single round-trip: join devices + device_auth to avoid multiple queries
        $record = Device::query()
            ->select([
                'devices.id',
                'devices.device_code',
                'devices.location',
                'devices.description',
                'devices.is_active',
                'device_auth.password_hash',
            ])
            ->leftJoin('device_auth', 'device_auth.device_id', '=', 'devices.id')
            ->where('devices.device_code', $deviceCode)
            ->first();

        if (!$record || !$record->password_hash || !Hash::check($password, $record->password_hash)) {
            return back()
                ->withInput()
                ->withErrors(['device_id' => 'Device ID atau kata sandi tidak valid.']);
        }

        if (!$record->is_active) {
            return back()
                ->withInput()
                ->withErrors(['device_id' => 'Perangkat sedang tidak aktif.']);
        }

        $deviceInfo = [
            'device_code' => $record->device_code,
            'location' => $record->location,
            'description' => $record->description,
            'is_active' => (bool) $record->is_active,
        ];

        session([
            'device_id' => $record->id,
            'device_code' => $record->device_code,
            'device_location' => $record->location,
            'device_info' => $deviceInfo,
            'api_credentials' => [
                'device_code' => $deviceCode,
                'password' => $password,
            ],
        ]);

        Log::info('Device logged in', [
            'device_code' => $deviceCode,
            'device_id' => $record->id,
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

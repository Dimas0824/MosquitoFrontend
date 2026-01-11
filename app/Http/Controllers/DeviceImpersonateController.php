<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeviceImpersonateController extends Controller
{
    public function impersonate(Device $device)
    {
        // Store admin session for later restoration
        $adminSession = [
            'admin_id' => session('admin_id'),
            'admin_email' => session('admin_email'),
            'is_admin' => session('is_admin'),
        ];

        session(['impersonating_from_admin' => $adminSession]);

        // Set device session like a device login
        session([
            'device_id' => $device->id,
            'device_code' => $device->device_code,
            'device_location' => $device->location,
            'device_info' => [
                'device_code' => $device->device_code,
                'location' => $device->location,
                'description' => $device->description,
                'is_active' => (bool) $device->is_active,
            ],
        ]);

        Log::info('Admin impersonating device', [
            'admin_id' => $adminSession['admin_id'],
            'device_id' => $device->id,
            'device_code' => $device->device_code,
        ]);

        return redirect()->route('dashboard')
            ->with('info', "Anda sedang melihat sebagai device: {$device->device_code}");
    }

    public function leave()
    {
        $impersonatingFrom = session('impersonating_from_admin');

        if (!$impersonatingFrom) {
            return redirect()->route('admin.dashboard');
        }

        // Clear device session
        session()->forget(['device_id', 'device_code', 'device_location', 'device_info', 'api_credentials']);

        // Restore admin session
        session([
            'admin_id' => $impersonatingFrom['admin_id'],
            'admin_email' => $impersonatingFrom['admin_email'],
            'is_admin' => $impersonatingFrom['is_admin'],
        ]);

        session()->forget('impersonating_from_admin');

        Log::info('Admin left device impersonation', [
            'admin_id' => $impersonatingFrom['admin_id'],
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Kembali ke panel admin');
    }
}

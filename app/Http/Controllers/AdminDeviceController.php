<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminDeviceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_code' => 'required|string|max:255|unique:devices,device_code',
            'password' => 'required|string|min:6',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $deviceData = $validated;
        unset($deviceData['password']);

        $deviceData['is_active'] = $request->boolean('is_active', true);

        $device = Device::create($deviceData);

        DeviceAuth::create([
            'device_id' => $device->id,
            'device_code' => $device->device_code,
            'password_hash' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Device berhasil ditambahkan.');
    }

    public function update(Request $request, Device $device)
    {
        if (!$request->filled('password')) {
            $request->merge(['password' => null]);
        }

        $validated = $request->validate([
            'device_code' => 'required|string|max:255|unique:devices,device_code,' . $device->id,
            'password' => 'nullable|string|min:6',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $deviceData = $validated;
        unset($deviceData['password']);

        $deviceData['is_active'] = $request->boolean('is_active', true);

        $device->update($deviceData);

        $deviceAuth = DeviceAuth::firstOrNew(['device_id' => $device->id]);
        $deviceAuth->device_code = $device->device_code;

        if ($request->filled('password')) {
            $deviceAuth->password_hash = Hash::make($request->input('password'));
        } elseif (!$deviceAuth->exists || !$deviceAuth->password_hash) {
            return redirect()->route('admin.dashboard')->withErrors([
                'password' => 'Password diperlukan untuk membuat kredensial perangkat.',
            ]);
        }

        $deviceAuth->save();

        return redirect()->route('admin.dashboard')->with('success', 'Device berhasil diperbarui.');
    }

    public function destroy(Device $device)
    {
        DeviceAuth::where('device_id', $device->id)->delete();
        $device->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Device berhasil dihapus.');
    }
}

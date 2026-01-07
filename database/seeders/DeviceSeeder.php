<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Note: Ini hanya untuk local testing.
     * Untuk production, device akan di-register via FastAPI backend.
     */
    public function run(): void
    {
        // Sample devices untuk testing (sync dengan backend FastAPI)
        $devices = [
            [
                'id' => '4ebc0405-7647-436a-a448-4ac8f0a462cb',
                'device_code' => 'test',
                'location' => null,
                'description' => null,
                'is_active' => true,
                'created_at' => '2026-01-01 20:44:27',
            ],
            [
                'id' => '6699effb-365f-4b45-b42a-f15e08c4323f',
                'device_code' => 'ESP32_test_01',
                'location' => null,
                'description' => 'Testing device',
                'is_active' => true,
                'created_at' => '2026-01-01 20:21:41',
            ],
        ];

        foreach ($devices as $device) {
            DB::table('devices')->insertOrIgnore($device);
        }

        // Device auth credentials
        $deviceAuth = [
            [
                'id' => 'b0f88989-2db1-4e86-a77b-a90696e4bd0b',
                'device_id' => '6699effb-365f-4b45-b42a-f15e08c4323f',
                'device_code' => 'ESP32_test_01',
                'password_hash' => '$2b$12$3cdTbfU0RR1bsaFXEQBNT.UTJ.FCyFnxfTYKGbM1EASXQzSkUIDai',
            ],
            [
                'id' => 'e5a26025-e60e-43f6-9f31-1d95719f0967',
                'device_id' => '4ebc0405-7647-436a-a448-4ac8f0a462cb',
                'device_code' => 'test',
                'password_hash' => '$2b$12$7trnP87B5t7XRLtr2XzE8elsydYNyE/D8w.Sr3cyEziPihWe70FDS',
            ],
        ];

        foreach ($deviceAuth as $auth) {
            DB::table('device_auth')->insertOrIgnore($auth);
        }

        $this->command->info('✓ Devices seeded from backend database');
        $this->command->info('✓ Test devices: test, ESP32_test_01');
    }
}

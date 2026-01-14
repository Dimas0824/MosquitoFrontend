<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RailwayDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Devices
        DB::table('devices')->insert([
            [
                'id' => '019b9ce5-018a-72a9-8625-01e0d66cdb19',
                'device_code' => 'ESP_admin',
                'location' => 'Malang',
                'description' => 'WOKWI',
                'is_active' => true,
                'created_at' => '2026-01-08 09:16:56',
            ],
            [
                'id' => '4ebc0405-7647-436a-a448-4ac8f0a462cb',
                'device_code' => 'test',
                'location' => null,
                'description' => null,
                'is_active' => true,
                'created_at' => '2026-01-01 20:44:27',
            ],
        ]);

        // Seed Device Auth
        DB::table('device_auth')->insert([
            [
                'id' => '019b9ce5-02c5-72de-a00a-8eda466b6c92',
                'device_id' => '019b9ce5-018a-72a9-8625-01e0d66cdb19',
                'device_code' => 'ESP_admin',
                'password_hash' => '$2y$12$lrMNzxjKXw451gc9yM2SFuMW6Wbkkvvhr07KMoi1LGnLCW2.U83cy',
            ],
            [
                'id' => 'e5a26025-e60e-43f6-9f31-1d95719f0967',
                'device_id' => '4ebc0405-7647-436a-a448-4ac8f0a462cb',
                'device_code' => 'test',
                'password_hash' => '$2y$12$zeD5m.KqF870NteVwPBlT.2bQoqJHxPtyBhjx4Z7EhqKUpS.wnQFy',
            ],
        ]);

        // Seed Device Controls
        DB::table('device_controls')->insert([
            'id' => '316c300a-6fd5-451e-a841-b80846089488',
            'device_id' => '4ebc0405-7647-436a-a448-4ac8f0a462cb',
            'device_code' => 'test',
            'control_command' => 'ACTIVATE_SERVO',
            'status' => 'PENDING',
            'message' => 'Servo activation requested',
            'created_at' => '2026-01-08 02:16:48',
            'updated_at' => '2026-01-08 02:40:00',
        ]);

        // Seed Alerts
        DB::table('alerts')->insert([
            'id' => 'a9390c97-3033-4c44-a26a-91f4705c1c66',
            'device_id' => '4ebc0405-7647-436a-a448-4ac8f0a462cb',
            'device_code' => 'test',
            'alert_type' => 'LARVA_DETECTED',
            'alert_message' => 'Terdeteksi 2 jentik nyamuk',
            'alert_level' => 'critical',
            'created_at' => '2026-01-01 21:22:24',
            'resolved_at' => null,
        ]);
    }
}

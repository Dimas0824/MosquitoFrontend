<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin123'),
        ]);

        // Seed data from railway export
        $this->call([
            RailwayDataSeeder::class,
        ]);
    }
}

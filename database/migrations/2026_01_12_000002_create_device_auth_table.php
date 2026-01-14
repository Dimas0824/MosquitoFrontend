<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('device_auth', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('device_id');
            $table->string('device_code')->unique();
            $table->string('password_hash');

            $table->index('device_code', 'idx_device_code');
            $table->index('device_id');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_auth');
    }
};

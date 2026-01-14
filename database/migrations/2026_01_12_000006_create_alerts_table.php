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
        Schema::create('alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('device_id');
            $table->string('device_code');
            $table->string('alert_type', 100)->nullable();
            $table->text('alert_message')->nullable();
            $table->string('alert_level', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();

            $table->index('device_code', 'idx_device_code');
            $table->index('device_id', 'idx_device_id');
            $table->index('created_at', 'idx_created_at');
            $table->index('resolved_at', 'idx_resolved_at');
            $table->index('alert_level', 'idx_alert_level');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};

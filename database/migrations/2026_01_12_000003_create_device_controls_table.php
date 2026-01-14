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
        Schema::create('device_controls', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->uuid('device_id')->unique();
            $table->string('device_code', 50)->unique();
            $table->enum('control_command', ['ACTIVATE', 'SLEEP', 'ACTIVATE_SERVO', 'STOP_SERVO']);
            $table->enum('status', ['PENDING', 'EXECUTED', 'FAILED'])->default('PENDING');
            $table->text('message')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('device_code')->references('device_code')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_controls');
    }
};

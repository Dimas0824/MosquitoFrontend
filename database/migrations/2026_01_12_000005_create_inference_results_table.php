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
        Schema::create('inference_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('image_id');
            $table->uuid('device_id');
            $table->string('device_code');
            $table->timestamp('inference_at')->useCurrent();
            $table->json('raw_prediction')->nullable();
            $table->integer('total_objects')->default(0);
            $table->integer('total_jentik')->default(0);
            $table->integer('total_non_jentik')->default(0);
            $table->float('avg_confidence')->default(0);
            $table->string('parsing_version', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->text('error_message')->nullable();

            $table->index('device_code', 'idx_device_code');
            $table->index('device_id', 'idx_device_id');
            $table->index('inference_at', 'idx_inference_at');
            $table->index('status', 'idx_status');
            $table->index('image_id');
            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inference_results');
    }
};

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
        Schema::create('images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('device_id');
            $table->string('device_code');
            $table->string('image_type', 50)->nullable();
            $table->string('image_path', 500)->nullable();
            $table->longblob('image_blob')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('checksum', 64)->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();

            $table->index('device_code', 'idx_device_code');
            $table->index('device_id', 'idx_device_id');
            $table->index('uploaded_at', 'idx_uploaded_at');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};

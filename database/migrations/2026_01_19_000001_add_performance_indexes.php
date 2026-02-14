<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }

    /**
     * Run the migrations.
     * These indexes are critical for query performance optimization.
     */
    public function up(): void
    {
        // Index for inference_results - most critical for slow queries
        Schema::table('inference_results', function (Blueprint $table) {
            if (!$this->indexExists('inference_results', 'idx_inference_device_date')) {
                $table->index(['device_id', 'inference_at'], 'idx_inference_device_date');
            }
            if (!$this->indexExists('inference_results', 'idx_inference_at')) {
                $table->index('inference_at', 'idx_inference_at');
            }
        });

        // Index for device_controls
        Schema::table('device_controls', function (Blueprint $table) {
            if (!$this->indexExists('device_controls', 'idx_device_controls_device_date')) {
                $table->index(['device_id', 'created_at'], 'idx_device_controls_device_date');
            }
        });

        // Index for images
        Schema::table('images', function (Blueprint $table) {
            if (!$this->indexExists('images', 'idx_images_type_captured')) {
                $table->index(['image_type', 'captured_at'], 'idx_images_type_captured');
            }
            if (!$this->indexExists('images', 'idx_images_device_captured')) {
                $table->index(['device_id', 'captured_at'], 'idx_images_device_captured');
            }
        });

        // Index for devices
        Schema::table('devices', function (Blueprint $table) {
            if (!$this->indexExists('devices', 'idx_devices_code')) {
                $table->index('device_code', 'idx_devices_code');
            }
        });

        // Index for device_auth
        Schema::table('device_auth', function (Blueprint $table) {
            if (!$this->indexExists('device_auth', 'idx_device_auth_device')) {
                $table->index('device_id', 'idx_device_auth_device');
            }
        });

        // Index for users
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'idx_users_email')) {
                $table->index('email', 'idx_users_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inference_results', function (Blueprint $table) {
            $table->dropIndex('idx_inference_device_date');
            $table->dropIndex('idx_inference_at');
        });

        Schema::table('device_controls', function (Blueprint $table) {
            $table->dropIndex('idx_device_controls_device_date');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->dropIndex('idx_images_type_captured');
            $table->dropIndex('idx_images_device_captured');
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->dropIndex('idx_devices_code');
        });

        Schema::table('device_auth', function (Blueprint $table) {
            $table->dropIndex('idx_device_auth_device');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
        });
    }
};

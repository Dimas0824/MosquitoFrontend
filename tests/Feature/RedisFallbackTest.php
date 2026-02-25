<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Redis;
use RuntimeException;
use Tests\TestCase;

class RedisFallbackTest extends TestCase
{
    private string $markerPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->markerPath = storage_path('framework/cache/redis_fallback_state.json');
        $this->deleteMarkerIfExists();
    }

    protected function tearDown(): void
    {
        $this->deleteMarkerIfExists();

        parent::tearDown();
    }

    public function test_it_switches_to_fallback_drivers_when_redis_is_unavailable(): void
    {
        config([
            'redis_fallback.enabled' => true,
            'redis_fallback.cache_store' => 'failover',
            'redis_fallback.session_driver' => 'database',
            'redis_fallback.queue_connection' => 'failover',
            'redis_fallback.probe_cooldown_seconds' => 15,
            'cache.default' => 'redis',
            'cache.stores.failover' => ['driver' => 'failover', 'stores' => ['database', 'array']],
            'session.driver' => 'redis',
            'queue.default' => 'redis',
            'queue.connections.failover' => ['driver' => 'failover', 'connections' => ['database', 'deferred']],
        ]);

        Redis::shouldReceive('connection->ping')
            ->times(2)
            ->andThrow(new RuntimeException('redis down'));

        (new AppServiceProvider($this->app))->boot();

        $this->assertSame('failover', config('cache.default'));
        $this->assertSame('database', config('session.driver'));
        $this->assertSame('failover', config('queue.default'));
    }

    public function test_it_uses_probe_cooldown_marker_when_redis_is_down(): void
    {
        config([
            'redis_fallback.enabled' => true,
            'redis_fallback.cache_store' => 'failover',
            'redis_fallback.session_driver' => 'database',
            'redis_fallback.queue_connection' => 'failover',
            'redis_fallback.probe_cooldown_seconds' => 60,
            'cache.default' => 'database',
            'session.driver' => 'redis',
            'queue.default' => 'database',
        ]);

        Redis::shouldReceive('connection->ping')
            ->once()
            ->andThrow(new RuntimeException('redis down'));

        (new AppServiceProvider($this->app))->boot();

        $this->assertFileExists($this->markerPath);
        $this->assertSame('database', config('session.driver'));

        config([
            'session.driver' => 'redis',
        ]);

        Redis::shouldReceive('connection->ping')->never();

        (new AppServiceProvider($this->app))->boot();

        $this->assertSame('database', config('session.driver'));
    }

    private function deleteMarkerIfExists(): void
    {
        if (is_file($this->markerPath)) {
            @unlink($this->markerPath);
        }
    }
}

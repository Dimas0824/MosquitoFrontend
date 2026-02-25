<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Per-request Redis availability cache by connection name.
     *
     * @var array<string, bool>
     */
    private array $redisAvailability = [];
    /**
     * Cached Redis failure state loaded from disk.
     *
     * @var array<string, int>|null
     */
    private ?array $redisFailureState = null;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->redisFallbackEnabled()) {
            return;
        }

        $this->applyCacheFallbackIfRedisUnavailable();
        $this->applySessionFallbackIfRedisUnavailable();
        $this->applyQueueFallbackIfRedisUnavailable();
    }

    private function redisFallbackEnabled(): bool
    {
        return (bool) config('redis_fallback.enabled', true);
    }

    private function applyCacheFallbackIfRedisUnavailable(): void
    {
        if (config('cache.default') !== 'redis') {
            return;
        }

        $connection = (string) (config('cache.stores.redis.connection') ?? 'cache');

        if ($this->isRedisConnectionAvailable($connection)) {
            return;
        }

        $fallbackStore = (string) config('redis_fallback.cache_store', $this->resolveCacheFallbackStore());

        if (! array_key_exists($fallbackStore, (array) config('cache.stores', [])) || $fallbackStore === 'redis') {
            $fallbackStore = $this->resolveCacheFallbackStore();
        }

        config(['cache.default' => $fallbackStore]);

        Log::warning('Redis unavailable. Falling back cache store.', [
            'redis_connection' => $connection,
            'cache_fallback_store' => $fallbackStore,
        ]);
    }

    private function applySessionFallbackIfRedisUnavailable(): void
    {
        if (config('session.driver') !== 'redis') {
            return;
        }

        $connection = (string) (config('session.connection') ?: 'default');

        if ($this->isRedisConnectionAvailable($connection)) {
            return;
        }

        $fallbackDriver = (string) config('redis_fallback.session_driver', $this->resolveSessionFallbackDriver());

        if ($fallbackDriver === 'redis') {
            $fallbackDriver = $this->resolveSessionFallbackDriver();
        }

        config(['session.driver' => $fallbackDriver]);

        if ($fallbackDriver !== 'redis') {
            config(['session.connection' => null, 'session.store' => null]);
        }

        Log::warning('Redis unavailable. Falling back session driver.', [
            'redis_connection' => $connection,
            'session_fallback_driver' => $fallbackDriver,
        ]);
    }

    private function applyQueueFallbackIfRedisUnavailable(): void
    {
        if (config('queue.default') !== 'redis') {
            return;
        }

        $connection = (string) (config('queue.connections.redis.connection') ?? 'default');

        if ($this->isRedisConnectionAvailable($connection)) {
            return;
        }

        $fallbackConnection = (string) config('redis_fallback.queue_connection', $this->resolveQueueFallbackConnection());

        if (! array_key_exists($fallbackConnection, (array) config('queue.connections', [])) || $fallbackConnection === 'redis') {
            $fallbackConnection = $this->resolveQueueFallbackConnection();
        }

        config(['queue.default' => $fallbackConnection]);

        Log::warning('Redis unavailable. Falling back queue connection.', [
            'redis_connection' => $connection,
            'queue_fallback_connection' => $fallbackConnection,
        ]);
    }

    private function isRedisConnectionAvailable(string $connection): bool
    {
        if (array_key_exists($connection, $this->redisAvailability)) {
            return $this->redisAvailability[$connection];
        }

        if ($this->shouldSkipRedisProbe($connection)) {
            return $this->redisAvailability[$connection] = false;
        }

        try {
            Redis::connection($connection)->ping();
            $this->clearRedisFailureMarker($connection);

            return $this->redisAvailability[$connection] = true;
        } catch (Throwable $exception) {
            $this->rememberRedisFailureMarker($connection);

            Log::warning('Redis ping failed.', [
                'redis_connection' => $connection,
                'error' => $exception->getMessage(),
            ]);

            return $this->redisAvailability[$connection] = false;
        }
    }

    private function shouldSkipRedisProbe(string $connection): bool
    {
        $cooldownSeconds = (int) config('redis_fallback.probe_cooldown_seconds', 15);

        if ($cooldownSeconds <= 0) {
            return false;
        }

        $state = $this->readRedisFailureState();
        $lastFailureAt = $state[$connection] ?? null;

        if (! is_int($lastFailureAt) || $lastFailureAt <= 0) {
            return false;
        }

        return (time() - $lastFailureAt) < $cooldownSeconds;
    }

    private function rememberRedisFailureMarker(string $connection): void
    {
        $state = $this->readRedisFailureState();
        $state[$connection] = time();

        $this->persistRedisFailureState($state);
    }

    private function clearRedisFailureMarker(string $connection): void
    {
        $state = $this->readRedisFailureState();

        if (! array_key_exists($connection, $state)) {
            return;
        }

        unset($state[$connection]);
        $this->persistRedisFailureState($state);
    }

    /**
     * @return array<string, int>
     */
    private function readRedisFailureState(): array
    {
        if ($this->redisFailureState !== null) {
            return $this->redisFailureState;
        }

        $path = $this->redisFailureStatePath();

        if (! is_file($path)) {
            return $this->redisFailureState = [];
        }

        $raw = @file_get_contents($path);

        if ($raw === false || $raw === '') {
            return $this->redisFailureState = [];
        }

        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            return $this->redisFailureState = [];
        }

        $state = [];

        foreach ($decoded as $connection => $timestamp) {
            if (is_string($connection) && is_numeric($timestamp)) {
                $state[$connection] = (int) $timestamp;
            }
        }

        return $this->redisFailureState = $state;
    }

    /**
     * @param array<string, int> $state
     */
    private function persistRedisFailureState(array $state): void
    {
        $this->redisFailureState = $state;
        $path = $this->redisFailureStatePath();

        try {
            $directory = dirname($path);

            if (! is_dir($directory) && ! @mkdir($directory, 0755, true) && ! is_dir($directory)) {
                return;
            }

            if ($state === []) {
                @unlink($path);

                return;
            }

            @file_put_contents($path, json_encode($state, JSON_THROW_ON_ERROR), LOCK_EX);
        } catch (Throwable $exception) {
            Log::debug('Unable to persist Redis failure state marker.', [
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function redisFailureStatePath(): string
    {
        return storage_path('framework/cache/redis_fallback_state.json');
    }

    private function resolveCacheFallbackStore(): string
    {
        $stores = array_keys((array) config('cache.stores', []));

        foreach (['failover', 'database', 'file', 'array'] as $candidate) {
            if (in_array($candidate, $stores, true)) {
                return $candidate;
            }
        }

        return 'array';
    }

    private function resolveSessionFallbackDriver(): string
    {
        return 'database';
    }

    private function resolveQueueFallbackConnection(): string
    {
        $connections = array_keys((array) config('queue.connections', []));

        foreach (['failover', 'database', 'sync', 'deferred'] as $candidate) {
            if (in_array($candidate, $connections, true) && $candidate !== 'redis') {
                return $candidate;
            }
        }

        return 'sync';
    }
}

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Redis Runtime Fallback
    |--------------------------------------------------------------------------
    |
    | When enabled, the application will probe Redis during boot and switch to
    | fallback drivers if the configured Redis connection is unreachable.
    |
    */
    'enabled' => env('REDIS_FALLBACK_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Fallback Targets
    |--------------------------------------------------------------------------
    |
    | These targets are used when a component is configured with Redis but the
    | connection cannot be reached.
    |
    */
    'cache_store' => env('REDIS_FALLBACK_CACHE_STORE', 'failover'),
    'session_driver' => env('REDIS_FALLBACK_SESSION_DRIVER', 'database'),
    'queue_connection' => env('REDIS_FALLBACK_QUEUE_CONNECTION', 'failover'),

    /*
    |--------------------------------------------------------------------------
    | Probe Cooldown
    |--------------------------------------------------------------------------
    |
    | When Redis is down, the app stores a short-lived marker to avoid pinging
    | Redis on every incoming request (which can increase latency).
    | Set to 0 to always probe on each request.
    |
    */
    'probe_cooldown_seconds' => (int) env('REDIS_FALLBACK_PROBE_COOLDOWN_SECONDS', 15),
];

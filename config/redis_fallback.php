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
];

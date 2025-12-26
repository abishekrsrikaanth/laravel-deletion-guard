<?php

// config for WorkDoneRight/DeletionGuard
return [
    /*
    |--------------------------------------------------------------------------
    | Dependency Discovery Mode
    |--------------------------------------------------------------------------
    | Options:
    | - explicit   → Reflection + explicit opt-in (default, safest)
    | - docblock   → Reflection + @deleteBlocker annotations
    */
    'mode' => env('DELETE_DEPENDENCY_MODE', 'explicit'),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => true,
        'store' => null, // default cache store
        'ttl' => 3600,
        'prefix' => 'delete_guard:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Force Delete Override
    |--------------------------------------------------------------------------
    */
    'allow_force_delete' => true,
];

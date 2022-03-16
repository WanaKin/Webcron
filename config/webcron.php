<?php

return [
    // Global timeout for scheduler and worker
    'lock_timeout' => env('WEBCRON_LOCK_TIMEOUT', '2 minutes'),

    // Disable default routes
    'routes' => env('WEBCRON_ROUTES', true),

    'scheduler' => [
        // Set to false to only use the worker
        'enabled' => env('WEBCRON_SCHEDULER', true),

        // Optional custom timeout for the scheduler
        'lock_timeout' => env('WEBCRON_SCHEDULER_LOCK_TIMEOUT')
    ],

    'worker' => [
        // Set to false to only use the scheduler
        'enabled' => env('WEBCRON_WORKER', true),

        // The max time for the queue:work command to run
        // Be sure to properly set the time limit for PHP scripts
        'max_time' => env('WEBCRON_WORKER_MAX_TIME', 3),

        // Optional custom timeout for the worker
        'lock_timeout' => env('WEBCRON_WORKER_LOCK_TIMEOUT')
    ]
];
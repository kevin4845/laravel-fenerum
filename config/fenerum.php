<?php

declare(strict_types=1);

return [
    'token' => env('FENERUM_TOKEN'),
    'client_system' => env('FENERUM_CLIENT_SYSTEM', 'Laravel'),
    'user' => env('FENERUM_USER'),
    'base_url' => env('FENERUM_BASE_URL', 'https://app.fenerum.com'),
    'timeout' => env('FENERUM_TIMEOUT', 10),
    'max_retries' => env('FENERUM_MAX_RETRIES', 2),
    'max_retry_delay' => env('FENERUM_MAX_RETRY_DELAY', 60),
];

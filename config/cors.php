<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | CORS_ALLOWED_ORIGINS env değişkeni ZORUNLUDUR.
    | Production'da wildcard (*) kullanımı güvenlik riski oluşturur.
    |
    | Örnek: CORS_ALLOWED_ORIGINS=https://app.example.com,https://admin.example.com
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => array_filter(explode(',', env('CORS_ALLOWED_ORIGINS', ''))),

    'allowed_origins_patterns' => array_filter(explode(',', env('CORS_ALLOWED_ORIGINS_PATTERNS', ''))),

    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept', 'X-Request-Id'],

    'exposed_headers' => ['X-Request-Id'],

    'max_age' => (int) env('CORS_MAX_AGE', 86400),

    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', false),

];

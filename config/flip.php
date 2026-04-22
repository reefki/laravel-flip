<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Flip API Secret Key
    |--------------------------------------------------------------------------
    |
    | Your Flip for Business API secret key. Find it in your Flip for Business
    | dashboard. This value is used as the username in HTTP Basic auth.
    |
    */
    'secret_key' => env('FLIP_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | Either "production" or "sandbox". The sandbox uses a separate base URL
    | and your sandbox API key (also obtained from the dashboard).
    |
    */
    'environment' => env('FLIP_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Default API Version
    |--------------------------------------------------------------------------
    |
    | Many Flip endpoints exist on both v2 and v3 (e.g. disbursement, accept
    | payment). Set the default here. You can override per-call with
    | Flip::useVersion('v2')->disbursement()->...
    |
    | Note: a few endpoints only exist on a specific version (e.g.
    | bank-account-inquiry, city/country lists, exchange-rates and the
    | international transfer family are v2 only). Those resources ignore the
    | default and pin to their actual version.
    |
    */
    'version' => env('FLIP_VERSION', 'v3'),

    /*
    |--------------------------------------------------------------------------
    | Base URLs
    |--------------------------------------------------------------------------
    */
    'base_urls' => [
        'production' => 'https://bigflip.id/api',
        'sandbox' => 'https://bigflip.id/big_sandbox_api',
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Token
    |--------------------------------------------------------------------------
    |
    | Token used to verify that incoming callbacks (webhooks) really come from
    | Flip. You can find / regenerate it in the dashboard. Used by the
    | webhook signature validator.
    |
    */
    'validation_token' => env('FLIP_VALIDATION_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client
    |--------------------------------------------------------------------------
    */
    'http' => [
        'timeout' => env('FLIP_HTTP_TIMEOUT', 30),
        'connect_timeout' => env('FLIP_HTTP_CONNECT_TIMEOUT', 10),
        'retry_times' => env('FLIP_HTTP_RETRY', 0),
        'retry_sleep_ms' => env('FLIP_HTTP_RETRY_SLEEP', 200),
    ],
];

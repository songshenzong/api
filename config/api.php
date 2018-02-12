<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    |
    |
    */

    'cors' => [
        'cors'        => env('SONGSHENZONG_API_CORS', true),
        'credentials' => env('SONGSHENZONG_API_CORS_CREDENTIALS', true),
        'max_age'     => env('SONGSHENZONG_API_CORS_MAX_AGE', 86400),
    ],

    'validator' => [
        'status_code' => env('SONGSHENZONG_API_VALIDATOR_HTTP_STATUS_CODE', 422),
        'message'     => env('SONGSHENZONG_API_VALIDATOR', 'Unprocessable Entity'),
    ],

    'dingo' => env('SONGSHENZONG_API_DINGO', false),
    'debug' => env('SONGSHENZONG_API_DEBUG', env('APP_DEBUG')),

    'prefix'  => env('SONGSHENZONG_API_PREFIX'),
    'exclude' => env('SONGSHENZONG_API_EXCLUDE'),
    'domain'  => env('SONGSHENZONG_API_DOMAIN'),
];

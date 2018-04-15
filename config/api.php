<?php

return [

    'cors' => [
        'cors'        => env('SONGSHENZONG_API_CORS', true),
        'credentials' => env('SONGSHENZONG_API_CORS_CREDENTIALS', true),
        'max_age'     => env('SONGSHENZONG_API_CORS_MAX_AGE', 86400),
    ],

    'dingo' => env('SONGSHENZONG_API_DINGO', false),
    'debug' => env('SONGSHENZONG_API_DEBUG', env('APP_DEBUG')),

    'prefix'  => env('SONGSHENZONG_API_PREFIX'),
    'exclude' => env('SONGSHENZONG_API_EXCLUDE'),
    'domain'  => env('SONGSHENZONG_API_DOMAIN'),

];

<?php

return [

    // Keep existing Laravel defaults for mail services...
    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Gemini — value comes from GEMINI_API_KEY in .env
    // Access anywhere via: config('services.gemini.api_key')
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'owner_reservations' => [
        'email' => env('OWNER_RESERVATION_EMAIL', 'spm3182@gmail.com'),
        'whatsapp_to' => env('OWNER_RESERVATION_WHATSAPP_TO', '491795973910'),
        'meta_phone_number_id' => env('OWNER_RESERVATION_META_PHONE_NUMBER_ID'),
        'meta_access_token' => env('OWNER_RESERVATION_META_ACCESS_TOKEN'),
        'meta_api_version' => env('OWNER_RESERVATION_META_API_VERSION', 'v21.0'),
    ],

];

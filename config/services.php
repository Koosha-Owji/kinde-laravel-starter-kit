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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | Kinde Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration section contains the settings required for Kinde
    | authentication integration. These values should be obtained from your
    | Kinde application dashboard and stored in your .env file.
    */
    'kinde' => [
        'domain' => env('KINDE_DOMAIN'),
        'client_id' => env('KINDE_CLIENT_ID'),
        'client_secret' => env('KINDE_CLIENT_SECRET'),
        'redirect_url' => env('KINDE_REDIRECT_URL'),
        'post_logout_redirect_url' => env('KINDE_POST_LOGOUT_REDIRECT_URL'),
    ],

];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    // Midtrans Payment Gateway
    'midtrans' => [
        'server_key'    => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-demo-key'),
        'client_key'    => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-demo-key'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        'is_sanitized'  => env('MIDTRANS_IS_SANITIZED', true),
        'is_3ds'        => env('MIDTRANS_IS_3DS', true),
        'snap_url'      => env('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/snap.js'),
    ],

    // Biteship Logistics & Shipping API
    'biteship' => [
        'api_key'            => env('BITESHIP_API_KEY', 'biteship_test_demo_key'),
        'base_url'           => env('BITESHIP_BASE_URL', 'https://api.biteship.com/v1'),
        'origin_area_id'     => env('BITESHIP_ORIGIN_AREA_ID', 'IDNP6IDJB164'),
        'origin_latitude'    => env('BITESHIP_ORIGIN_LATITUDE', -6.225587),
        'origin_longitude'   => env('BITESHIP_ORIGIN_LONGITUDE', 106.800542),
        'origin_postal_code' => env('BITESHIP_ORIGIN_POSTAL_CODE', '12190'),
    ],

    // WhatsApp Gateway (Fonnte API)
    'fonnte' => [
        'token'   => env('FONNTE_API_TOKEN', 'demo_fonnte_token'),
        'api_url' => env('FONNTE_API_URL', 'https://api.fonnte.com/send'),
    ],

    // Google OAuth 2.0 (Socialite)
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
    ],

];

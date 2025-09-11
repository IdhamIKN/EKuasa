<?php
// config/services.php

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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'whatsapp' => [
        'api_url' => env('WHATSAPP_API_URL'),
        'api_key' => env('WHATSAPP_API_KEY'),
        'sender' => env('WHATSAPP_SENDER_NUMBER'),
    ],

    'google' => [
        'enabled' => env('GOOGLE_SHEETS_ENABLED', false),
        'service_account_path' => env('GOOGLE_SERVICE_ACCOUNT_PATH', storage_path('app/google-service-account.json')),
        'spreadsheet_id' => env('GOOGLE_SPREADSHEET_ID'),
        'drive_folder_id' => env('GOOGLE_DRIVE_FOLDER_ID', null),
    ],

    'mail' => [
        'admin_email' => env('ADMIN_EMAIL', 'admin@example.com'),
    ],


];

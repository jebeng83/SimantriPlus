<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SATUSEHAT Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi dengan SATUSEHAT
    |
    */

    // URL API SATUSEHAT (staging atau production)
    'api_url' => env('SATUSEHAT_API_URL', 'https://api-satusehat.kemkes.go.id'),

    // URL Autentikasi SATUSEHAT (staging atau production)
    'auth_url' => env('SATUSEHAT_AUTH_URL', 'https://api-satusehat.kemkes.go.id/oauth2/v1'),

    // Client ID untuk autentikasi
    'client_id' => env('SATUSEHAT_CLIENT_ID', ''),

    // Client Secret untuk autentikasi
    'client_secret' => env('SATUSEHAT_CLIENT_SECRET', ''),

    // Organization ID
    'organization_id' => env('SATUSEHAT_ORGANIZATION_ID', ''),

    // Token (untuk pengujian, dalam produksi sebaiknya menggunakan sistem refresh token)
    'token' => env('SATUSEHAT_TOKEN', ''),

    // Masa berlaku token dalam detik (default: 3600 detik / 1 jam)
    'token_expiry' => env('SATUSEHAT_TOKEN_EXPIRY', 3600),
]; 
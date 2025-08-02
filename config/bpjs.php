<?php

return [
    /*
    |--------------------------------------------------------------------------
    | BPJS Configuration (Format Lama - Trustmark BPJS)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi dengan BPJS Kesehatan API
    |
    */

    // Menggunakan format lama (langsung dari .env)
    'pcare' => [
        'base_url' => env('BPJS_PCARE_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/pcare-rest'),
        'cons_id' => env('BPJS_PCARE_CONS_ID', ''),
        'secret_key' => env('BPJS_PCARE_CONS_PWD', ''),
        'username' => env('BPJS_PCARE_USER', ''),
        'password' => env('BPJS_PCARE_PASS', ''),
        'user_key' => env('BPJS_PCARE_USER_KEY', ''),
        'app_code' => env('BPJS_PCARE_APP_CODE', '095'),
        'kode_ppk' => env('BPJS_PCARE_KODE_PPK', ''),
    ],

    // Konfigurasi untuk Antrean BPJS
    'antrean' => [
        'base_url_v1' => env('BPJS_ANTREAN_BASE_URL_V1', 'https://kerjo.simkeskhanza.com/api-bpjsfktp/'),
        'base_url' => env('BPJS_ANTREAN_BASE_URL', 'https://kerjo.simkeskhanza.com/MjknKhanza/'),
        'auth_url' => env('BPJS_ANTREAN_AUTH_URL', 'https://kerjo.simkeskhanza.com/api-bpjsfktp/auth'),
        'username' => env('BPJS_ANTREAN_USERNAME', ''),
        'password' => env('BPJS_ANTREAN_PASSWORD', ''),
        'cons_id' => env('BPJS_ANTREAN_CONS_ID', ''),
        'cons_pwd' => env('BPJS_ANTREAN_CONS_PWD', ''),
        'user_key' => env('BPJS_ANTREAN_USER_KEY', ''),
        'user' => env('BPJS_ANTREAN_USER', ''),
        'pass' => env('BPJS_ANTREAN_PASS', ''),
    ],
    
    'timeout' => 30,
];
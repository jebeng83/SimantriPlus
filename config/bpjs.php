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
    
    'timeout' => 30,
]; 
<?php

return [
    'base_url' => env('BPJS_PCARE_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id') . '/' . env('BPJS_SERVICE_NAME', 'pcare-rest') . '/',
    'cons_id' => env('BPJS_PCARE_CONS_ID'),
    'secret_key' => env('BPJS_PCARE_CONS_PWD'),
    'user_key' => env('BPJS_PCARE_USER_KEY'),
    'service_name' => env('BPJS_SERVICE_NAME', 'pcare-rest'),
    
    // PCare specific configuration
    'pcare_user' => env('BPJS_PCARE_USER'),
    'pcare_pass' => env('BPJS_PCARE_PASSWORD'),
    'kode_ppk' => env('BPJS_PCARE_KODE_PPK'),
    'app_code' => env('BPJS_APP_CODE', '095'),
];

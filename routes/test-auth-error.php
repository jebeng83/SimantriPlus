<?php

use Illuminate\Support\Facades\Route;

// Test route untuk simulasi authentication error
Route::get('/test/bpjs-auth-error', function () {
    return response()->json([
        'metaData' => [
            'code' => 401,
            'message' => 'Maaf Cek Kembali Password Pcare Anda'
        ],
        'response' => null
    ], 401);
});

// Test route untuk simulasi error biasa
Route::get('/test/bpjs-general-error', function () {
    return response()->json([
        'metaData' => [
            'code' => 500,
            'message' => 'Server BPJS mengalami gangguan'
        ],
        'response' => null
    ], 500);
});
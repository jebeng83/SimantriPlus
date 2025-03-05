<?php

namespace App\Traits;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Encryption\DecryptException;

trait EnkripsiData
{
    public function encryptData($data)
    {
        try {
            return Crypt::encrypt($data);
        } catch (\Exception $e) {
            Log::error('Enkripsi gagal: ' . $e->getMessage());
            return $data; // Kembalikan data asli jika enkripsi gagal
        }
    }

    public function decryptData($data)
    {
        try {
            return Crypt::decrypt($data);
        } catch (DecryptException $e) {
            Log::warning('Dekripsi gagal: ' . $e->getMessage() . '. Mengembalikan data asli.');
            return $data; // Kembalikan data asli jika dekripsi gagal
        } catch (\Exception $e) {
            Log::error('Error lain saat dekripsi: ' . $e->getMessage());
            return $data; // Kembalikan data asli jika terjadi error lain
        }
    }
}
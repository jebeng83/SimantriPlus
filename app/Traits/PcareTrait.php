<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use LZCompressor\LZString;
use Exception;

trait PcareTrait
{
    /**
     * Mendapatkan timestamp untuk request
     * @return string
     */
    protected function getTimestamp()
    {
        date_default_timezone_set('UTC');
        return strval(time());
    }

    /**
     * Membuat signature untuk request
     * @param string $timestamp
     * @return string
     */
    protected function generateSignature($timestamp)
    {
        $consId = env('BPJS_PCARE_CONS_ID');
        $secretKey = env('BPJS_PCARE_CONS_PWD');
        
        $data = $consId . "&" . $timestamp;
        
        $signature = hash_hmac('sha256', $data, $secretKey, true);
        return base64_encode($signature);
    }

    /**
     * Membuat authorization header
     * @return string
     */
    protected function generateAuthorization()
    {
        $username = env('BPJS_PCARE_USER');
        $password = env('BPJS_PCARE_PASS');
        $appCode = env('BPJS_PCARE_APP_CODE', "095");
        
        // Pastikan format password sesuai dengan yang berhasil (Pcare152# bukan Pcare152)
        // Jika password tidak mengandung karakter #, tambahkan
        if (strpos($password, '#') === false) {
            $password .= '#';
        }
        
        $data = $username . ":" . $password . ":" . $appCode;
        return base64_encode($data);
    }

    /**
     * Mengirim request ke PCare
     * @param string $endpoint
     * @param string $method
     * @param array|null $data
     * @return array
     */
    protected function requestPcare($endpoint, $method = 'GET', $data = null)
    {
        try {
            // Gunakan konfigurasi lama
            $baseUrl = env('BPJS_PCARE_BASE_URL');
            
            $consId = env('BPJS_PCARE_CONS_ID');
            $userKey = env('BPJS_PCARE_USER_KEY');
            
            $timestamp = $this->getTimestamp();
            $signature = $this->generateSignature($timestamp);
            $authorization = $this->generateAuthorization();
            
            $headers = [
                'X-cons-id' => $consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $signature,
                'X-authorization' => 'Basic ' . $authorization,
                'user_key' => $userKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];
            
            // Format endpoint
            if (strpos($endpoint, 'peserta') !== false) {
                // Tidak perlu menambahkan v1 atau v1.svc untuk endpoint peserta
                // Format: {Base URL}/{Service Name}/peserta/{Parameter 1}
                // Contoh: https://apijkn.bpjs-kesehatan.go.id/pcare-rest/peserta/0001441909697
                
                // Ganti format nokartu/123456 menjadi format /123456 jika belum dalam format tersebut
                if (strpos($endpoint, 'nokartu/') !== false) {
                    $parts = explode('nokartu/', $endpoint);
                    $endpoint = 'peserta/' . $parts[1];
                } elseif (strpos($endpoint, 'nik/') !== false) {
                    $parts = explode('nik/', $endpoint);
                    $endpoint = 'peserta/nik/' . $parts[1];
                }
            } else {
                // Untuk endpoint lain seperti provider, dokter, dll
                if (strpos($endpoint, '?') === false) {
                    $endpoint .= '?offset=0&limit=10';
                } else if (strpos($endpoint, 'offset=') === false) {
                    $endpoint .= '&offset=0&limit=10';
                }
            }
            
            // Format URL langsung menggunakan pcare-rest tanpa menambahkan v1 atau v1.svc
            $baseUrl = rtrim($baseUrl, '/');
            $fullUrl = $baseUrl . '/' . $endpoint;
            
            // Log request
            Log::info('PCare API Request', [
                'url' => $fullUrl,
                'method' => $method,
                'headers' => $headers,
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            // Kirim request sesuai method
            $response = match($method) {
                'GET' => Http::withHeaders($headers)->get($fullUrl),
                'POST' => Http::withHeaders($headers)->post($fullUrl, $data),
                'PUT' => Http::withHeaders($headers)->put($fullUrl, $data),
                'DELETE' => Http::withHeaders($headers)->delete($fullUrl, $data),
                default => throw new Exception("Method HTTP tidak valid")
            };
            
            // Log response
            Log::info('PCare API Response', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            // Cek jika response error StartIndex
            if ($response->status() === 400 && strpos($response->body(), 'StartIndex cannot be less than zero') !== false) {
                Log::warning('PCare API Error StartIndex, coba akses dengan format alternatif', [
                    'original_endpoint' => $endpoint
                ]);
                
                // Coba dengan parameter startIndex=1 jika error StartIndex
                if (strpos($endpoint, '?') === false) {
                    $altEndpoint = $endpoint . '?startIndex=1&count=10';
                } else {
                    $altEndpoint = $endpoint . '&startIndex=1&count=10';
                }
                
                $altUrl = $baseUrl . '/' . $altEndpoint;
                Log::info('PCare API Retry Request', ['url' => $altUrl]);
                
                // Retry dengan endpoint alternatif
                $response = Http::withHeaders($headers)->get($altUrl);
                
                Log::info('PCare API Retry Response', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
            
            // Decode response
            $responseData = $response->json() ?? [];
            
            // Decrypt response jika ada
            if (isset($responseData['response'])) {
                $responseData['response'] = $this->decrypt($responseData['response'], $timestamp);
            }
            
            return $responseData;
            
        } catch (Exception $e) {
            Log::error('PCare API Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Mendekripsi response dari PCare
     * @param string $response
     * @param string $timestamp
     * @return array|null
     */
    protected function decrypt($response, $timestamp)
    {
        if (empty($response)) {
            return null;
        }
        
        try {
            $consId = env('BPJS_PCARE_CONS_ID');
            $consSecret = env('BPJS_PCARE_CONS_PWD');
            
            // Generate decryption key
            $key = $consId . $consSecret . $timestamp;
            
            // Decrypt
            $decrypted = $this->stringDecrypt($key, $response);
            
            // Decompress
            $decompressed = LZString::decompressFromEncodedURIComponent($decrypted);
            
            return json_decode($decompressed, true);
        } catch (Exception $e) {
            Log::error('Decrypt Error', [
                'message' => $e->getMessage(),
                'response' => $response
            ]);
            
            return $response;
        }
    }
    
    /**
     * Fungsi dekripsi menggunakan metode AES-256-CBC
     * @param string $key
     * @param string $string
     * @return string
     */
    protected function stringDecrypt($key, $string)
    {
        $encrypt_method = 'AES-256-CBC';
        
        // Hash key menggunakan SHA-256
        $key_hash = hex2bin(hash('sha256', $key));
        
        // Ambil 16 bytes pertama dari key hash sebagai IV
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        
        // Decrypt
        $output = openssl_decrypt(
            base64_decode($string),
            $encrypt_method,
            $key_hash,
            OPENSSL_RAW_DATA,
            $iv
        );
        
        return $output;
    }

    /**
     * Mendapatkan pesan error yang lebih user-friendly
     * @param Exception $e
     * @return string
     */
    protected function getErrorMessage(Exception $e)
    {
        $message = $e->getMessage();
        
        // Cek apakah error timeout atau network
        if (strpos($message, 'cURL error 28') !== false) {
            return 'Timeout saat menghubungi server BPJS. Silahkan coba lagi.';
        }
        
        if (strpos($message, 'cURL error 6') !== false || strpos($message, 'cURL error 7') !== false) {
            return 'Tidak dapat terhubung ke server BPJS. Periksa koneksi internet Anda.';
        }
        
        // Return message default
        return 'Terjadi kesalahan saat memproses permintaan: ' . $message;
    }
} 
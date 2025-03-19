<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use LZCompressor\LZString;
use Exception;

trait IcareTrait
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
        $consId = env('BPJS_ICARE_CONS_ID');
        $secretKey = env('BPJS_ICARE_CONS_PWD');
        
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
        $username = env('BPJS_ICARE_USER');
        $password = env('BPJS_ICARE_PASS');
        $appCode = env('BPJS_ICARE_APP_CODE', "095");
        
        // Pastikan format password sesuai
        if (strpos($password, '#') === false && substr($password, -1) !== '#') {
            $password .= '#';
        }
        
        $data = $username . ":" . $password . ":" . $appCode;
        return base64_encode($data);
    }

    /**
     * Mengirim request ke iCare BPJS
     * @param string $endpoint
     * @param string $method
     * @param array|null $data
     * @return array
     */
    protected function requestIcare($endpoint, $method = 'GET', $data = null)
    {
        try {
            // Gunakan konfigurasi dari env
            $baseUrl = env('BPJS_ICARE_BASE_URL');
            $consId = env('BPJS_ICARE_CONS_ID');
            $consSecret = env('BPJS_ICARE_CONS_PWD');
            $userKey = env('BPJS_ICARE_USER_KEY');
            
            // Validasi konfigurasi dasar
            if (empty($baseUrl) || empty($consId) || empty($consSecret) || empty($userKey)) {
                Log::error('BPJS Configuration Error: ' . 
                    (empty($baseUrl) ? 'Missing BPJS_ICARE_BASE_URL ' : '') . 
                    (empty($consId) ? 'Missing BPJS_ICARE_CONS_ID ' : '') . 
                    (empty($consSecret) ? 'Missing BPJS_ICARE_CONS_PWD ' : '') . 
                    (empty($userKey) ? 'Missing BPJS_ICARE_USER_KEY' : '')
                );
                throw new Exception('Konfigurasi BPJS tidak lengkap');
            }
            
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
            
            // Format URL - Bersihkan URL
            $baseUrl = rtrim($baseUrl, '/');
            
            // Log original URL dan endpoint untuk debugging
            Log::info('BPJS URL Components', [
                'original_base_url' => $baseUrl,
                'original_endpoint' => $endpoint
            ]);
            
            // Bersihkan duplikasi pada endpoint validate
            if ($endpoint === 'validate') {
                // Endpoint validate harus langsung dihubungkan dengan baseUrl
                $fullUrl = $baseUrl . '/validate';
            } 
            // Handle kasus lain
            else {
                // Cek apakah baseUrl sudah mengandung 'api/pcare'
                $hasPcareInBase = (strpos($baseUrl, 'api/pcare') !== false);
                
                // Cek apakah endpoint mengandung 'api/pcare'
                $hasPcareInEndpoint = (strpos($endpoint, 'api/pcare') === 0);
                
                // Cek apakah endpoint mengandung 'api/icare'
                $hasIcareInEndpoint = (strpos($endpoint, 'api/icare') === 0);
                
                // Bersihkan endpoint dari duplikasi
                if ($hasPcareInBase && $hasPcareInEndpoint) {
                    // Hapus 'api/pcare/' dari awal endpoint
                    $endpoint = substr($endpoint, 10);
                }
                
                if ($hasPcareInBase && $hasIcareInEndpoint) {
                    // Hapus 'api/icare/' dari awal endpoint
                    $endpoint = substr($endpoint, 10);
                }
                
                // Hapus duplikasi 'api/pcare/api/icare' yang mungkin terjadi
                if (strpos($endpoint, 'api/pcare/api/icare') === 0) {
                    $endpoint = substr($endpoint, 20);
                }
                
                // Hubungkan baseUrl dan endpoint yang sudah dibersihkan
                $fullUrl = $baseUrl . '/' . ltrim($endpoint, '/');
            }
            
            // Log URL final untuk debugging
            Log::info('BPJS URL Final', [
                'endpoint' => $endpoint,
                'fullUrl' => $fullUrl
            ]);
            
            // Log request
            Log::info('ICare API Request', [
                'url' => $fullUrl,
                'method' => $method,
                'headers' => array_keys($headers), // Hanya tampilkan keys untuk keamanan
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
            Log::info('ICare API Response', [
                'status' => $response->status(),
                'body_length' => strlen($response->body()),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            // Check for HTTP errors
            if ($response->failed()) {
                Log::error('BPJS ICARE Error', [
                    'message' => $response->status() . ' ' . $response->reason(),
                    'url' => $fullUrl,
                    'request' => $data
                ]);
                
                // Jika response JSON valid, kembalikan sebagai output
                try {
                    $errorJson = $response->json();
                    if ($errorJson) {
                        return $errorJson;
                    }
                } catch (\Exception $jsonEx) {
                    // Ignore JSON parse error dan lanjutkan dengan pesan error default
                }
                
                throw new Exception('Gagal melakukan request ke BPJS: ' . $response->status());
            }
            
            // Decode response
            $responseData = $response->json() ?? [];
            
            // Decrypt response jika ada
            if (isset($responseData['response'])) {
                $responseData['response'] = $this->decrypt($responseData['response'], $timestamp);
            }
            
            return $responseData;
            
        } catch (Exception $e) {
            Log::error('ICare API Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Mendekripsi response dari iCare
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
            $consId = env('BPJS_ICARE_CONS_ID');
            $consSecret = env('BPJS_ICARE_CONS_PWD');
            
            // Generate decryption key
            $key = $consId . $consSecret . $timestamp;
            
            // Decrypt
            $decrypted = $this->stringDecrypt($key, $response);
            
            // Decompress
            $decompressed = LZString::decompressFromEncodedURIComponent($decrypted);
            
            return json_decode($decompressed, true);
        } catch (Exception $e) {
            Log::error('ICare Decrypt Error', [
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
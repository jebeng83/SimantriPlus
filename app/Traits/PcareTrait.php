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
     * @param string|null $contentType Override content type (opsional)
     * @return array
     */
    protected function requestPcare($endpoint, $method = 'GET', $data = null, $contentType = null)
    {
        try {
            // Cek jika request peserta sudah ada di cache
            $cacheKey = 'pcare_' . md5($endpoint . json_encode($data));
            if ($method === 'GET' && \Cache::has($cacheKey)) {
                Log::info('PCare API Cache Hit', ['endpoint' => $endpoint]);
                return \Cache::get($cacheKey);
            }
            
            // Gunakan konfigurasi dari .env
            $baseUrl = env('BPJS_PCARE_BASE_URL');
            $consId = env('BPJS_PCARE_CONS_ID');
            $userKey = env('BPJS_PCARE_USER_KEY');
            
            $timestamp = $this->getTimestamp();
            $signature = $this->generateSignature($timestamp);
            $authorization = $this->generateAuthorization();
            
            // Default headers
            $headers = [
                'X-cons-id' => $consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $signature,
                'X-authorization' => 'Basic ' . $authorization,
                'user_key' => $userKey
            ];
            
            // Tambahkan content type header berdasarkan parameter atau default ke application/json
            if ($contentType === 'text/plain') {
                $headers['Content-Type'] = 'text/plain';
                $headers['Accept'] = 'application/json';
                
                // Konversi data ke JSON string jika method bukan GET
                if ($method !== 'GET' && !is_null($data)) {
                    $data = json_encode($data);
                }
            } else {
                // Untuk peserta, gunakan content type yang spesifik sesuai dokumentasi BPJS
                if (strpos($endpoint, 'peserta') !== false) {
                    $headers['Content-Type'] = 'application/json; charset=utf-8';
                } else {
                    $headers['Content-Type'] = 'application/json';
                }
                $headers['Accept'] = 'application/json';
            }
            
            // Normalisasi endpoint
            if (strpos($endpoint, 'peserta') !== false) {
                // Tidak perlu menambahkan v1 atau v1.svc untuk endpoint peserta
                // Format: {Base URL}/{Service Name}/peserta/{Parameter 1}
                
                // Untuk endpoint peserta, pastikan formatnya benar
                if (strpos($endpoint, 'nokartu/') !== false) {
                    $parts = explode('nokartu/', $endpoint);
                    $endpoint = 'peserta/' . $parts[1];
                } elseif (strpos($endpoint, 'nik/') !== false) {
                    $parts = explode('nik/', $endpoint);
                    $endpoint = 'peserta/nik/' . $parts[1];
                }
            } else {
                // Untuk endpoint lain seperti provider, dokter, dll
                if ($method === 'GET') {
                    if (strpos($endpoint, '?') === false) {
                        $endpoint .= '?offset=0&limit=10';
                    } else if (strpos($endpoint, 'offset=') === false) {
                        $endpoint .= '&offset=0&limit=10';
                    }
                }
            }
            
            // Format URL dengan benar
            $baseUrl = rtrim($baseUrl, '/');
            
            // Pastikan tidak ada duplikasi path
            if (strpos($baseUrl, 'pcare-rest') !== false) {
                // Jika base URL sudah mengandung pcare-rest
                $fullUrl = $baseUrl . '/' . $endpoint;
            } else {
                // Jika base URL tidak mengandung pcare-rest
                $fullUrl = $baseUrl . '/pcare-rest/' . $endpoint;
            }
            
            // Debug info untuk URL
            Log::debug('PCare API URL Debug', [
                'baseUrl' => $baseUrl,
                'endpoint' => $endpoint,
                'fullUrl' => $fullUrl,
                'method' => $method
            ]);
            
            // Log request - kurangi data sensitif yang dilog
            Log::info('PCare API Request', [
                'url' => $fullUrl,
                'method' => $method,
                'contentType' => $headers['Content-Type'], // Log content type dari header, bukan parameter
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            // Untuk method GET pada endpoint peserta, gunakan content type yang spesifik
            if ($method === 'GET' && strpos($endpoint, 'peserta') !== false) {
                $httpClient = Http::timeout(30)
                    ->withHeaders($headers)
                    ->withOptions([
                        'headers' => [
                            'Content-Type' => 'application/json; charset=utf-8'
                        ]
                    ]);
            } else {
                $httpClient = Http::timeout(30)->withHeaders($headers);
            }
            
            // Function untuk melakukan retry
            $sendRequest = function() use ($method, $httpClient, $fullUrl, $data, $contentType, $endpoint) {
                // Khusus untuk peserta dengan method GET
                if ($method === 'GET' && strpos($endpoint, 'peserta') !== false) {
                    return $httpClient->get($fullUrl);
                }
                
                return match($method) {
                    'GET' => $httpClient->get($fullUrl),
                    'POST' => $httpClient->withBody($data, $contentType ?? 'application/json')->post($fullUrl),
                    'PUT' => $httpClient->withBody($data, $contentType ?? 'application/json')->put($fullUrl),
                    'DELETE' => $httpClient->withBody($data, $contentType ?? 'application/json')->delete($fullUrl),
                    default => throw new \Exception("Method HTTP tidak valid")
                };
            };
            
            // Coba dengan retry
            $maxRetries = 3;
            $attempt = 0;
            $response = null;
            
            do {
                $attempt++;
                try {
                    $response = $sendRequest();
                    break; // Jika berhasil, keluar dari loop
                } catch (\Exception $e) {
                    if ($attempt >= $maxRetries) {
                        throw $e; // Jika sudah mencapai max retry, lempar exception
                    }
                    
                    Log::warning('PCare API Retry', [
                        'attempt' => $attempt,
                        'error' => $e->getMessage()
                    ]);
                    
                    // Tunggu sebentar sebelum retry
                    sleep(1);
                }
            } while ($attempt < $maxRetries);
            
            // Log response
            Log::info('PCare API Response', [
                'status' => $response->status(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            // Log response body terpisah untuk mengurangi ukuran log
            if ($response->status() >= 400) {
                Log::warning('PCare API Response Body', [
                    'body' => $response->body()
                ]);
            }
            
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
                
                // Buat URL alternatif dengan benar
                if (strpos($baseUrl, 'pcare-rest') !== false) {
                    $altUrl = $baseUrl . '/' . $altEndpoint;
                } else {
                    $altUrl = $baseUrl . '/pcare-rest/' . $altEndpoint;
                }
                
                Log::info('PCare API Retry Request', ['url' => $altUrl]);
                
                // Retry dengan endpoint alternatif
                $response = $httpClient->get($altUrl);
                
                Log::info('PCare API Retry Response', [
                    'status' => $response->status()
                ]);
            }
            
            // Decode response
            $responseData = $response->json() ?? [];
            
            // Decrypt response jika ada
            if (isset($responseData['response']) && is_string($responseData['response'])) {
                $responseData['response'] = $this->decrypt($responseData['response'], $timestamp);
            }
            
            // Simpan ke cache jika GET request
            if ($method === 'GET' && isset($responseData['metaData']) && $responseData['metaData']['code'] == 200) {
                // Simpan cache selama 30 menit
                \Cache::put($cacheKey, $responseData, now()->addMinutes(30));
            }
            
            return $responseData;
            
        } catch (\Exception $e) {
            Log::error('PCare API Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'endpoint' => $endpoint
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
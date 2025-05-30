<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use LZCompressor\LZString;

class PCare
{
    protected $baseUrl;
    protected $consId;
    protected $secretKey;
    protected $userKey;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->baseUrl = env('BPJS_PCARE_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/pcare-rest-v3');
        $this->consId = env('BPJS_PCARE_APP_CODE', '095');
        $this->secretKey = env('BPJS_MOBILEJKN_CONS_PWD', '2eF2C8E837');
        $this->userKey = env('BPJS_MOBILEJKN_USER_KEY', 'e0fc15a6c8f737a8c46d9072e63b6102');
        $this->username = env('BPJS_PCARE_USER', '');
        $this->password = env('BPJS_PCARE_PASS', '');
    }

    public function request($endpoint, $method = 'GET', $data = [], $contentType = 'application/json')
    {
        try {
            // Generate timestamp
            $timestamp = time();
            
            // Generate signature
            $signature = $this->generateSignature($timestamp);

            // Set headers
            $headers = [
                'X-cons-id' => $this->consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $signature,
                'user_key' => $this->userKey
            ];

            // Log request
            Log::info('PCare Request', [
                'endpoint' => $endpoint,
                'method' => $method,
                'headers' => $headers,
                'data' => $data
            ]);

            // Kirim request
            $response = Http::withHeaders($headers)
                ->withOptions(['verify' => false]) // Skip SSL verification
                ->send($method, $this->baseUrl . '/' . $endpoint, [
                    'headers' => ['Content-Type' => $contentType],
                    'body' => $method !== 'GET' ? json_encode($data) : ''
                ]);

            // Log raw response
            Log::info('PCare Raw Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Parse response
            $responseData = $response->json();

            // Jika ada data terenkripsi, decrypt
            if (isset($responseData['response']) && is_string($responseData['response'])) {
                $decrypted = $this->decrypt($responseData['response'], $timestamp);
                $responseData['response'] = json_decode($decrypted, true);
            }

            return $responseData;

        } catch (\Exception $e) {
            Log::error('PCare Request Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    protected function generateSignature($timestamp)
    {
        $data = $this->consId . '&' . $timestamp;
        return hash_hmac('sha256', $data, $this->secretKey);
    }

    protected function decrypt($string, $timestamp)
    {
        try {
            // Key untuk dekripsi
            $key = $this->consId . $this->secretKey . $timestamp;
            
            // Hash key dengan SHA-256
            $key_hash = hex2bin(hash('sha256', $key));
            
            // IV dari hash key
            $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
            
            // Dekripsi dengan AES-256-CBC
            $output = openssl_decrypt(base64_decode($string), 'AES-256-CBC', $key_hash, OPENSSL_RAW_DATA, $iv);
            
            if ($output === false) {
                throw new \Exception('Dekripsi gagal: ' . openssl_error_string());
            }
            
            // Dekompresi hasil dekripsi
            $decompressed = LZString::decompressFromEncodedURIComponent($output);
            
            if ($decompressed === false || $decompressed === null) {
                throw new \Exception('Dekompresi gagal');
            }
            
            Log::info('Decrypt Success', [
                'original' => substr($string, 0, 100) . '...',
                'decrypted' => substr($output, 0, 100) . '...',
                'decompressed' => substr($decompressed, 0, 100) . '...'
            ]);
            
            return $decompressed;
            
        } catch (\Exception $e) {
            Log::error('Decrypt Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}

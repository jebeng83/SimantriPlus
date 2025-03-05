<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use LZCompressor\LZString;
use GuzzleHttp\Client;

trait BpjsTraits
{
    public function requestGetBpjs($suburl)
    {
        try {
            $url = rtrim(env('BPJS_ICARE_BASE_URL'), '/') . '/' . ltrim($suburl, '/');
            
            // Generate timestamp sesuai dokumentasi BPJS
            date_default_timezone_set('UTC');
            $timestamp = strval(time());
            
            Log::info('BPJS Timestamp Generated', [
                'timestamp' => $timestamp,
                'utc_time' => gmdate('Y-m-d H:i:s', time())
            ]);

            // Ambil credentials dari env
            $consId = env('BPJS_CONS_ID');
            $secretKey = env('BPJS_CONS_PWD');
            $userKey = env('BPJS_USER_KEY');
            
            // Generate X-Authorization sesuai dokumentasi
            // Format: Base64(username:password:kdAplikasi)
            $username = env('BPJS_USER');
            $password = env('BPJS_PASS');
            // Gunakan kode aplikasi 095 sesuai dengan dokumentasi BPJS
            $kdAplikasi = "095"; // Nilai hardcoded sesuai dengan yang digunakan di BPJSTestController
            
            // Pastikan password dengan karakter khusus ditangani dengan benar
            // Tidak perlu urlencode karena base64_encode sudah menangani karakter khusus
            $authString = $username . ':' . $password . ':' . $kdAplikasi;
            $encodedAuth = base64_encode($authString);
            
            Log::info('BPJS Authorization Generated', [
                'auth_string_length' => strlen($authString),
                'encoded_length' => strlen($encodedAuth),
                'auth_string' => $username . ':' . str_repeat('*', strlen($password)) . ':' . $kdAplikasi // Log auth string untuk debugging (password disamarkan)
            ]);

            // Generate signature sesuai dokumentasi BPJS
            // Format: HMAC-SHA256(consId&timestamp, secretKey)
            $message = $consId . '&' . $timestamp;
            $signature = hash_hmac('sha256', $message, $secretKey, true);
            $encodedSignature = base64_encode($signature);
            
            Log::info('BPJS Signature Generated', [
                'message' => $message,
                'signature_length' => strlen($encodedSignature),
                'signature' => $encodedSignature // Log signature untuk debugging
            ]);

            // Set headers sesuai dokumentasi BPJS
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-cons-id' => $consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $encodedSignature,
                'X-authorization' => $encodedAuth,
                'user_key' => $userKey
            ];

            // Log request dengan menyembunyikan informasi sensitif
            Log::info('BPJS Request', [
                'method' => 'GET',
                'url' => $url,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-cons-id' => $consId,
                    'X-timestamp' => $timestamp,
                    'X-signature' => '***',
                    'X-authorization' => '***',
                    'user_key' => '***'
                ]
            ]);

            // Kirim request
            $client = new Client();
            $response = $client->get($url, [
                'headers' => $headers,
                'verify' => false
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            
            // Log raw response
            Log::info('BPJS Raw Response', [
                'status_code' => $statusCode,
                'body' => $body
            ]);
            
            // Coba parse response sebagai JSON
            $jsonResponse = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response from BPJS API");
            }

            return response()->json($jsonResponse);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('BPJS Request Error', [
                'message' => $e->getMessage(),
                'url' => $url ?? null
            ]);
            
            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Gagal menghubungi server BPJS: ' . $e->getMessage()
                ],
                'response' => null
            ], 500);
        } catch (\Exception $e) {
            Log::error('BPJS Error', [
                'message' => $e->getMessage(),
                'url' => $url ?? null
            ]);
            
            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ],
                'response' => null
            ], 500);
        }
    }

    public function requestPostBpjs($suburl, $request)
    {
        try {
            $url = rtrim(env('BPJS_ICARE_BASE_URL'), '/') . '/' . ltrim($suburl, '/');
            
            // Generate timestamp sesuai dokumentasi BPJS
            date_default_timezone_set('UTC');
            $timestamp = strval(time());
            
            Log::info('BPJS Timestamp Generated', [
                'timestamp' => $timestamp,
                'utc_time' => gmdate('Y-m-d H:i:s', time())
            ]);

            // Ambil credentials dari env
            $consId = env('BPJS_CONS_ID');
            $secretKey = env('BPJS_CONS_PWD');
            $userKey = env('BPJS_USER_KEY');
            
            // Generate X-Authorization sesuai dokumentasi
            // Format: Base64(username:password:kdAplikasi)
            $username = env('BPJS_USER');
            $password = env('BPJS_PASS');
            // Gunakan kode aplikasi 095 sesuai dengan dokumentasi BPJS
            $kdAplikasi = "095"; // Nilai hardcoded sesuai dengan yang digunakan di BPJSTestController
            
            // Pastikan password dengan karakter khusus ditangani dengan benar
            // Tidak perlu urlencode karena base64_encode sudah menangani karakter khusus
            $authString = $username . ':' . $password . ':' . $kdAplikasi;
            $encodedAuth = base64_encode($authString);
            
            Log::info('BPJS Authorization Generated', [
                'auth_string_length' => strlen($authString),
                'encoded_length' => strlen($encodedAuth),
                'auth_string' => $username . ':' . str_repeat('*', strlen($password)) . ':' . $kdAplikasi // Log auth string untuk debugging (password disamarkan)
            ]);

            // Generate signature sesuai dokumentasi BPJS
            // Format: HMAC-SHA256(consId&timestamp, secretKey)
            $message = $consId . '&' . $timestamp;
            $signature = hash_hmac('sha256', $message, $secretKey, true);
            $encodedSignature = base64_encode($signature);
            
            Log::info('BPJS Signature Generated', [
                'message' => $message,
                'signature_length' => strlen($encodedSignature)
            ]);

            // Set headers sesuai dokumentasi BPJS
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-cons-id' => $consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $encodedSignature,
                'X-authorization' => $encodedAuth,
                'user_key' => $userKey
            ];

            // Log request dengan menyembunyikan informasi sensitif
            Log::info('BPJS Request', [
                'method' => 'POST',
                'url' => $url,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-cons-id' => $consId,
                    'X-timestamp' => $timestamp,
                    'X-signature' => '***',
                    'X-authorization' => '***',
                    'user_key' => '***'
                ],
                'body' => json_encode($request, JSON_PRETTY_PRINT)
            ]);

            // Kirim request
            $client = new Client();
            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $request,
                'verify' => false
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            
            // Log raw response
            Log::info('BPJS Raw Response', [
                'status_code' => $statusCode,
                'body' => $body
            ]);
            
            // Coba parse response sebagai JSON
            $jsonResponse = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response from BPJS API");
            }

            return response()->json($jsonResponse);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('BPJS Request Error', [
                'message' => $e->getMessage(),
                'url' => $url ?? null,
                'request' => $request ?? null
            ]);
            
            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Gagal menghubungi server BPJS: ' . $e->getMessage()
                ],
                'response' => null
            ], 500);
        } catch (\Exception $e) {
            Log::error('BPJS Error', [
                'message' => $e->getMessage(),
                'url' => $url ?? null,
                'request' => $request ?? null
            ]);
            
            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ],
                'response' => null
            ], 500);
        }
    }

    public function requestPutBpjs($suburl, $request)
    {
        try {
            $data['request'] = $request->all();
            $xTimestamp = $this->createTimestamp();
            $res = Http::accept('application/json')->withHeaders([
                'X-cons-id' => env('BPJS_CONS_ID'),
                'X-timestamp' => $xTimestamp,
                'X-signature' => $this->createSign($xTimestamp, env('BPJS_CONS_ID')),
                'X-authorization' => base64_encode(env('BPJS_USER').':'.env('BPJS_PASS').':'."095"),
                'user_key' => env('BPJS_USER_KEY'),
            ])->withBody(json_encode($data), 'json')->put(env('BPJS_ICARE_BASE_URL') . $suburl);
            return $this->responseDataBpjs($res->json(), $xTimestamp);
        } catch (\Exception $e) {
            $statusError['flag'] = 'RSB Middleware Webservice';
            $statusError['result'] = 'Communication Errors With BPJS Kesehatan Webservice';
            $statusError['data'] = $e;
            return response()->json($statusError, 400);
        }
    }

    public function requestDeleteBpjs($suburl, $request)
    {
        try {
            $data['request'] = $request->all();
            $xTimestamp = $this->createTimestamp();
            $res = Http::accept('application/json')->withHeaders([
                'X-cons-id' => env('BPJS_CONS_ID'),
                'X-timestamp' => $xTimestamp,
                'X-signature' => $this->createSign($xTimestamp, env('BPJS_CONS_ID')),
                'X-authorization' => base64_encode(env('BPJS_USER').':'.env('BPJS_PASS').':'."095"),
                'user_key' => env('BPJS_USER_KEY'),
            ])->withBody(json_encode($data), 'json')->delete(env('BPJS_ICARE_BASE_URL') . $suburl, 'json');
            return $this->responseDataBpjs($res->json(), $xTimestamp);
        } catch (\Exception $e) {
            $statusError['flag'] = 'RSB Middleware Webservice';
            $statusError['result'] = 'Communication Errors With BPJS Kesehatan Webservice';
            $statusError['data'] = $e;
            return response()->json($statusError, 400);
        }
    }

    private function responseDataBpjs($res, $xTimestamp)
    {
        try {
            // Validasi format response
            if (!isset($res['metaData'])) {
                throw new \Exception('Invalid response format from BPJS: Missing metaData');
            }

            // Siapkan response dasar
            $response = [
                'response' => $res['response'] ?? null,
                'metaData' => [
                    'code' => $res['metaData']['code'] ?? null,
                    'message' => $res['metaData']['message'] ?? null
                ]
            ];

            // Jika response berisi URL, langsung kembalikan
            if (isset($res['response']['url'])) {
                return response()->json($response, 200);
            }

            // Handle response data jika perlu decrypt
            if (isset($res['response']) && is_string($res['response'])) {
                try {
                    // Generate decryption key
                    $key = env('BPJS_CONS_ID') . env('BPJS_CONS_PWD') . $xTimestamp;

                    // Step 1: Decrypt
                    $decrypted = $this->stringDecrypt($key, $res['response']);
                    if ($decrypted === false) {
                        throw new \Exception('Decryption failed');
                    }

                    // Step 2: Decompress
                    $decompressed = $this->decompress($decrypted);
                    if ($decompressed === false) {
                        throw new \Exception('Decompression failed');
                    }

                    // Step 3: Parse JSON
                    $decoded = json_decode($decompressed, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('JSON decode failed: ' . json_last_error_msg());
                    }

                    $response['response'] = $decoded;

                } catch (\Exception $e) {
                    Log::error('BPJS Decrypt Error', [
                        'message' => $e->getMessage()
                    ]);

                    $response['response'] = null;
                    $response['decrypt_error'] = $e->getMessage();
                }
            }

            return response()->json($response, 200);

        } catch (\Exception $e) {
            Log::error('BPJS Response Processing Error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Error processing BPJS response: ' . $e->getMessage()
                ],
                'response' => null
            ], 500);
        }
    }

    private function createTimestamp()
    {
        // Set timezone ke UTC sesuai standar BPJS
        date_default_timezone_set('UTC');
        
        // Generate timestamp sesuai format BPJS: jumlah detik sejak epoch (1970-01-01 00:00:00 UTC)
        $timestamp = strval(time());
        
        Log::info('BPJS Timestamp Generated', [
            'timestamp' => $timestamp,
            'utc_time' => gmdate('Y-m-d H:i:s', time())
        ]);
        
        return $timestamp;
    }

    private function createAuthorization()
    {
        $username = env('BPJS_USER');
        $password = env('BPJS_PASS');
        $kdAplikasi = "095"; // Gunakan kode aplikasi 095 sesuai dengan dokumentasi BPJS

        if (empty($username) || empty($password)) {
            Log::error('BPJS Configuration Error: Missing authorization credentials');
            throw new \Exception('Missing required BPJS authorization credentials');
        }

        // Format sesuai dengan standar BPJS: username:password:kdAplikasi
        $authString = $username . ':' . $password . ':' . $kdAplikasi;
        
        // Encode dengan base64
        $encodedAuth = base64_encode($authString);
        
        Log::info('BPJS Authorization Generated', [
            'auth_string_length' => strlen($authString),
            'encoded_length' => strlen($encodedAuth)
        ]);

        return $encodedAuth;
    }

    private function createSign($consId, $timestamp)
    {
        if (empty($consId)) {
            Log::error('BPJS Configuration Error: Missing BPJS_CONS_ID');
            throw new \Exception('Missing consumer ID configuration');
        }

        $secretKey = env('BPJS_CONS_PWD');
        if (empty($secretKey)) {
            Log::error('BPJS Configuration Error: Missing BPJS_CONS_PWD');
            throw new \Exception('Missing consumer password configuration');
        }

        // Format message sesuai standar BPJS: ConsID&Timestamp
        $message = $consId . "&" . $timestamp;
        
        // Generate signature menggunakan HMAC-SHA256 dengan output binary (true)
        // Tidak perlu urlencode karena hash_hmac sudah menangani karakter khusus
        $signature = hash_hmac('sha256', $message, $secretKey, true);
        
        // Encode signature ke base64
        $encodedSignature = base64_encode($signature);
        
        Log::info('BPJS Signature Generated', [
            'message' => $message,
            'signature_length' => strlen($encodedSignature),
            'signature' => $encodedSignature // Log signature untuk debugging
        ]);

        return $encodedSignature;
    }

    private function createKeyForDecode($tStamp)
    {
        $consid = env('BPJS_CONS_ID');
        $conspwd = env('BPJS_CONS_PWD');
        
        if (empty($consid) || empty($conspwd)) {
            Log::error('BPJS Configuration Error: Missing required credentials');
            throw new \Exception('Missing required BPJS credentials');
        }

        return $consid . $conspwd . $tStamp;
    }

    private function stringDecrypt($key, $string)
    {
        $encrypt_method = 'AES-256-CBC';

        // hash
        $key_hash = hex2bin(hash('sha256', $key));

        // iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);

        return $output;
    }

    private function decompress($string)
    {
        return \LZCompressor\LZString::decompressFromEncodedURIComponent($string);
    }
}


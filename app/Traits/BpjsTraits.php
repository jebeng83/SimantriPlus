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
    public function requestGetBpjs($suburl, $type = 'pcare')
    {
        try {
            // Tentukan prefix berdasarkan tipe
            $prefix = strtoupper($type);
            
            // Validasi dan ambil base URL
            $baseUrl = env("BPJS_{$prefix}_BASE_URL");
            if (empty($baseUrl)) {
                throw new \Exception("BPJS_{$prefix}_BASE_URL tidak dikonfigurasi");
            }
            
            $url = rtrim($baseUrl, '/') . '/' . ltrim($suburl, '/');
            
            // Generate timestamp sesuai dokumentasi BPJS
            date_default_timezone_set('UTC');
            $timestamp = strval(time());
            
            Log::info("BPJS {$prefix} Request Details", [
                'url' => $url,
                'timestamp' => $timestamp,
                'utc_time' => gmdate('Y-m-d H:i:s', time())
            ]);

            // Ambil credentials dari env dengan prefix yang sesuai
            $consId = env("BPJS_{$prefix}_CONS_ID");
            $secretKey = env("BPJS_{$prefix}_CONS_PWD");
            $userKey = env("BPJS_{$prefix}_USER_KEY");
            
            if (empty($consId) || empty($secretKey) || empty($userKey)) {
                throw new \Exception("Kredensial BPJS {$prefix} tidak lengkap");
            }
            
            // Generate X-Authorization sesuai dokumentasi
            $username = env("BPJS_{$prefix}_USER");
            $password = env("BPJS_{$prefix}_PASS");
            $kdAplikasi = "095";
            
            if (empty($username) || empty($password)) {
                throw new \Exception("Username atau password BPJS {$prefix} tidak dikonfigurasi");
            }
            
            $authString = $username . ':' . $password . ':' . $kdAplikasi;
            $encodedAuth = base64_encode($authString);
            
            // Generate signature
            $message = $consId . '&' . $timestamp;
            $signature = hash_hmac('sha256', $message, $secretKey, true);
            $encodedSignature = base64_encode($signature);

            // Set headers
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-cons-id' => $consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $encodedSignature,
                'X-authorization' => $encodedAuth,
                'user_key' => $userKey
            ];

            Log::info("BPJS {$prefix} Request Headers", [
                'headers' => array_merge(
                    $headers,
                    ['X-signature' => '***', 'X-authorization' => '***', 'user_key' => '***']
                )
            ]);

            // Kirim request
            $client = new Client();
            $response = $client->get($url, [
                'headers' => $headers,
                'verify' => false
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            
            Log::info("BPJS {$prefix} Response Details", [
                'status_code' => $statusCode,
                'response_length' => strlen($body)
            ]);
            
            $jsonResponse = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response: " . json_last_error_msg());
            }

            return response()->json($jsonResponse);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error("BPJS {$prefix} Request Error", [
                'message' => $e->getMessage(),
                'url' => $url ?? null,
                'response' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);
            
            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => "Gagal menghubungi server BPJS {$prefix}: " . $e->getMessage()
                ],
                'response' => null
            ], 500);
        } catch (\Exception $e) {
            Log::error("BPJS {$prefix} Error", [
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

    public function requestPostBpjs($suburl, $request, $type = 'pcare')
    {
        try {
            // Tentukan prefix berdasarkan tipe
            $prefix = strtoupper($type);
            
            // Validasi dan ambil base URL
            $baseUrl = env("BPJS_{$prefix}_BASE_URL");
            if (empty($baseUrl)) {
                throw new \Exception("BPJS_{$prefix}_BASE_URL tidak dikonfigurasi");
            }
            
            $url = rtrim($baseUrl, '/') . '/' . ltrim($suburl, '/');
            
            // Generate timestamp sesuai dokumentasi BPJS
            date_default_timezone_set('UTC');
            $timestamp = strval(time());
            
            // Ambil credentials dari env dengan prefix yang sesuai
            $consId = env("BPJS_{$prefix}_CONS_ID");
            $secretKey = env("BPJS_{$prefix}_CONS_PWD");
            $userKey = env("BPJS_{$prefix}_USER_KEY");
            $username = env("BPJS_{$prefix}_USER");
            $password = env("BPJS_{$prefix}_PASS");
            
            if (empty($consId) || empty($secretKey) || empty($userKey) || empty($username) || empty($password)) {
                throw new \Exception("Kredensial BPJS {$prefix} tidak lengkap");
            }

            // Set headers
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-cons-id' => $consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $this->createSign($timestamp, $consId),
                'X-authorization' => base64_encode($username . ':' . $password . ':095'),
                'user_key' => $userKey
            ];

            // Kirim request
            $client = new Client();
            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $request,
                'verify' => false
            ]);

            return $this->responseDataBpjs($response->getBody()->getContents(), $timestamp);

        } catch (\Exception $e) {
            Log::error("BPJS {$prefix} Error", [
                'message' => $e->getMessage(),
                'url' => $url ?? null,
                'request' => $request
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
                    $key = $this->createKeyForDecode($xTimestamp);

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

        $secretKey = env('BPJS_ICARE_CONS_PWD');
        if (empty($secretKey)) {
            Log::error('BPJS Configuration Error: Missing BPJS_ICARE_CONS_PWD');
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
        $consid = env('BPJS_ICARE_CONS_ID');
        $conspwd = env('BPJS_ICARE_CONS_PWD');
        
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


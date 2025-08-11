<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
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
        
        // Gunakan password sesuai dengan yang dikonfigurasi di environment
        // PCare tidak selalu memerlukan '#' di akhir password
        
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
            if ($method === 'GET' && Cache::has($cacheKey)) {
                Log::info('PCare API Cache Hit', ['endpoint' => $endpoint]);
                return Cache::get($cacheKey);
            }
            
            // Gunakan konfigurasi dari .env
            $baseUrl = env('BPJS_PCARE_BASE_URL');
            $consId = env('BPJS_PCARE_CONS_ID');
            $userKey = env('BPJS_PCARE_USER_KEY');
            
            // Validasi konfigurasi environment
            if (empty($baseUrl) || empty($consId) || empty($userKey)) {
                Log::error('PCare API Configuration Missing', [
                    'baseUrl_empty' => empty($baseUrl),
                    'consId_empty' => empty($consId),
                    'userKey_empty' => empty($userKey)
                ]);
                throw new \Exception('Konfigurasi PCare tidak lengkap. Periksa environment variables.');
            }
            
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
                } elseif (strpos($endpoint, 'dokter') !== false) {
                    // Untuk endpoint dokter, gunakan content type sesuai katalog BPJS
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
            } elseif (strpos($endpoint, 'dokter') !== false) {
                // Khusus untuk endpoint dokter sesuai katalog BPJS
                // Format: {Base URL}/{Service Name}/dokter/{Parameter 1}/{Parameter 2}
                // Parameter 1: Row data awal yang akan ditampilkan
                // Parameter 2: Limit jumlah data yang akan ditampilkan
                
                if (!preg_match('/dokter\/(\d+)\/(\d+)/', $endpoint)) {
                    // Jika endpoint dokter tidak memiliki format pagination, tambahkan default
                    if ($endpoint === 'dokter') {
                        $endpoint = 'dokter/0/100';
                    }
                }
            } else {
                // Untuk endpoint lain seperti provider, dll
                if ($method === 'GET') {
                    // Jangan tambahkan parameter offset/limit jika endpoint sudah menggunakan format pagination
                    // seperti dokter/{start}/{limit} atau poli/{start}/{limit}
                    $isPaginatedEndpoint = preg_match('/\/(\d+)\/(\d+)$/', $endpoint);
                    
                    if (!$isPaginatedEndpoint) {
                        if (strpos($endpoint, '?') === false) {
                            $endpoint .= '?offset=0&limit=10';
                        } else if (strpos($endpoint, 'offset=') === false) {
                            $endpoint .= '&offset=0&limit=10';
                        }
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
                'headers' => array_keys($headers), // Log header keys untuk debugging
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            // Untuk method GET pada endpoint peserta dan dokter, gunakan content type yang spesifik
            if ($method === 'GET' && (strpos($endpoint, 'peserta') !== false || strpos($endpoint, 'dokter') !== false)) {
                $httpClient = Http::timeout(30)
                    ->withHeaders($headers);
            } else {
                $httpClient = Http::timeout(30)->withHeaders($headers);
            }
            
            // Function untuk melakukan retry
            $sendRequest = function() use ($method, $httpClient, $fullUrl, $data, $contentType, $endpoint) {
                // Log pengiriman data ke PCare untuk endpoint pendaftaran
                if (strpos($endpoint, 'pendaftaran') !== false && $method === 'POST') {
                    Log::info('=== MENGIRIM DATA KE PENDAFTARAN PCARE ===', [
                        'endpoint' => $endpoint,
                        'url' => $fullUrl,
                        'method' => $method,
                        'content_type' => $contentType,
                        'data_to_send' => $data,
                        'timestamp' => now()->toDateTimeString()
                    ]);
                }
                
                if ($method === 'POST') {
                    if ($contentType === 'text/plain' && !is_null($data)) {
                        // Untuk PCare pendaftaran dengan content-type text/plain
                        Log::info('Mengirim POST request dengan text/plain body', [
                            'endpoint' => $endpoint,
                            'body_content' => $data
                        ]);
                        return $httpClient->withBody($data, 'text/plain')->post($fullUrl);
                    } else {
                        // Untuk request JSON biasa
                        return $httpClient->post($fullUrl, $data);
                    }
                } elseif ($method === 'PUT') {
                    if ($contentType === 'text/plain' && !is_null($data)) {
                        return $httpClient->withBody($data, 'text/plain')->put($fullUrl);
                    } else {
                        return $httpClient->put($fullUrl, $data);
                    }
                } elseif ($method === 'DELETE') {
                    if ($contentType === 'text/plain' && !is_null($data)) {
                        return $httpClient->withBody($data, 'text/plain')->delete($fullUrl);
                    } else {
                        return $httpClient->delete($fullUrl, $data);
                    }
                } elseif ($method === 'GET') {
                    return $httpClient->get($fullUrl);
                } else {
                    throw new \Exception("Method HTTP tidak valid: " . $method);
                }
            };
            
            // Coba dengan retry
            $maxRetries = 3;
            $attempt = 0;
            $response = null;
            $lastException = null;
            
            do {
                $attempt++;
                try {
                    $response = $sendRequest();
                    break; // Jika berhasil, keluar dari loop
                } catch (\Exception $e) {
                    $lastException = $e;
                    
                    // Cek apakah ini adalah HTML error response dari BPJS
                    if ($e instanceof \Illuminate\Http\Client\RequestException && $e->response) {
                        $responseBody = $e->response->body();
                        if (stripos($responseBody, '<!DOCTYPE html') !== false || 
                            stripos($responseBody, '<html') !== false ||
                            stripos($responseBody, 'Request Error') !== false) {
                            
                            Log::error('PCare API HTML Error Response', [
                                'message' => 'Server BPJS PCare mengembalikan halaman error HTML. Status: ' . $e->response->status() . '. Endpoint: ' . $endpoint,
                                'attempt' => $attempt,
                                'endpoint' => $endpoint
                            ]);
                            
                            // Return error response immediately for HTML errors
                            return [
                                'metaData' => [
                                    'code' => $e->response->status(),
                                    'message' => 'Server BPJS PCare sedang bermasalah. Silakan coba beberapa saat lagi.'
                                ],
                                'response' => null
                            ];
                        }
                    }
                    
                    if ($attempt >= $maxRetries) {
                        // Jangan throw exception, akan dihandle di catch block
                        break;
                    }
                    
                    Log::warning('PCare API Retry', [
                        'attempt' => $attempt,
                        'error' => $e->getMessage()
                    ]);
                    
                    // Tunggu sebentar sebelum retry
                    sleep(1);
                }
            } while ($attempt < $maxRetries);
            
            // Jika masih ada exception setelah max retries, throw untuk dihandle di catch block
            if ($lastException && !$response) {
                throw $lastException;
            }
            
            // Log response
            Log::info('PCare API Response', [
                'status' => $response->status(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            // Log khusus untuk response pendaftaran PCare
            if (strpos($endpoint, 'pendaftaran') !== false) {
                Log::info('=== RESPONSE PENDAFTARAN PCARE DITERIMA ===', [
                    'endpoint' => $endpoint,
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                    'response_headers' => $response->headers(),
                    'success' => $response->successful(),
                    'timestamp' => now()->toDateTimeString()
                ]);
            }
            
            // Log response body terpisah untuk mengurangi ukuran log
            if ($response->status() >= 400) {
                $responseBody = $response->body();
                Log::warning('PCare API Response Body', [
                    'status' => $response->status(),
                    'endpoint' => $endpoint,
                    'body' => $responseBody,
                    'body_length' => strlen($responseBody),
                    'is_html' => (stripos($responseBody, '<!DOCTYPE html') !== false || stripos($responseBody, '<html') !== false),
                    'has_request_error' => (stripos($responseBody, 'Request Error') !== false)
                ]);
                
                // Cek apakah response adalah HTML error page
                if (stripos($responseBody, '<!DOCTYPE html') !== false || 
                    stripos($responseBody, '<html') !== false ||
                    stripos($responseBody, 'Request Error') !== false) {
                    
                    Log::error('PCare API Error', [
                        'message' => 'Server BPJS PCare mengembalikan halaman error HTML. Status: ' . $response->status() . '. Endpoint: ' . $endpoint,
                        'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5),
                        'endpoint' => $endpoint
                    ]);
                    
                    // Return error response instead of throwing exception
                    return [
                        'metaData' => [
                            'code' => $response->status(),
                            'message' => 'Server BPJS PCare sedang bermasalah. Silakan coba beberapa saat lagi.'
                        ],
                        'response' => null
                    ];
                }
            }
            
            // Cek jika response error StartIndex
            if ($response->status() === 400 && strpos($response->body(), 'StartIndex cannot be less than zero') !== false) {
                Log::warning('PCare API Error StartIndex, coba akses dengan format alternatif', [
                    'original_endpoint' => $endpoint
                ]);
                
                // Hanya tambahkan parameter startIndex untuk endpoint yang memerlukan pagination
                // Endpoint yang memerlukan pagination: pendaftaran/tglDaftar, kelompok/kegiatan, dokter, dll
                // Jangan tambahkan untuk endpoint individual seperti peserta/{noKartu} atau peserta/nik/{nik}
                $needsPagination = (
                    strpos($endpoint, 'pendaftaran/tglDaftar') !== false ||
                    strpos($endpoint, 'kelompok/kegiatan') !== false ||
                    strpos($endpoint, 'kunjungan/tglDaftar') !== false ||
                    strpos($endpoint, 'dokter/') !== false
                );
                
                // Jangan tambahkan pagination untuk endpoint peserta individual
                $isIndividualPeserta = (
                    preg_match('/peserta\/[0-9]+/', $endpoint) || // peserta/{noKartu}
                    strpos($endpoint, 'peserta/nik/') !== false    // peserta/nik/{nik}
                );
                
                if ($isIndividualPeserta) {
                    $needsPagination = false;
                }
                
                if ($needsPagination) {
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
                    
                    Log::info('PCare API Retry Request with pagination', ['url' => $altUrl]);
                    
                    // Retry dengan endpoint alternatif
                    $response = $httpClient->get($altUrl);
                    
                    Log::info('PCare API Retry Response', [
                        'status' => $response->status()
                    ]);
                } else {
                    Log::warning('PCare API StartIndex error pada endpoint yang tidak memerlukan pagination', [
                        'endpoint' => $endpoint,
                        'response_body' => $response->body()
                    ]);
                }
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
                Cache::put($cacheKey, $responseData, now()->addMinutes(30));
            }
            
            return $responseData;
            
        } catch (\Exception $e) {
            Log::error('PCare API Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'endpoint' => $endpoint
            ]);
            
            // Log error untuk debugging
            Log::error('PcareTrait Exception', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'has_response' => ($e instanceof \Illuminate\Http\Client\RequestException && $e->response)
            ]);
            
            // Cek apakah error authentication/credential
            $errorMessage = $e->getMessage();
            $statusCode = 500;
            
            if ($e instanceof \Illuminate\Http\Client\RequestException && $e->response) {
                $statusCode = $e->response->status();
                $responseBody = $e->response->body();
                
                Log::info('PcareTrait Response Body', [
                    'status_code' => $statusCode,
                    'response_body' => $responseBody
                ]);
                
                // Khusus untuk endpoint dokter, coba retry dengan format alternatif jika error
                if (strpos($endpoint, 'dokter') !== false && $statusCode === 400) {
                    Log::info('Dokter endpoint error, attempting retry with alternative formats');
                    
                    // Coba format alternatif untuk dokter
                    $alternativeEndpoints = [];
                    
                    // Jika endpoint saat ini adalah dokter/0/100, coba dokter/1/100
                    if (preg_match('/dokter\/(\d+)\/(\d+)/', $endpoint, $matches)) {
                        $start = intval($matches[1]);
                        $limit = intval($matches[2]);
                        
                        if ($start === 0) {
                            $alternativeEndpoints[] = "dokter/1/{$limit}";
                        }
                        $alternativeEndpoints[] = 'dokter';
                        $alternativeEndpoints[] = 'ref/dokter';
                    } else {
                        $alternativeEndpoints[] = 'dokter/0/100';
                        $alternativeEndpoints[] = 'dokter/1/100';
                        $alternativeEndpoints[] = 'ref/dokter';
                    }
                    
                    // Coba setiap endpoint alternatif
                    foreach ($alternativeEndpoints as $altEndpoint) {
                        try {
                            Log::info('Trying alternative dokter endpoint', ['endpoint' => $altEndpoint]);
                            
                            // Buat URL alternatif
                            if (strpos($baseUrl, 'pcare-rest') !== false) {
                                $altUrl = $baseUrl . '/' . $altEndpoint;
                            } else {
                                $altUrl = $baseUrl . '/pcare-rest/' . $altEndpoint;
                            }
                            
                            // Gunakan headers yang sama tapi dengan Content-Type yang sesuai katalog BPJS
                            $altHeaders = $headers;
                            $altHeaders['Content-Type'] = 'application/json; charset=utf-8';
                            
                            $altResponse = Http::timeout(30)->withHeaders($altHeaders)->get($altUrl);
                            
                            Log::info('Alternative dokter endpoint response', [
                                'endpoint' => $altEndpoint,
                                'status' => $altResponse->status()
                            ]);
                            
                            if ($altResponse->successful()) {
                                $altResponseData = $altResponse->json() ?? [];
                                
                                // Decrypt response jika ada
                                if (isset($altResponseData['response']) && is_string($altResponseData['response'])) {
                                    $altResponseData['response'] = $this->decrypt($altResponseData['response'], $timestamp);
                                }
                                
                                return $altResponseData;
                            }
                        } catch (\Exception $altE) {
                            Log::warning('Alternative dokter endpoint failed', [
                                'endpoint' => $altEndpoint,
                                'error' => $altE->getMessage()
                            ]);
                            continue;
                        }
                    }
                }
                
                // Cek response body untuk error authentication
                if (stripos($responseBody, 'username') !== false ||
                    stripos($responseBody, 'password') !== false ||
                    stripos($responseBody, 'authentication') !== false ||
                    stripos($responseBody, 'credential') !== false ||
                    stripos($responseBody, 'unauthorized') !== false) {
                    
                    Log::info('Authentication error detected in response body');
                    return [
                        'metaData' => [
                            'code' => 401,
                            'message' => 'Maaf Cek Kembali Password Pcare Anda'
                        ],
                        'response' => null
                    ];
                }
            }
            
            // Cek apakah error HTML response dari server BPJS
            if (stripos($errorMessage, 'Request Error') !== false ||
                stripos($errorMessage, 'html') !== false ||
                stripos($errorMessage, 'DOCTYPE') !== false ||
                stripos($errorMessage, 'Server BPJS PCare mengembalikan halaman error HTML') !== false) {
                
                Log::info('HTML error page detected from BPJS PCare server');
                return [
                    'metaData' => [
                        'code' => 503,
                        'message' => 'Server BPJS PCare sedang bermasalah. Silakan coba beberapa saat lagi.'
                    ],
                    'response' => null
                ];
            }
            
            // Cek error message untuk authentication keywords
            if ($statusCode === 401 || $statusCode === 403 ||
                stripos($errorMessage, 'username') !== false || 
                stripos($errorMessage, 'password') !== false ||
                stripos($errorMessage, 'authentication') !== false ||
                stripos($errorMessage, 'credential') !== false ||
                stripos($errorMessage, 'unauthorized') !== false ||
                stripos($errorMessage, 'xxxxx') !== false) {
                
                Log::info('Authentication error detected in error message');
                return [
                    'metaData' => [
                        'code' => 401,
                        'message' => 'Maaf Cek Kembali Password Pcare Anda'
                    ],
                    'response' => null
                ];
            }
            
            return [
                'metaData' => [
                    'code' => $statusCode,
                    'message' => $this->getErrorMessage($e)
                ],
                'response' => null
            ];
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
        
        // Cek apakah error HTML response dari server BPJS
        if (stripos($message, 'Request Error') !== false ||
            stripos($message, 'html') !== false ||
            stripos($message, 'DOCTYPE') !== false ||
            stripos($message, 'Server BPJS PCare mengembalikan halaman error HTML') !== false) {
            return 'Server BPJS PCare sedang bermasalah. Silakan coba beberapa saat lagi.';
        }
        
        // Cek apakah error authentication/credential
        if (stripos($message, 'username') !== false || 
            stripos($message, 'password') !== false ||
            stripos($message, 'authentication') !== false ||
            stripos($message, 'credential') !== false ||
            stripos($message, 'unauthorized') !== false ||
            stripos($message, '401') !== false ||
            stripos($message, '403') !== false) {
            return 'Maaf Cek Kembali Password Pcare Anda';
        }
        
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
<?php

namespace App\Http\Controllers\PCare;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Exception;
use LZCompressor\LZString;
use Illuminate\Support\Facades\DB;
use App\Services\PCare;

class ReferensiDokterController extends Controller
{
    protected $config;
    protected $client;
    protected $pcare;

    public function __construct(PCare $pcare)
    {
        $this->config = [
            'base_url' => rtrim(env('BPJS_MOBILEJKN_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/antreanfktp'), '/'),
            'cons_id' => env('BPJS_MOBILEJKN_CONS_ID', '7925'),
            'secret_key' => env('BPJS_MOBILEJKN_CONS_PWD', '2eF2C8E837'),
            'user_key' => env('BPJS_MOBILEJKN_USER_KEY', 'e0fc15a6c8f737a8c46d9072e63b6102'),
            'username' => env('BPJS_MOBILEJKN_USER', 'siswo-11251616'),
            'password' => env('BPJS_MOBILEJKN_PASS', 'Siswo102#'),
            'kode_aplikasi' => env('BPJS_PCARE_APP_CODE', '095')
        ];

        $this->client = new Client([
            'base_uri' => $this->config['base_url'],
            'timeout' => 30,
            'connect_timeout' => 5,
            'verify' => false,
            'debug' => true,
            'retry_on_status' => [500, 503],
            'max_retry_attempts' => 3,
            'retry_delay' => 1000,
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
        
        Log::info('PCare configuration loaded:', [
            'base_url' => $this->config['base_url'],
            'cons_id' => $this->config['cons_id'],
            'username' => $this->config['username'],
            'kode_aplikasi' => $this->config['kode_aplikasi']
        ]);

        $this->pcare = $pcare;
    }

    public function index()
    {
        return view('Pcare.refrensi-dokter');
    }

    protected function generateTimestamp()
    {
        try {
            // Set timezone ke UTC sesuai spesifikasi BPJS
            date_default_timezone_set('UTC');
            
            // Generate timestamp dalam format Unix timestamp
            $utcTime = Carbon::now('UTC');
            $timestamp = $utcTime->timestamp;
            
            Log::info('Generated timestamp:', [
                'timestamp' => $timestamp,
                'utc_time' => $utcTime->format('Y-m-d H:i:s'),
                'timezone' => date_default_timezone_get()
            ]);
            
            return strval($timestamp);
        } catch (\Exception $e) {
            Log::error('Error generating timestamp:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function generateSignature($consId, $secretKey, $timestamp)
    {
        try {
            // Format: ConsumerID&Timestamp
            $data = $consId . "&" . $timestamp;
            
            // Generate signature dengan HMAC-SHA256
            $signature = hash_hmac('sha256', $data, $secretKey, true);
            
            // Encode dengan base64
            $encodedSignature = base64_encode($signature);
            
            Log::info('Generated signature:', [
                'message' => $data,
                'timestamp' => $timestamp,
                'signature' => $encodedSignature,
                'cons_id' => $consId
            ]);
            
            return $encodedSignature;
        } catch (\Exception $e) {
            Log::error('Error generating signature:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function generateAuthorization()
    {
        try {
            // Format: username:password:kodeAplikasi
            $data = $this->config['username'] . ':' . $this->config['password'] . ':' . $this->config['kode_aplikasi'];
            $encoded = base64_encode($data);
            
            Log::info('Generating authorization:', [
                'username' => $this->config['username'],
                'kode_aplikasi' => $this->config['kode_aplikasi'],
                'encoded' => $encoded
            ]);
            
            return 'Basic ' . $encoded;
        } catch (\Exception $e) {
            Log::error('Error generating authorization:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function stringDecrypt($key, $string)
    {
        try {
            $encrypt_method = 'AES-256-CBC';

            // Generate key hash menggunakan SHA256
            $key_hash = hex2bin(hash('sha256', $key));

            // Generate IV (16 bytes) dari key hash
            $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

            Log::info('String decrypt params:', [
                'key' => $key,
                'string' => $string,
                'key_hash_length' => strlen($key_hash),
                'iv_length' => strlen($iv)
            ]);

            // Dekripsi menggunakan OpenSSL
            $output = openssl_decrypt(
                base64_decode($string),
                $encrypt_method,
                $key_hash,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($output === false) {
                throw new Exception('Decrypt failed: ' . openssl_error_string());
            }

            Log::info('Decrypted output:', [
                'output' => $output
            ]);

            return $output;
        } catch (Exception $e) {
            Log::error('String decrypt error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function decompress($string)
    {
        try {
            if (empty($string)) {
                throw new \Exception('String kosong tidak dapat didekompresi');
            }
            
            Log::info('Decompressing string:', [
                'string_length' => strlen($string)
            ]);
            
            $decompressed = LZString::decompressFromEncodedURIComponent($string);
            
            if ($decompressed === null || $decompressed === '') {
                throw new \Exception('Hasil dekompresi tidak valid');
            }
            
            return $decompressed;
        } catch (\Exception $e) {
            Log::error('Error in decompress:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function decryptResponse($response, $timestamp)
    {
        try {
            // Generate decrypt key
            $key = $this->config['cons_id'] . $this->config['secret_key'] . $timestamp;

            // Get encrypted string
            $encrypted = $response['response'];

            Log::info('Decrypting response:', [
                'key' => $key,
                'encrypted' => $encrypted,
                'timestamp' => $timestamp
            ]);

            // Decrypt
            $decrypted = $this->stringDecrypt($key, $encrypted);

            Log::info('Decrypted string:', [
                'decrypted' => $decrypted
            ]);

            // Decompress menggunakan LZString
            $decompressed = LZString::decompressFromEncodedURIComponent($decrypted);

            Log::info('Decompressed string:', [
                'decompressed' => $decompressed
            ]);

            // Parse JSON
            $result = json_decode($decompressed, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON: ' . json_last_error_msg());
            }

            Log::info('Parsed JSON:', [
                'result' => $result
            ]);

            return $result;
        } catch (Exception $e) {
            Log::error('Error decrypting response:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function handleBPJSError($e, $endpoint)
    {
        Log::error('BPJS API Request Error:', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString(),
            'request' => [
                'method' => 'GET',
                'url' => $this->config['base_url'] . $endpoint,
                'headers' => [
                    'X-Cons-ID' => $this->config['cons_id'],
                    'user_key' => '[HIDDEN]'
                ]
            ]
        ]);
        
        if ($e->hasResponse()) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            
            try {
                $errorBody = json_decode($body, true);
            } catch (\Exception $jsonError) {
                $errorBody = ['metadata' => ['message' => 'Invalid JSON response from BPJS']];
            }
            
            Log::error('BPJS Error Response:', [
                'status_code' => $statusCode,
                'body' => $errorBody
            ]);
            
            switch ($statusCode) {
                case 401:
                    return [
                        'code' => 401,
                        'message' => 'Unauthorized: Periksa kembali konfigurasi BPJS'
                    ];
                case 403:
                    return [
                        'code' => 403,
                        'message' => 'Forbidden: Tidak memiliki akses ke layanan BPJS'
                    ];
                case 404:
                    return [
                        'code' => 404,
                        'message' => 'Data tidak ditemukan'
                    ];
                case 405:
                    return [
                        'code' => 405,
                        'message' => 'Method tidak diizinkan'
                    ];
                case 408:
                    return [
                        'code' => 408,
                        'message' => 'Request timeout, coba lagi nanti'
                    ];
                case 429:
                    return [
                        'code' => 429,
                        'message' => 'Terlalu banyak request, coba lagi nanti'
                    ];
                case 500:
                    return [
                        'code' => 500,
                        'message' => 'Internal Server Error BPJS'
                    ];
                case 503:
                    return [
                        'code' => 503,
                        'message' => 'Layanan BPJS sedang maintenance atau tidak tersedia'
                    ];
                default:
                    return [
                        'code' => $statusCode,
                        'message' => $errorBody['metadata']['message'] ?? 'Terjadi kesalahan pada layanan BPJS'
                    ];
            }
        }
        
        return [
            'code' => 500,
            'message' => 'Tidak dapat terhubung ke server BPJS'
        ];
    }

    protected function formatTanggal($tanggal)
    {
        try {
            // Coba parse tanggal dari format YYYY-MM-DD
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                $date = Carbon::createFromFormat('Y-m-d', $tanggal);
            } 
            // Coba parse tanggal dari format DD-MM-YYYY
            else if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $tanggal)) {
                $date = Carbon::createFromFormat('d-m-Y', $tanggal);
            } else {
                throw new \Exception('Format tanggal tidak valid');
            }
            
            // Format ulang ke format yang diharapkan BPJS (YYYY-MM-DD)
            return $date->format('Y-m-d');
            
        } catch (\Exception $e) {
            throw new \Exception('Format tanggal tidak valid: ' . $e->getMessage());
        }
    }

    public function getDokter(Request $request)
    {
        try {
            // Ambil parameter dari request
            $kodePoli = $request->input('kodePoli');
            $tanggal = $request->input('tanggal');

            Log::info('Request Ref Dokter:', [
                'kodePoli' => $kodePoli,
                'tanggal' => $tanggal,
                'raw_request' => $request->all()
            ]);

            // Validasi parameter
            if (empty($kodePoli) || empty($tanggal)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter kodePoli dan tanggal harus diisi',
                    'data' => null
                ], 400);
            }

            try {
                $tanggal = $this->formatTanggal($tanggal);
                Log::info('Tanggal diformat:', [
                    'input' => $request->input('tanggal'),
                    'output' => $tanggal
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'data' => null
                ], 400);
            }

            // Generate timestamp
            $timestamp = $this->generateTimestamp();
            
            // Generate signature
            $signature = $this->generateSignature($this->config['cons_id'], $this->config['secret_key'], $timestamp);

            // Format endpoint sesuai dokumentasi (hapus /antreanfktp dari base_url)
            $baseUrl = str_replace('/antreanfktp', '', $this->config['base_url']);
            $endpoint = "/antreanfktp/ref/dokter/kodepoli/{$kodePoli}/tanggal/{$tanggal}";

            Log::info('BPJS API Request Details:', [
                'base_url' => $baseUrl,
                'endpoint' => $endpoint,
                'full_url' => $baseUrl . $endpoint,
                'timestamp' => $timestamp,
                'signature' => $signature,
                'tanggal_format' => $tanggal,
                'headers' => [
                    'x-cons-id' => $this->config['cons_id'],
                    'x-timestamp' => $timestamp,
                    'user_key' => $this->config['user_key']
                ]
            ]);

            // Set headers sesuai dokumentasi
            $headers = [
                'x-cons-id' => $this->config['cons_id'],
                'x-timestamp' => $timestamp,
                'x-signature' => $signature,
                'user_key' => $this->config['user_key']
            ];

            // Buat client baru dengan base_url yang sudah dikoreksi
            $client = new Client([
                'base_uri' => $baseUrl,
                'timeout' => 30,
                'verify' => false,
                'debug' => true
            ]);

            // Kirim request ke PCare
            $response = $client->request('GET', $endpoint, [
                'headers' => $headers
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            Log::info('BPJS Raw Response:', [
                'status_code' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => $responseBody
            ]);

            // Jika response berhasil dan terenkripsi
            if (isset($responseBody['response'])) {
                try {
                    // Decrypt response
                    $decryptedResponse = $this->decryptResponse($responseBody, $timestamp);
                    
                    Log::info('Decrypted Response:', [
                        'decrypted' => $decryptedResponse,
                        'metadata' => $responseBody['metadata'] ?? null
                    ]);

                    // Format response sesuai contoh
                    $formattedResponse = [];
                    
                    // Jika response adalah array langsung (tanpa 'list')
                    if (is_array($decryptedResponse) && !isset($decryptedResponse['list'])) {
                        foreach ($decryptedResponse as $dokter) {
                            if (is_array($dokter)) {
                                $formattedResponse[] = [
                                    'namadokter' => $dokter['namadokter'] ?? '',
                                    'kodedokter' => intval($dokter['kodedokter'] ?? 0),
                                    'jampraktek' => $dokter['jampraktek'] ?? '',
                                    'kapasitas' => intval($dokter['kapasitas'] ?? 0)
                                ];
                            }
                        }
                    }
                    // Jika response memiliki struktur dengan 'list'
                    else if (isset($decryptedResponse['list']) && is_array($decryptedResponse['list'])) {
                        foreach ($decryptedResponse['list'] as $dokter) {
                            $formattedResponse[] = [
                                'namadokter' => $dokter['namadokter'] ?? '',
                                'kodedokter' => intval($dokter['kodedokter'] ?? 0),
                                'jampraktek' => $dokter['jampraktek'] ?? '',
                                'kapasitas' => intval($dokter['kapasitas'] ?? 0)
                            ];
                        }
                    }

                    Log::info('Final Formatted Response:', [
                        'formatted' => $formattedResponse
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Data dokter berhasil diambil',
                        'data' => $formattedResponse
                    ]);

                } catch (\Exception $e) {
                    Log::error('Decrypt Response Error:', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'raw_response' => $responseBody
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mendekripsi response: ' . $e->getMessage(),
                        'data' => null
                    ], 500);
                }
            }

            // Jika response tidak sesuai format
            Log::warning('Invalid Response Format:', [
                'response' => $responseBody,
                'metadata' => $responseBody['metadata'] ?? null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $responseBody['metadata']['message'] ?? 'Format response tidak valid atau data tidak ditemukan',
                'metadata' => $responseBody['metadata'] ?? null,
                'data' => null
            ], 404);

        } catch (\Exception $e) {
            Log::error('PCare Get Ref Dokter Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        // TODO: Implement Excel export
        return response()->json(['message' => 'Fitur export Excel akan segera tersedia']);
    }

    public function exportPdf(Request $request)
    {
        // TODO: Implement PDF export
        return response()->json(['message' => 'Fitur export PDF akan segera tersedia']);
    }
} 
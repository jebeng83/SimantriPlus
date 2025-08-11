<?php

namespace App\Http\Controllers\PCare;

use App\Http\Controllers\Controller;
use App\Traits\PcareTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Exception;
use LZCompressor\LZString;
use Illuminate\Support\Facades\DB;

class ReferensiDokterController extends Controller
{
    use PcareTrait;
    
    protected $config;
    protected $client;

    public function __construct()
    {
        $this->config = [
            'base_url' => rtrim(env('BPJS_MOBILEJKN_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/antreanfktp'), '/'),
            'cons_id' => env('BPJS_MOBILEJKN_CONS_ID', '7925'),
            'secret_key' => env('BPJS_MOBILEJKN_CONS_PWD', '2eF2C8E837'),
            'user_key' => env('BPJS_MOBILEJKN_USER_KEY', 'e0fc15a6c8f737a8c46d9072e63b6102'),
            'username' => env('BPJS_MOBILEJKN_USER', 'siswo-11251919'),
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
    }

    public function index()
    {
        $poliMap = [
            '001' => 'POLI UMUM',
            '002' => 'POLI GIGI & MULUT',
            '003' => 'POLI KIA',
            '004' => 'LABORATORIUM',
            '008' => 'POLI KB'
        ];

        return view('Pcare.referensi.referensi-dokter-bpjs', [
            'poliList' => $poliMap
        ]);
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

    protected function getPoliData()
    {
        try {
            // Generate timestamp dan signature
            $timestamp = $this->generateTimestamp();
            $signature = $this->generateSignature($this->config['cons_id'], $this->config['secret_key'], $timestamp);

            // Format endpoint
            $baseUrl = str_replace('/antreanfktp', '', $this->config['base_url']);
            $endpoint = "/antreanfktp/ref/poli";

            Log::info('BPJS API Request Poli:', [
                'base_url' => $baseUrl,
                'endpoint' => $endpoint,
                'timestamp' => $timestamp
            ]);

            // Set headers
            $headers = [
                'x-cons-id' => $this->config['cons_id'],
                'x-timestamp' => $timestamp,
                'x-signature' => $signature,
                'user_key' => $this->config['user_key']
            ];

            // Buat client
            $client = new Client([
                'base_uri' => $baseUrl,
                'timeout' => 30,
                'verify' => false
            ]);

            // Kirim request
            $response = $client->request('GET', $endpoint, [
                'headers' => $headers
            ]);

            $responseContent = $response->getBody()->getContents();
            
            // Ekstrak JSON yang valid
            if (preg_match('/({.*})$/s', $responseContent, $matches)) {
                $jsonContent = $matches[1];
                $responseBody = json_decode($jsonContent, true);

                if (isset($responseBody['response'])) {
                    // Decrypt response
                    $decryptedResponse = $this->decryptResponse($responseBody, $timestamp);
                    
                    // Format response menjadi array dengan key kodepoli
                    $poliData = [];
                    foreach ($decryptedResponse as $poli) {
                        if (isset($poli['kdpoli']) && isset($poli['nmpoli'])) {
                            $poliData[$poli['kdpoli']] = [
                                'kodepoli' => $poli['kdpoli'],
                                'namapoli' => $poli['nmpoli']
                            ];
                        }
                    }
                    return $poliData;
                }
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Error getting poli data:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    public function getDokter(Request $request, $tanggal = null)
    {
        try {
            $tanggal = $tanggal ?? $request->input('tanggal', date('Y-m-d'));
            $kodePoli = $request->input('kodepoli', '001');
            $timestamp = $this->generateTimestamp();
            
            Log::info('Getting dokter data:', [
                'tanggal' => $tanggal,
                'kodepoli' => $kodePoli,
                'timestamp' => $timestamp
            ]);

            // Validasi format tanggal
            try {
                $tanggal = $this->formatTanggal($tanggal);
            } catch (\Exception $e) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Format tanggal tidak valid'
                    ],
                    'response' => [
                        'list' => []
                    ]
                ]);
            }

            // Cek dulu referensi poli
            $poliResponse = $this->getListPoli();
            $poliData = json_decode($poliResponse->getContent(), true);
            
            if (!isset($poliData['response']['list']) || empty($poliData['response']['list'])) {
                return response()->json([
                    'metadata' => [
                        'code' => 404,
                        'message' => 'Data referensi poli tidak ditemukan'
                    ],
                    'response' => [
                        'list' => []
                    ]
                ]);
            }

            // Cek apakah kode poli valid
            $validPoli = false;
            foreach ($poliData['response']['list'] as $poli) {
                if ($poli['kodePoli'] === $kodePoli) {
                    $validPoli = true;
                    break;
                }
            }

            if (!$validPoli) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Kode poli tidak valid'
                    ],
                    'response' => [
                        'list' => []
                    ]
                ]);
            }
            
            $headers = [
                'X-cons-id' => $this->config['cons_id'],
                'X-timestamp' => $timestamp,
                'X-signature' => $this->generateSignature($this->config['cons_id'], $this->config['secret_key'], $timestamp),
                'user_key' => $this->config['user_key']
            ];

            // Endpoint untuk referensi dokter
            $endpoint = "/antreanfktp/ref/dokter/kodepoli/{$kodePoli}/tanggal/{$tanggal}";
            
            Log::info('Request detail:', [
                'url' => $this->config['base_url'] . $endpoint,
                'headers' => array_merge($headers, ['X-signature' => '******'])
            ]);

            $response = $this->client->request('GET', $endpoint, [
                'headers' => $headers,
                'debug' => false
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            
            if (preg_match('/{.*}$/s', $responseBody, $matches)) {
                $responseBody = $matches[0];
            }
            
            Log::info('Response from BPJS:', [
                'status_code' => $statusCode,
                'response_length' => strlen($responseBody)
            ]);

            $result = json_decode($responseBody, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response: ' . json_last_error_msg());
            }

            if (!isset($result['response']) || empty($result['response'])) {
                Log::info('No data from BPJS, using default data');
                
                // Data default sesuai dengan poli yang sudah divalidasi
                $defaultDokter = $this->getDefaultDokter($kodePoli);
                
                return response()->json([
                    'metadata' => [
                        'code' => 200,
                        'message' => 'OK (Data Default)'
                    ],
                    'response' => [
                        'list' => $defaultDokter
                    ]
                ]);
            }

            if (isset($result['response']) && is_string($result['response'])) {
                $key = $this->config['cons_id'] . $this->config['secret_key'] . $timestamp;
                $decryptedResponse = $this->stringDecrypt($key, $result['response']);
                $decompressedResponse = $this->decompress($decryptedResponse);
                $decodedResponse = json_decode($decompressedResponse, true);
                
                Log::info('Raw decoded response from BPJS:', [
                    'decoded_response' => $decodedResponse
                ]);

                $finalData = [];
                if (is_array($decodedResponse)) {
                    foreach ($decodedResponse as $dokter) {
                        Log::info('Processing dokter data:', [
                            'dokter_data' => $dokter,
                            'requested_poli' => $kodePoli
                        ]);

                        // Periksa struktur data dokter
                        if (!isset($dokter['kodedokter']) || !isset($dokter['namadokter'])) {
                            Log::warning('Incomplete dokter data:', [
                                'dokter' => $dokter
                            ]);
                            continue;
                        }

                        // Tambahkan dokter ke data final
                        $finalData[] = [
                            'kdDokter' => $dokter['kodedokter'],
                            'nmDokter' => $dokter['namadokter'],
                            'kdPoli' => $kodePoli,
                            'nmPoli' => $this->getPoliName($kodePoli),
                            'jamPraktek' => $this->getJadwalPraktek($dokter),
                            'kapasitas' => $dokter['kapasitas'] ?? 30
                        ];
                    }
                }

                Log::info('Final formatted data:', [
                    'count' => count($finalData),
                    'data' => $finalData
                ]);

                // Jika tidak ada data dokter, gunakan data default
                if (empty($finalData)) {
                    Log::info('No doctors found, using default data for poli:', [
                        'kodePoli' => $kodePoli
                    ]);
                    
                    $finalData = $this->getDefaultDokter($kodePoli);
                }

                return response()->json([
                    'metadata' => [
                        'code' => 200,
                        'message' => 'OK'
                    ],
                    'response' => [
                        'list' => $finalData
                    ]
                ]);
            }

            throw new Exception('Invalid response format: encrypted response not found');

        } catch (Exception $e) {
            Log::error('Error in getDokter:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Gunakan data default jika terjadi error
            $defaultData = $this->getDefaultDokter($kodePoli);
            
            return response()->json([
                'metadata' => [
                    'code' => 200,
                    'message' => 'Menggunakan data default: ' . $e->getMessage()
                ],
                'response' => [
                    'list' => $defaultData
                ]
            ], 200);
        }
    }

    /**
     * Mendapatkan jadwal praktek dokter dari berbagai kemungkinan field
     */
    protected function getJadwalPraktek($dokter)
    {
        // Cek berbagai kemungkinan nama field untuk jadwal
        $jadwalFields = ['jadwal', 'jampraktek', 'jadwalpraktek', 'jam_praktek'];
        
        foreach ($jadwalFields as $field) {
            if (isset($dokter[$field]) && !empty($dokter[$field])) {
                return $dokter[$field];
            }
        }

        // Jika tidak ada jadwal yang ditemukan, gunakan default
        return '08:00-14:00';
    }

    /**
     * Mendapatkan data default dokter berdasarkan poli
     */
    protected function getDefaultDokter($kodePoli)
    {
        $defaultData = [
            '001' => [ // POLI UMUM
                [
                    'kdDokter' => 'D0001',
                    'nmDokter' => 'dr. SISWO HARIYONO',
                    'kdPoli' => '001',
                    'nmPoli' => 'POLI UMUM',
                    'jamPraktek' => '08:00-14:00',
                    'kapasitas' => 35
                ]
            ],
            '002' => [ // POLI GIGI
                [
                    'kdDokter' => 'D0002',
                    'nmDokter' => 'drg. RATNA SARI',
                    'kdPoli' => '002',
                    'nmPoli' => 'POLI GIGI',
                    'jamPraktek' => '08:00-14:00',
                    'kapasitas' => 20
                ]
            ],
            '003' => [ // POLI KIA
                [
                    'kdDokter' => 'D0003',
                    'nmDokter' => 'dr. MARIA ULFA',
                    'kdPoli' => '003',
                    'nmPoli' => 'POLI KIA',
                    'jamPraktek' => '08:00-14:00',
                    'kapasitas' => 25
                ]
            ],
            '004' => [ // LABORATORIUM
                [
                    'kdDokter' => 'D0004',
                    'nmDokter' => 'dr. BAMBANG SUTRISNO',
                    'kdPoli' => '004',
                    'nmPoli' => 'LABORATORIUM',
                    'jamPraktek' => '08:00-16:00',
                    'kapasitas' => 40
                ]
            ],
            '008' => [ // POLI KB
                [
                    'kdDokter' => 'D0005',
                    'nmDokter' => 'dr. SITI AMINAH',
                    'kdPoli' => '008',
                    'nmPoli' => 'POLI KB',
                    'jamPraktek' => '08:00-14:00',
                    'kapasitas' => 30
                ]
            ]
        ];

        return $defaultData[$kodePoli] ?? [];
    }

    protected function getPoliName($kodePoli)
    {
        // Data mapping kode poli ke nama poli sesuai referensi BPJS PCare
        $poliMap = [
            '001' => 'POLI UMUM',
            '002' => 'POLI GIGI & MULUT',
            '003' => 'POLI KIA',
            '004' => 'LABORATORIUM',
            '008' => 'POLI KB'
        ];

        return $poliMap[$kodePoli] ?? 'POLI TIDAK DIKETAHUI';
    }

    /**
     * Mendapatkan daftar poli yang tersedia
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListPoli()
    {
        try {
            $poliList = [];
            // Data lengkap poli
            $poliMap = [
                '001' => 'POLI UMUM',
                '002' => 'POLI GIGI & MULUT',
                '003' => 'POLI KIA',
                '004' => 'LABORATORIUM',
                '008' => 'POLI KB'
            ];

            // Data untuk filter dropdown (menggunakan nama yang sama dengan data lengkap)
            $poliFilter = [
                '001' => 'POLI UMUM',
                '002' => 'POLI GIGI & MULUT',
                '003' => 'POLI KIA',
                '004' => 'LABORATORIUM',
                '008' => 'POLI KB'
            ];

            // Menambahkan semua poli ke dalam list
            foreach ($poliMap as $kode => $nama) {
                $poliList[] = [
                    'kodePoli' => $kode,
                    'namaPoli' => $nama
                ];
            }

            return response()->json([
                'metadata' => [
                    'code' => 200,
                    'message' => 'OK'
                ],
                'response' => [
                    'list' => $poliList,
                    'filter' => $poliFilter
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getListPoli:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ],
                'response' => [
                    'list' => [],
                    'filter' => []
                ]
            ], 200);
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
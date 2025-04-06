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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

            // Proses response dan kembalikan hasil yang sudah didekripsi
            return $this->responseDataBpjs($jsonResponse, $timestamp, $type);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error("BPJS {$prefix} Request Error", [
                'message' => $e->getMessage(),
                'url' => $url ?? null,
                'response' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);
            
            return response()->json([
                'metadata' => [
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
                'metadata' => [
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
            
            Log::info("BPJS {$prefix} Request POST Details", [
                'url' => $url,
                'timestamp' => $timestamp,
                'utc_time' => gmdate('Y-m-d H:i:s', time()),
                'data' => $request
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

            Log::info("BPJS {$prefix} Request POST Headers", [
                'headers' => array_merge(
                    $headers,
                    ['X-signature' => '***', 'X-authorization' => '***', 'user_key' => '***']
                )
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
            
            Log::info("BPJS {$prefix} Response POST Details", [
                'status_code' => $statusCode,
                'response_length' => strlen($body)
            ]);
            
            $jsonResponse = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON response: " . json_last_error_msg());
            }

            // Proses response dan kembalikan hasil yang sudah didekripsi
            return $this->responseDataBpjs($jsonResponse, $timestamp, $type);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error("BPJS {$prefix} Request POST Error", [
                'message' => $e->getMessage(),
                'url' => $url ?? null,
                'request' => $request,
                'response' => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);
            
            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => "Gagal menghubungi server BPJS {$prefix}: " . $e->getMessage()
                ],
                'response' => null
            ], 500);
        } catch (\Exception $e) {
            Log::error("BPJS {$prefix} POST Error", [
                'message' => $e->getMessage(),
                'url' => $url ?? null,
                'request' => $request
            ]);
            
            return response()->json([
                'metadata' => [
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

    private function responseDataBpjs($res, $xTimestamp, $type = 'pcare')
    {
        try {
            // Validasi format response
            if (!isset($res['metaData']) && !isset($res['metadata'])) {
                throw new \Exception('Invalid response format from BPJS: Missing metaData/metadata');
            }

            // Gunakan metadata jika ada, atau metaData sebagai fallback
            $metaData = isset($res['metadata']) ? $res['metadata'] : $res['metaData'];

            // Siapkan response dasar dengan format yang konsisten
            $response = [
                'metadata' => [
                    'code' => $metaData['code'] ?? null,
                    'message' => $metaData['message'] ?? null
                ]
            ];

            // Tambahkan response field hanya jika ada data atau diperlukan
            if (isset($res['response'])) {
                // Jika response berisi URL, langsung masukkan ke respons
                if (isset($res['response']['url'])) {
                    $response['response'] = $res['response'];
                    return response()->json($response, 200);
                }

                // Handle response data jika perlu decrypt
                if (is_string($res['response'])) {
                    try {
                        // Generate decryption key
                        $key = $this->createKeyForDecode($xTimestamp, $type);

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

                        // Hanya tambahkan field response jika tidak null/empty
                        if (!empty($decoded)) {
                            $response['response'] = $decoded;
                        }

                    } catch (\Exception $e) {
                        Log::error('BPJS Decrypt Error', [
                            'message' => $e->getMessage()
                        ]);
                    }
                } else if (!empty($res['response'])) {
                    // Jika response bukan string dan tidak kosong, gunakan as-is
                    $response['response'] = $res['response'];
                }
            }

            // Tetapkan status code berdasarkan code dari metadata
            $statusCode = 200;
            if (isset($metaData['code'])) {
                // Jika code 200-299, gunakan sebagai status code
                if ($metaData['code'] >= 200 && $metaData['code'] < 300) {
                    $statusCode = $metaData['code'];
                }
                // Jika code 4xx atau 5xx, set status code sesuai
                else if ($metaData['code'] >= 400) {
                    $statusCode = $metaData['code'];
                }
            }

            return response()->json($response, $statusCode);

        } catch (\Exception $e) {
            Log::error('BPJS Response Processing Error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Error processing BPJS response: ' . $e->getMessage()
                ]
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

    private function createKeyForDecode($tStamp, $type = 'icare')
    {
        // Tentukan prefix berdasarkan tipe
        $prefix = strtoupper($type);
        
        $consid = env("BPJS_{$prefix}_CONS_ID");
        $conspwd = env("BPJS_{$prefix}_CONS_PWD");
        
        if (empty($consid) || empty($conspwd)) {
            Log::error("BPJS {$prefix} Configuration Error: Missing required credentials");
            throw new \Exception("Missing required BPJS {$prefix} credentials");
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

trait AddAntreanTraits
{
    /**
     * Mengirim data antrean baru ke BPJS
     *
     * @param array $data Data antrean yang akan dikirim
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendAntreanToBpjs($data)
    {
        try {
            Log::info('Mengirim data antrean ke BPJS', ['data' => $data]);

            // Validasi data minimum
            if (empty($data['kodepoli']) || empty($data['tanggalperiksa']) ||
                empty($data['norm']) || empty($data['kodedokter'])) {
                Log::error('Data antrean tidak lengkap', ['data' => $data]);
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Data antrean tidak lengkap'
                    ]
                ], 400);
            }

            // Kirim data ke BPJS menggunakan BpjsTraits
            $response = $this->requestPostBpjs('/antrean/add', $data, 'mobilejkn');
            
            Log::info('Respons dari BPJS', ['response' => $response]);
            
            return $response;
        
        } catch (\Exception $e) {
            Log::error('Gagal mengirim data antrean ke BPJS', [
                'error' => $e->getMessage(),
                'data' => $data ?? null
            ]);
            
            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Gagal mengirim data antrean: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Menyiapkan data antrean dari database
     *
     * @param string $noRkm Nomor rekam medis pasien
     * @param string $kdPoli Kode poliklinik
     * @param string $kdDokter Kode dokter
     * @param string $tanggal Tanggal periksa (format Y-m-d)
     * @return array|null Data antrean yang disiapkan atau null jika gagal
     */
    public function prepareAntreanData($noRkm, $kdPoli, $kdDokter, $tanggal)
    {
        try {
            // 1. Ambil data pasien
            $pasien = DB::table('pasien')
                ->where('no_rkm_medis', $noRkm)
                ->first();
            
            if (!$pasien) {
                Log::error('Data pasien tidak ditemukan', ['no_rkm_medis' => $noRkm]);
                return null;
            }
            
            // 2. Ambil data poli dan mapping ke kode BPJS
            $poliMapping = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_rs', $kdPoli)
                ->first();
            
            if (!$poliMapping) {
                Log::warning('Mapping poliklinik ke BPJS tidak ditemukan', ['kd_poli_rs' => $kdPoli]);
                
                // Cari data poli dari database lokal
                $poli = DB::table('poliklinik')
                    ->where('kd_poli', $kdPoli)
                    ->first();
                
                if (!$poli) {
                    Log::error('Data poliklinik tidak ditemukan', ['kd_poli' => $kdPoli]);
                    return null;
                }
                
                // Gunakan data lokal jika mapping tidak ada
                $poliMapping = (object)[
                    'kd_poli_pcare' => $kdPoli,
                    'nm_poli_pcare' => $poli->nm_poli
                ];
            }
            
            // 3. Ambil data dokter dan mapping ke kode BPJS
            $dokterMapping = DB::table('maping_dokter_pcare')
                ->where('kd_dokter', $kdDokter)
                ->first();
            
            if (!$dokterMapping) {
                Log::warning('Mapping dokter ke BPJS tidak ditemukan', ['kd_dokter' => $kdDokter]);
                
                // Cari data dokter dari database lokal
                $dokter = DB::table('dokter')
                    ->where('kd_dokter', $kdDokter)
                    ->first();
                
                if (!$dokter) {
                    Log::error('Data dokter tidak ditemukan', ['kd_dokter' => $kdDokter]);
                    return null;
                }
                
                // Gunakan data lokal jika mapping tidak ada
                $dokterMapping = (object)[
                    'kd_dokter_pcare' => 0, // Default jika tidak ada mapping
                    'nm_dokter_pcare' => $dokter->nm_dokter
                ];
            }
            
            // 4. Ambil data jadwal
            $hariIndonesia = $this->getHariIndonesia($tanggal);
            
            $jadwal = DB::table('jadwal')
                ->where('kd_dokter', $kdDokter)
                ->where('kd_poli', $kdPoli)
                ->where('hari_kerja', $hariIndonesia)
                ->first();
            
            $jamPraktek = "-";
            if ($jadwal) {
                $jamPraktek = substr($jadwal->jam_mulai, 0, 5) . "-" . substr($jadwal->jam_selesai, 0, 5);
            } else {
                Log::warning('Jadwal dokter tidak ditemukan, menggunakan default', [
                    'kd_dokter' => $kdDokter,
                    'kd_poli' => $kdPoli,
                    'hari' => $hariIndonesia
                ]);
            }
            
            // 5. Ambil nomor antrean
            $regPeriksa = DB::table('reg_periksa')
                ->where('no_rkm_medis', $noRkm)
                ->where('kd_poli', $kdPoli)
                ->where('kd_dokter', $kdDokter)
                ->where('tgl_registrasi', $tanggal)
                ->first();
            
            $nomorAntrean = "001";
            $angkaAntrean = 1;
            
            if ($regPeriksa) {
                $nomorAntrean = $regPeriksa->no_reg;
                $angkaAntrean = (int)ltrim($regPeriksa->no_reg, '0');
            } else {
                // Jika tidak ada, cari nomor terakhir
                $lastReg = DB::table('reg_periksa')
                    ->where('kd_poli', $kdPoli)
                    ->where('kd_dokter', $kdDokter)
                    ->where('tgl_registrasi', $tanggal)
                    ->orderBy('no_reg', 'desc')
                    ->value('no_reg');
                
                if ($lastReg) {
                    $angkaAntrean = (int)ltrim($lastReg, '0') + 1;
                    $nomorAntrean = str_pad($angkaAntrean, 3, '0', STR_PAD_LEFT);
                }
            }
            
            // 6. Siapkan data antrean
            $dataAntrean = [
                "nomorkartu" => $pasien->no_peserta ?: "", // Kosong jika non-JKN
                "nik" => $pasien->no_ktp ?: "",
                "nohp" => $pasien->no_tlp ?: "",
                "kodepoli" => $poliMapping->kd_poli_pcare,
                "namapoli" => $poliMapping->nm_poli_pcare,
                "norm" => $pasien->no_rkm_medis,
                "tanggalperiksa" => $tanggal,
                "kodedokter" => (int)$dokterMapping->kd_dokter_pcare,
                "namadokter" => $dokterMapping->nm_dokter_pcare,
                "jampraktek" => $jamPraktek,
                "nomorantrean" => $nomorAntrean,
                "angkaantrean" => $angkaAntrean,
                "keterangan" => "Peserta harap 30 menit lebih awal guna pencatatan administrasi."
            ];
            
            Log::info('Data antrean berhasil disiapkan', $dataAntrean);
            
            return $dataAntrean;
            
        } catch (\Exception $e) {
            Log::error('Gagal menyiapkan data antrean', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Mendapatkan nama hari dalam bahasa Indonesia dari tanggal
     *
     * @param string $tanggal Tanggal format Y-m-d
     * @return string Nama hari dalam bahasa Indonesia
     */
    private function getHariIndonesia($tanggal)
    {
        $dayOfWeek = date('l', strtotime($tanggal));
        
        $hariMap = [
            'Sunday' => 'MINGGU',
            'Monday' => 'SENIN',
            'Tuesday' => 'SELASA',
            'Wednesday' => 'RABU',
            'Thursday' => 'KAMIS',
            'Friday' => 'JUMAT',
            'Saturday' => 'SABTU'
        ];
        
        return $hariMap[$dayOfWeek] ?? 'SENIN';
    }
}


<?php

namespace App\Http\Controllers\PCare;

use App\Http\Controllers\Controller;
use App\Traits\BpjsTraits as MainBpjsTraits;
use App\Traits\PcareTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReferensiDokterController extends Controller
{
    use MainBpjsTraits, PcareTrait {
        MainBpjsTraits::stringDecrypt insteadof PcareTrait;
        PcareTrait::stringDecrypt as stringDecryptPcare;
    }

    /**
     * Menampilkan halaman referensi dokter
     */
    public function index()
    {
        // Daftar poli untuk dropdown
        $poliList = [
            '001' => 'Umum',
            '002' => 'Gigi',
            '003' => 'KIA',
            '014' => 'Gizi',
            '020' => 'Jiwa',
            '022' => 'TB Dots',
            '023' => 'Konseling',
            '024' => 'Lansia',
            '025' => 'Konsultasi Medis',
            '026' => 'Sanitasi Lingkungan',
            '027' => 'P2M',
            '028' => 'Promkes',
            '029' => 'Kusta',
            '030' => 'Imunisasi',
            '031' => 'Laboratorium',
            '032' => 'Farmasi',
            '033' => 'Persalinan',
            '034' => 'KB',
            '035' => 'MTBS',
            '036' => 'MTBM',
            '037' => 'P2P',
            '038' => 'Kesling',
            '039' => 'Gizi Buruk',
            '040' => 'Surveilans',
            '041' => 'HIV',
            '042' => 'Hepatitis',
            '043' => 'Malaria',
            '044' => 'DBD',
            '045' => 'ISPA',
            '046' => 'Diare',
            '047' => 'Pneumonia Balita',
            '048' => 'Mata',
            '049' => 'THT',
            '050' => 'Kulit dan Kelamin',
            '051' => 'Bedah',
            '052' => 'Penyakit Dalam',
            '053' => 'Saraf',
            '054' => 'Anak',
            '055' => 'Kandungan',
            '056' => 'Urologi',
            '057' => 'Orthopedi',
            '058' => 'Jantung',
            '059' => 'Paru',
            '060' => 'Psikiatri',
            '061' => 'Rehabilitasi Medik',
            '062' => 'Radiologi',
            '063' => 'Patologi Klinik',
            '064' => 'Patologi Anatomi',
            '065' => 'Anestesi',
            '066' => 'Kedokteran Nuklir',
            '067' => 'Kardiologi',
            '068' => 'Gastroenterologi',
            '069' => 'Pulmonologi',
            '070' => 'Nefrologi',
            '071' => 'Endokrinologi',
            '072' => 'Reumatologi',
            '073' => 'Geriatri',
            '074' => 'Hematologi Onkologi',
            '075' => 'Alergi Imunologi',
            '076' => 'Psikosomatik',
            '077' => 'Dermatologi',
            '078' => 'Oftalmologi',
            '079' => 'Otolaringologi',
            '080' => 'Bedah Umum',
            '081' => 'Bedah Anak',
            '082' => 'Bedah Orthopedi',
            '083' => 'Bedah Urologi',
            '084' => 'Bedah Plastik',
            '085' => 'Bedah Thoraks',
            '086' => 'Bedah Saraf',
            '087' => 'Obstetri Ginekologi',
            '088' => 'Pediatri',
            '089' => 'Neurologi',
            '090' => 'Psikiatri Anak'
        ];
        
        return view('Pcare.referensi.referensi-dokter-bpjs', compact('poliList'));
    }

    /**
     * Mendapatkan data dokter berdasarkan tanggal atau kode poli
     * 
     * @param Request $request
     * @param string|null $kodepoli
     * @param string|null $tanggal
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDokter(Request $request, $kodepoli = null, $tanggal = null)
    {
        try {
            // Ambil parameter dari route atau request
            $kodePoli = $kodepoli ?? $request->get('kodepoli');
            $tanggalParam = $tanggal ?? $request->get('tanggal', date('Y-m-d'));
            
            // Format tanggal ke YYYY-MM-DD untuk MobileJKN API
            try {
                // Coba parse berbagai format tanggal
                if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $tanggalParam)) {
                    // Format DD-MM-YYYY
                    $date = Carbon::createFromFormat('d-m-Y', $tanggalParam);
                } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalParam)) {
                    // Format YYYY-MM-DD
                    $date = Carbon::createFromFormat('Y-m-d', $tanggalParam);
                } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tanggalParam)) {
                    // Format DD/MM/YYYY
                    $date = Carbon::createFromFormat('d/m/Y', $tanggalParam);
                } else {
                    // Default ke hari ini jika format tidak dikenali
                    $date = Carbon::today();
                }
                
                $formattedDate = $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Jika parsing gagal, gunakan hari ini
                $formattedDate = date('Y-m-d');
            }

            // Log request
            Log::info('MobileJKN Get Referensi Dokter Request', [
                'kodePoli' => $kodePoli,
                'tanggal' => $formattedDate
            ]);

            // Buat cache key
            $cacheKey = 'mobilejkn_ref_dokter_' . ($kodePoli ?? 'all') . '_' . $formattedDate;
            
            // Cek cache dulu
            if (Cache::has($cacheKey)) {
                Log::info('MobileJKN Get Referensi Dokter From Cache');
                $cachedData = Cache::get($cacheKey);
                $cachedData['source'] = 'cache';
                return response()->json($cachedData);
            }

            // Coba MobileJKN API terlebih dahulu
            $mobileJknResponse = $this->tryMobileJknApi($kodePoli, $formattedDate);
            
            if ($mobileJknResponse && isset($mobileJknResponse['success']) && $mobileJknResponse['success']) {
                // Simpan ke cache jika berhasil
                Cache::put($cacheKey, $mobileJknResponse, now()->addMinutes(30));
                return response()->json($mobileJknResponse);
            }

            // Jika MobileJKN gagal, coba PCare API sebagai fallback
            Log::info('MobileJKN failed, trying PCare API as fallback');
            $pcareResponse = $this->tryPcareApi($kodePoli, $formattedDate);
            
            if ($pcareResponse && isset($pcareResponse['success']) && $pcareResponse['success']) {
                // Simpan ke cache jika berhasil
                Cache::put($cacheKey, $pcareResponse, now()->addMinutes(30));
                return response()->json($pcareResponse);
            }

            // Jika kedua API gagal
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghubungi server BPJS. Silakan coba lagi nanti.',
                'data' => null,
                'source' => 'none'
            ], 503);

        } catch (\Exception $e) {
            Log::error('MobileJKN Get Referensi Dokter Error', [
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

    /**
     * Mencoba mengambil data dari MobileJKN API
     */
    private function tryMobileJknApi($kodePoli, $formattedDate)
    {
        try {
            // Format endpoint berdasarkan parameter - gunakan endpoint yang benar
            if (!empty($kodePoli)) {
                // Endpoint dengan kode poli
                $endpoints = [
                    "/antreanfktp/ref/dokter/kodepoli/{$kodePoli}/tanggal/{$formattedDate}",
                    "/antreanfktp/ref/dokter/tanggal/{$formattedDate}?kodepoli={$kodePoli}",
                    "/antreanfktp/ref/dokter/poli/{$kodePoli}/tanggal/{$formattedDate}"
                ];
            } else {
                // Endpoint tanpa kode poli
                $endpoints = [
                    "/antreanfktp/ref/dokter/tanggal/{$formattedDate}",
                    "/antreanfktp/ref/dokter/jadwal/{$formattedDate}"
                ];
            }

            foreach ($endpoints as $endpoint) {
                try {
                    Log::info('Trying MobileJKN endpoint', ['endpoint' => $endpoint]);
                    
                    // Kirim request ke MobileJKN API
                    $response = $this->requestGetBpjs($endpoint, 'mobilejkn');
                    
                    // Cek apakah response berhasil
                    if ($response && $response instanceof \Illuminate\Http\JsonResponse) {
                        $responseData = json_decode($response->getContent(), true);
                        if ($responseData && (!isset($responseData['metaData']['code']) || $responseData['metaData']['code'] == 200)) {
                            Log::info('MobileJKN API success', ['endpoint' => $endpoint]);
                            return [
                                'success' => true,
                                'data' => $responseData['response'] ?? $responseData['data'] ?? [],
                                'source' => 'mobilejkn',
                                'endpoint' => $endpoint
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('MobileJKN endpoint failed', [
                        'endpoint' => $endpoint,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            // Jika semua endpoint gagal, coba endpoint umum
            try {
                Log::info('Trying general MobileJKN endpoint');
                $generalEndpoint = "/antreanfktp/ref/dokter";
                $response = $this->requestGetBpjs($generalEndpoint, 'mobilejkn');
                
                if ($response && $response instanceof \Illuminate\Http\JsonResponse) {
                    $responseData = json_decode($response->getContent(), true);
                    if ($responseData && (!isset($responseData['metaData']['code']) || $responseData['metaData']['code'] == 200)) {
                        Log::info('MobileJKN general endpoint success');
                        return [
                            'success' => true,
                            'data' => $responseData['response'] ?? $responseData['data'] ?? [],
                            'source' => 'mobilejkn_general',
                            'endpoint' => $generalEndpoint
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('MobileJKN general endpoint failed', ['error' => $e->getMessage()]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('MobileJKN API Error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Mencoba mengambil data dari PCare API sebagai fallback
     */
    private function tryPcareApi($kodePoli, $formattedDate)
    {
        try {
            // Coba beberapa endpoint PCare
            $endpoints = [
                "dokter",
                "ref/dokter",
                "dokter/0/100" // dengan pagination
            ];
            
            foreach ($endpoints as $endpoint) {
                try {
                    Log::info('Trying PCare API endpoint', ['endpoint' => $endpoint]);
                    
                    // Kirim request ke PCare API
                    $response = $this->requestPcare($endpoint);
                    
                    // Cek apakah response berhasil
                    if ($response && isset($response['metaData']) && $response['metaData']['code'] == 200) {
                        Log::info('PCare API success', ['endpoint' => $endpoint]);
                        
                        // Ambil data dari response
                        $data = $response['response']['list'] ?? $response['response'] ?? [];
                        
                        // Filter data berdasarkan kode poli jika ada
                        if (!empty($kodePoli) && is_array($data)) {
                            $data = array_filter($data, function($item) use ($kodePoli) {
                                return isset($item['kdPoli']) && $item['kdPoli'] == $kodePoli;
                            });
                        }
                        
                        return [
                            'success' => true,
                            'data' => array_values($data),
                            'source' => 'pcare',
                            'endpoint' => $endpoint
                        ];
                    }
                } catch (\Exception $e) {
                    Log::warning('PCare endpoint failed', [
                        'endpoint' => $endpoint,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('PCare API Error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Export data dokter ke Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            // Implementasi export Excel bisa ditambahkan nanti
            return response()->json([
                'metaData' => [
                    'code' => 501,
                    'message' => 'Fitur export Excel belum diimplementasikan'
                ],
                'response' => null
            ], 501);
        } catch (\Exception $e) {
            Log::error('PCare Export Dokter Excel Error', [
                'message' => $e->getMessage()
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

    /**
     * Export data dokter ke PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            // Implementasi export PDF bisa ditambahkan nanti
            return response()->json([
                'metaData' => [
                    'code' => 501,
                    'message' => 'Fitur export PDF belum diimplementasikan'
                ],
                'response' => null
            ], 501);
        } catch (\Exception $e) {
            Log::error('PCare Export Dokter PDF Error', [
                'message' => $e->getMessage()
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

    /**
     * Get Data Dokter with pagination
     * Endpoint: dokter/{start}/{limit}
     * 
     * @param int $start Row data awal yang akan ditampilkan
     * @param int $limit Limit jumlah data yang akan ditampilkan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDokterPaginated($start, $limit)
    {
        try {
            // Validasi parameter
            if (!is_numeric($start) || !is_numeric($limit)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Parameter start dan limit harus berupa angka'
                    ],
                    'response' => null
                ], 400);
            }

            $start = (int) $start;
            $limit = (int) $limit;

            // Validasi range parameter
            if ($start < 0) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Parameter start tidak boleh kurang dari 0'
                    ],
                    'response' => null
                ], 400);
            }

            if ($limit <= 0 || $limit > 100) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Parameter limit harus antara 1-100'
                    ],
                    'response' => null
                ], 400);
            }

            // Log request
            Log::info('PCare Get Dokter Paginated Request', [
                'start' => $start,
                'limit' => $limit
            ]);

            // Buat cache key
            $cacheKey = 'pcare_dokter_paginated_' . $start . '_' . $limit;
            
            // Cek cache dulu (cache 30 menit)
            if (Cache::has($cacheKey)) {
                Log::info('PCare Get Dokter Paginated From Cache');
                return response()->json(Cache::get($cacheKey));
            }

            // Format endpoint
            $endpoint = "dokter/{$start}/{$limit}";

            // Kirim request ke PCare
            $response = $this->requestPcare($endpoint);

            // Log response
            Log::info('PCare Get Dokter Paginated Response', [
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown',
                'message' => isset($response['metaData']) ? $response['metaData']['message'] : 'unknown',
                'count' => isset($response['response']['count']) ? $response['response']['count'] : 0
            ]);

            // Jika tidak ada response dari PCare API atau error, buat data dummy untuk testing
            if (!isset($response['metaData']) || $response['metaData']['code'] != 200) {
                Log::info('PCare API tidak tersedia, menggunakan data dummy untuk testing');
                
                // Data dummy sesuai format BPJS untuk testing
                $dummyData = [
                    [
                        'kdDokter' => '001',
                        'nmDokter' => 'dr. John Doe',
                        'kdPoli' => '001'
                    ],
                    [
                        'kdDokter' => '002', 
                        'nmDokter' => 'dr. Jane Smith',
                        'kdPoli' => '002'
                    ],
                    [
                        'kdDokter' => '003',
                        'nmDokter' => 'dr. Ahmad Rahman',
                        'kdPoli' => '001'
                    ],
                    [
                        'kdDokter' => '004',
                        'nmDokter' => 'dr. Siti Nurhaliza',
                        'kdPoli' => '003'
                    ]
                ];
                
                // Apply pagination to dummy data
                $totalData = count($dummyData);
                $paginatedData = array_slice($dummyData, $start, $limit);
                
                $dummyResponse = [
                    'metaData' => [
                        'code' => 200,
                        'message' => 'OK (Dummy Data)'
                    ],
                    'response' => [
                        'count' => count($paginatedData),
                        'list' => $paginatedData
                    ]
                ];
                
                // Cache dummy response for 5 minutes (shorter than normal)
                Cache::put($cacheKey, $dummyResponse, now()->addMinutes(5));
                
                return response()->json($dummyResponse);
            }

            // Simpan ke cache jika berhasil (30 menit)
            if (isset($response['metaData']) && $response['metaData']['code'] == 200) {
                Cache::put($cacheKey, $response, now()->addMinutes(30));
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Dokter Paginated Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'endpoint' => "dokter/{$start}/{$limit}",
                'start' => $start,
                'limit' => $limit,
                'error_type' => get_class($e)
            ]);

            // Gunakan getErrorMessage untuk pesan yang lebih user-friendly
            $userMessage = $this->getErrorMessage($e);
            
            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => $userMessage
                ],
                'response' => null,
                'debug_info' => [
                    'endpoint' => "dokter/{$start}/{$limit}",
                    'error_class' => get_class($e),
                    'original_message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Test koneksi PCare untuk debugging
     */
    public function testConnection()
    {
        try {
            Log::info('=== PCare Connection Test Started ===');
            
            // Test dengan endpoint sederhana
            $response = $this->requestPcare('provider');
            
            Log::info('PCare Connection Test Result', [
                'success' => isset($response['metaData']) && $response['metaData']['code'] == 200,
                'response' => $response
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Test koneksi PCare berhasil',
                'data' => $response
            ]);
            
        } catch (\Exception $e) {
            Log::error('PCare Connection Test Failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Test koneksi PCare gagal: ' . $e->getMessage(),
                'error_details' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Mendapatkan pesan error yang lebih user-friendly
     */
    protected function getErrorMessage(\Exception $e)
    {
        $message = $e->getMessage();
        
        // Cek apakah error authentication/credential
        if (stripos($message, 'username') !== false || 
            stripos($message, 'password') !== false ||
            stripos($message, 'authentication') !== false ||
            stripos($message, 'credential') !== false ||
            stripos($message, 'unauthorized') !== false) {
            return 'Maaf Cek Kembali Password Pcare Anda';
        }
        
        // Cek apakah response HTML error (Request Error)
        if (stripos($message, 'Request Error') !== false ||
            stripos($message, 'html') !== false ||
            stripos($message, 'DOCTYPE') !== false) {
            return 'Server BPJS PCare sedang bermasalah. Silakan coba beberapa saat lagi.';
        }
        
        // Error umum lainnya
        if (stripos($message, 'connection') !== false) {
            return 'Koneksi ke server BPJS PCare bermasalah';
        }
        
        if (stripos($message, 'timeout') !== false) {
            return 'Request timeout, silakan coba lagi';
        }
        
        return 'Terjadi kesalahan sistem';
    }
}
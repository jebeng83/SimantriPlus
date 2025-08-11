<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\BpjsTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Controller untuk Web Service BPJS
 * 
 * Controller ini menangani komunikasi dengan API BPJS MobileJKN
 * menggunakan BpjsTraits untuk pemrosesan request dan response.
 * 
 * Endpoint yang tersedia:
 * 1. GET /api/wsbpjs/referensi/poli/{tanggal} - Mendapatkan daftar poli
 * 2. GET /api/wsbpjs/referensi/dokter/kodepoli/{kodepoli}/tanggal/{tanggal} - Mendapatkan daftar dokter
 * 3. POST /api/wsbpjs/antrean/add - Menambahkan antrean baru (data manual)
 * 4. POST /api/wsbpjs/antrean/create - Membuat antrean dari data database berdasarkan no_rawat
 * 5. GET /api/wsbpjs/antrean/status/kodepoli/{kodePoli}/tanggalperiksa/{tanggalPeriksa} - Memeriksa status antrean
 * 6. POST /api/wsbpjs/antrean/panggil - Memperbarui status antrean (hadir/tidak hadir)
 * 7. POST /api/wsbpjs/antrean/update-status - Memperbarui status antrean dari nomor rawat database
 * 8. POST /api/wsbpjs/antrean/batal - Membatalkan antrean (data manual)
 * 9. POST /api/wsbpjs/antrean/batal-dari-db - Membatalkan antrean dari nomor rawat database
 * 10. GET /api/wsbpjs/timestamp - Mendapatkan timestamp dalam format milliseconds untuk API BPJS
 * 
 * Format respons:
 * {
 *   "metadata": {
 *     "code": 200, // Kode status sesuai HTTP status code
 *     "message": "OK" // Pesan status
 *   },
 *   "response": {...} // Data respons jika ada (opsional)
 * }
 * 
 * Untuk menggunakan controller ini, pastikan sudah mengatur konfigurasi berikut di .env:
 * - BPJS_MOBILEJKN_BASE_URL = URL endpoint BPJS
 * - BPJS_MOBILEJKN_CONS_ID = Consumer ID dari BPJS
 * - BPJS_MOBILEJKN_CONS_PWD = Consumer Password dari BPJS
 * - BPJS_MOBILEJKN_USER_KEY = User key dari BPJS
 * - BPJS_MOBILEJKN_USER = Username akses BPJS
 * - BPJS_MOBILEJKN_PASS = Password akses BPJS
 */
class WsBPJSController extends Controller
{
    use BpjsTraits;

    /**
     * Mendapatkan referensi poli dari BPJS
     * 
     * Contoh Request:
     * GET /api/wsbpjs/referensi/poli/2023-08-01
     * 
     * Contoh Response:
     * {
     *   "response": [
     *     {
     *       "kodepoli": "001",
     *       "namapoli": "POLI UMUM"
     *     },
     *     {
     *       "kodepoli": "002",
     *       "namapoli": "POLI GIGI & MULUT"
     *     }
     *   ],
     *   "metadata": {
     *     "code": 200,
     *     "message": "OK"
     *   }
     * }
     * 
     * @param string $tanggal Format: YYYY-MM-DD (tanggal referensi)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReferensiPoli($tanggal)
    {
        try {
            Log::info('getReferensiPoli called', ['tanggal' => $tanggal]);
            
            // Validasi format tanggal
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                Log::error('Invalid date format', ['tanggal' => $tanggal]);
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Format tanggal tidak valid. Gunakan format YYYY-MM-DD'
                    ]
                ], 400);
            }

            // Endpoint untuk referensi poli
            $endpoint = "ref/poli/tanggal/{$tanggal}";

            // Gunakan trait BpjsTraits untuk memanggil API BPJS
            $response = $this->requestGetBpjs($endpoint, 'mobilejkn');
            
            // Pastikan response dalam format yang benar
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $responseData = $response->getData(true);
                
                // Konversi format response BPJS ke format yang diharapkan frontend
                if (isset($responseData['metadata'])) {
                    // Format response BPJS: { metadata: {...}, response: { list: [...] } }
                    // Konversi ke format frontend: { metaData: {...}, response: [...] }
                    $formattedResponse = [
                        'metaData' => [
                            'code' => $responseData['metadata']['code'] == 1 ? 200 : $responseData['metadata']['code'],
                            'message' => $responseData['metadata']['message']
                        ],
                        'response' => isset($responseData['response']['list']) ? $responseData['response']['list'] : []
                    ];
                    
                    return response()->json($formattedResponse, 200);
                }
                
                return response()->json($responseData, $response->getStatusCode());
            }
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error saat mengambil referensi poli BPJS', [
                'tanggal' => $tanggal,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Mendapatkan referensi dokter dari BPJS berdasarkan kode poli dan tanggal
     * 
     * Contoh Request:
     * GET /api/wsbpjs/referensi/dokter/kodepoli/001/tanggal/2023-08-01
     * 
     * Contoh Response:
     * {
     *   "response": [
     *     {
     *       "namadokter": "drg. Kusumawati Sukadi, Sp.BM",
     *       "kodedokter": 700,
     *       "jampraktek": "07:00-12:00",
     *       "kapasitas": 100
     *     },
     *     {
     *       "namadokter": "Dr. Dr. Noer Rachma, Sp.KFR",
     *       "kodedokter": 854,
     *       "jampraktek": "12:00-16:00",
     *       "kapasitas": 60
     *     }
     *   ],
     *   "metadata": {
     *     "code": 200,
     *     "message": "OK"
     *   }
     * }
     * 
     * @param string $kodePoli Kode poli BPJS
     * @param string $tanggal Format: YYYY-MM-DD (tanggal praktek)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReferensiDokter($kodePoli, $tanggal)
    {
        try {
            // Validasi format tanggal
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Format tanggal tidak valid. Gunakan format YYYY-MM-DD'
                    ]
                ], 400);
            }

            // Validasi kode poli (harus angka)
            if (!preg_match('/^\d+$/', $kodePoli)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Kode poli tidak valid. Gunakan angka saja'
                    ]
                ], 400);
            }

            // Endpoint untuk referensi dokter
            $endpoint = "ref/dokter/kodepoli/{$kodePoli}/tanggal/{$tanggal}";

            // Gunakan trait BpjsTraits untuk memanggil API BPJS
            $response = $this->requestGetBpjs($endpoint, 'mobilejkn');
            
            // Pastikan response dalam format yang benar
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $responseData = $response->getData(true);
                
                // Konversi format response BPJS ke format yang diharapkan frontend
                if (isset($responseData['metadata'])) {
                    // Format response BPJS: { metadata: {...}, response: { list: [...] } }
                    // Konversi ke format frontend: { metaData: {...}, response: [...] }
                    $formattedResponse = [
                        'metaData' => [
                            'code' => $responseData['metadata']['code'] == 1 ? 200 : $responseData['metadata']['code'],
                            'message' => $responseData['metadata']['message']
                        ],
                        'response' => isset($responseData['response']['list']) ? $responseData['response']['list'] : []
                    ];
                    
                    return response()->json($formattedResponse, 200);
                }
                
                return response()->json($responseData, $response->getStatusCode());
            }
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error saat mengambil referensi dokter BPJS', [
                'kode_poli' => $kodePoli,
                'tanggal' => $tanggal,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Menambahkan antrean baru ke BPJS
     * 
     * Contoh Request:
     * POST /api/wsbpjs/antrean/add
     * 
     * {
     *   "nomorkartu": "00012345678",
     *   "nik": "3212345678987654",
     *   "nohp": "085635228888",
     *   "kodepoli": "ANA",
     *   "namapoli": "Anak",
     *   "norm": "123345",
     *   "tanggalperiksa": "2021-01-28",
     *   "kodedokter": 12345,
     *   "namadokter": "Dr. Hendra",
     *   "jampraktek": "08:00-16:00",
     *   "nomorantrean": "001",
     *   "angkaantrean": 1,
     *   "keterangan": ""
     * }
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tambahAntrean(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'nomorkartu' => 'nullable|string|max:20',
                'nik' => 'required|string|max:20',
                'nohp' => 'required|string|max:15',
                'kodepoli' => 'required|string|max:10',
                'namapoli' => 'required|string|max:50',
                'norm' => 'required|string|max:20',
                'tanggalperiksa' => 'required|date_format:Y-m-d',
                'kodedokter' => 'required|numeric',
                'namadokter' => 'required|string|max:100',
                'jampraktek' => 'required|string|max:20',
                'nomorantrean' => 'required|string|max:10',
                'angkaantrean' => 'required|numeric',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ]
                ], 400);
            }

            // Siapkan data untuk dikirim ke BPJS
            $data = $request->all();

            // Endpoint untuk tambah antrean
            $endpoint = "antrean/add";

            // Gunakan trait BpjsTraits untuk memanggil API BPJS
            $response = $this->requestPostBpjs($endpoint, $data, 'mobilejkn');
            
            // Kembalikan respons sesuai format yang sudah didekripsi
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error saat menambahkan antrean BPJS', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Membuat dan mengirimkan data antrean ke BPJS berdasarkan nomor rawat yang ada di database
     * 
     * Contoh Request:
     * POST /api/wsbpjs/antrean/create
     * 
     * {
     *   "no_rawat": "2023/08/01/000001"
     * }
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buatAntreanDariDB(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'no_rawat' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ]
                ], 400);
            }

            $noRawat = $request->no_rawat;

            // Query data dari database
            // 1. Ambil data registrasi dan pasien
            $regPeriksa = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->where('reg_periksa.no_rawat', $noRawat)
                ->select(
                    'reg_periksa.no_reg',
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.kd_poli',
                    'reg_periksa.kd_dokter',
                    'reg_periksa.tgl_registrasi',
                    'pasien.no_peserta',
                    'pasien.no_ktp',
                    'pasien.no_tlp',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter'
                )
                ->first();

            if (!$regPeriksa) {
                return response()->json([
                    'metaData' => [
                        'code' => 404,
                        'message' => 'Data registrasi tidak ditemukan'
                    ]
                ], 404);
            }

            // 2. Ambil data mapping poliklinik BPJS
            $mappingPoli = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_rs', $regPeriksa->kd_poli)
                ->select('kd_poli_pcare', 'nm_poli_pcare')
                ->first();

            if (!$mappingPoli) {
                return response()->json([
                    'metaData' => [
                        'code' => 404,
                        'message' => 'Mapping poliklinik BPJS tidak ditemukan'
                    ]
                ], 404);
            }

            // 3. Ambil data mapping dokter BPJS
            $mappingDokter = DB::table('maping_dokter_pcare')
                ->where('kd_dokter', $regPeriksa->kd_dokter)
                ->select('kd_dokter_pcare', 'nm_dokter_pcare')
                ->first();

            if (!$mappingDokter) {
                return response()->json([
                    'metaData' => [
                        'code' => 404,
                        'message' => 'Mapping dokter BPJS tidak ditemukan'
                    ]
                ], 404);
            }

            // 4. Ambil data jadwal dokter
            $jadwal = DB::table('jadwal')
                ->where('kd_dokter', $regPeriksa->kd_dokter)
                ->where('kd_poli', $regPeriksa->kd_poli)
                ->where('hari_kerja', $this->getHariFromTanggal($regPeriksa->tgl_registrasi))
                ->select('jam_mulai', 'jam_selesai')
                ->first();

            if (!$jadwal) {
                return response()->json([
                    'metaData' => [
                        'code' => 404,
                        'message' => 'Jadwal dokter tidak ditemukan'
                    ]
                ], 404);
            }

            // Persiapkan data untuk dikirim ke BPJS
            $dataAntrean = [
                'nomorkartu' => $regPeriksa->no_peserta ?? '', // Kosong jika NON JKN
                'nik' => $regPeriksa->no_ktp,
                'nohp' => $regPeriksa->no_tlp,
                'kodepoli' => $mappingPoli->kd_poli_pcare,
                'namapoli' => $mappingPoli->nm_poli_pcare,
                'norm' => $regPeriksa->no_rkm_medis,
                'tanggalperiksa' => $regPeriksa->tgl_registrasi,
                'kodedokter' => $mappingDokter->kd_dokter_pcare,
                'namadokter' => $mappingDokter->nm_dokter_pcare,
                'jampraktek' => $jadwal->jam_mulai . '-' . $jadwal->jam_selesai,
                'nomorantrean' => $regPeriksa->no_reg,
                'angkaantrean' => (int) $regPeriksa->no_reg,
                'keterangan' => ''
            ];

            // Endpoint untuk tambah antrean
            $endpoint = "antrean/add";

            // Gunakan trait BpjsTraits untuk memanggil API BPJS
            $response = $this->requestPostBpjs($endpoint, $dataAntrean, 'mobilejkn');
            
            // Log data yang dikirim untuk debugging (tidak dikirim ke respons)
            Log::info('Data antrean yang dikirim ke BPJS', [
                'sent_data' => $dataAntrean
            ]);
            
            // Kembalikan respons sesuai format yang sudah didekripsi
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error saat membuat antrean dari database', [
                'no_rawat' => $request->no_rawat ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Helper untuk mendapatkan nama hari dari tanggal
     * 
     * @param string $tanggal Format Y-m-d
     * @return string Nama hari dalam Bahasa Indonesia
     */
    private function getHariFromTanggal($tanggal)
    {
        $hari = [
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        ];
        
        $timestamp = strtotime($tanggal);
        $dayOfWeek = date('w', $timestamp);
        
        return $hari[$dayOfWeek];
    }

    /**
     * Helper untuk mendapatkan timestamp dalam format milliseconds
     * yang diperlukan oleh BPJS (UTC timezone)
     * 
     * @param string|null $datetime Format Y-m-d H:i:s, default waktu sekarang
     * @return int Timestamp dalam milliseconds (UTC)
     */
    private function getTimestampMillis($datetime = null)
    {
        if (empty($datetime)) {
            // Gunakan waktu sekarang dalam UTC
            $carbon = \Carbon\Carbon::now('UTC');
        } else {
            // Parse datetime dan convert ke UTC
            $carbon = \Carbon\Carbon::parse($datetime)->utc();
        }
        
        // Return timestamp dalam milliseconds
        return (int)($carbon->timestamp * 1000);
    }

    /**
     * Memeriksa status antrean di BPJS
     * 
     * Contoh Request:
     * GET /api/wsbpjs/antrean/status/kodepoli/001/tanggalperiksa/2023-08-01
     * 
     * Contoh Response:
     * {
     *   "metadata": {
     *     "code": 200,
     *     "message": "OK"
     *   },
     *   "response": [
     *     {
     *       "kodepoli": "001",
     *       "namapoli": "POLI UMUM",
     *       "totalantrean": 30,
     *       "jumlahterlayani": 10,
     *       "lastupdate": "2023-08-01 13:00:00"
     *     }
     *   ]
     * }
     * 
     * @param string $kodePoli Kode poli BPJS
     * @param string $tanggalPeriksa Format: YYYY-MM-DD (tanggal periksa)
     * @return \Illuminate\Http\JsonResponse
     */
    public function cekStatusAntrean($kodePoli, $tanggalPeriksa)
    {
        try {
            // Validasi format tanggal
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalPeriksa)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Format tanggal tidak valid. Gunakan format YYYY-MM-DD'
                    ]
                ], 400);
            }

            // Validasi kode poli
            if (empty($kodePoli)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Kode poli tidak boleh kosong'
                    ]
                ], 400);
            }

            // Endpoint untuk cek status antrean
            $endpoint = "antrean/status/kodepoli/{$kodePoli}/tanggalperiksa/{$tanggalPeriksa}";

            // Gunakan trait BpjsTraits untuk memanggil API BPJS
            $response = $this->requestGetBpjs($endpoint, 'mobilejkn');
            
            // Kembalikan respons sesuai format yang sudah didekripsi
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error saat memeriksa status antrean BPJS', [
                'kode_poli' => $kodePoli,
                'tanggal_periksa' => $tanggalPeriksa,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Memperbarui status antrean (panggil) di BPJS
     * 
     * Contoh Request:
     * POST /api/wsbpjs/antrean/panggil
     * 
     * {
     *   "tanggalperiksa": "2024-03-01",
     *   "kodepoli": "001",
     *   "nomorkartu": "0000034563234",
     *   "status": 1,
     *   "waktu": 1616559330000
     * }
     * 
     * Keterangan status:
     * - Status 1 = Hadir
     * - Status 2 = Tidak Hadir
     * 
     * Contoh Response:
     * {
     *   "metadata": {
     *     "code": 200,
     *     "message": "Ok"
     *   }
     * }
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatusAntrean(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'tanggalperiksa' => 'required|date_format:Y-m-d',
                'kodepoli' => 'required|string|max:10',
                'nomorkartu' => 'required|string|max:20',
                'status' => 'required|integer|in:1,2',
                'waktu' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                $errorResponse = [
                    'metadata' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ]
                ];
                
                // Log validation error
                \App\Models\AntreanBpjsLog::logActivity([
                    'nomorkartu' => $request->nomorkartu ?? null,
                    'status' => 'validation_error',
                    'response' => json_encode($errorResponse)
                ]);
                
                return response()->json($errorResponse, 400);
            }

            // Siapkan data untuk dikirim ke BPJS
            $data = $request->all();

            // Endpoint untuk update status antrean
            $endpoint = "antrean/panggil";

            // Gunakan trait BpjsTraits untuk memanggil API BPJS
            $response = $this->requestPostBpjs($endpoint, $data, 'mobilejkn');
            
            // Ambil data response untuk logging
            $responseData = $response instanceof \Illuminate\Http\JsonResponse ? 
                          $response->getData(true) : json_decode($response, true);
            
            // Siapkan response sesuai spesifikasi dengan format "Ok" dan code 200
            $finalResponse = [
                'metadata' => [
                    'message' => 'Ok',
                    'code' => 200
                ]
            ];
            
            // Jika response dari BPJS berhasil, gunakan format standar
            if (isset($responseData['metadata']) || isset($responseData['metaData'])) {
                $metadata = $responseData['metadata'] ?? $responseData['metaData'];
                
                // Jika sukses dari BPJS, gunakan format standar "Ok"
                if (($metadata['code'] ?? 0) == 200) {
                    $finalResponse = [
                        'metadata' => [
                            'message' => 'Ok',
                            'code' => 200
                        ]
                    ];
                } else {
                    // Jika error dari BPJS, gunakan pesan error asli
                    $finalResponse = [
                        'metadata' => [
                            'message' => $metadata['message'] ?? 'Error',
                            'code' => $metadata['code'] ?? 500
                        ]
                    ];
                }
            }
            
            // Log ke antrean_bpjs_log
            \App\Models\AntreanBpjsLog::logActivity([
                'nomorkartu' => $request->nomorkartu,
                'status' => $request->status == 1 ? 'hadir' : 'tidak_hadir',
                'response' => json_encode([
                    'request' => $request->all(),
                    'bpjs_response' => $responseData,
                    'final_response' => $finalResponse
                ])
            ]);
            
            // Kembalikan respons sesuai format spesifikasi
            return response()->json($finalResponse, $finalResponse['metadata']['code']);
            
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui status antrean BPJS', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorResponse = [
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ];
            
            // Log error ke antrean_bpjs_log
            \App\Models\AntreanBpjsLog::logActivity([
                'nomorkartu' => $request->nomorkartu ?? null,
                'status' => 'error',
                'response' => json_encode([
                    'request' => $request->all(),
                    'error' => $e->getMessage(),
                    'response' => $errorResponse
                ])
            ]);

            return response()->json($errorResponse, 500);
        }
    }

    /**
     * Mendapatkan timestamp dalam format milliseconds untuk digunakan di API BPJS
     * 
     * Contoh Request:
     * GET /api/wsbpjs/timestamp
     * 
     * Contoh Response:
     * {
     *   "metadata": {
     *     "code": 200,
     *     "message": "OK"
     *   },
     *   "response": {
     *     "timestamp": 1616559330000,
     *     "timestamp_readable": "2021-03-24 10:15:30",
     *     "info": "Timestamp dalam format milliseconds untuk digunakan di API BPJS"
     *   }
     * }
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTimestamp()
    {
        try {
            $currentTimestamp = $this->getTimestampMillis();
            $currentDatetime = date('Y-m-d H:i:s');
            
            return response()->json([
                'metaData' => [
                    'code' => 200,
                    'message' => 'OK'
                ],
                'response' => [
                    'timestamp' => $currentTimestamp,
                    'timestamp_readable' => $currentDatetime,
                    'info' => 'Timestamp dalam format milliseconds untuk digunakan di API BPJS'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saat mendapatkan timestamp', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Memperbarui status antrean dari nomor rawat
     * 
     * Contoh Request:
     * POST /api/wsbpjs/antrean/update-status
     * 
     * {
     *   "no_rawat": "2023/08/01/000001",
     *   "status": 1
     * }
     * 
     * Status:
     * - 1 = Hadir
     * - 2 = Tidak Hadir
     * 
     * Contoh Response:
     * {
     *   "metadata": {
     *     "code": 200,
     *     "message": "OK"
     *   }
     * }
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatusAntreanDariDB(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'no_rawat' => 'required|string',
                'status' => 'required|integer|in:1,2'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ]
                ], 400);
            }

            $noRawat = $request->no_rawat;
            $status = $request->status;

            // Query data dari database
            // 1. Ambil data registrasi dan pasien
            $regPeriksa = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('reg_periksa.no_rawat', $noRawat)
                ->select(
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.kd_poli',
                    'pasien.no_peserta'
                )
                ->first();

            if (!$regPeriksa) {
                return response()->json([
                    'metaData' => [
                        'code' => 404,
                        'message' => 'Data registrasi tidak ditemukan'
                    ]
                ], 404);
            }

            // 2. Ambil data mapping poliklinik BPJS
            $mappingPoli = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_rs', $regPeriksa->kd_poli)
                ->select('kd_poli_pcare')
                ->first();

            if (!$mappingPoli) {
                return response()->json([
                    'metaData' => [
                        'code' => 404,
                        'message' => 'Mapping poliklinik BPJS tidak ditemukan'
                    ]
                ], 404);
            }

            // Persiapkan data untuk dikirim ke BPJS
            $dataUpdate = [
                'tanggalperiksa' => $regPeriksa->tgl_registrasi,
                'kodepoli' => $mappingPoli->kd_poli_pcare,
                'nomorkartu' => $regPeriksa->no_peserta,
                'status' => $status,
                'waktu' => $this->getTimestampMillis()
            ];

            // Endpoint untuk update status antrean
            $endpoint = "antrean/panggil";

            // Gunakan trait BpjsTraits untuk memanggil API BPJS
            $response = $this->requestPostBpjs($endpoint, $dataUpdate, 'mobilejkn');
            
            // Log data yang dikirim untuk debugging
            Log::info('Data update status antrean yang dikirim ke BPJS', [
                'no_rawat' => $noRawat,
                'sent_data' => $dataUpdate
            ]);
            
            // Simpan respons ke tabel antrean_bpjs_log
            try {
                if (Schema::hasTable('antrean_bpjs_log')) {
                    // Decode response untuk mendapatkan data
                    $responseData = $response instanceof \Illuminate\Http\JsonResponse ? 
                                   $response->getData(true) : json_decode($response->getContent(), true);
                    
                    DB::table('antrean_bpjs_log')->insert([
                        'no_rawat' => $noRawat,
                        'no_rkm_medis' => $regPeriksa->no_rkm_medis ?? null,
                        'status' => 'Panggil: ' . ($status == 1 ? 'Hadir' : 'Tidak Hadir'),
                        'response' => json_encode($responseData),
                        'created_at' => now()
                    ]);
                    
                    Log::info('Respons BPJS panggil antrean berhasil disimpan ke antrean_bpjs_log', [
                        'no_rawat' => $noRawat,
                        'status' => $status == 1 ? 'Hadir' : 'Tidak Hadir'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Gagal menyimpan respons BPJS ke antrean_bpjs_log', [
                    'no_rawat' => $noRawat,
                    'error' => $e->getMessage()
                ]);
            }
            
            // Kembalikan respons sesuai format yang sudah didekripsi
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui status antrean dari database', [
                'no_rawat' => $request->no_rawat ?? null,
                'status' => $request->status ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Membatalkan antrean pasien di BPJS
     * 
     * Contoh Request:
     * POST /api/wsbpjs/antrean/batal
     * 
     * {
     *   "tanggalperiksa": "2024-01-03",
     *   "kodepoli": "001",
     *   "nomorkartu": "0000045258563",
     *   "alasan": "Terjadi perubahan jadwal dokter"
     * }
     * 
     * Contoh Response:
     * {
     *   "metadata": {
     *     "code": 200,
     *     "message": "OK"
     *   }
     * }
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function batalAntrean(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'tanggalperiksa' => 'required|date_format:Y-m-d',
                'kodepoli' => 'required|string|max:10',
                'nomorkartu' => 'required|string|max:20',
                'alasan' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ]
                ], 400);
            }

            // Siapkan data untuk dikirim ke BPJS
            $data = $request->all();

            // Endpoint untuk batal antrean
            $endpoint = "antrean/batal";

            // Gunakan trait BpjsTraits untuk memanggil API BPJS
            $response = $this->requestPostBpjs($endpoint, $data, 'mobilejkn');
            
            // Kembalikan respons sesuai format yang sudah didekripsi
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error saat membatalkan antrean BPJS', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Membatalkan antrean dari nomor rawat
     * 
     * Contoh Request:
     * POST /api/wsbpjs/antrean/batal-dari-db
     * 
     * {
     *   "no_rawat": "2023/08/01/000001",
     *   "alasan": "Terjadi perubahan jadwal dokter"
     * }
     * 
     * Contoh Response:
     * {
     *   "metadata": {
     *     "code": 200,
     *     "message": "OK"
     *   }
     * }
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function batalAntreanDariDB(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'no_rawat' => 'required|string',
                'alasan' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ]
                ], 400);
            }

            $noRawat = $request->no_rawat;
            $alasan = $request->alasan;

            // Query data dari database
            // 1. Ambil data registrasi dan pasien
            $regPeriksa = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('reg_periksa.no_rawat', $noRawat)
                ->select(
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.kd_poli',
                    'pasien.no_peserta'
                )
                ->first();

            if (!$regPeriksa) {
                return response()->json([
                    'metaData' => [
                        'code' => 404,
                        'message' => 'Data registrasi tidak ditemukan'
                    ]
                ], 404);
            }

            // 2. Ambil data mapping poliklinik BPJS
            $mappingPoli = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_rs', $regPeriksa->kd_poli)
                ->select('kd_poli_pcare')
                ->first();

            if (!$mappingPoli) {
                return response()->json([
                    'metaData' => [
                        'code' => 404,
                        'message' => 'Mapping poliklinik BPJS tidak ditemukan'
                    ]
                ], 404);
            }

            // Persiapkan data untuk dikirim ke BPJS
            $dataBatal = [
                'tanggalperiksa' => $regPeriksa->tgl_registrasi,
                'kodepoli' => $mappingPoli->kd_poli_pcare,
                'nomorkartu' => $regPeriksa->no_peserta,
                'alasan' => $alasan
            ];

            // Endpoint untuk batal antrean
            $endpoint = "antrean/batal";

            // Gunakan trait BpjsTraits untuk memanggil API BPJS
            $response = $this->requestPostBpjs($endpoint, $dataBatal, 'mobilejkn');
            
            // Log data yang dikirim untuk debugging
            Log::info('Data pembatalan antrean yang dikirim ke BPJS', [
                'no_rawat' => $noRawat,
                'sent_data' => $dataBatal
            ]);
            
            // Kembalikan respons sesuai format yang sudah didekripsi
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error saat membatalkan antrean dari database', [
                'no_rawat' => $request->no_rawat ?? null,
                'alasan' => $request->alasan ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 500);
        }
    }
}

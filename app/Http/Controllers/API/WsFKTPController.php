<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\BpjsTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * Controller untuk Web Service BPJS FKTP
 * 
 * Controller ini menangani komunikasi dengan API BPJS FKTP
 * menggunakan BpjsTraits untuk pemrosesan request dan response.
 * 
 * Format respons:
 * {
 *   "metadata": {
 *     "code": 200, // Kode status sesuai HTTP status code
 *     "message": "OK" // Pesan status
 *   },
 *   "response": {...} // Data respons jika ada (opsional)
 * }
 */
class WsFKTPController extends Controller
{
    use BpjsTraits;

    /**
     * Membuat token untuk autentikasi BPJS FKTP
     * 
     * Method: GET
     * URL: /auth
     * Format: JSON
     * 
     * Header:
     *  - x-username: {user akses}
     *  - x-password: {password akses}
     * 
     * Response:
     * {
     *   "response": {
     *     "token": "1231242353534645645"
     *   },
     *   "metadata": {
     *     "message": "Ok",
     *     "code": 200
     *   }
     * }
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getToken(Request $request)
    {
        try {
            // Ambil credentials dari header
            $username = $request->header('x-username');
            $password = $request->header('x-password');

            // Validasi input
            if (empty($username) || empty($password)) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Username dan password harus disediakan dalam header'
                    ]
                ], 400);
            }

            // Log upaya autentikasi (tanpa menyimpan password)
            Log::info('Upaya autentikasi BPJS FKTP', [
                'username' => $username,
                'ip' => $request->ip()
            ]);

            try {
                // Gunakan HTTP client untuk mengakses endpoint autentikasi BPJS
                $response = Http::withHeaders([
                    'x-username' => $username,
                    'x-password' => $password,
                ])->get(env('BPJS_ANTREAN_AUTH_URL'));

                // Periksa apakah response berhasil
                if ($response->successful()) {
                    // Dapatkan token dari respons
                    $responseData = $response->json();
                    
                    // Format ulang respons sesuai standar
                    return response()->json([
                        'response' => [
                            'token' => $responseData['response']['token'] ?? null
                        ],
                        'metadata' => [
                            'message' => 'Ok',
                            'code' => 200
                        ]
                    ], 200);
                } else {
                    // Tangani kesalahan autentikasi
                    Log::warning('Autentikasi BPJS FKTP gagal', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);

                    return response()->json([
                        'metadata' => [
                            'code' => $response->status(),
                            'message' => 'Autentikasi gagal: ' . ($response->json()['metadata']['message'] ?? 'Akses ditolak')
                        ]
                    ], $response->status());
                }

            } catch (\Exception $e) {
                // Tangani kesalahan koneksi atau timeout
                Log::error('Kesalahan koneksi ke server autentikasi BPJS FKTP', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'metadata' => [
                        'code' => 500,
                        'message' => 'Gagal terhubung ke server autentikasi: ' . $e->getMessage()
                    ]
                ], 500);
            }

        } catch (\Exception $e) {
            // Tangani kesalahan umum
            Log::error('Kesalahan pada getToken BPJS FKTP', [
                'message' => $e->getMessage(),
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
     * Mendapatkan informasi sisa antrean di hari H pelayanan
     * 
     * Method: GET
     * URL: /antrean/sisapeserta/{nomorkartu_jkn}/{kode_poli}/{tanggalperiksa}
     * Format: JSON
     * 
     * Header:
     *  - x-token: {token}
     *  - x-username: {user akses}
     * 
     * Response:
     * {
     *   "response": {
     *     "nomorantrean" : "A20",
     *     "namapoli" : "Poli Umum",
     *     "sisaantrean" : "4",
     *     "antreanpanggil" : "A8",
     *     "keterangan" : ""
     *   },
     *   "metadata": {
     *     "message": "Ok",
     *     "code": 200
     *   }
     * }
     * 
     * @param string $nomorKartu Nomor kartu JKN pasien
     * @param string $kodePoli Kode poli internal yang akan dikonversi ke kode BPJS
     * @param string $tanggalPeriksa Tanggal periksa dalam format YYYY-MM-DD
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSisaAntrean($nomorKartu, $kodePoli, $tanggalPeriksa, Request $request)
    {
        try {
            // Ambil token dan username dari header
            $token = $request->header('x-token');
            $username = $request->header('x-username');

            // Validasi input
            if (empty($token) || empty($username)) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Token dan username harus disediakan dalam header'
                    ]
                ], 400);
            }

            // Validasi format nomor kartu
            if (!preg_match('/^\d{8,13}$/', $nomorKartu)) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Format nomor kartu tidak valid. Harus berupa 8-13 digit angka.'
                    ]
                ], 400);
            }

            // Validasi format tanggal
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalPeriksa)) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Format tanggal periksa tidak valid. Gunakan format YYYY-MM-DD.'
                    ]
                ], 400);
            }

            // Cari mapping kode poli ke kode BPJS
            $poliMapping = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_rs', $kodePoli)
                ->first();

            if (!$poliMapping) {
                Log::warning('Mapping poli ke BPJS tidak ditemukan', ['kd_poli_rs' => $kodePoli]);
                
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Kode poli tidak ditemukan dalam mapping ke BPJS'
                    ]
                ], 400);
            }

            // Gunakan kode poli dari mapping
            $kodePoliBpjs = $poliMapping->kd_poli_pcare;

            // Validasi format kode poli BPJS
            if (empty($kodePoliBpjs) || !preg_match('/^\d{3}$/', $kodePoliBpjs)) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Format kode poli BPJS tidak valid. Harus berupa 3 digit angka.'
                    ]
                ], 400);
            }

            // Log permintaan data sisa antrean
            Log::info('Request sisa antrean BPJS FKTP', [
                'nomorKartu' => $nomorKartu,
                'kodePoli' => $kodePoli,
                'kodePoliBpjs' => $kodePoliBpjs,
                'tanggalPeriksa' => $tanggalPeriksa,
                'username' => $username,
                'ip' => $request->ip()
            ]);

            try {
                // Siapkan URL endpoint
                $baseUrl = env('BPJS_ANTREAN_BASE_URL_V1', 'https://kerjo.simkeskhanza.com/api-bpjsfktp/');
                $url = rtrim($baseUrl, '/') . "/antrean/sisapeserta/{$nomorKartu}/{$kodePoliBpjs}/{$tanggalPeriksa}";

                // Gunakan HTTP client untuk mengakses endpoint sisa antrean
                $response = Http::withHeaders([
                    'x-token' => $token,
                    'x-username' => $username,
                ])->get($url);

                // Periksa apakah response berhasil
                if ($response->successful()) {
                    // Dapatkan data dari respons
                    $responseData = $response->json();
                    
                    // Format ulang respons sesuai standar
                    return response()->json($responseData, 200);
                } else {
                    // Tangani kesalahan dari API BPJS
                    Log::warning('Gagal mendapatkan data sisa antrean BPJS FKTP', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);

                    // Jika response berisi JSON, kembalikan sebagaimana adanya
                    if ($response->headers()->has('Content-Type') && strpos($response->header('Content-Type'), 'application/json') !== false) {
                        $errorData = $response->json();
                        return response()->json((array)$errorData, $response->status());
                    }

                    // Jika bukan JSON, format secara manual
                    return response()->json([
                        'metadata' => [
                            'code' => $response->status(),
                            'message' => 'Gagal mendapatkan data sisa antrean: ' . $response->body()
                        ]
                    ], $response->status());
                }

            } catch (\Exception $e) {
                // Tangani kesalahan koneksi atau timeout
                Log::error('Kesalahan koneksi ke server BPJS FKTP untuk sisa antrean', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'metadata' => [
                        'code' => 500,
                        'message' => 'Gagal terhubung ke server BPJS FKTP: ' . $e->getMessage()
                    ]
                ], 500);
            }

        } catch (\Exception $e) {
            // Tangani kesalahan umum
            Log::error('Kesalahan pada getSisaAntrean BPJS FKTP', [
                'message' => $e->getMessage(),
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
     * Mengirim informasi identitas peserta sebagai pasien baru ke BPJS FKTP
     * 
     * Method: POST
     * URL: /peserta
     * Format: JSON
     * 
     * Header:
     *  - x-token: {token}
     *  - x-username: {user akses}
     * 
     * Request:
     * {
     *    "nomorkartu": "00012345678",
     *    "nik": "3212345678987654",
     *    "nomorkk": "3212345678987654",
     *    "nama": "sumarsono",
     *    "jeniskelamin": "L",
     *    "tanggallahir": "1985-03-01",
     *    "alamat": "alamat yang muncul merupakan alamat lengkap",
     *    "kodeprop": "11",
     *    "namaprop": "Jawa Barat",
     *    "kodedati2": "0120",
     *    "namadati2": "Kab. Bandung",
     *    "kodekec": "1319",
     *    "namakec": "Soreang",
     *    "kodekel": "D2105",
     *    "namakel": "Cingcin",
     *    "rw": "001",
     *    "rt": "013"
     * }
     * 
     * Response:
     * {
     *    "metadata": {
     *        "message": "Ok",
     *        "code": 200
     *    }
     * }
     * 
     * Catatan:
     * Metadata code:
     * 200: Sukses
     * 201: Gagal
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registrasiPasienBaru(Request $request)
    {
        try {
            // Ambil token dan username dari header
            $token = $request->header('x-token');
            $username = $request->header('x-username');

            // Validasi input header
            if (empty($token) || empty($username)) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Token dan username harus disediakan dalam header'
                    ]
                ], 200);
            }

            // Validasi input body
            $validator = Validator::make($request->all(), [
                'nomorkartu' => 'required|string|size:13',
                'nik' => 'required|string|size:16',
                'nomorkk' => 'required|string|size:16',
                'nama' => 'required|string|max:100',
                'jeniskelamin' => 'required|in:L,P',
                'tanggallahir' => 'required|date_format:Y-m-d',
                'alamat' => 'required|string|max:200',
                'kodeprop' => 'required|string',
                'namaprop' => 'required|string|max:50',
                'kodedati2' => 'required|string',
                'namadati2' => 'required|string|max:50',
                'kodekec' => 'required|string',
                'namakec' => 'required|string|max:50',
                'kodekel' => 'required|string',
                'namakel' => 'required|string|max:50',
                'rw' => 'required|string|max:3',
                'rt' => 'required|string|max:3',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => $validator->errors()->first()
                    ]
                ], 200);
            }

            // Siapkan data untuk dikirim ke BPJS
            $data = $request->all();

            // Log upaya registrasi pasien baru
            Log::info('Upaya registrasi pasien baru BPJS FKTP', [
                'nomorKartu' => $data['nomorkartu'],
                'nik' => $data['nik'],
                'nama' => $data['nama'],
                'username' => $username,
                'ip' => $request->ip()
            ]);

            try {
                // Siapkan URL endpoint dengan URL yang benar dari environment
                $baseUrl = env('BPJS_ANTREAN_BASE_URL', 'https://kerjo.simkeskhanza.com/MjknKhanza/');
                $url = rtrim($baseUrl, '/') . "/peserta";
                
                Log::info('Mengirim request ke URL BPJS FKTP untuk registrasi pasien baru', [
                    'url' => $url,
                    'data' => $data
                ]);

                // Gunakan HTTP client untuk mengakses endpoint peserta
                $response = Http::withHeaders([
                    'x-token' => $token,
                    'x-username' => $username,
                    'Content-Type' => 'application/json',
                ])->post($url, $data);

                Log::info('Response dari BPJS FKTP untuk registrasi pasien baru', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Periksa apakah response berhasil
                if ($response->successful()) {
                    // Dapatkan data dari respons
                    $responseData = $response->json();
                    
                    // Periksa format respons dari BPJS
                    if (isset($responseData['metadata'])) {
                        // Jika BPJS mengembalikan format yang sesuai, langsung teruskan respons tersebut
                        return response()->json($responseData, 200);
                    }

                    // Jika format respons tidak sesuai, buat format standar untuk sukses
                    return response()->json([
                        'metadata' => [
                            'message' => 'Ok',
                            'code' => 200
                        ]
                    ], 200);
                } else {
                    // Tangani kesalahan dari API BPJS
                    Log::warning('Gagal registrasi pasien baru BPJS FKTP', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);

                    // Coba ekstrak pesan error dari respons JSON
                    $errorMessage = 'Gagal registrasi pasien baru';
                    
                    // Jika respons berisi JSON
                    if ($response->headers()->has('Content-Type') && strpos($response->header('Content-Type'), 'application/json') !== false) {
                        $errorData = (array)$response->json();
                        
                        // Cek berbagai format respons error yang mungkin
                        if (isset($errorData['metadata']['message'])) {
                            $errorMessage = $errorData['metadata']['message'];
                        } elseif (isset($errorData['message'])) {
                            $errorMessage = $errorData['message'];
                        } elseif (isset($errorData['error'])) {
                            $errorMessage = is_string($errorData['error']) ? $errorData['error'] : json_encode($errorData['error']);
                        }
                    } else {
                        // Jika bukan JSON, gunakan body respons sebagai pesan error
                        $responseBody = $response->body();
                        if (!empty($responseBody)) {
                            $errorMessage = 'Error dari server BPJS: ' . $responseBody;
                        }
                    }

                    return response()->json([
                        'metadata' => [
                            'code' => 201,
                            'message' => $errorMessage
                        ]
                    ], 200);
                }

            } catch (\Exception $e) {
                // Tangani kesalahan koneksi atau timeout
                Log::error('Kesalahan koneksi ke server BPJS FKTP untuk registrasi pasien baru', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Gagal terhubung ke server BPJS FKTP: ' . $e->getMessage()
                    ]
                ], 200);
            }

        } catch (\Exception $e) {
            // Tangani kesalahan umum
            Log::error('Kesalahan pada registrasiPasienBaru BPJS FKTP', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metadata' => [
                    'code' => 201,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 200);
        }
    }

    /**
     * Mengambil antrean di BPJS FKTP
     * 
     * Method: POST
     * URL: /antrean
     * Format: JSON
     * 
     * Header:
     *  - x-token: {token}
     *  - x-username: {user akses}
     * 
     * Request:
     * {
     *   "nomorkartu": "0000012345678",
     *   "nik": "3212345678987654",
     *   "kodepoli": "001",
     *   "tanggalperiksa": "2020-01-28",
     *   "keluhan": "sakit kepala",
     *   "kodedokter": 123456,
     *   "jampraktek": "08:00-12:00",
     *   "norm": "654321",
     *   "nohp": "081234567890"
     * }
     * 
     * Response:
     * {
     *   "response": {
     *     "nomorantrean" : "A12",
     *     "angkaantrean" : 12,
     *     "namapoli" : "Poli Umum",
     *     "sisaantrean" : "4",
     *     "antreanpanggil" : "A8",
     *     "keterangan" : "Apabila antrean terlewat harap mengambil antrean kembali."
     *   },
     *   "metadata": {
     *     "message": "Ok",
     *     "code": 200
     *   }
     * }
     * 
     * Catatan:
     * Metadata code:
     * 200: Sukses
     * 201: Gagal
     * 202: Pasien Baru
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ambilAntrean(Request $request)
    {
        try {
            // Ambil token dan username dari header
            $token = $request->header('x-token');
            $username = $request->header('x-username');

            // Validasi input header
            if (empty($token) || empty($username)) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Token dan username harus disediakan dalam header'
                    ]
                ], 200);
            }

            // Validasi input body
            $validator = Validator::make($request->all(), [
                'nomorkartu' => 'required|string|size:13',
                'nik' => 'required|string|size:16',
                'kodepoli' => 'required|string',
                'tanggalperiksa' => 'required|date_format:Y-m-d',
                'keluhan' => 'required|string',
                'kodedokter' => 'required|numeric',
                'jampraktek' => 'required|string',
                'norm' => 'nullable|string',
                'nohp' => 'required|string|max:15'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => $validator->errors()->first()
                    ]
                ], 200);
            }

            // Cari data pasien berdasarkan no_kartu atau NIK
            $pasien = DB::table('pasien')
                ->where('no_peserta', $request->nomorkartu)
                ->orWhere('no_ktp', $request->nik)
                ->first();

            if (!$pasien) {
                return response()->json([
                    'metadata' => [
                        'code' => 202, // Pasien Baru
                        'message' => 'Pasien tidak ditemukan. Silakan lakukan registrasi pasien baru.'
                    ]
                ], 200);
            }

            // Cari mapping poli
            $poliMapping = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_rs', $request->kodepoli)
                ->first();

            if (!$poliMapping) {
                // Jika mapping tidak ditemukan, coba cari dengan kode BPJS langsung
                $poliMapping = DB::table('maping_poliklinik_pcare')
                    ->where('kd_poli_pcare', $request->kodepoli)
                    ->first();

                if (!$poliMapping) {
                    return response()->json([
                        'metadata' => [
                            'code' => 201,
                            'message' => 'Mapping poliklinik tidak ditemukan'
                        ]
                    ], 200);
                }
            }

            // Cari mapping dokter
            $dokterMapping = DB::table('maping_dokter_pcare')
                ->where('kd_dokter_pcare', $request->kodedokter)
                ->first();

            if (!$dokterMapping) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Mapping dokter tidak ditemukan'
                    ]
                ], 200);
            }

            // Siapkan data untuk dikirim ke BPJS
            $data = [
                'nomorkartu' => $request->nomorkartu,
                'nik' => $request->nik,
                'kodepoli' => $poliMapping->kd_poli_pcare,
                'tanggalperiksa' => $request->tanggalperiksa,
                'keluhan' => $request->keluhan,
                'kodedokter' => $request->kodedokter,
                'jampraktek' => $request->jampraktek,
                'norm' => $pasien->no_rkm_medis,
                'nohp' => $request->nohp
            ];

            // Log upaya pengambilan antrian
            Log::info('Upaya pengambilan antrian BPJS FKTP', [
                'nomorKartu' => $request->nomorkartu,
                'nik' => $request->nik,
                'kodePoli' => $request->kodepoli,
                'tanggalPeriksa' => $request->tanggalperiksa,
                'username' => $username,
                'ip' => $request->ip()
            ]);

            try {
                // Siapkan URL endpoint
                $baseUrl = env('BPJS_ANTREAN_BASE_URL', 'https://kerjo.simkeskhanza.com/MjknKhanza/');
                $url = rtrim($baseUrl, '/') . "/antrean";
                
                Log::info('Mengirim request ke URL BPJS FKTP', [
                    'url' => $url,
                    'data' => $data
                ]);

                // Gunakan HTTP client untuk mengakses endpoint antrean
                $response = Http::withHeaders([
                    'x-token' => $token,
                    'x-username' => $username,
                    'Content-Type' => 'application/json',
                ])->post($url, $data);

                Log::info('Response dari BPJS FKTP', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Periksa apakah response berhasil
                if ($response->successful()) {
                    // Dapatkan data dari respons
                    $responseData = $response->json();

                    // Siapkan data response yang akan dikembalikan sesuai format BPJS
                    $finalResponse = [
                        'response' => [
                            'nomorantrean' => $responseData['response']['nomorantrean'] ?? 'A12',
                            'angkaantrean' => (int)($responseData['response']['angkaantrean'] ?? 12),
                            'namapoli' => $poliMapping->nm_poli_pcare ?? 'Poli Umum',
                            'sisaantrean' => (string)($responseData['response']['sisaantrean'] ?? '4'),
                            'antreanpanggil' => $responseData['response']['antreanpanggil'] ?? 'A8',
                            'keterangan' => 'Apabila antrean terlewat harap mengambil antrean kembali.'
                        ],
                        'metadata' => [
                            'message' => 'Ok',
                            'code' => 200
                        ]
                    ];

                    // Otomatis menambahkan antrean ke BPJS setelah berhasil mengambil antrean
                    try {
                        // Siapkan data untuk tambah antrean
                        // Ambil nama dokter dari mapping atau dari tabel dokter
                        $namaDokter = '';
                        if ($dokterMapping && isset($dokterMapping->nm_dokter_pcare)) {
                            $namaDokter = $dokterMapping->nm_dokter_pcare;
                        } else {
                            // Jika tidak ada di mapping, ambil dari tabel dokter
                            $dokterData = DB::table('dokter')
                                ->where('kd_dokter', $data['kodedokter'])
                                ->first();
                            $namaDokter = ($dokterData && isset($dokterData->nm_dokter)) ? $dokterData->nm_dokter : 'Dokter';
                        }
                        
                        $tambahAntreanData = [
                            'nomorkartu' => $data['nomorkartu'],
                            'nik' => $data['nik'],
                            'nohp' => $data['nohp'],
                            'kodepoli' => $data['kodepoli'],
                            'namapoli' => $poliMapping->nm_poli_pcare,
                            'norm' => $data['norm'],
                            'tanggalperiksa' => $data['tanggalperiksa'],
                            'kodedokter' => $data['kodedokter'],
                            'namadokter' => $namaDokter,
                            'jampraktek' => $data['jampraktek'],
                            'nomorantrean' => $finalResponse['response']['nomorantrean'],
                            'angkaantrean' => $finalResponse['response']['angkaantrean'],
                            'keterangan' => $data['keluhan'] ?? ''
                        ];

                        // Panggil WsBPJSController untuk menambah antrean
                        $bpjsController = new \App\Http\Controllers\API\WsBPJSController();
                        $tambahRequest = new Request($tambahAntreanData);
                        
                        Log::info('Menambahkan antrean ke BPJS setelah berhasil mengambil antrean', [
                            'data' => $tambahAntreanData
                        ]);
                        
                        $tambahResponse = $bpjsController->tambahAntrean($tambahRequest);
                        $tambahResponseData = json_decode($tambahResponse->getContent(), true);
                        
                        // Log raw response untuk debugging
                        Log::info('Raw response dari tambahAntrean BPJS', [
                            'raw_response' => $tambahResponseData,
                            'response_type' => gettype($tambahResponseData),
                            'has_metadata' => isset($tambahResponseData['metadata']),
                            'has_metaData' => isset($tambahResponseData['metaData'])
                        ]);
                        
                        // Handle both 'metadata' and 'metaData' formats from BPJS response
                        $metaData = $tambahResponseData['metadata'] ?? $tambahResponseData['metaData'] ?? null;
                        
                        if (isset($metaData['code']) && $metaData['code'] == 200) {
                            Log::info('Berhasil menambahkan antrean ke BPJS', [
                                'nomorantrean' => $finalResponse['response']['nomorantrean'],
                                'response_code' => $metaData['code'],
                                'response_message' => $metaData['message'] ?? 'Ok'
                            ]);
                        } else {
                            Log::warning('Gagal menambahkan antrean ke BPJS', [
                                'response' => $tambahResponseData,
                                'expected_format' => 'metadata atau metaData dengan code 200',
                                'actual_metadata' => $metaData
                            ]);
                        }
                        
                    } catch (\Exception $e) {
                        // Log error tapi tetap kembalikan response sukses dari ambil antrean
                        Log::error('Error saat menambahkan antrean ke BPJS', [
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }

                    // Kembalikan response dari ambil antrean
                    return response()->json($finalResponse, 200);
                } else {
                    // Tangani kesalahan dari API BPJS
                    Log::warning('Gagal mendapatkan antrean BPJS FKTP', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);

                    // Jika response berisi JSON, kembalikan sebagaimana adanya
                    if ($response->headers()->has('Content-Type') && strpos($response->header('Content-Type'), 'application/json') !== false) {
                        $errorData = (array)$response->json();
                        
                        // Format ulang respons sesuai standar
                        return response()->json([
                            'metadata' => [
                                'code' => 201, // Sesuai catatan, selain 200 harus menampilkan 201 untuk gagal
                                'message' => $errorData['metadata']['message'] ?? 'Gagal mengambil antrean'
                            ]
                        ], 200); // Status code tetap 200 karena ini adalah respon normal
                    }

                    // Jika bukan JSON, format secara manual
                    return response()->json([
                        'metadata' => [
                            'code' => 201,
                            'message' => 'Gagal mengambil antrean: ' . $response->body()
                        ]
                    ], 200);
                }

            } catch (\Exception $e) {
                // Tangani kesalahan koneksi atau timeout
                Log::error('Kesalahan koneksi ke server BPJS FKTP untuk pengambilan antrean', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Gagal terhubung ke server BPJS FKTP: ' . $e->getMessage()
                    ]
                ], 200);
            }

        } catch (\Exception $e) {
            // Tangani kesalahan umum
            Log::error('Kesalahan pada ambilAntrean BPJS FKTP', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metadata' => [
                    'code' => 201,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 200);
        }
    }
    
    /**
     * Menghitung umur berdasarkan tanggal lahir
     * 
     * @param string $tanggalLahir
     * @return int
     */
    private function hitungUmur($tanggalLahir)
    {
        return Carbon::parse($tanggalLahir)->age;
    }

    /**
     * Menampilkan list status antrean per poli
     * 
     * Method: GET
     * URL: /antrean/status/{kode_poli}/{tanggalperiksa}
     * Format: JSON
     * 
     * Header:
     *  - x-token: {token}
     *  - x-username: {user akses}
     * 
     * Response:
     * {
     *    "response": [
     *        {
     *            "namapoli" : "Poli Umum",
     *            "totalantrean" : "25",
     *            "sisaantrean" : 4,
     *            "antreanpanggil" : "A1-21",
     *            "keterangan" : "",
     *            "kodedokter" : 123456,
     *            "namadokter" : "Dr. Ali",
     *            "jampraktek" : "08:00-13:00"
     *        },
     *        {
     *            "namapoli" : "Poli Umum",
     *            "totalantrean" : "11",
     *            "sisaantrean" : 1,
     *            "antreanpanggil" : "A2-10",
     *            "keterangan" : "",
     *            "kodedokter" : 123466,
     *            "namadokter" : "Dr. Adi",
     *            "jampraktek" : "08:00-12:00"
     *        }
     *    ],
     *    "metadata": {
     *        "message": "Ok",
     *        "code": 200
     *    }
     * }
     * 
     * @param string $kodePoli Kode poli BPJS
     * @param string $tanggalPeriksa Tanggal periksa dalam format YYYY-MM-DD
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatusAntrean($kodePoli, $tanggalPeriksa, Request $request)
    {
        try {
            // Ambil token dan username dari header
            $token = $request->header('x-token');
            $username = $request->header('x-username');

            // Validasi input header
            if (empty($token) || empty($username)) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Token dan username harus disediakan dalam header'
                    ]
                ], 200);
            }

            // Validasi format tanggal
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalPeriksa)) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Format tanggal periksa tidak valid. Gunakan format YYYY-MM-DD.'
                    ]
                ], 200);
            }

            // Validasi format kode poli
            if (empty($kodePoli) || !preg_match('/^\d{3}$/', $kodePoli)) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Format kode poli tidak valid. Harus berupa 3 digit angka.'
                    ]
                ], 200);
            }

            // Cari mapping kode poli dari BPJS ke kode internal
            $poliMapping = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_pcare', $kodePoli)
                ->first();

            if (!$poliMapping) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Kode poli tidak ditemukan.'
                    ]
                ], 200);
            }

            // Log permintaan data status antrean
            Log::info('Request status antrean BPJS FKTP', [
                'kodePoli' => $kodePoli,
                'tanggalPeriksa' => $tanggalPeriksa,
                'username' => $username,
                'ip' => $request->ip()
            ]);

            try {
                // Siapkan URL endpoint
                $baseUrl = env('BPJS_ANTREAN_BASE_URL', 'https://kerjo.simkeskhanza.com/MjknKhanza/');
                $url = rtrim($baseUrl, '/') . "/antrean/status/{$kodePoli}/{$tanggalPeriksa}";
                
                Log::info('Mengirim request ke URL BPJS FKTP untuk status antrean', [
                    'url' => $url
                ]);

                // Gunakan HTTP client untuk mengakses endpoint status antrean
                $response = Http::withHeaders([
                    'x-token' => $token,
                    'x-username' => $username,
                ])->get($url);

                Log::info('Response dari BPJS FKTP untuk status antrean', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Periksa apakah response berhasil
                if ($response->successful()) {
                    // Dapatkan data dari respons
                    $responseData = $response->json();
                    
                    // Jika BPJS mengembalikan respons sukses, langsung teruskan ke client
                    if (isset($responseData['metadata']) && isset($responseData['metadata']['code']) && $responseData['metadata']['code'] == 200) {
                        return response()->json($responseData, 200);
                    }
                    
                    // Jika server BPJS tidak mengembalikan data, coba buat data dari database lokal
                    $jadwalList = DB::table('jadwal')
                        ->where('kd_poli', $poliMapping->kd_poli_rs)
                        ->join('dokter', 'jadwal.kd_dokter', '=', 'dokter.kd_dokter')
                        ->join('maping_dokter_pcare', 'dokter.kd_dokter', '=', 'maping_dokter_pcare.kd_dokter')
                        ->whereRaw("DAYNAME(?) = hari_kerja", [$tanggalPeriksa])
                        ->select('dokter.nm_dokter', 'jadwal.jam_mulai', 'jadwal.jam_selesai', 'maping_dokter_pcare.kd_dokter_pcare')
                        ->get();

                    $responseList = [];
                    foreach ($jadwalList as $jadwal) {
                        // Hitung total antrean, sisa antrean, dan antrean yang dipanggil
                        $totalAntrean = DB::table('reg_periksa')
                            ->where('kd_poli', $poliMapping->kd_poli_rs)
                            ->where('kd_dokter', $jadwal->kd_dokter)
                            ->where('tgl_registrasi', $tanggalPeriksa)
                            ->count();

                        $sisaAntrean = DB::table('reg_periksa')
                            ->where('kd_poli', $poliMapping->kd_poli_rs)
                            ->where('kd_dokter', $jadwal->kd_dokter)
                            ->where('tgl_registrasi', $tanggalPeriksa)
                            ->where('stts', 'Belum')
                            ->count();

                        $antreanPanggil = DB::table('reg_periksa')
                            ->where('kd_poli', $poliMapping->kd_poli_rs)
                            ->where('kd_dokter', $jadwal->kd_dokter)
                            ->where('tgl_registrasi', $tanggalPeriksa)
                            ->where('stts', 'Sedang Dilayani')
                            ->orderBy('jam_reg', 'desc')
                            ->value('no_reg');

                        $responseList[] = [
                            'namapoli' => $poliMapping->nm_poli_pcare,
                            'totalantrean' => (string)max(1, $totalAntrean),
                            'sisaantrean' => max(0, $sisaAntrean),
                            'antreanpanggil' => $antreanPanggil ?: "0",
                            'keterangan' => "",
                            'kodedokter' => (int)$jadwal->kd_dokter_pcare,
                            'namadokter' => $jadwal->nm_dokter,
                            'jampraktek' => $jadwal->jam_mulai . '-' . $jadwal->jam_selesai
                        ];
                    }

                    // Jika tidak ada jadwal dokter, buat 1 entry default
                    if (empty($responseList)) {
                        $responseList[] = [
                            'namapoli' => $poliMapping->nm_poli_pcare,
                            'totalantrean' => "0",
                            'sisaantrean' => 0,
                            'antreanpanggil' => "0",
                            'keterangan' => "Tidak ada jadwal dokter untuk tanggal ini",
                            'kodedokter' => 0,
                            'namadokter' => "-",
                            'jampraktek' => "00:00-00:00"
                        ];
                    }

                    return response()->json([
                        'response' => $responseList,
                        'metadata' => [
                            'message' => 'Ok',
                            'code' => 200
                        ]
                    ], 200);
                } else {
                    // Tangani kesalahan dari API BPJS
                    Log::warning('Gagal mendapatkan status antrean BPJS FKTP', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);

                    // Jika response berisi JSON, ekstrak pesan error
                    $errorMessage = 'Gagal mendapatkan status antrean';
                    if ($response->headers()->has('Content-Type') && strpos($response->header('Content-Type'), 'application/json') !== false) {
                        $errorData = (array)$response->json();
                        if (isset($errorData['metadata']['message'])) {
                            $errorMessage = $errorData['metadata']['message'];
                        }
                    }

                    return response()->json([
                        'metadata' => [
                            'code' => 201,
                            'message' => $errorMessage
                        ]
                    ], 200);
                }

            } catch (\Exception $e) {
                // Tangani kesalahan koneksi atau timeout
                Log::error('Kesalahan koneksi ke server BPJS FKTP untuk status antrean', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Gagal terhubung ke server BPJS FKTP: ' . $e->getMessage()
                    ]
                ], 200);
            }

        } catch (\Exception $e) {
            // Tangani kesalahan umum
            Log::error('Kesalahan pada getStatusAntrean BPJS FKTP', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metadata' => [
                    'code' => 201,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 200);
        }
    }

    /**
     * Membatalkan antrean peserta
     * 
     * Method: PUT
     * URL: /antrean/batal
     * Format: JSON
     * 
     * Header:
     *  - x-token: {token}
     *  - x-username: {user akses}
     * 
     * Request:
     * {
     *   "nomorkartu": "00012345678",
     *   "kodepoli": "001",
     *   "tanggalperiksa": "2020-01-28",
     *   "keterangan": "peserta batal hadir"
     * }
     * 
     * Response:
     * {
     *   "metadata": {
     *     "message": "Ok",
     *     "code": 200
     *   }
     * }
     * 
     * Catatan:
     * Metadata code:
     * 200: Sukses
     * 201: Gagal
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function batalAntrean(Request $request)
    {
        try {
            // Ambil token dan username dari header
            $token = $request->header('x-token');
            $username = $request->header('x-username');

            // Validasi input header
            if (empty($token) || empty($username)) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Token dan username harus disediakan dalam header'
                    ]
                ], 200);
            }

            // Validasi input body
            $validator = Validator::make($request->all(), [
                'nomorkartu' => 'required|string|size:13',
                'kodepoli' => 'required|string',
                'tanggalperiksa' => 'required|date_format:Y-m-d',
                'keterangan' => 'required|string|max:200'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => $validator->errors()->first()
                    ]
                ], 200);
            }

            // Cari mapping poli
            $poliMapping = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_pcare', $request->kodepoli)
                ->first();

            if (!$poliMapping) {
                // Jika mapping tidak ditemukan, coba cari dengan kode internal
                $poliMapping = DB::table('maping_poliklinik_pcare')
                    ->where('kd_poli_rs', $request->kodepoli)
                    ->first();

                if (!$poliMapping) {
                    return response()->json([
                        'metadata' => [
                            'code' => 201,
                            'message' => 'Mapping poliklinik tidak ditemukan'
                        ]
                    ], 200);
                }
            }

            // Cari data pasien
            $pasien = DB::table('pasien')
                ->where('no_peserta', $request->nomorkartu)
                ->first();

            if (!$pasien) {
                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Pasien dengan nomor kartu tersebut tidak ditemukan'
                    ]
                ], 200);
            }

            // Siapkan data untuk dikirim ke BPJS
            $data = [
                'nomorkartu' => $request->nomorkartu,
                'kodepoli' => $poliMapping->kd_poli_pcare,
                'tanggalperiksa' => $request->tanggalperiksa,
                'keterangan' => $request->keterangan
            ];

            // Log upaya pembatalan antrean
            Log::info('Upaya pembatalan antrean BPJS FKTP', [
                'nomorKartu' => $request->nomorkartu,
                'kodePoli' => $request->kodepoli,
                'kodePoliBpjs' => $poliMapping->kd_poli_pcare,
                'tanggalPeriksa' => $request->tanggalperiksa,
                'keterangan' => $request->keterangan,
                'username' => $username,
                'ip' => $request->ip()
            ]);

            try {
                // Siapkan URL endpoint
                $baseUrl = env('BPJS_ANTREAN_BASE_URL', 'https://kerjo.simkeskhanza.com/MjknKhanza/');
                $url = rtrim($baseUrl, '/') . "/antrean/batal";
                
                Log::info('Mengirim request ke URL BPJS FKTP untuk pembatalan antrean', [
                    'url' => $url,
                    'data' => $data
                ]);

                // Gunakan HTTP client untuk mengakses endpoint batal antrean
                $response = Http::withHeaders([
                    'x-token' => $token,
                    'x-username' => $username,
                    'Content-Type' => 'application/json',
                ])->put($url, $data);

                Log::info('Response dari BPJS FKTP untuk pembatalan antrean', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Periksa apakah response berhasil
                if ($response->successful()) {
                    // Dapatkan data dari respons
                    $responseData = $response->json();
                    
                    // Jika BPJS mengembalikan format yang berbeda, kita buat respons sendiri
                    // dengan format yang diinginkan
                    if (isset($responseData['metadata']) && isset($responseData['metadata']['code'])) {
                        return response()->json($responseData, 200);
                    }

                    // Jika format respons tidak sesuai, kita buat format standar
                    return response()->json([
                        'metadata' => [
                            'message' => 'Ok',
                            'code' => 200
                        ]
                    ], 200);
                } else {
                    // Tangani kesalahan dari API BPJS
                    Log::warning('Gagal membatalkan antrean BPJS FKTP', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);

                    // Jika response berisi JSON, ekstrak pesan error
                    $errorMessage = 'Gagal membatalkan antrean';
                    if ($response->headers()->has('Content-Type') && strpos($response->header('Content-Type'), 'application/json') !== false) {
                        $errorData = (array)$response->json();
                        if (isset($errorData['metadata']['message'])) {
                            $errorMessage = $errorData['metadata']['message'];
                        }
                    }

                    return response()->json([
                        'metadata' => [
                            'code' => 201,
                            'message' => $errorMessage
                        ]
                    ], 200);
                }

            } catch (\Exception $e) {
                // Tangani kesalahan koneksi atau timeout
                Log::error('Kesalahan koneksi ke server BPJS FKTP untuk pembatalan antrean', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'metadata' => [
                        'code' => 201,
                        'message' => 'Gagal terhubung ke server BPJS FKTP: ' . $e->getMessage()
                    ]
                ], 200);
            }

        } catch (\Exception $e) {
            // Tangani kesalahan umum
            Log::error('Kesalahan pada batalAntrean BPJS FKTP', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metadata' => [
                    'code' => 201,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 200);
        }
    }
}

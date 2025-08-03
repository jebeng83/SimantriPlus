<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MobileJknController extends Controller
{
    /**
     * Menampilkan halaman pendaftaran Mobile JKN
     */
    public function index()
    {
        return view('mobile-jkn.index');
    }

    /**
     * Mendapatkan data poli dari API BPJS HFIS
     */
    public function getPoli(Request $request)
    {
        try {
            $tanggal = $request->input('tanggal', date('Y-m-d'));
            
            // Gunakan WsBPJSController langsung untuk menghindari circular call
            $wsBpjsController = new \App\Http\Controllers\API\WsBPJSController();
            $response = $wsBpjsController->getReferensiPoli($tanggal);
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Error mengambil data poli HFIS: ' . $e->getMessage(), [
                'tanggal' => $tanggal ?? date('Y-m-d'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan saat mengambil data poli: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Mendapatkan data dokter berdasarkan kode poli dan tanggal dari HFIS
     */
    public function getDokter(Request $request)
    {
        try {
            $kodePoli = $request->input('kodePoli');
            $tanggal = $request->input('tanggal', date('Y-m-d'));
            
            if (!$kodePoli) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Parameter kodePoli diperlukan'
                    ]
                ], 400);
            }
            
            // Gunakan WsBPJSController langsung untuk menghindari circular call
            $wsBpjsController = new \App\Http\Controllers\API\WsBPJSController();
            $response = $wsBpjsController->getReferensiDokter($kodePoli, $tanggal);
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Error mengambil data dokter HFIS: ' . $e->getMessage(), [
                'kodePoli' => $kodePoli ?? '',
                'tanggal' => $tanggal ?? date('Y-m-d'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan saat mengambil data dokter: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Mendapatkan data peserta berdasarkan nomor kartu BPJS
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPeserta(Request $request)
    {
        $nomorKartu = $request->input('nomorKartu');
        
        if (empty($nomorKartu)) {
            return response()->json([
                'metadata' => [
                    'code' => 400,
                    'message' => 'Nomor kartu harus diisi'
                ]
            ], 400);
        }
        
        try {
            // Bersihkan nomor kartu dari karakter non-digit
            $nomorKartu = preg_replace('/[^0-9]/', '', $nomorKartu);
            
            // Pastikan panjang 13 digit dengan leading zero jika kurang
            if (strlen($nomorKartu) < 13) {
                $nomorKartu = str_pad($nomorKartu, 13, '0', STR_PAD_LEFT);
            } elseif (strlen($nomorKartu) > 13) {
                $nomorKartu = substr($nomorKartu, -13);
            }
            
            Log::info('Mencari data peserta dengan nomor kartu: ' . $nomorKartu);
            
            // Cari data pasien di database lokal berdasarkan nomor kartu BPJS
            $pasien = DB::table('pasien')
                ->where('no_peserta', $nomorKartu)
                ->first();
            
            if ($pasien) {
                // Jika pasien ditemukan di database lokal
                $dataPeserta = [
                    'noKartu' => $pasien->no_peserta,
                    'nik' => $pasien->no_ktp,
                    'nama' => $pasien->nm_pasien,
                    'jenisKelamin' => $pasien->jk,
                    'tglLahir' => date('Y-m-d', strtotime($pasien->tgl_lahir)),
                    'noHP' => $pasien->no_tlp ?: '-',
                    'alamat' => $pasien->alamat,
                    'noRm' => $pasien->no_rkm_medis,
                    'statusPeserta' => 'AKTIF',
                    'jenisPeserta' => $pasien->pekerjaan ?: 'TIDAK DIKETAHUI',
                    'faskes' => 'PKM KERJO'
                ];
                
                return response()->json([
                    'response' => $dataPeserta,
                    'metadata' => [
                        'message' => 'Ok',
                        'code' => 200
                    ]
                ]);
            } else {
                // Jika pasien tidak ditemukan di database lokal,
                // Gunakan WsFKTPController untuk mendapatkan token
                $fktpController = new \App\Http\Controllers\API\WsFKTPController();
                
                // Buat request baru untuk otentikasi
                $authRequest = new Request();
                $authRequest->headers->set('x-username', env('BPJS_ANTREAN_USERNAME'));
                $authRequest->headers->set('x-password', env('BPJS_ANTREAN_PASSWORD'));
                
                // Dapatkan token menggunakan getToken method dari WsFKTPController
                $tokenResponse = $fktpController->getToken($authRequest);
                $tokenData = json_decode($tokenResponse->getContent(), true);
                
                if (!isset($tokenData['response']['token']) || empty($tokenData['response']['token'])) {
                    Log::error('Gagal mendapatkan token BPJS');
                    return response()->json([
                        'metadata' => [
                            'code' => 500,
                            'message' => 'Gagal mendapatkan token otentikasi BPJS'
                        ]
                    ], 500);
                }
                
                $token = $tokenData['response']['token'];
                
                // Gunakan HTTP client untuk memanggil API BPJS untuk mendapatkan data peserta
                try {
                    $apiUrl = env('BPJS_PCARE_BASE_URL') . '/peserta/' . $nomorKartu;
                    $conId = env('BPJS_PCARE_CONS_ID');
                    $secretKey = env('BPJS_PCARE_CONS_PWD');
                    $userKey = env('BPJS_PCARE_USER_KEY');
                    
                    $timestamp = time();
                    $signature = base64_encode(hash_hmac('sha256', $conId . '&' . $timestamp, $secretKey, true));
                    
                    $response = Http::withHeaders([
                        'X-cons-id' => $conId,
                        'X-timestamp' => $timestamp,
                        'X-signature' => $signature,
                        'X-authorization' => 'Basic ' . base64_encode($token . ':' . env('BPJS_ANTREAN_PASSWORD')),
                        'user_key' => $userKey
                    ])->get($apiUrl);
                    
                    if ($response->successful()) {
                        $bpjsData = $response->json();
                        
                        if (isset($bpjsData['response']['peserta'])) {
                            $peserta = $bpjsData['response']['peserta'];
                            
                            return response()->json([
                                'response' => [
                                    'noKartu' => $peserta['noKartu'] ?? $nomorKartu,
                                    'nik' => $peserta['nik'] ?? '',
                                    'nama' => $peserta['nama'] ?? '',
                                    'jenisKelamin' => $peserta['sex'] ?? '',
                                    'tglLahir' => $peserta['tglLahir'] ?? '',
                                    'noHP' => $peserta['noHP'] ?? '-',
                                    'alamat' => $peserta['alamat'] ?? '',
                                    'statusPeserta' => $peserta['statusPeserta']['keterangan'] ?? 'TIDAK DIKETAHUI',
                                    'jenisPeserta' => $peserta['jenisPeserta']['keterangan'] ?? 'TIDAK DIKETAHUI',
                                    'faskes' => $peserta['provUmum']['nmProvider'] ?? 'TIDAK DIKETAHUI'
                                ],
                                'metadata' => [
                                    'message' => 'Ok',
                                    'code' => 200
                                ]
                            ]);
                        }
                    }
                    
                    // Jika gagal mendapatkan data dari BPJS
                    Log::warning('Gagal mendapatkan data peserta dari API BPJS', [
                        'response' => $response->body(),
                        'status' => $response->status()
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('Error saat menghubungi API BPJS: ' . $e->getMessage());
                }
                
                // Jika tidak berhasil mendapatkan data dari API BPJS
                return response()->json([
                    'metadata' => [
                        'code' => 404,
                        'message' => 'Data peserta tidak ditemukan'
                    ]
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error mendapatkan data peserta: ' . $e->getMessage());
            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Mendapatkan status antrean aktif pasien
     */
    public function statusAntrean(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nomorkartu' => 'required|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ]
                ], 400);
            }
            
            $nomorKartu = preg_replace('/[^0-9]/', '', $request->nomorkartu);
            
            if (strlen($nomorKartu) < 13) {
                $nomorKartu = str_pad($nomorKartu, 13, '0', STR_PAD_LEFT);
            } elseif (strlen($nomorKartu) > 13) {
                $nomorKartu = substr($nomorKartu, -13);
            }
            
            // Gunakan WsFKTPController untuk mendapatkan token
            $fktpController = new \App\Http\Controllers\API\WsFKTPController();
            
            // Buat request baru untuk otentikasi
            $authRequest = new Request();
            $authRequest->headers->set('x-username', env('BPJS_ANTREAN_USERNAME'));
            $authRequest->headers->set('x-password', env('BPJS_ANTREAN_PASSWORD'));
            
            // Dapatkan token menggunakan getToken method dari WsFKTPController
            $tokenResponse = $fktpController->getToken($authRequest);
            $tokenData = json_decode($tokenResponse->getContent(), true);
            
            if (!isset($tokenData['response']['token']) || empty($tokenData['response']['token'])) {
                Log::error('Gagal mendapatkan token BPJS');
                return response()->json([
                    'metadata' => [
                        'code' => 500,
                        'message' => 'Gagal mendapatkan token otentikasi BPJS'
                    ]
                ], 500);
            }
            
            $token = $tokenData['response']['token'];
            
            // Cari pendaftaran aktif untuk pasien ini
            $antrean = DB::table('booking_registrasi')
                ->select(
                    'booking_registrasi.*',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter',
                    'maping_poliklinik_pcare.kd_poli_pcare'
                )
                ->leftJoin('poliklinik', 'booking_registrasi.kd_poli', '=', 'poliklinik.kd_poli')
                ->leftJoin('dokter', 'booking_registrasi.kd_dokter', '=', 'dokter.kd_dokter')
                ->leftJoin('maping_poliklinik_pcare', 'booking_registrasi.kd_poli', '=', 'maping_poliklinik_pcare.kd_poli_rs')
                ->where('booking_registrasi.no_rkm_medis', function($query) use ($nomorKartu) {
                    $query->select('no_rkm_medis')
                        ->from('pasien')
                        ->where('no_peserta', $nomorKartu)
                        ->limit(1);
                })
                ->where('booking_registrasi.tanggal', '>=', date('Y-m-d'))
                ->orderBy('booking_registrasi.tanggal', 'asc')
                ->first();
            
            if (!$antrean) {
                return response()->json([
                    'metadata' => [
                        'code' => 404,
                        'message' => 'Tidak ada antrean aktif untuk pasien ini'
                    ]
                ], 404);
            }
            
            // Jika ada, gunakan getSisaAntrean dari WsFKTPController untuk mendapatkan data dari BPJS
            if ($antrean->kd_poli_pcare) {
                $kodePoli = $antrean->kd_poli_pcare;
                $tanggalPeriksa = $antrean->tanggal;
                
                $sisaAntreanRequest = new Request();
                $sisaAntreanRequest->headers->set('x-token', $token);
                $sisaAntreanRequest->headers->set('x-username', env('BPJS_ANTREAN_USERNAME'));
                
                $sisaAntreanResponse = $fktpController->getSisaAntrean(
                    $nomorKartu,
                    $kodePoli,
                    $tanggalPeriksa,
                    $sisaAntreanRequest
                );
                
                $sisaAntreanData = json_decode($sisaAntreanResponse->getContent(), true);
                
                // Jika berhasil mendapatkan data dari BPJS
                if (isset($sisaAntreanData['metadata']) && $sisaAntreanData['metadata']['code'] == 200) {
                    return $sisaAntreanResponse;
                }
            }
            
            // Jika tidak dapat mendapatkan data dari BPJS, gunakan data lokal
            return response()->json([
                'response' => [
                    'nomorantrean' => $antrean->no_reg,
                    'namapoli' => $antrean->nm_poli,
                    'sisaantrean' => 0, // Tidak dapat ditentukan tanpa data dari BPJS
                    'antreanpanggil' => '-',
                    'keterangan' => 'Data lokal, tidak terhubung dengan BPJS',
                    'namadokter' => $antrean->nm_dokter,
                    'kodedokter' => $antrean->kd_dokter,
                    'jampraktek' => $antrean->jam_praktek ?? '08:00-16:00',
                    'waktuperiksa' => date('Y-m-d H:i:s', strtotime($antrean->tanggal . ' ' . $antrean->jam_praktek ?? '08:00:00'))
                ],
                'metadata' => [
                    'message' => 'Ok',
                    'code' => 200
                ]
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Error memeriksa status antrean: ' . $e->getMessage());
            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 500);
        }
    }
    
    /**
     * Membatalkan antrean pasien
     */
    public function batalAntrean(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nomorkartu' => 'required|string',
                'tanggalperiksa' => 'required|date_format:Y-m-d',
                'kodepoli' => 'required|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ]
                ], 400);
            }
            
            $nomorKartu = preg_replace('/[^0-9]/', '', $request->nomorkartu);
            
            if (strlen($nomorKartu) < 13) {
                $nomorKartu = str_pad($nomorKartu, 13, '0', STR_PAD_LEFT);
            } elseif (strlen($nomorKartu) > 13) {
                $nomorKartu = substr($nomorKartu, -13);
            }
            
            // Cari pasien
            $pasien = DB::table('pasien')
                ->where('no_peserta', $nomorKartu)
                ->first();
            
            if (!$pasien) {
                return response()->json([
                    'metadata' => [
                        'code' => 404,
                        'message' => 'Data pasien tidak ditemukan'
                    ]
                ], 404);
            }
            
            // Cari pendaftaran untuk dibatalkan
            $antrean = DB::table('booking_registrasi')
                ->select('booking_registrasi.*', 'maping_poliklinik_pcare.kd_poli_pcare')
                ->leftJoin('maping_poliklinik_pcare', 'booking_registrasi.kd_poli', '=', 'maping_poliklinik_pcare.kd_poli_rs')
                ->where('booking_registrasi.no_rkm_medis', $pasien->no_rkm_medis)
                ->where('booking_registrasi.tanggal_periksa', $request->tanggalperiksa)
                ->where(function($query) use ($request) {
                    $query->where('booking_registrasi.kd_poli', $request->kodepoli)
                          ->orWhereExists(function($subquery) use ($request) {
                              $subquery->from('maping_poliklinik_pcare')
                                      ->whereColumn('maping_poliklinik_pcare.kd_poli_rs', 'booking_registrasi.kd_poli')
                                      ->where('maping_poliklinik_pcare.kd_poli_pcare', $request->kodepoli);
                          });
                })
                ->first();
            
            if (!$antrean) {
                return response()->json([
                    'metadata' => [
                        'code' => 404,
                        'message' => 'Data pendaftaran tidak ditemukan'
                    ]
                ], 404);
            }
            
            // Gunakan WsFKTPController untuk mendapatkan token
            $fktpController = new \App\Http\Controllers\API\WsFKTPController();
            
            // Buat request baru untuk otentikasi
            $authRequest = new Request();
            $authRequest->headers->set('x-username', env('BPJS_ANTREAN_USERNAME'));
            $authRequest->headers->set('x-password', env('BPJS_ANTREAN_PASSWORD'));
            
            // Dapatkan token menggunakan getToken method dari WsFKTPController
            $tokenResponse = $fktpController->getToken($authRequest);
            $tokenData = json_decode($tokenResponse->getContent(), true);
            
            if (!isset($tokenData['response']['token']) || empty($tokenData['response']['token'])) {
                Log::error('Gagal mendapatkan token BPJS');
                return response()->json([
                    'metadata' => [
                        'code' => 500,
                        'message' => 'Gagal mendapatkan token otentikasi BPJS'
                    ]
                ], 500);
            }
            
            $token = $tokenData['response']['token'];
            
            // Buat request ke BPJS untuk pembatalan
            $newRequest = new Request([
                'nomorkartu' => $nomorKartu,
                'kodepoli' => $antrean->kd_poli_pcare ?? $request->kodepoli,
                'tanggalperiksa' => $request->tanggalperiksa,
                'keterangan' => 'Dibatalkan oleh pasien melalui Mobile JKN'
            ]);
            
            // Set header yang diperlukan
            $newRequest->headers->set('x-token', $token);
            $newRequest->headers->set('x-username', env('BPJS_ANTREAN_USERNAME'));
            
            // Gunakan batalAntrean dari WsFKTPController
            $response = $fktpController->batalAntrean($newRequest);
            $responseData = json_decode($response->getContent(), true);
            
            // Hapus data booking jika berhasil membatalkan di BPJS atau kode error bukan karena antrean tidak ditemukan
            if (
                (isset($responseData['metadata']['code']) && $responseData['metadata']['code'] == 200) ||
                !str_contains(strtolower($responseData['metadata']['message'] ?? ''), 'tidak ditemukan')
            ) {
                DB::table('booking_registrasi')
                    ->where('no_rkm_medis', $pasien->no_rkm_medis)
                    ->where('tanggal_periksa', $request->tanggalperiksa)
                    ->where('kd_poli', $antrean->kd_poli)
                    ->delete();
                
                // Jika response bukan sukses, ubah response untuk menunjukkan sukses lokal
                if (isset($responseData['metadata']['code']) && $responseData['metadata']['code'] != 200) {
                    return response()->json([
                        'metadata' => [
                            'message' => 'Pendaftaran dibatalkan di sistem lokal. ' . ($responseData['metadata']['message'] ?? ''),
                            'code' => 200
                        ]
                    ], 200);
                }
            }
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error membatalkan antrean: ' . $e->getMessage());
            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Mendaftar antrean baru menggunakan WsFKTPController
     */
    public function daftarAntrean(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nomorkartu' => 'required|string',
                'nik' => 'required|string',
                'nohp' => 'required|string',
                'kodepoli' => 'required|string',
                'tanggalperiksa' => 'required|date_format:Y-m-d',
                'kodedokter' => 'required'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ]
                ], 400);
            }
            
            // Format nomor kartu BPJS
            $requestData = $request->all();
            $nomorKartu = preg_replace('/[^0-9]/', '', $requestData['nomorkartu']);
            $nik = preg_replace('/[^0-9]/', '', $requestData['nik']);
            
            // Pastikan panjang 13 digit dengan leading zero jika kurang
            if (strlen($nomorKartu) < 13) {
                $nomorKartu = str_pad($nomorKartu, 13, '0', STR_PAD_LEFT);
            } elseif (strlen($nomorKartu) > 13) {
                $nomorKartu = substr($nomorKartu, -13);
            }
            
            // Verifikasi pasien ada di database dan ambil nomor BPJS yang valid
            $pasien = DB::table('pasien')
                ->where(function($query) use ($nomorKartu, $nik) {
                    $query->where('no_peserta', $nomorKartu)
                          ->orWhere('no_ktp', $nik);
                })
                ->first();
            
            if (!$pasien) {
                return response()->json([
                    'metadata' => [
                        'code' => 404,
                        'message' => 'Data pasien tidak ditemukan dalam database'
                    ]
                ], 404);
            }
            
            // Gunakan nomor BPJS yang benar dari database
            $nomorPeserta = $pasien->no_peserta;
            
            if (empty($nomorPeserta)) {
                return response()->json([
                    'metadata' => [
                        'code' => 404,
                        'message' => 'Nomor BPJS pasien tidak ditemukan dalam database'
                    ]
                ], 404);
            }
            
            // Update request dengan data pasien yang valid
            $requestData['nomorkartu'] = $nomorPeserta;
            $requestData['nik'] = $pasien->no_ktp;
            $requestData['norm'] = $pasien->no_rkm_medis;
            
            // Log informasi pendaftaran
            Log::info('Mendaftarkan antrean dengan data pasien dari database', [
                'nomor_kartu' => $nomorPeserta,
                'nik' => $pasien->no_ktp,
                'no_rm' => $pasien->no_rkm_medis
            ]);
            
            // Update nomor telepon pasien jika berbeda
            if ($pasien->no_tlp != $requestData['nohp']) {
                DB::table('pasien')
                    ->where('no_rkm_medis', $pasien->no_rkm_medis)
                    ->update(['no_tlp' => $requestData['nohp']]);
            }
            
            // Gunakan WsFKTPController untuk mendapatkan token
            $fktpController = new \App\Http\Controllers\API\WsFKTPController();
            
            // Buat request baru untuk otentikasi
            $authRequest = new Request();
            $authRequest->headers->set('x-username', env('BPJS_ANTREAN_USERNAME'));
            $authRequest->headers->set('x-password', env('BPJS_ANTREAN_PASSWORD'));
            
            // Dapatkan token menggunakan getToken method dari WsFKTPController
            $tokenResponse = $fktpController->getToken($authRequest);
            $tokenData = json_decode($tokenResponse->getContent(), true);
            
            if (!isset($tokenData['response']['token']) || empty($tokenData['response']['token'])) {
                Log::error('Gagal mendapatkan token BPJS');
                return response()->json([
                    'metadata' => [
                        'code' => 500,
                        'message' => 'Gagal mendapatkan token otentikasi BPJS'
                    ]
                ], 500);
            }
            
            $token = $tokenData['response']['token'];
            
            // Buat request baru dengan data yang sudah diupdate
            $newRequest = new Request($requestData);
            
            // Tambahkan header yang diperlukan ke request
            $newRequest->headers->set('x-token', $token);
            $newRequest->headers->set('x-username', env('BPJS_ANTREAN_USERNAME'));
            
            // Pastikan beberapa data wajib sudah ada dalam format yang benar
            if (!isset($requestData['keluhan']) || empty($requestData['keluhan'])) {
                $newRequest->merge(['keluhan' => 'Belum ada keluhan']);
            }
            
            if (!isset($requestData['jampraktek']) || empty($requestData['jampraktek'])) {
                $newRequest->merge(['jampraktek' => '08:00-12:00']);
            }
            
            // Gunakan WsFKTPController langsung untuk ambil antrean
            $response = $fktpController->ambilAntrean($newRequest);
            
            // Jika response adalah objek response, return langsung
            if (is_object($response) && method_exists($response, 'getContent')) {
                return $response;
            }
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Error mendaftar antrean: ' . $e->getMessage());
            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan saat mendaftar antrean: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Mendapatkan sisa antrean berdasarkan nomor kartu, kode poli, dan tanggal periksa
     */
    public function getSisaAntrean(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nomorKartu' => 'required|string',
                'kodePoli' => 'required|string',
                'tanggalPeriksa' => 'required|date_format:Y-m-d'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ]
                ], 400);
            }
            
            $nomorKartu = $request->input('nomorKartu');
            $kodePoli = $request->input('kodePoli');
            $tanggalPeriksa = $request->input('tanggalPeriksa');
            
            // Format nomor kartu BPJS
            $nomorKartu = preg_replace('/[^0-9]/', '', $nomorKartu);
            
            // Pastikan panjang 13 digit dengan leading zero jika kurang
            if (strlen($nomorKartu) < 13) {
                $nomorKartu = str_pad($nomorKartu, 13, '0', STR_PAD_LEFT);
            } elseif (strlen($nomorKartu) > 13) {
                $nomorKartu = substr($nomorKartu, -13);
            }
            
            // Log nomor kartu yang akan digunakan
            Log::info('Mendapatkan sisa antrean dengan nomor kartu: ' . $nomorKartu . ', kode poli: ' . $kodePoli . ', tanggal: ' . $tanggalPeriksa);
            
            // Verifikasi pasien ada di database
            $pasien = DB::table('pasien')
                ->where('no_peserta', $nomorKartu)
                ->orWhere('no_ktp', $nomorKartu)
                ->first();
            
            if (!$pasien) {
                return response()->json([
                    'metadata' => [
                        'code' => 404,
                        'message' => 'Data peserta tidak ditemukan dalam database'
                    ]
                ], 404);
            }
            
            // Gunakan WsFKTPController untuk mendapatkan sisa antrean
            // Pastikan nomor kartu yang dikirim adalah no_peserta yang valid
            $nomorPeserta = $pasien->no_peserta;
            
            if (empty($nomorPeserta)) {
                return response()->json([
                    'metadata' => [
                        'code' => 404,
                        'message' => 'Nomor BPJS pasien tidak ditemukan dalam database'
                    ]
                ], 404);
            }
            
            // Ambil token dan username dari konfigurasi
            $token = env('BPJS_API_TOKEN', ''); 
            $username = env('BPJS_API_USERNAME', '');
            
            if (empty($token) || empty($username)) {
                Log::error('Token atau username BPJS tidak dikonfigurasi');
                return response()->json([
                    'metadata' => [
                        'code' => 500,
                        'message' => 'Konfigurasi API BPJS tidak lengkap'
                    ]
                ], 500);
            }
            
            // Tambahkan header yang diperlukan ke request
            $request->headers->set('x-token', $token);
            $request->headers->set('x-username', $username);
            
            // Gunakan WsFKTPController untuk mendapatkan sisa antrean
            $fktpController = new \App\Http\Controllers\API\WsFKTPController();
            $response = $fktpController->getSisaAntrean($nomorPeserta, $kodePoli, $tanggalPeriksa, $request);
            
            // Jika response adalah objek response, ambil jsonnya
            if (is_object($response) && method_exists($response, 'getContent')) {
                return $response;
            }
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Error mendapatkan sisa antrean: ' . $e->getMessage());
            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan saat mendapatkan sisa antrean: ' . $e->getMessage()
                ]
            ], 500);
        }
    }
    
    /**
     * Menampilkan halaman referensi poli HFIS BPJS
     */
    public function refrensiPoliHfis()
    {
        return view('mobile-jkn.refrensi-poli-hfis');
    }
    
    /**
     * Menampilkan halaman referensi dokter HFIS BPJS
     */
    public function refrensiDokterHfis()
    {
        return view('mobile-jkn.refrensi-dokter-hfis');
    }
}

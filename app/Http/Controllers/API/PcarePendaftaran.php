<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Traits\PcareTrait;

class PcarePendaftaran extends Controller
{
    use PcareTrait;

    /**
     * Get pendaftaran berdasarkan nomor urut dan tanggal daftar
     * Format endpoint: {Base URL}/{Service Name}/pendaftaran/noUrut/{Parameter 1}/tglDaftar/{Parameter 2}
     * 
     * @param string $noUrut Nomor Urut Pendaftaran
     * @param string $tglDaftar Tanggal Pendaftaran (format: DD-MM-YYYY)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendaftaranByNoUrut($noUrut, $tglDaftar)
    {
        try {
            // Validasi parameter
            if (empty($noUrut)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Nomor urut pendaftaran tidak boleh kosong'
                    ],
                    'response' => null
                ], 400);
            }

            if (empty($tglDaftar) || !preg_match('/^\d{2}-\d{2}-\d{4}$/', $tglDaftar)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Format tanggal pendaftaran tidak valid, gunakan format DD-MM-YYYY'
                    ],
                    'response' => null
                ], 400);
            }

            // Log request
            Log::info('PCare Get Pendaftaran By Nomor Urut Request', [
                'noUrut' => $noUrut,
                'tglDaftar' => $tglDaftar
            ]);

            // Format endpoint
            $endpoint = "pendaftaran/noUrut/{$noUrut}/tglDaftar/{$tglDaftar}";

            // Kirim request ke PCare
            $response = $this->requestPcare($endpoint);

            // Log response
            Log::info('PCare Get Pendaftaran By Nomor Urut Response', [
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown',
                'message' => isset($response['metaData']) ? $response['metaData']['message'] : 'unknown',
                'data' => $response['response'] ?? null
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Pendaftaran By Nomor Urut Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => $this->getErrorMessage($e)
                ],
                'response' => null
            ], 500);
        }
    }
    
    /**
     * Get data pendaftaran provider berdasarkan tanggal, start, dan limit
     * Format endpoint: {Base URL}/{Service Name}/pendaftaran/tglDaftar/{Parameter 1}/{Parameter 2}/{Parameter 3}
     * 
     * @param string $tglDaftar Tanggal Pendaftaran (format: DD-MM-YYYY)
     * @param int $start Row data awal yang ditampilkan (default 0)
     * @param int $limit Limit jumlah data yang ditampilkan (default 10)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendaftaranByProvider($tglDaftar, $start = 0, $limit = 10)
    {
        try {
            // Validasi parameter
            if (empty($tglDaftar) || !preg_match('/^\d{2}-\d{2}-\d{4}$/', $tglDaftar)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Format tanggal pendaftaran tidak valid, gunakan format DD-MM-YYYY'
                    ],
                    'response' => null
                ], 400);
            }
            
            // Pastikan start dan limit adalah angka
            if (!is_numeric($start) || $start < 0) {
                $start = 0;
            }
            
            if (!is_numeric($limit) || $limit < 1) {
                $limit = 10;
            }

            // Log request
            Log::info('PCare Get Pendaftaran By Provider Request', [
                'tglDaftar' => $tglDaftar,
                'start' => $start,
                'limit' => $limit
            ]);

            // Format endpoint
            $endpoint = "pendaftaran/tglDaftar/{$tglDaftar}/{$start}/{$limit}";

            // Kirim request ke PCare
            $response = $this->requestPcare($endpoint);

            // Log response
            Log::info('PCare Get Pendaftaran By Provider Response', [
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown',
                'message' => isset($response['metaData']) ? $response['metaData']['message'] : 'unknown',
                'count' => isset($response['response']['count']) ? $response['response']['count'] : 0
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Pendaftaran By Provider Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => $this->getErrorMessage($e)
                ],
                'response' => null
            ], 500);
        }
    }

    /**
     * Mendapatkan kdProviderPeserta dari environment variable
     * 
     * @param string $noKartu
     * @return string|null
     */
    private function getKdProviderPeserta($noKartu)
    {
        try {
            // Ambil kdProviderPeserta dari environment variable BPJS_PCARE_KODE_PPK
            $kdProviderPeserta = env('BPJS_PCARE_KODE_PPK', '11251919');
            
            Log::info('Getting kdProviderPeserta from environment', [
                'noKartu' => $noKartu,
                'kdProviderPeserta' => $kdProviderPeserta
            ]);
            
            if (empty($kdProviderPeserta)) {
                Log::error('BPJS_PCARE_KODE_PPK environment variable is not set');
                return null;
            }
            
            return $kdProviderPeserta;
            
        } catch (\Exception $e) {
            Log::error('Error getting kdProviderPeserta from environment', [
                'noKartu' => $noKartu,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Menambahkan data pendaftaran ke PCare
     * Format endpoint: {Base URL}/{Service Name}/pendaftaran
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPendaftaran(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'tglDaftar' => 'required|string|regex:/^\d{2}-\d{2}-\d{4}$/',
                'noKartu' => 'required|string',
                'kdPoli' => 'required|string',
                'kunjSakit' => 'required|boolean',
                'sistole' => 'integer',
                'diastole' => 'integer',
                'beratBadan' => 'integer',
                'tinggiBadan' => 'integer',
                'respRate' => 'integer',
                'lingkar_perut' => 'integer',
                'heartRate' => 'integer',
                'rujukBalik' => 'integer',
                'kdTkp' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ],
                    'response' => null
                ], 400);
            }

            // Dapatkan kdProviderPeserta yang benar dari API PCare
            $kdProviderPeserta = $this->getKdProviderPeserta($request->noKartu);
            
            if (!$kdProviderPeserta) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Tidak dapat mendapatkan kdProviderPeserta untuk nomor kartu: ' . $request->noKartu
                    ],
                    'response' => null
                ], 400);
            }

            // Data untuk dikirim ke PCare
            $data = [
                'kdProviderPeserta' => $kdProviderPeserta, // Gunakan kdProvider yang benar
                'tglDaftar' => $request->tglDaftar,
                'noKartu' => $request->noKartu,
                'kdPoli' => $request->kdPoli,
                'keluhan' => $request->keluhan ?: null,
                'kunjSakit' => (bool) $request->kunjSakit,
                'sistole' => (int) $request->sistole,
                'diastole' => (int) $request->diastole,
                'beratBadan' => (int) $request->beratBadan,
                'tinggiBadan' => (int) $request->tinggiBadan,
                'respRate' => (int) $request->respRate,
                'lingkarPerut' => $request->lingkar_perut ? (int) $request->lingkar_perut : 0,
                'heartRate' => (int) $request->heartRate,
                'rujukBalik' => (int) $request->rujukBalik,
                'kdTkp' => $request->kdTkp
            ];

            // Log request dengan detail lengkap
            Log::info('PCare Add Pendaftaran Request', [
                'endpoint' => 'pendaftaran',
                'method' => 'POST',
                'data' => $data,
                'timestamp' => now()->toISOString(),
                'user_agent' => request()->header('User-Agent'),
                'ip_address' => request()->ip()
            ]);

            // Validasi data sebelum kirim
            $this->validatePcareData($data);

            // Format endpoint dan kirim request dengan Content-Type text/plain
            $endpoint = "pendaftaran";
            $response = $this->requestPcare($endpoint, 'POST', $data, 'text/plain');

            // Log response dengan detail lengkap
            Log::info('PCare Add Pendaftaran Response', [
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown',
                'message' => isset($response['metaData']) ? $response['metaData']['message'] : 'unknown',
                'noUrut' => isset($response['response']['message']) ? $response['response']['message'] : null,
                'full_response' => $response,
                'response_size' => strlen(json_encode($response)),
                'timestamp' => now()->toISOString()
            ]);

            // Jika berhasil, simpan ke database
            if (isset($response['metaData']['code']) && $response['metaData']['code'] == 201) {
                try {
                    // Mendapatkan noUrut dari response
                    $noUrut = $response['response']['message'] ?? '';
                    
                    // Mendapatkan data dari request untuk disimpan
                    $no_rawat = $request->no_rawat;
                    $no_rkm_medis = $request->no_rkm_medis;
                    $nm_pasien = $request->nm_pasien;
                    $nmPoli = $request->nmPoli;
                    
                    // Cari no_rawat yang valid dari tabel reg_periksa
                    $rawatTerbaru = DB::table('reg_periksa')
                        ->where('no_rkm_medis', $no_rkm_medis)
                        ->orderBy('tgl_registrasi', 'desc')
                        ->orderBy('jam_reg', 'desc')
                        ->first();
                    
                    if ($rawatTerbaru) {
                        $no_rawat = $rawatTerbaru->no_rawat;
                        Log::info('Menggunakan no_rawat dari reg_periksa', [
                            'no_rawat_original' => $request->no_rawat,
                            'no_rawat_valid' => $no_rawat
                        ]);
                    }
                    
                    // Konversi nilai kunjSakit
                    $kunjSakit = $request->kunjSakit ? 'Kunjungan Sakit' : 'Kunjungan Sehat';
                    
                    // Konversi nilai kdTkp
                    $kdTkpLabel = '';
                    switch ($request->kdTkp) {
                        case '10':
                            $kdTkpLabel = '10 Rawat Jalan';
                            break;
                        case '20':
                            $kdTkpLabel = '20 Rawat Inap';
                            break;
                        case '50':
                            $kdTkpLabel = '50 Promotif Preventif';
                            break;
                        default:
                            $kdTkpLabel = '10 Rawat Jalan';
                    }
                    
                    // Format tanggal untuk database (YYYY-MM-DD)
                    $tglDaftarParts = explode('-', $request->tglDaftar);
                    $tglDaftarDB = $tglDaftarParts[2] . '-' . $tglDaftarParts[1] . '-' . $tglDaftarParts[0];
                    
                    // Simpan ke database
                    DB::table('pcare_pendaftaran')->insert([
                        'no_rawat' => $no_rawat,
                        'tglDaftar' => $tglDaftarDB,
                        'no_rkm_medis' => $no_rkm_medis,
                        'nm_pasien' => $nm_pasien,
                        'kdProviderPeserta' => $request->kdProviderPeserta,
                        'noKartu' => $request->noKartu,
                        'kdPoli' => $request->kdPoli,
                        'nmPoli' => $nmPoli,
                        'keluhan' => $request->keluhan ?? '',
                        'kunjSakit' => $kunjSakit,
                        'sistole' => $request->sistole ?? '0',
                        'diastole' => $request->diastole ?? '0',
                        'beratBadan' => $request->beratBadan ?? '0',
                        'tinggiBadan' => $request->tinggiBadan ?? '0',
                        'respRate' => $request->respRate ?? '0',
                        'lingkar_perut' => $request->lingkar_perut ?? '0',
                        'heartRate' => $request->heartRate ?? '0',
                        'rujukBalik' => $request->rujukBalik ?? '0',
                        'kdTkp' => $kdTkpLabel,
                        'noUrut' => $noUrut,
                        'status' => 'Terkirim'
                    ]);
                    
                    Log::info('PCare Add Pendaftaran Saved to Database', [
                        'no_rawat' => $no_rawat,
                        'noUrut' => $noUrut
                    ]);
                } catch (\Exception $dbError) {
                    Log::error('PCare Add Pendaftaran Database Error', [
                        'message' => $dbError->getMessage(),
                        'trace' => $dbError->getTraceAsString()
                    ]);
                    
                    // Tetap kembalikan response dari BPJS meskipun penyimpanan ke DB gagal
                    // Berikan tambahan warning
                    $response['metaData']['warning'] = 'Pendaftaran berhasil di PCare tetapi gagal disimpan ke database: ' . $dbError->getMessage();
                }
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Add Pendaftaran Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => $this->getErrorMessage($e)
                ],
                'response' => null
            ], 500);
        }
    }
    
    /**
     * Validasi data sebelum dikirim ke PCare
     * @param array $data
     * @throws \Exception
     */
    private function validatePcareData($data)
    {
        // Validasi format tanggal
        if (!preg_match('/^\d{2}-\d{2}-\d{4}$/', $data['tglDaftar'])) {
            throw new \Exception('Format tanggal tidak valid: ' . $data['tglDaftar']);
        }
        
        // Validasi nomor kartu BPJS
        if (strlen($data['noKartu']) < 13) {
            throw new \Exception('Nomor kartu BPJS tidak valid: ' . $data['noKartu']);
        }
        
        // Validasi kode poli
        if (empty($data['kdPoli'])) {
            throw new \Exception('Kode poli tidak boleh kosong');
        }
        
        // Validasi vital signs
        if ($data['sistole'] < 50 || $data['sistole'] > 300) {
            Log::warning('Sistole di luar range normal', ['sistole' => $data['sistole']]);
        }
        
        if ($data['diastole'] < 30 || $data['diastole'] > 200) {
            Log::warning('Diastole di luar range normal', ['diastole' => $data['diastole']]);
        }
        
        Log::info('Validasi data PCare berhasil', ['data_keys' => array_keys($data)]);
    }

    /**
     * Menghapus data pendaftaran di PCare
     * Format endpoint: {Base URL}/{Service Name}/pendaftaran/peserta/{Parameter 1}/tglDaftar/{Parameter 2}/noUrut/{Parameter 3}/kdPoli/{Parameter 4}
     * 
     * @param string $noKartu Nomor Kartu Peserta
     * @param string $tglDaftar Tanggal Pendaftaran (format: DD-MM-YYYY)
     * @param string $noUrut Nomor Urut Pendaftaran
     * @param string $kdPoli Kode Poli
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePendaftaran($noKartu, $tglDaftar, $noUrut, $kdPoli)
    {
        try {
            // Validasi parameter
            if (empty($noKartu)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Nomor kartu peserta tidak boleh kosong'
                    ],
                    'response' => null
                ], 400);
            }

            if (empty($tglDaftar) || !preg_match('/^\d{2}-\d{2}-\d{4}$/', $tglDaftar)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Format tanggal pendaftaran tidak valid, gunakan format DD-MM-YYYY'
                    ],
                    'response' => null
                ], 400);
            }

            if (empty($noUrut)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Nomor urut pendaftaran tidak boleh kosong'
                    ],
                    'response' => null
                ], 400);
            }

            if (empty($kdPoli)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Kode poli tidak boleh kosong'
                    ],
                    'response' => null
                ], 400);
            }

            // Log request
            Log::info('PCare Delete Pendaftaran Request', [
                'noKartu' => $noKartu,
                'tglDaftar' => $tglDaftar,
                'noUrut' => $noUrut,
                'kdPoli' => $kdPoli
            ]);

            // Format endpoint
            $endpoint = "pendaftaran/peserta/{$noKartu}/tglDaftar/{$tglDaftar}/noUrut/{$noUrut}/kdPoli/{$kdPoli}";

            // Kirim request DELETE ke PCare
            $response = $this->requestPcare($endpoint, 'DELETE');

            // Log response
            Log::info('PCare Delete Pendaftaran Response', [
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown',
                'message' => isset($response['metaData']) ? $response['metaData']['message'] : 'unknown'
            ]);

            // Jika berhasil, update status di database
            if (isset($response['metaData']['code']) && $response['metaData']['code'] == 200) {
                try {
                    // Format tanggal untuk query database (YYYY-MM-DD)
                    $tglDaftarParts = explode('-', $tglDaftar);
                    $tglDaftarDB = $tglDaftarParts[2] . '-' . $tglDaftarParts[1] . '-' . $tglDaftarParts[0];
                    
                    // Update status pendaftaran di database
                    $updated = DB::table('pcare_pendaftaran')
                        ->where('noKartu', $noKartu)
                        ->where('tglDaftar', $tglDaftarDB)
                        ->where('noUrut', $noUrut)
                        ->where('kdPoli', $kdPoli)
                        ->update(['status' => 'Dihapus']);
                    
                    Log::info('PCare Delete Pendaftaran Database Update', [
                        'noKartu' => $noKartu,
                        'tglDaftar' => $tglDaftarDB,
                        'noUrut' => $noUrut,
                        'kdPoli' => $kdPoli,
                        'updated' => $updated
                    ]);
                } catch (\Exception $dbError) {
                    Log::error('PCare Delete Pendaftaran Database Error', [
                        'message' => $dbError->getMessage(),
                        'trace' => $dbError->getTraceAsString()
                    ]);
                    
                    // Tambahkan warning ke response
                    $response['metaData']['warning'] = 'Pendaftaran berhasil dihapus di PCare tetapi gagal mengupdate status di database: ' . $dbError->getMessage();
                }
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Delete Pendaftaran Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => $this->getErrorMessage($e)
                ],
                'response' => null
            ], 500);
        }
    }
}

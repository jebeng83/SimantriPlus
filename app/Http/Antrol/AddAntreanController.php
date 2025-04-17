<?php

namespace App\Http\Antrol;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\BpjsTraits;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AddAntreanController extends Controller
{
    use BpjsTraits;

    public function add(Request $request)
    {
        try {
            // Log request
            Log::info('Permintaan tambah antrean', [
                'request' => $request->all()
            ]);

            // Validasi input
            $validator = Validator::make($request->all(), [
                'nomorkartu' => 'nullable|string|max:13',
                'nik' => 'required|string|max:16',
                'nohp' => 'required|string|max:15',
                'kodepoli' => 'required|string|max:10',
                'namapoli' => 'required|string|max:50',
                'norm' => 'required|string|max:15',
                'tanggalperiksa' => 'required|date_format:Y-m-d',
                'kodedokter' => 'required|numeric',
                'namadokter' => 'required|string|max:50',
                'jampraktek' => 'required|string|max:11',
                'nomorantrean' => 'required|string|max:5',
                'angkaantrean' => 'required|numeric',
                'keterangan' => 'nullable|string|max:200'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ]
                ], 400);
            }

            // Validasi bahwa data pasien sudah terdaftar
            $pasien = DB::table('pasien')
                ->leftJoin('penjab', 'pasien.kd_pj', '=', 'penjab.kd_pj')
                ->where('no_rkm_medis', $request->norm)
                ->select('pasien.*', 'penjab.kd_pj', 'penjab.png_jawab')
                ->first();

            if (!$pasien) {
                return response()->json([
                    'metadata' => [
                        'code' => 404,
                        'message' => 'Data pasien tidak ditemukan'
                    ]
                ], 404);
            }

            // Verifikasi bahwa pasien menggunakan BPJS
            $bpjsCodes = ['A14', 'A15', 'BPJ']; // A14 = PBI, A15 = NON PBI, BPJ = BPJS
            $isBpjs = in_array($pasien->kd_pj, $bpjsCodes) || 
                     (isset($pasien->png_jawab) && preg_match('/bpjs/i', $pasien->png_jawab));
            
            if (!$isBpjs) {
                Log::warning('Pasien bukan pengguna BPJS', [
                    'norm' => $request->norm,
                    'kd_pj' => $pasien->kd_pj,
                    'png_jawab' => $pasien->png_jawab ?? 'undefined'
                ]);
                
                // Tetap lanjutkan proses untuk fleksibilitas
                Log::info('Melanjutkan proses meskipun bukan pasien BPJS');
            } else {
                Log::info('Pasien teridentifikasi sebagai pengguna BPJS', [
                    'kd_pj' => $pasien->kd_pj,
                    'jenis' => in_array($pasien->kd_pj, ['A14']) ? 'PBI' : 
                              (in_array($pasien->kd_pj, ['A15']) ? 'NON PBI' : 'BPJS')
                ]);
            }

            // Validasi poliklinik
            $poliklinik = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_pcare', $request->kodepoli)
                ->orWhere('nm_poli_pcare', $request->namapoli)
                ->first();

            if (!$poliklinik) {
                // Coba cari dari data poliklinik lokal
                $poli = DB::table('poliklinik')
                    ->where('kd_poli', $request->kodepoli)
                    ->orWhere('nm_poli', $request->namapoli)
                    ->first();
                    
                if ($poli) {
                    // Buat mapping sementara
                    $poliklinik = (object)[
                        'kd_poli_rs' => $poli->kd_poli,
                        'kd_poli_pcare' => $request->kodepoli,
                        'nm_poli_pcare' => $request->namapoli
                    ];
                    
                    Log::info('Menggunakan mapping poliklinik sementara', [
                        'kd_poli_rs' => $poli->kd_poli,
                        'kd_poli_pcare' => $request->kodepoli,
                        'nm_poli_pcare' => $request->namapoli
                    ]);
                } else {
                    return response()->json([
                        'metadata' => [
                            'code' => 404,
                            'message' => 'Data poliklinik tidak ditemukan'
                        ]
                    ], 404);
                }
            }

            // Validasi dokter
            $dokter = DB::table('maping_dokter_pcare')
                ->where('kd_dokter_pcare', $request->kodedokter)
                ->orWhere('nm_dokter_pcare', $request->namadokter)
                ->first();

            if (!$dokter) {
                // Coba cari dari data dokter lokal
                $drLokal = DB::table('dokter')
                    ->where('nm_dokter', $request->namadokter)
                    ->first();
                    
                if ($drLokal) {
                    // Buat mapping sementara
                    $dokter = (object)[
                        'kd_dokter' => $drLokal->kd_dokter,
                        'kd_dokter_pcare' => $request->kodedokter,
                        'nm_dokter_pcare' => $request->namadokter
                    ];
                    
                    Log::info('Menggunakan mapping dokter sementara', [
                        'kd_dokter' => $drLokal->kd_dokter,
                        'kd_dokter_pcare' => $request->kodedokter,
                        'nm_dokter_pcare' => $request->namadokter
                    ]);
                } else {
                    return response()->json([
                        'metadata' => [
                            'code' => 404,
                            'message' => 'Data dokter tidak ditemukan'
                        ]
                    ], 404);
                }
            }

            // Kirim data ke BPJS Mobile JKN
            $endpoint = "/antrean/add";
            $apiUrl = env('MOBILEJKN_API_URL');
            
            if (!$apiUrl) {
                Log::error('Konfigurasi MOBILEJKN_API_URL tidak ditemukan');
                return response()->json([
                    'metadata' => [
                        'code' => 500,
                        'message' => 'Konfigurasi API URL tidak ditemukan'
                    ]
                ], 500);
            }

            // Siapkan data yang akan dikirim ke BPJS
            $dataAntrean = [
                "nomorkartu" => $request->nomorkartu ?? "",
                "nik" => $request->nik,
                "nohp" => $request->nohp,
                "kodepoli" => $request->kodepoli,
                "namapoli" => $request->namapoli,
                "norm" => $request->norm,
                "tanggalperiksa" => $request->tanggalperiksa,
                "kodedokter" => $request->kodedokter,
                "namadokter" => $request->namadokter,
                "jampraktek" => $request->jampraktek,
                "nomorantrean" => $request->nomorantrean,
                "angkaantrean" => $request->angkaantrean,
                "keterangan" => $request->keterangan ?? "Peserta harap 30 menit lebih awal guna pencatatan administrasi."
            ];

            // Kirim data ke API BPJS
            $response = $this->requestPostBpjs($endpoint, $dataAntrean, 'mobilejkn');
            $responseData = $response instanceof \Illuminate\Http\JsonResponse ? 
                            $response->getData(true) : json_decode($response, true);
            
            // Log response dari BPJS
            Log::info('Respons dari BPJS Mobile JKN', [
                'data' => $responseData
            ]);
            
            // Pastikan format metadata sesuai standar BPJS
            $responseMetadata = isset($responseData['metadata']) ? $responseData['metadata'] : 
                               (isset($responseData['metaData']) ? $responseData['metaData'] : 
                               ['code' => 200, 'message' => 'Ok']);
            
            // Standardisasi format respons
            $standardResponse = [
                'metadata' => [
                    'code' => $responseMetadata['code'] ?? 200,
                    'message' => $responseMetadata['message'] ?? 'Ok'
                ]
            ];
            
            // Jika respons berhasil (code 200), tambahkan data ke reg_periksa jika belum ada
            $isSuccess = isset($standardResponse['metadata']['code']) && 
                        $standardResponse['metadata']['code'] == 200;
            
            // Cek jika sudah terdaftar di reg_periksa
            $cekRegPeriksa = DB::table('reg_periksa')
                ->where('no_rkm_medis', $request->norm)
                ->where('kd_poli', $poliklinik->kd_poli_rs)
                ->where('kd_dokter', $dokter->kd_dokter)
                ->where('tgl_registrasi', $request->tanggalperiksa)
                ->first();

            // Jika belum terdaftar di reg_periksa dan respons berhasil, tambahkan
            if (!$cekRegPeriksa && $isSuccess && env('MOBILEJKN_ADD_ANTRIAN') === 'yes') {
                try {
                    // Cek dulu apakah ada pendaftaran untuk pasien ini hari ini di semua poli
                    $cekPendaftaranHariIni = DB::table('reg_periksa')
                        ->where('no_rkm_medis', $request->norm)
                        ->where('tgl_registrasi', $request->tanggalperiksa)
                        ->orderBy('jam_reg', 'desc')
                        ->first();
                        
                    if ($cekPendaftaranHariIni) {
                        // Jika sudah ada pendaftaran hari ini, gunakan data yang sudah ada
                        Log::info('Pasien sudah terdaftar hari ini di poli lain. Menggunakan data yang ada.', [
                            'no_rawat_existing' => $cekPendaftaranHariIni->no_rawat,
                            'poli_existing' => $cekPendaftaranHariIni->kd_poli,
                            'no_rkm_medis' => $request->norm
                        ]);
                        
                        $noRawat = $cekPendaftaranHariIni->no_rawat;
                        $standardResponse['reg_periksa'] = [
                            'no_rawat' => $noRawat,
                            'no_reg' => $cekPendaftaranHariIni->no_reg,
                            'keterangan' => 'Menggunakan pendaftaran yang sudah ada'
                        ];
                        
                        // Log informasi ke antrean_bpjs_log
                        if (Schema::hasTable('antrean_bpjs_log')) {
                            DB::table('antrean_bpjs_log')->insert([
                                'no_rawat' => $noRawat,
                                'no_rkm_medis' => $request->norm,
                                'status' => 'Info',
                                'response' => json_encode([
                                    'message' => 'Menggunakan pendaftaran yang sudah ada',
                                    'no_rawat' => $noRawat
                                ]),
                                'created_at' => now()
                            ]);
                        }
                    } else {
                        // Generate no_rawat otomatis dengan format YYYY/MM/DD/nnnnnn
                        $tanggal = date('Y/m/d', strtotime($request->tanggalperiksa));
                        $lastNoRawat = DB::table('reg_periksa')
                            ->where('no_rawat', 'like', $tanggal . '/%')
                            ->orderBy('no_rawat', 'desc')
                            ->value('no_rawat');
                        
                        $urutan = 1;
                        if ($lastNoRawat) {
                            $nomorUrut = (int) substr($lastNoRawat, -6);
                            $urutan = $nomorUrut + 1;
                        }
                        $noRawat = $tanggal . '/' . str_pad($urutan, 6, '0', STR_PAD_LEFT);
                        
                        // Insert ke reg_periksa
                        DB::table('reg_periksa')->insert([
                            'no_reg' => $request->nomorantrean,
                            'no_rawat' => $noRawat,
                            'tgl_registrasi' => $request->tanggalperiksa,
                            'jam_reg' => date('H:i:s'),
                            'kd_dokter' => $dokter->kd_dokter,
                            'no_rkm_medis' => $request->norm,
                            'kd_poli' => $poliklinik->kd_poli_rs,
                            'p_jawab' => $pasien->namakeluarga ?? 'PASIEN',
                            'almt_pj' => $pasien->alamat ?? '-',
                            'hubunganpj' => $pasien->keluarga ?? 'DIRI SENDIRI',
                            'biaya_reg' => 0,
                            'stts' => 'Belum',
                            'stts_daftar' => 'Mobile JKN',
                            'status_lanjut' => 'Ralan',
                            'kd_pj' => $pasien->kd_pj,
                            'umurdaftar' => $this->hitungUmur($pasien->tgl_lahir),
                            'sttsumur' => $this->kategoriUmur($pasien->tgl_lahir),
                            'status_bayar' => 'Belum Bayar',
                            'status_poli' => 'Lama'
                        ]);
                        
                        Log::info('Berhasil mendaftarkan pasien ke reg_periksa', [
                            'no_rawat' => $noRawat,
                            'no_rkm_medis' => $request->norm
                        ]);
                        
                        // Tambahkan info reg_periksa ke respons
                        $standardResponse['reg_periksa'] = [
                            'no_rawat' => $noRawat,
                            'no_reg' => $request->nomorantrean
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Gagal mendaftarkan pasien ke reg_periksa', [
                        'error' => $e->getMessage(),
                        'no_rkm_medis' => $request->norm
                    ]);
                }
            }

            // Tambahkan log ke tabel antrean_bpjs_log jika ada
            try {
                if (Schema::hasTable('antrean_bpjs_log')) {
                    DB::table('antrean_bpjs_log')->insert([
                        'no_rawat' => $cekRegPeriksa->no_rawat ?? $noRawat ?? null,
                        'no_rkm_medis' => $request->norm,
                        'status' => $isSuccess ? 'Berhasil' : 'Gagal',
                        'response' => json_encode($standardResponse),
                        'created_at' => now()
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Gagal menyimpan log antrean BPJS', [
                    'error' => $e->getMessage()
                ]);
            }

            // Kembalikan response dengan format standar BPJS
            return response()->json($standardResponse, $isSuccess ? 200 : 400);

        } catch (\Exception $e) {
            Log::error('Error pada AddAntreanController::add', [
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

    private function hitungUmur($tanggalLahir)
    {
        $birthDate = new \DateTime($tanggalLahir);
        $today = new \DateTime('today');
        $umur = $birthDate->diff($today)->y;
        return $umur;
    }

    private function kategoriUmur($tanggalLahir)
    {
        $umur = $this->hitungUmur($tanggalLahir);
        
        if ($umur > 65) {
            return 'MANULA';
        } elseif ($umur >= 55) {
            return 'LANSIA';
        } elseif ($umur >= 17) {
            return 'DEWASA';
        } elseif ($umur >= 12) {
            return 'REMAJA';
        } elseif ($umur >= 5) {
            return 'ANAK-ANAK';
        } elseif ($umur >= 1) {
            return 'BALITA';
        } else {
            return 'BAYI';
        }
    }
}

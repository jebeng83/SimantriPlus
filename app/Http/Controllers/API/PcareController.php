<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Traits\PcareTrait;
use Illuminate\Validation\ValidationException;

class PcareController extends Controller
{
    use PcareTrait;

    /**
     * Mendapatkan data peserta berdasarkan nomor kartu
     * URL: {Base URL}/{Service Name}/peserta/{Parameter 1}
     * Parameter 1: Nomor Kartu Peserta
     */
    public function getPeserta($noKartu)
    {
        try {
            // Perbaiki format nomor kartu (hapus non-digit, hapus leading zeros, padding hingga 13 digit)
            $noKartu = preg_replace('/[^0-9]/', '', $noKartu); // Hapus karakter non-digit
            $noKartuClean = ltrim($noKartu, '0'); // Hapus leading zeros
            $noKartu = str_pad($noKartuClean, 13, '0', STR_PAD_LEFT); // Padding hingga 13 digit
            
            // Log nomor kartu yang sudah diperbaiki
            Log::info('PCare Get Peserta Format Fix', [
                'original' => $noKartu,
                'cleaned' => $noKartuClean,
                'padded' => $noKartu
            ]);
            
            // Validasi format nomor kartu
            if (!preg_match('/^\d{13}$/', $noKartu)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Nomor kartu harus 13 digit'
                    ],
                    'response' => null
                ], 400);
            }

            // Cek cache dulu
            $cacheKey = 'peserta_' . $noKartu;
            if (Cache::has($cacheKey)) {
                Log::info('PCare Get Peserta From Cache', ['noKartu' => $noKartu]);
                return response()->json(Cache::get($cacheKey));
            }

            // Log request
            Log::info('PCare Get Peserta Request', [
                'noKartu' => $noKartu,
                'timestamp' => now()
            ]);

            // Format endpoint
            $endpoint = "peserta/{$noKartu}";

            // Debugging info - tampilkan semua variabel environment yang diperlukan
            Log::info('PCare Environment Variables', [
                'base_url' => env('BPJS_PCARE_BASE_URL'),
                'cons_id' => env('BPJS_PCARE_CONS_ID'),
                'user_key' => env('BPJS_PCARE_USER_KEY'),
                'username' => env('BPJS_PCARE_USER'),
                'has_password' => !empty(env('BPJS_PCARE_PASS')),
                'has_cons_pwd' => !empty(env('BPJS_PCARE_CONS_PWD')),
                'app_code' => env('BPJS_PCARE_APP_CODE')
            ]);

            // Kirim request ke PCare
            $response = $this->requestPcare($endpoint);

            // Debug response
            Log::info('PCare Response Debug', [
                'response' => $response
            ]);

            // Cek response
            if (isset($response['metaData']) && $response['metaData']['code'] == 200) {
                // Simpan ke cache selama 6 jam
                Cache::put($cacheKey, $response, now()->addHours(6));
                
                // Format response sesuai dengan contoh Java
                $peserta = $response['response'];
                
                // Data yang ditampilkan sesuai dengan contoh Java
                $formattedData = [
                    ['No.Kartu', ': '.$peserta['noKartu']],
                    ['Nama', ': '.$peserta['nama']],
                    ['Hubungan Keluarga', ': '.$peserta['hubunganKeluarga']],
                    ['Jenis Kelamin', ': '.str_replace(['L', 'P'], ['Laki-Laki', 'Perempuan'], $peserta['sex'])],
                    ['Tanggal Lahir', ': '.$peserta['tglLahir']],
                    ['Mulai Aktif', ': '.$peserta['tglMulaiAktif']],
                    ['Akhir Berlaku', ': '.$peserta['tglAkhirBerlaku']],
                    ['Provider Umum', ':'],
                    ['       Kode Provider', ': '.($peserta['kdProviderPst']['kdProvider'] ?? '-')],
                    ['       Nama Provider', ': '.($peserta['kdProviderPst']['nmProvider'] ?? '-')],
                    ['Provider Gigi', ':'],
                    ['       Kode Provider', ': '.($peserta['kdProviderGigi']['kdProvider'] ?? '-')],
                    ['       Nama Provider', ': '.($peserta['kdProviderGigi']['nmProvider'] ?? '-')],
                    ['Kelas Tanggungan', ':'],
                    ['       Kode Kelas', ': '.$peserta['jnsKelas']['kode']],
                    ['       Nama Kelas', ': '.$peserta['jnsKelas']['nama']],
                    ['Jenis Peserta', ':'],
                    ['       Kode Jenis', ': '.$peserta['jnsPeserta']['kode']],
                    ['       Nama Jenis', ': '.$peserta['jnsPeserta']['nama']],
                    ['Golongan Darah', ': '.$peserta['golDarah']],
                    ['Nomor HP', ': '.$peserta['noHP']],
                    ['Nomor KTP', ': '.$peserta['noKTP']],
                    ['Peserta Prolanis', ': '.($peserta['pstProl'] ?? '-')],
                    ['Peserta PRB', ': '.($peserta['pstPrb'] ?? '-')],
                    ['Status', ': '.$peserta['ketAktif']],
                    ['Asuransi/COB', ':'],
                    ['       Kode Asuransi', ': '.($peserta['asuransi']['kdAsuransi'] ?? '-')],
                    ['       Nama Asuransi', ': '.($peserta['asuransi']['nmAsuransi'] ?? '-')],
                    ['       Nomer Asuransi', ': '.($peserta['asuransi']['noAsuransi'] ?? '-')],
                    ['       COB', ': '.($peserta['asuransi']['cob'] ? 'Ya' : 'Tidak')],
                    ['Tunggakan', ': '.$peserta['tunggakan']]
                ];

                $response['formattedData'] = $formattedData;
                return response()->json($response);
            }

            // Debug response error
            Log::warning('PCare Response Error', [
                'response' => $response,
                'endpoint' => $endpoint
            ]);

            // Cek jika ada error authentication
            if (isset($response['metaData']) && $response['metaData']['code'] == 401) {
                return response()->json($response, 401);
            }
            
            // Cek jika ada error authentication
            if (isset($response['metaData']) && $response['metaData']['code'] == 401) {
                return response()->json($response, 401);
            }
            
            return response()->json($response, 400);

        } catch (\Exception $e) {
            Log::error('PCare Get Peserta Error', [
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
     * Mendapatkan data peserta berdasarkan NIK
     */
    public function getPesertaByNIK($nik)
    {
        try {
            // Validasi format NIK
            if (!preg_match('/^\d{16}$/', $nik)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'NIK harus 16 digit'
                    ],
                    'response' => null
                ], 400);
            }

            // Cek cache dulu
            $cacheKey = 'peserta_nik_' . $nik;
            if (Cache::has($cacheKey)) {
                Log::info('PCare Get Peserta By NIK From Cache', ['nik' => $nik]);
                return response()->json(Cache::get($cacheKey));
            }

            // Log request
            Log::info('PCare Get Peserta By NIK Request', [
                'nik' => $nik,
                'timestamp' => now()
            ]);

            // Format endpoint
            $endpoint = "peserta/nik/{$nik}";

            // Kirim request ke PCare
            $response = $this->requestPcare($endpoint);

            // Cek response dan simpan ke cache
            if (isset($response['metaData']) && $response['metaData']['code'] == 200) {
                Cache::put($cacheKey, $response, now()->addHours(6));
                return response()->json($response);
            }

            return response()->json($response, 400);

        } catch (\Exception $e) {
            Log::error('PCare Get Peserta By NIK Error', [
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
     * Mendapatkan data provider PCare
     */
    public function getProvider()
    {
        try {
            // Kirim request ke PCare
            $response = $this->requestPcare('provider');

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Provider Error', [
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
     * Mendapatkan data dokter PCare
     */
    public function getDokter()
    {
        try {
            // Kirim request ke PCare
            $response = $this->requestPcare('dokter');

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Dokter Error', [
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
     * Mendapatkan data diagnosa PCare
     */
    public function getDiagnosa($keyword)
    {
        try {
            // Kirim request ke PCare
            $response = $this->requestPcare('diagnosa/' . urlencode($keyword));

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Diagnosa Error', [
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
     * Mendapatkan data tindakan PCare
     */
    public function getTindakan($keyword)
    {
        try {
            // Kirim request ke PCare
            $response = $this->requestPcare('tindakan/' . urlencode($keyword));

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Tindakan Error', [
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
     * Mendapatkan data obat PCare
     */
    public function getObat($keyword)
    {
        try {
            // Kirim request ke PCare
            $response = $this->requestPcare('obat/' . urlencode($keyword));

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Obat Error', [
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
     * Mendapatkan data kunjungan PCare
     */
    public function getKunjungan($noKartu)
    {
        try {
            // Validasi format nomor kartu
            if (!preg_match('/^\d{13}$/', $noKartu)) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Nomor kartu harus 13 digit'
                    ],
                    'response' => null
                ], 400);
            }

            // Kirim request ke PCare dengan endpoint kunjungan/peserta/[noKartu]
            $response = $this->requestPcare('kunjungan/peserta/' . $noKartu);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Kunjungan Error', [
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
     * Mendapatkan data status pulang PCare
     */
    public function getStatusPulang()
    {
        try {
            // Kirim request ke PCare
            $response = $this->requestPcare('statuspulang');

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Status Pulang Error', [
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
     * Mendapatkan data poli PCare
     */
    public function getPoli()
    {
        try {
            // Kirim request ke PCare
            $response = $this->requestPcare('poli');

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Poli Error', [
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
     * Mendapatkan mapping poliklinik PCare berdasarkan kode poli rumah sakit
     * Digunakan untuk form pendaftaran PCare
     */
    public function getMappingPoli($kd_poli_rs = null)
    {
        try {
            // Log request
            Log::info('PCare Get Mapping Poli Request', [
                'kd_poli_rs' => $kd_poli_rs
            ]);

            // Query untuk mendapatkan mapping poli
            $query = DB::table('maping_poliklinik_pcare')
                ->select(
                    'maping_poliklinik_pcare.kd_poli_rs',
                    'maping_poliklinik_pcare.kd_poli_pcare',
                    'maping_poliklinik_pcare.nm_poli_pcare',
                    'poliklinik.nm_poli as nm_poli_rs'
                )
                ->join('poliklinik', 'maping_poliklinik_pcare.kd_poli_rs', '=', 'poliklinik.kd_poli');

            // Filter berdasarkan kode poli jika ada
            if ($kd_poli_rs) {
                $query->where('maping_poliklinik_pcare.kd_poli_rs', $kd_poli_rs);
            }

            $mappingPoli = $query->get();

            if ($mappingPoli->isEmpty() && $kd_poli_rs) {
                // Jika mapping untuk poli tertentu tidak ditemukan
                Log::warning('PCare Mapping Poli Not Found', [
                    'kd_poli_rs' => $kd_poli_rs
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Mapping poli tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Log success
            Log::info('PCare Get Mapping Poli Success', [
                'count' => $mappingPoli->count()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data mapping poli berhasil didapatkan',
                'data' => $mappingPoli
            ]);

        } catch (\Exception $e) {
            Log::error('PCare Get Mapping Poli Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Mendapatkan data kelompok sehat PCare
     */
    public function getKelompokSehat()
    {
        try {
            // Kirim request ke PCare
            $response = $this->requestPcare('kelompok');

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Kelompok Sehat Error', [
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
     * Mendapatkan data klub prolanis PCare
     */
    public function getKlubProlanis()
    {
        try {
            // Kirim request ke PCare
            $response = $this->requestPcare('klubprolanis');

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('PCare Get Klub Prolanis Error', [
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
     * Menambahkan pendaftaran PCare
     * Format endpoint: {Base URL}/{Service Name}/pendaftaran
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPendaftaran(Request $request)
    {
        try {
            // Log raw request untuk debugging
            Log::info('PCare Add Pendaftaran - Raw Request', [
                'raw_input' => $request->all()
            ]);
            
            // Penanganan khusus untuk Kunjungan Sehat dari fungsi daftarKunjunganSehat()
            if (isset($request->kunjSakit) && $request->kunjSakit === false && 
                isset($request->noKartu) && !isset($request->no_rawat) && !isset($request->no_rkm_medis) && 
                isset($request->keluhan) && $request->keluhan === 'Konsultasi Kesehatan') {
                
                Log::info('PCare Kunjungan Sehat - Request Khusus Terdeteksi');
                
                // Persiapkan data untuk dikirim ke PCare (minimal yang diperlukan)
                $dataRequest = [
                    'kdProviderPeserta' => $request->kdProviderPeserta ?? env('BPJS_PCARE_KODE_PPK', '11251919'),
                    'tglDaftar' => $request->tglDaftar,
                    'noKartu' => $request->noKartu,
                    'kdPoli' => $request->kdPoli ?? '021',
                    'keluhan' => $request->keluhan ?? 'Konsultasi Kesehatan',
                    'kunjSakit' => false,
                    'sistole' => $request->sistole ?? '0',
                    'diastole' => $request->diastole ?? '0',
                    'beratBadan' => $request->beratBadan ?? '0',
                    'tinggiBadan' => $request->tinggiBadan ?? '0',
                    'respRate' => $request->respRate ?? '0',
                    'lingkarPerut' => $request->lingkarPerut ?? '0',
                    'heartRate' => $request->heartRate ?? '0',
                    'rujukBalik' => $request->rujukBalik ?? '0',
                    'kdTkp' => $request->kdTkp ?? '10'
                ];
                
                Log::info('PCare Kunjungan Sehat - Request Data', [
                    'data' => $dataRequest
                ]);
                
                // Kirim request ke PCare dengan Content-Type text/plain
                $response = $this->requestPcare('pendaftaran', 'POST', $dataRequest, 'text/plain');
                
                Log::info('PCare Kunjungan Sehat - Response', [
                    'metaData' => $response['metaData'] ?? null,
                    'response' => $response['response'] ?? null
                ]);
                
                return response()->json($response);
            }
            
            // Validasi input (untuk pendaftaran normal)
            $validator = Validator::make($request->all(), [
                'no_rawat' => 'required|string',
                'no_rkm_medis' => 'required|string',
                'nm_pasien' => 'required|string',
                'kdProviderPeserta' => 'required|string',
                'tglDaftar' => 'required|string', // format dd-mm-yyyy
                'noKartu' => 'required|string',
                'kdPoli' => 'required|string',
                'nmPoli' => 'required|string',
                'keluhan' => 'nullable|string',
                'kunjSakit' => 'required',  // Bisa string "true"/"false" atau boolean
                'sistole' => 'required|numeric',
                'diastole' => 'required|numeric',
                'beratBadan' => 'required|numeric',
                'tinggiBadan' => 'required|numeric',
                'respRate' => 'required|numeric',
                'lingkarPerut' => 'required|numeric',
                'heartRate' => 'required|numeric',
                'rujukBalik' => 'required|numeric',
                'kdTkp' => 'required|string', // 10 = Rawat Jalan, 20 = Rawat Inap, 50 = Promotif Preventif
            ]);

            if ($validator->fails()) {
                Log::warning('PCare Add Pendaftaran - Validation Failed', [
                    'errors' => $validator->errors()->toArray()
                ]);
                
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ],
                    'response' => null
                ], 400);
            }

            // Konversi kunjSakit dari string ke boolean jika perlu
            $kunjSakit = $request->kunjSakit;
            if (is_string($kunjSakit)) {
                // Cek jika string adalah "true", "false", "Kunjungan Sakit", "Kunjungan Sehat"
                if (strtolower($kunjSakit) === 'true' || strtolower($kunjSakit) === 'kunjungan sakit') {
                    $kunjSakit = true;
                } else {
                    $kunjSakit = false;
                }
                
                Log::info('PCare Add Pendaftaran - Converted kunjSakit', [
                    'original' => $request->kunjSakit,
                    'converted' => $kunjSakit ? 'Kunjungan Sakit' : 'Kunjungan Sehat'
                ]);
            }

            // Persiapkan data untuk dikirim ke PCare
            $dataRequest = [
                'kdProviderPeserta' => substr($request->kdProviderPeserta ?? '', 0, 15),
                'tglDaftar' => $request->tglDaftar,
                'noKartu' => substr($request->noKartu ?? '', 0, 25),
                'kdPoli' => substr($request->kdPoli ?? '', 0, 5),
                'keluhan' => substr($request->keluhan ?: '', 0, 400),
                'kunjSakit' => $kunjSakit,
                'sistole' => substr($request->sistole ?? '120', 0, 3),
                'diastole' => substr($request->diastole ?? '80', 0, 3),
                'beratBadan' => substr($request->beratBadan ?? '60', 0, 5),
                'tinggiBadan' => substr($request->tinggiBadan ?? '165', 0, 5),
                'respRate' => substr($request->respRate ?? '20', 0, 3),
                'lingkarPerut' => substr($request->lingkar_perut ?? '80', 0, 5),
                'heartRate' => substr($request->heartRate ?? '80', 0, 3),
                'rujukBalik' => substr($request->rujukBalik ?? '0', 0, 3),
                'kdTkp' => substr($request->kdTkp ?? '10', 0, 5)
            ];

            // Log data yang akan dikirim ke PCare
            Log::info('PCare Add Pendaftaran - Request Data', [
                'data' => $dataRequest
            ]);

            // Kirim request ke PCare dengan Content-Type text/plain
            $response = $this->requestPcare('pendaftaran', 'POST', $dataRequest, 'text/plain');

            // Log response dari PCare
            Log::info('PCare Add Pendaftaran - Response', [
                'metaData' => $response['metaData'] ?? null,
                'response' => $response['response'] ?? null
            ]);

            // Jika berhasil, simpan ke database
            if (isset($response['metaData']['code']) && $response['metaData']['code'] == 201) {
                $noUrut = isset($response['response']['message']) ? $response['response']['message'] : '';
                
                // Persiapkan data untuk disimpan ke database
                // Pastikan nilai untuk field enum (kunjSakit) sesuai dengan tipe data di database
                $kunjSakitForDB = $kunjSakit ? 'Kunjungan Sakit' : 'Kunjungan Sehat';
                
                $dataSave = [
                    'no_rawat' => $request->no_rawat,
                    'no_rkm_medis' => $request->no_rkm_medis,
                    'nm_pasien' => $request->nm_pasien,
                    'kdProviderPeserta' => $request->kdProviderPeserta,
                    'tglDaftar' => $request->tglDaftar,
                    'noKartu' => $request->noKartu,
                    'kdPoli' => $request->kdPoli,
                    'nmPoli' => $request->nmPoli,
                    'keluhan' => $request->keluhan,
                    'kunjSakit' => $kunjSakitForDB, // String enum untuk database
                    'sistole' => $request->sistole,
                    'diastole' => $request->diastole,
                    'beratBadan' => $request->beratBadan,
                    'tinggiBadan' => $request->tinggiBadan,
                    'respRate' => $request->respRate,
                    'lingkar_perut' => $request->lingkar_perut,
                    'heartRate' => $request->heartRate,
                    'rujukBalik' => $request->rujukBalik,
                    'kdTkp' => $request->kdTkp,
                    'kd_dokter' => $request->kd_dokter ?? null,
                    'suhu_tubuh' => $request->suhu_tubuh ?? null,
                    'alergiMakanan' => $request->alergiMakanan ?? null,
                    'alergiUdara' => $request->alergiUdara ?? null,
                    'alergiObat' => $request->alergiObat ?? null,
                    'terapiObat' => $request->terapiObat ?? null,
                    'terapiNonObat' => $request->terapiNonObat ?? null,
                    'BMHP' => $request->BMHP ?? null,
                    'prognosa' => $request->prognosa ?? null
                ];
                
                // Log data yang akan disimpan
                Log::info('PCare Add Pendaftaran - Data yang akan disimpan', [
                    'no_rawat' => $dataSave['no_rawat'],
                    'no_rkm_medis' => $dataSave['no_rkm_medis'],
                    'kunjSakit' => $dataSave['kunjSakit']
                ]);
                
                // Simpan ke database
                $saved = $this->simpanPendaftaranKeDatabase($dataSave, $noUrut);
                
                if (!$saved) {
                    // Berikan peringatan jika gagal menyimpan ke database
                    $response['metaData']['warning'] = 'Pendaftaran berhasil di PCare tetapi gagal disimpan ke database lokal.';
                }
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('PCare Add Pendaftaran - Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
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
     * Menyimpan data pendaftaran PCare ke database
     * Digunakan untuk menyimpan data ke tabel pcare_pendaftaran, pemeriksaan_ralan, dan pemeriksaan_ranap
     * 
     * @param array $data Data pendaftaran dari request
     * @param string $noUrut Nomor urut yang didapat dari response PCare
     * @return bool
     */
    protected function simpanPendaftaranKeDatabase($data, $noUrut)
    {
        try {
            // Log data yang diterima untuk debugging
            Log::info('Menyimpan pendaftaran PCare ke database', [
                'no_rawat_original' => $data['no_rawat'],
                'no_rkm_medis' => $data['no_rkm_medis']
            ]);

            // Cek apakah pasien sudah terdaftar hari ini
            $today = date('Y-m-d');
            $pendaftaranPasienHariIni = DB::table('reg_periksa')
                ->where('tgl_registrasi', $today)
                ->where('no_rkm_medis', $data['no_rkm_medis'])
                ->orderBy('no_rawat', 'desc')
                ->first();
                
            if ($pendaftaranPasienHariIni) {
                // Jika pasien sudah terdaftar hari ini, prioritaskan menggunakan pendaftaran yang sudah ada
                $data['no_rawat'] = $pendaftaranPasienHariIni->no_rawat;
                Log::info('Pasien sudah terdaftar hari ini, menggunakan pendaftaran yang sudah ada', [
                    'no_rawat' => $data['no_rawat'],
                    'no_rkm_medis' => $data['no_rkm_medis'],
                    'kd_poli' => $pendaftaranPasienHariIni->kd_poli,
                    'tgl_registrasi' => $pendaftaranPasienHariIni->tgl_registrasi
                ]);
                
                // Lanjut ke proses simpan data PCare tanpa membuat entry baru di reg_periksa
            } else {
                // Jika pasien belum terdaftar hari ini, cari pendaftaran terbaru untuk pasien ini
                $rawatTerbaru = DB::table('reg_periksa')
                    ->where('no_rkm_medis', $data['no_rkm_medis'])
                    ->orderBy('tgl_registrasi', 'desc')
                    ->orderBy('jam_reg', 'desc')
                    ->first();
                
                if ($rawatTerbaru) {
                    // Periksa apakah pendaftaran terbaru adalah hari ini
                    if ($rawatTerbaru->tgl_registrasi == $today) {
                        // Jika pendaftaran terbaru adalah hari ini, gunakan no_rawat tersebut
                        $data['no_rawat'] = $rawatTerbaru->no_rawat;
                        Log::info('Menggunakan pendaftaran terbaru pasien hari ini', [
                            'no_rawat' => $data['no_rawat'],
                            'tgl_registrasi' => $rawatTerbaru->tgl_registrasi
                        ]);
                    } else {
                        // Jika pendaftaran terbaru bukan hari ini, cari pendaftaran lain hari ini
                        $pendaftaranHariIni = DB::table('reg_periksa')
                            ->where('tgl_registrasi', $today)
                            ->orderBy('no_rawat', 'desc')
                            ->first();
                        
                        if ($pendaftaranHariIni) {
                            // Buat no_rawat baru berdasarkan pendaftaran lain hari ini
                            $formattedDate = date('Y/m/d');
                            $lastNumber = substr($pendaftaranHariIni->no_rawat, -6);
                            $newNumber = str_pad(intval($lastNumber) + 1, 6, '0', STR_PAD_LEFT);
                            $data['no_rawat'] = $formattedDate . '/' . $newNumber;
                            
                            Log::info('Membuat no_rawat baru untuk pasien hari ini', [
                                'no_rawat_baru' => $data['no_rawat']
                            ]);
                            
                            // Buat pendaftaran baru di reg_periksa
                            DB::table('reg_periksa')->insert([
                                'no_rawat' => $data['no_rawat'],
                                'no_reg' => $newNumber,
                                'tgl_registrasi' => $today,
                                'jam_reg' => date('H:i:s'),
                                'kd_dokter' => $data['kd_dokter'] ?? '1',
                                'no_rkm_medis' => $data['no_rkm_medis'],
                                'kd_poli' => 'U0002', // Default ke poli umum
                                'p_jawab' => 'BPJS',
                                'almt_pj' => '-',
                                'hubunganpj' => '-',
                                'biaya_reg' => 0,
                                'stts' => 'Belum',
                                'stts_daftar' => 'Lama',
                                'status_lanjut' => ($data['kdTkp'] == '20') ? 'Ranap' : 'Ralan',
                                'kd_pj' => 'BPJ',
                                'umurdaftar' => 0,
                                'sttsumur' => 'Th',
                                'status_bayar' => 'Belum Bayar',
                                'status_poli' => 'Lama'
                            ]);
                            
                            Log::info('Entry baru dibuat di reg_periksa untuk pendaftaran PCare', [
                                'no_rawat' => $data['no_rawat']
                            ]);
                        } else {
                            // Jika tidak ada pendaftaran lain hari ini, gunakan format default
                            $formattedDate = date('Y/m/d');
                            $data['no_rawat'] = $formattedDate . '/000001';
                            
                            Log::info('Membuat no_rawat default untuk pendaftaran baru', [
                                'no_rawat_default' => $data['no_rawat']
                            ]);
                            
                            // Buat pendaftaran baru dengan format default
                            DB::table('reg_periksa')->insert([
                                'no_rawat' => $data['no_rawat'],
                                'no_reg' => '000001',
                                'tgl_registrasi' => $today,
                                'jam_reg' => date('H:i:s'),
                                'kd_dokter' => $data['kd_dokter'] ?? '1',
                                'no_rkm_medis' => $data['no_rkm_medis'],
                                'kd_poli' => 'U0002', // Default ke poli umum
                                'p_jawab' => 'BPJS',
                                'almt_pj' => '-',
                                'hubunganpj' => '-',
                                'biaya_reg' => 0,
                                'stts' => 'Belum',
                                'stts_daftar' => 'Lama',
                                'status_lanjut' => ($data['kdTkp'] == '20') ? 'Ranap' : 'Ralan',
                                'kd_pj' => 'BPJ',
                                'umurdaftar' => 0,
                                'sttsumur' => 'Th',
                                'status_bayar' => 'Belum Bayar',
                                'status_poli' => 'Lama'
                            ]);
                            
                            Log::info('Entry default dibuat di reg_periksa', [
                                'no_rawat' => $data['no_rawat']
                            ]);
                        }
                    }
                } else {
                    // Jika pasien belum pernah terdaftar, gunakan format no_rawat yang dikirim
                    // atau buat nomor baru jika format tidak valid
                    if (!preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d+$/', $data['no_rawat'])) {
                        // Format no_rawat tidak valid, buat yang baru
                        $pendaftaranHariIni = DB::table('reg_periksa')
                            ->where('tgl_registrasi', $today)
                            ->orderBy('no_rawat', 'desc')
                            ->first();
                        
                        if ($pendaftaranHariIni) {
                            // Buat no_rawat baru berdasarkan pendaftaran hari ini
                            $formattedDate = date('Y/m/d');
                            $lastNumber = substr($pendaftaranHariIni->no_rawat, -6);
                            $newNumber = str_pad(intval($lastNumber) + 1, 6, '0', STR_PAD_LEFT);
                            $data['no_rawat'] = $formattedDate . '/' . $newNumber;
                        } else {
                            // Jika tidak ada pendaftaran hari ini, buat nomor default
                            $formattedDate = date('Y/m/d');
                            $data['no_rawat'] = $formattedDate . '/000001';
                            $newNumber = '000001';
                        }
                        
                        Log::info('Format no_rawat tidak valid, membuat yang baru', [
                            'no_rawat_baru' => $data['no_rawat']
                        ]);
                        
                        // Buat entry baru di reg_periksa
                        DB::table('reg_periksa')->insert([
                            'no_rawat' => $data['no_rawat'],
                            'no_reg' => $newNumber ?? '000001',
                            'tgl_registrasi' => $today,
                            'jam_reg' => date('H:i:s'),
                            'kd_dokter' => $data['kd_dokter'] ?? '1',
                            'no_rkm_medis' => $data['no_rkm_medis'],
                            'kd_poli' => 'U0002', // Default ke poli umum
                            'p_jawab' => 'BPJS',
                            'almt_pj' => '-',
                            'hubunganpj' => '-',
                            'biaya_reg' => 0,
                            'stts' => 'Belum',
                            'stts_daftar' => 'Lama',
                            'status_lanjut' => ($data['kdTkp'] == '20') ? 'Ranap' : 'Ralan',
                            'kd_pj' => 'BPJ',
                            'umurdaftar' => 0,
                            'sttsumur' => 'Th',
                            'status_bayar' => 'Belum Bayar',
                            'status_poli' => 'Lama'
                        ]);
                        
                        Log::info('Entry baru dibuat di reg_periksa untuk pendaftaran PCare', [
                            'no_rawat' => $data['no_rawat']
                        ]);
                    }
                }
            }
            
            // Periksa kembali apakah no_rawat valid di reg_periksa
            $cekNoRawat = DB::table('reg_periksa')
                ->where('no_rawat', $data['no_rawat'])
                ->first();
                
            if (!$cekNoRawat) {
                throw new \Exception('No_rawat tidak valid dan tidak dapat dibuat: ' . $data['no_rawat']);
            }

            DB::beginTransaction();

            // Format tanggal untuk database (YYYY-MM-DD)
            $tglDaftarParts = explode('-', $data['tglDaftar']);
            $tglDaftarDB = $tglDaftarParts[2] . '-' . $tglDaftarParts[1] . '-' . $tglDaftarParts[0];
            
            // Validasi dan konversi kdTkp ke format enum yang benar
            $kdTkpLabel = '';
            switch ($data['kdTkp']) {
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

            // Validasi dan konversi kunjSakit ke format enum yang benar
            $kunjSakit = 'Kunjungan Sakit'; // Default value
            if (is_string($data['kunjSakit'])) {
                // Jika string, validasi nilainya
                if ($data['kunjSakit'] === 'Kunjungan Sehat' || strtolower($data['kunjSakit']) === 'kunjungan sehat') {
                    $kunjSakit = 'Kunjungan Sehat';
                }
            } else if (is_bool($data['kunjSakit'])) {
                // Jika boolean, konversi ke string
                $kunjSakit = $data['kunjSakit'] ? 'Kunjungan Sakit' : 'Kunjungan Sehat';
            }
            
            // Log data yang akan disimpan ke database
            Log::info('Data pendaftaran PCare yang akan disimpan ke database', [
                'no_rawat' => $data['no_rawat'],
                'tglDaftar' => $tglDaftarDB,
                'no_rkm_medis' => $data['no_rkm_medis'],
                'kunjSakit' => $kunjSakit,
                'kdTkp' => $kdTkpLabel
            ]);
            
            // Cek apakah sudah ada pendaftaran dengan no_rawat yang sama
            $cekPendaftaran = DB::table('pcare_pendaftaran')
                ->where('no_rawat', $data['no_rawat'])
                ->first();
                
            // Prepare data untuk insert/update
            $pendaftaranData = [
                'tglDaftar' => $tglDaftarDB,
                'no_rkm_medis' => substr($data['no_rkm_medis'] ?? '', 0, 15),
                'nm_pasien' => substr($data['nm_pasien'] ?? '', 0, 40),
                'kdProviderPeserta' => substr($data['kdProviderPeserta'] ?? '', 0, 15),
                'noKartu' => substr($data['noKartu'] ?? '', 0, 25),
                'kdPoli' => substr($data['kdPoli'] ?? '', 0, 5),
                'nmPoli' => substr($data['nmPoli'] ?? '', 0, 50),
                'keluhan' => substr($data['keluhan'] ?? 'Tidak Ada', 0, 400),
                'kunjSakit' => $kunjSakit,
                'sistole' => substr($data['sistole'] ?? '0', 0, 3),
                'diastole' => substr($data['diastole'] ?? '0', 0, 3),
                'beratBadan' => substr($data['beratBadan'] ?? '0', 0, 5),
                'tinggiBadan' => substr($data['tinggiBadan'] ?? '0', 0, 5),
                'respRate' => substr($data['respRate'] ?? '0', 0, 3),
                'lingkar_perut' => substr($data['lingkar_perut'] ?? $data['lingkarPerut'] ?? '', 0, 5),
                'heartRate' => substr($data['heartRate'] ?? '0', 0, 3),
                'rujukBalik' => substr($data['rujukBalik'] ?? '0', 0, 3),
                'kdTkp' => $kdTkpLabel,
                'noUrut' => substr($noUrut ?? '', 0, 5),
                'status' => 'Terkirim'
            ];
            
            if ($cekPendaftaran) {
                Log::info('Pendaftaran PCare sudah ada, melakukan update', [
                    'no_rawat' => $data['no_rawat']
                ]);
                
                // Update pendaftaran yang sudah ada
                try {
                    DB::table('pcare_pendaftaran')
                        ->where('no_rawat', $data['no_rawat'])
                        ->update($pendaftaranData);
                        
                    Log::info('Update pendaftaran PCare berhasil', [
                        'no_rawat' => $data['no_rawat']
                    ]);
                } catch (\Exception $updateError) {
                    Log::error('Gagal update pendaftaran PCare', [
                        'error' => $updateError->getMessage(),
                        'no_rawat' => $data['no_rawat']
                    ]);
                    throw $updateError;
                }
            } else {
                // Insert pendaftaran baru
                try {
                    // Tambahkan no_rawat ke data
                    $pendaftaranData['no_rawat'] = $data['no_rawat'];
                    
                    Log::info('Inserting data to pcare_pendaftaran', [
                        'data' => $pendaftaranData
                    ]);
                    
                    DB::table('pcare_pendaftaran')->insert($pendaftaranData);
                    
                    Log::info('Insert pendaftaran PCare berhasil', [
                        'no_rawat' => $data['no_rawat']
                    ]);
                } catch (\Exception $insertError) {
                    Log::error('Gagal insert pendaftaran PCare', [
                        'error' => $insertError->getMessage(),
                        'no_rawat' => $data['no_rawat'],
                        'data' => $pendaftaranData
                    ]);
                    
                    // Jika gagal, coba lagi dengan nilai minimal
                    try {
                        $minimalData = [
                            'no_rawat' => $data['no_rawat'],
                            'tglDaftar' => $tglDaftarDB,
                            'no_rkm_medis' => substr($data['no_rkm_medis'] ?? '', 0, 15),
                            'nm_pasien' => substr($data['nm_pasien'] ?? '-', 0, 40),
                            'kdProviderPeserta' => substr($data['kdProviderPeserta'] ?? '-', 0, 15),
                            'noKartu' => substr($data['noKartu'] ?? '-', 0, 25),
                            'kdPoli' => substr($data['kdPoli'] ?? '-', 0, 5),
                            'nmPoli' => substr($data['nmPoli'] ?? '-', 0, 50),
                            'keluhan' => substr('Tidak Ada', 0, 400),
                            'kunjSakit' => 'Kunjungan Sakit',
                            'sistole' => '0',
                            'diastole' => '0',
                            'beratBadan' => '0',
                            'tinggiBadan' => '0',
                            'respRate' => '0',
                            'lingkar_perut' => '0',
                            'heartRate' => '0',
                            'rujukBalik' => '0',
                            'kdTkp' => '10 Rawat Jalan',
                            'noUrut' => substr($noUrut ?? '-', 0, 5),
                            'status' => 'Terkirim'
                        ];
                        
                        DB::table('pcare_pendaftaran')->insert($minimalData);
                        
                        Log::info('Insert pendaftaran PCare dengan data minimal berhasil', [
                            'no_rawat' => $data['no_rawat']
                        ]);
                    } catch (\Exception $minimalInsertError) {
                        Log::error('Gagal insert pendaftaran PCare dengan data minimal', [
                            'error' => $minimalInsertError->getMessage(),
                            'no_rawat' => $data['no_rawat']
                        ]);
                        throw $minimalInsertError;
                    }
                }
            }
            
            // Tentukan jenis pemeriksaan berdasarkan reg_periksa
            $jenisRawat = DB::table('reg_periksa')
                ->where('no_rawat', $data['no_rawat'])
                ->value('status_lanjut');
                
            // Jika data status_lanjut tidak ditemukan, gunakan kdTkp
            if (!$jenisRawat) {
                $jenisRawat = ($data['kdTkp'] == '20') ? 'Ranap' : 'Ralan';
            }
            
            // Simpan ke tabel pemeriksaan berdasarkan jenis rawat
            if ($jenisRawat == 'Ralan' || (isset($data['save_to_pemeriksaan_ralan']) && $data['save_to_pemeriksaan_ralan'] == true)) {
                // 2. Simpan ke tabel pemeriksaan_ralan
                Log::info('Menyimpan data ke pemeriksaan_ralan', [
                    'alasan' => ($jenisRawat == 'Ralan' ? 'Jenis rawat Ralan' : 'Flag save_to_pemeriksaan_ralan = true'),
                    'no_rawat' => $data['no_rawat']
                ]);
                
                // Cek apakah sudah ada pemeriksaan ralan
                $cekPemeriksaan = DB::table('pemeriksaan_ralan')
                    ->where('no_rawat', $data['no_rawat'])
                    ->where('tgl_perawatan', $tglDaftarDB)
                    ->first();
                    
                // Dapatkan user yang sedang login
                $usernameLogin = session('username') ?? '';
                Log::info('Mencari NIP berdasarkan username login', [
                    'username' => $usernameLogin
                ]);
                
                // Pertama coba cari di tabel pegawai
                $nipPegawai = null;
                if (!empty($usernameLogin)) {
                    $pegawai = DB::table('pegawai')
                        ->where('nik', $usernameLogin)
                        ->first();
                    
                    if ($pegawai) {
                        $nipPegawai = $pegawai->nik;
                        Log::info('NIP pegawai ditemukan', [
                            'nik' => $pegawai->nik,
                            'nama' => $pegawai->nama
                        ]);
                    } else {
                        // Coba cari di tabel user
                        $user = DB::table('user')
                            ->where('id_user', $usernameLogin)
                            ->first();
                            
                        if ($user && !empty($user->nip)) {
                            $nipPegawai = $user->nip;
                            Log::info('NIP pegawai ditemukan dari tabel user', [
                                'nip' => $user->nip
                            ]);
                        } else {
                            Log::warning('NIP pegawai tidak ditemukan untuk username', [
                                'username' => $usernameLogin
                            ]);
                        }
                    }
                }
                
                // Jika tidak ditemukan NIP pegawai, gunakan kd_dokter dari permintaan atau dari reg_periksa
                $nip = $nipPegawai;
                if (empty($nip)) {
                    // Dapatkan dokter dari reg_periksa jika kd_dokter tidak tersedia
                    $dokter = $data['kd_dokter'] ?? '';
                    if (empty($dokter)) {
                        $dokter = DB::table('reg_periksa')
                            ->where('no_rawat', $data['no_rawat'])
                            ->value('kd_dokter');
                    }
                    
                    // Jika masih kosong, gunakan default dokter
                    if (empty($dokter)) {
                        $dokter = DB::table('dokter')
                            ->orderBy('kd_dokter', 'asc')
                            ->value('kd_dokter') ?? 'D0000001';
                    }
                    
                    $nip = $dokter;
                }
                
                $pemeriksaanData = [
                    'no_rawat' => substr($data['no_rawat'], 0, 17),
                    'tgl_perawatan' => $tglDaftarDB,
                    'jam_rawat' => date('H:i:s'),
                    'suhu_tubuh' => substr($data['suhu_tubuh'] ?? '', 0, 5),
                    'tensi' => substr(($data['sistole'].'/'.$data['diastole']), 0, 8),
                    'nadi' => substr($data['heartRate'] ?? '', 0, 3),
                    'respirasi' => substr($data['respRate'] ?? '', 0, 3),
                    'tinggi' => substr($data['tinggiBadan'] ?? '', 0, 5),
                    'berat' => substr($data['beratBadan'] ?? '', 0, 5),
                    'spo2' => substr($data['spo2'] ?? '', 0, 3),
                    'gcs' => substr($data['gcs'] ?? '', 0, 10),
                    'kesadaran' => $data['kesadaran'] ?? 'Compos Mentis',
                    'keluhan' => substr($data['keluhan'] ?? 'Tidak Ada', 0, 2000),
                    'pemeriksaan' => substr($data['pemeriksaan'] ?? '', 0, 2000),
                    'alergi' => substr(isset($data['alergi']) ? 'Makanan : ' . ($data['alergiMakanan'] ?? '-') . ', Udara : ' . ($data['alergiUdara'] ?? '-') . ', Obat : ' . ($data['alergiObat'] ?? '-') : '', 0, 80),
                    'lingkar_perut' => substr($data['lingkar_perut'] ?? $data['lingkarPerut'] ?? '', 0, 5),
                    'rtl' => substr(isset($data['terapiObat']) || isset($data['terapiNonObat']) ? 'Terapi Obat : ' . ($data['terapiObat'] ?? '-') . ', Terapi Non Obat : ' . ($data['terapiNonObat'] ?? '-') . ', BMHP : ' . ($data['BMHP'] ?? '-') : '', 0, 2000),
                    'penilaian' => substr($data['prognosa'] ?? '', 0, 2000),
                    'instruksi' => substr($data['instruksi'] ?? '', 0, 2000),
                    'evaluasi' => substr($data['evaluasi'] ?? '', 0, 2000),
                    'nip' => substr($nip, 0, 20)
                ];
                
                Log::info('Data pemeriksaan_ralan yang akan disimpan', [
                    'no_rawat' => $data['no_rawat'],
                    'nip' => $nip,
                    'tgl_perawatan' => $tglDaftarDB
                ]);
                
                try {
                    if ($cekPemeriksaan) {
                        // Update pemeriksaan yang sudah ada
                        DB::table('pemeriksaan_ralan')
                            ->where('no_rawat', $data['no_rawat'])
                            ->where('tgl_perawatan', $tglDaftarDB)
                            ->update($pemeriksaanData);
                        
                        Log::info('Update pemeriksaan_ralan berhasil', [
                            'no_rawat' => $data['no_rawat']
                        ]);
                    } else {
                        // Insert pemeriksaan baru
                        DB::table('pemeriksaan_ralan')->insert($pemeriksaanData);
                        
                        Log::info('Insert pemeriksaan_ralan berhasil', [
                            'no_rawat' => $data['no_rawat']
                        ]);
                    }
                } catch (\Exception $pemeriksaanError) {
                    Log::error('Gagal menyimpan ke pemeriksaan_ralan', [
                        'error' => $pemeriksaanError->getMessage(),
                        'no_rawat' => $data['no_rawat']
                    ]);
                    // Tidak throw exception agar proses tetap berlanjut
                }
            } 
            
            // Pemeriksaan ranap - Simpan jika jenisRawat = Ranap atau jika flag save_to_pemeriksaan_ranap = true
            if ($jenisRawat == 'Ranap' || (isset($data['save_to_pemeriksaan_ranap']) && $data['save_to_pemeriksaan_ranap'] == true)) {
                // 3. Simpan ke tabel pemeriksaan_ranap
                Log::info('Menyimpan data ke pemeriksaan_ranap', [
                    'alasan' => ($jenisRawat == 'Ranap' ? 'Jenis rawat Ranap' : 'Flag save_to_pemeriksaan_ranap = true'),
                    'no_rawat' => $data['no_rawat']
                ]);
                
                // Cek apakah sudah ada pemeriksaan ranap
                $cekPemeriksaan = DB::table('pemeriksaan_ranap')
                    ->where('no_rawat', $data['no_rawat'])
                    ->where('tgl_perawatan', $tglDaftarDB)
                    ->first();
                    
                // Dapatkan user yang sedang login jika belum didefinisikan
                if (!isset($usernameLogin)) {
                    $usernameLogin = session('username') ?? '';
                    Log::info('Mencari NIP berdasarkan username login untuk pemeriksaan_ranap', [
                        'username' => $usernameLogin
                    ]);
                }
                
                // Gunakan NIP yang sudah dicari atau cari jika belum ada
                if (!isset($nipPegawai)) {
                    // Pertama coba cari di tabel pegawai
                    $nipPegawai = null;
                    if (!empty($usernameLogin)) {
                        $pegawai = DB::table('pegawai')
                            ->where('nik', $usernameLogin)
                            ->first();
                        
                        if ($pegawai) {
                            $nipPegawai = $pegawai->nik;
                            Log::info('NIP pegawai ditemukan untuk pemeriksaan_ranap', [
                                'nik' => $pegawai->nik,
                                'nama' => $pegawai->nama
                            ]);
                        } else {
                            // Coba cari di tabel user
                            $user = DB::table('user')
                                ->where('id_user', $usernameLogin)
                                ->first();
                                
                            if ($user && !empty($user->nip)) {
                                $nipPegawai = $user->nip;
                                Log::info('NIP pegawai ditemukan dari tabel user untuk pemeriksaan_ranap', [
                                    'nip' => $user->nip
                                ]);
                            } else {
                                Log::warning('NIP pegawai tidak ditemukan untuk username (pemeriksaan_ranap)', [
                                    'username' => $usernameLogin
                                ]);
                            }
                        }
                    }
                }
                
                // Jika tidak ditemukan NIP pegawai, gunakan kd_dokter dari permintaan atau dari reg_periksa
                $nip = $nipPegawai;
                if (empty($nip)) {
                    // Dapatkan dokter dari data request atau reg_periksa
                    $dokter = $data['kd_dokter'] ?? '';
                    if (empty($dokter)) {
                        $dokter = DB::table('reg_periksa')
                            ->where('no_rawat', $data['no_rawat'])
                            ->value('kd_dokter');
                        
                        // Jika masih kosong, gunakan default dokter
                        if (empty($dokter)) {
                            $dokter = DB::table('dokter')
                                ->orderBy('kd_dokter', 'asc')
                                ->value('kd_dokter') ?? 'D0000001';
                        }
                    }
                    
                    $nip = $dokter;
                }
                
                $pemeriksaanData = [
                    'no_rawat' => substr($data['no_rawat'], 0, 17),
                    'tgl_perawatan' => $tglDaftarDB,
                    'jam_rawat' => date('H:i:s'),
                    'suhu_tubuh' => substr($data['suhu_tubuh'] ?? '', 0, 5),
                    'tensi' => substr(($data['sistole'].'/'.$data['diastole']), 0, 8),
                    'nadi' => substr($data['heartRate'] ?? '', 0, 3),
                    'respirasi' => substr($data['respRate'] ?? '', 0, 3),
                    'tinggi' => substr($data['tinggiBadan'] ?? '', 0, 5),
                    'berat' => substr($data['beratBadan'] ?? '', 0, 5),
                    'spo2' => substr($data['spo2'] ?? '', 0, 3),
                    'gcs' => substr($data['gcs'] ?? '', 0, 10),
                    'kesadaran' => $data['kesadaran'] ?? 'Compos Mentis',
                    'keluhan' => substr($data['keluhan'] ?? 'Tidak Ada', 0, 2000),
                    'pemeriksaan' => substr($data['pemeriksaan'] ?? '', 0, 2000),
                    'alergi' => substr(isset($data['alergi']) ? 'Makanan : ' . ($data['alergiMakanan'] ?? '-') . ', Udara : ' . ($data['alergiUdara'] ?? '-') . ', Obat : ' . ($data['alergiObat'] ?? '-') : '', 0, 80),
                    'lingkar_perut' => substr($data['lingkar_perut'] ?? $data['lingkarPerut'] ?? '', 0, 5),
                    'rtl' => substr(isset($data['terapiObat']) || isset($data['terapiNonObat']) ? 'Terapi Obat : ' . ($data['terapiObat'] ?? '-') . ', Terapi Non Obat : ' . ($data['terapiNonObat'] ?? '-') . ', BMHP : ' . ($data['BMHP'] ?? '-') : '', 0, 2000),
                    'penilaian' => substr($data['prognosa'] ?? '', 0, 2000),
                    'instruksi' => substr($data['instruksi'] ?? '', 0, 2000),
                    'evaluasi' => substr($data['evaluasi'] ?? '', 0, 2000),
                    'nip' => substr($nip, 0, 20)
                ];
                
                Log::info('Data pemeriksaan_ranap yang akan disimpan', [
                    'no_rawat' => $data['no_rawat'],
                    'nip' => $nip,
                    'tgl_perawatan' => $tglDaftarDB
                ]);
                
                try {
                    if ($cekPemeriksaan) {
                        // Update pemeriksaan yang sudah ada
                        DB::table('pemeriksaan_ranap')
                            ->where('no_rawat', $data['no_rawat'])
                            ->where('tgl_perawatan', $tglDaftarDB)
                            ->update($pemeriksaanData);
                            
                        Log::info('Update pemeriksaan_ranap berhasil', [
                                'no_rawat' => $data['no_rawat']
                            ]);
                    } else {
                        // Insert pemeriksaan baru
                        DB::table('pemeriksaan_ranap')->insert($pemeriksaanData);
                        
                        Log::info('Insert pemeriksaan_ranap berhasil', [
                            'no_rawat' => $data['no_rawat']
                        ]);
                    }
                } catch (\Exception $pemeriksaanError) {
                    Log::error('Gagal menyimpan ke pemeriksaan_ranap', [
                        'error' => $pemeriksaanError->getMessage(),
                        'no_rawat' => $data['no_rawat']
                    ]);
                    // Tidak throw exception agar proses tetap berlanjut
                }
            }
            
            DB::commit();
            Log::info('PCare Pendaftaran berhasil disimpan ke database', [
                'no_rawat' => $data['no_rawat'],
                'no_rkm_medis' => $data['no_rkm_medis'],
                'noUrut' => $noUrut,
                'jenis_rawat' => $jenisRawat
            ]);
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan pendaftaran PCare ke database', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}


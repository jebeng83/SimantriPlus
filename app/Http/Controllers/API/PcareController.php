<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            if (\Cache::has($cacheKey)) {
                Log::info('PCare Get Peserta From Cache', ['noKartu' => $noKartu]);
                return response()->json(\Cache::get($cacheKey));
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
                \Cache::put($cacheKey, $response, now()->addHours(6));
                
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
            if (\Cache::has($cacheKey)) {
                Log::info('PCare Get Peserta By NIK From Cache', ['nik' => $nik]);
                return response()->json(\Cache::get($cacheKey));
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
                \Cache::put($cacheKey, $response, now()->addHours(6));
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
     * Melakukan pendaftaran kunjungan ke PCare
     */
    public function addPendaftaran(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'tglDaftar' => 'required|string',
                'noKartu' => 'required|string',
                'keluhan' => 'nullable|string',
                'kdProviderPeserta' => 'nullable|string',
                'kdTkp' => 'required|string',
                'noUrut' => 'nullable|integer',
                'kdPoli' => 'required|string',
                'kunjSakit' => 'required|boolean',
                'sistole' => 'nullable|integer',
                'diastole' => 'nullable|integer',
                'beratBadan' => 'nullable|integer',
                'tinggiBadan' => 'nullable|integer',
                'respRate' => 'nullable|integer',
                'heartRate' => 'nullable|integer',
                'lingkarPerut' => 'nullable|integer',
                'kdKelompokSehat' => 'nullable|string',
                'kdStatusPulang' => 'nullable|string',
                'tglPulang' => 'nullable|string',
                'kdDokter' => 'nullable|string',
                'kdDiag1' => 'nullable|string',
                'kdDiag2' => 'nullable|string',
                'kdDiag3' => 'nullable|string',
                'rujukBalik' => 'nullable|integer',
                'kdSadar' => 'nullable|string',
            ]);

            // Validasi format tanggal (harus DD-MM-YYYY)
            if (!preg_match('/^\d{2}-\d{2}-\d{4}$/', $validatedData['tglDaftar'])) {
                return response()->json([
                    'metaData' => [
                        'code' => 422,
                        'message' => 'Format tanggal harus DD-MM-YYYY'
                    ],
                    'response' => null
                ], 422);
            }

            // Validasi format nomor kartu (harus 13 digit)
            if (!preg_match('/^\d{13}$/', $validatedData['noKartu'])) {
                return response()->json([
                    'metaData' => [
                        'code' => 422,
                        'message' => 'Nomor kartu harus 13 digit'
                    ],
                    'response' => null
                ], 422);
            }

            // Dapatkan kode dokter dari tabel maping_dokter_pcare jika tidak disediakan
            $kdDokter = $validatedData['kdDokter'] ?? null;
            
            if (empty($kdDokter)) {
                try {
                    // Ambil kode dokter default dari tabel maping
                    $dokter = \DB::table('maping_dokter_pcare')->first();
                    if ($dokter) {
                        $kdDokter = $dokter->kd_dokter_pcare;
                        Log::info('PCare Pendaftaran Dokter', [
                            'dokter_id' => $dokter->kd_dokter,
                            'dokter_pcare' => $kdDokter
                        ]);
                    } else {
                        throw new \Exception('Tidak ada dokter yang terdaftar di PCare');
                    }
                } catch (\Exception $e) {
                    Log::error('PCare Pendaftaran Error - Database', [
                        'message' => $e->getMessage()
                    ]);
                    throw new \Exception('Gagal mendapatkan data dokter: ' . $e->getMessage());
                }
            }

            // Endpoint untuk pendaftaran
            $endpoint = 'pendaftaran';

            // Siapkan data pendaftaran
            $pendaftaranData = [
                "kdProviderPeserta" => $validatedData['kdProviderPeserta'] ?? env('BPJS_PCARE_KODE_PPK', '11251616'),
                "tglDaftar" => $validatedData['tglDaftar'],
                "noKartu" => $validatedData['noKartu'],
                "kdPoli" => $validatedData['kdPoli'] ?? '021',
                "keluhan" => $validatedData['keluhan'] ?? 'Konsultasi Kesehatan',
                "kunjSakit" => $validatedData['kunjSakit'] ?? false,
                "sistole" => isset($validatedData['sistole']) ? (int)$validatedData['sistole'] : 0,
                "diastole" => isset($validatedData['diastole']) ? (int)$validatedData['diastole'] : 0,
                "beratBadan" => isset($validatedData['beratBadan']) ? (int)$validatedData['beratBadan'] : 0,
                "tinggiBadan" => isset($validatedData['tinggiBadan']) ? (int)$validatedData['tinggiBadan'] : 0,
                "respRate" => isset($validatedData['respRate']) ? (int)$validatedData['respRate'] : 0,
                "heartRate" => isset($validatedData['heartRate']) ? (int)$validatedData['heartRate'] : 0,
                "lingkarPerut" => isset($validatedData['lingkarPerut']) ? (int)$validatedData['lingkarPerut'] : 0,
                "rujukBalik" => $validatedData['rujukBalik'] ?? 0,
                "kdTkp" => $validatedData['kdTkp'] ?? '10',
                "kdDokter" => $kdDokter,
                "kdSadar" => $validatedData['kdSadar'] ?? "01",
            ];

            // Log data pendaftaran untuk debugging
            Log::info('PCare Pendaftaran Request Data', [
                'request' => $pendaftaranData,
            ]);

            // Kirim request ke PCare API dengan content-type: text/plain
            $response = $this->requestPcare($endpoint, 'POST', $pendaftaranData, 'text/plain');

            // Log response untuk debugging
            Log::info('PCare Pendaftaran Response', [
                'status' => isset($response['metaData']) ? $response['metaData']['code'] : 'unknown',
                'message' => isset($response['metaData']) ? $response['metaData']['message'] : 'unknown',
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            // Cek jika response menunjukkan kesalahan
            if (isset($response['metaData']) && $response['metaData']['code'] != 200 && $response['metaData']['code'] != 201) {
                Log::warning('PCare Pendaftaran Failed', [
                    'response' => $response,
                    'request' => $pendaftaranData
                ]);
            }

            return response()->json($response);
        } catch (ValidationException $e) {
            Log::error('PCare Pendaftaran Validation Error', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'metaData' => [
                    'code' => 422,
                    'message' => 'Validation Error',
                ],
                'response' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('PCare Pendaftaran Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Error: ' . $e->getMessage(),
                ],
                'response' => null
            ], 500);
        }
    }
} 


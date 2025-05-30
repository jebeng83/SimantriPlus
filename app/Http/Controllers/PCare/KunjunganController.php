<?php

namespace App\Http\Controllers\PCare;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use AamDsam\Bpjs\PCare;

class KunjunganController extends Controller
{
    protected $config;
    protected $maxRetries = 3; // Jumlah maksimal percobaan
    protected $retryDelay = 1000; // Delay dalam milidetik (1 detik)

    public function __construct()
    {
        $this->config = [
            'base_url' => env('BPJS_PCARE_BASE_URL', 'https://apijkn.bpjs-kesehatan.go.id/pcare-rest'),
            'service_name' => 'kunjungan/v1',
            'username' => env('BPJS_PCARE_USER', '11251616'),
            'password' => env('BPJS_PCARE_PASS', 'Pcare153#'),
            'cons_id' => env('BPJS_PCARE_CONS_ID', '7925'),
            'cons_pwd' => env('BPJS_PCARE_CONS_PWD', '2eF2C8E837'),
            'user_key' => env('BPJS_PCARE_USER_KEY', '403bf17ddf158790afcfe1e8dd682a67'),
            'kode_ppk' => env('BPJS_PCARE_KODE_PPK', '11251616'),
            'app_code' => env('BPJS_PCARE_APP_CODE', '095')
        ];
    }

    protected function sendToPCare($dataKunjungan, $attempt = 1)
    {
        try {
            // Format tanggal sesuai spesifikasi PCare (dd-mm-yyyy)
            $tglDaftar = date('d-m-Y', strtotime(str_replace('-', '/', $dataKunjungan['tglDaftar'])));
            $tglPulang = date('d-m-Y', strtotime(str_replace('-', '/', $dataKunjungan['tglPulang'])));

            // Siapkan data sesuai format PCare
            $requestData = [
                'noKunjungan' => null, // PCare akan generate
                'noKartu' => $dataKunjungan['noKartu'],
                'tglDaftar' => $tglDaftar,
                'tglPulang' => $tglPulang,
                'ppkPelayanan' => $this->config['kode_ppk'],
                'kdPoli' => $dataKunjungan['kdPoli'],
                'keluhan' => $dataKunjungan['keluhan'],
                'kdSadar' => $dataKunjungan['kdSadar'],
                'sistole' => intval($dataKunjungan['sistole']),
                'diastole' => intval($dataKunjungan['diastole']),
                'beratBadan' => intval($dataKunjungan['beratBadan']),
                'tinggiBadan' => intval($dataKunjungan['tinggiBadan']),
                'respRate' => intval($dataKunjungan['respRate']),
                'heartRate' => intval($dataKunjungan['heartRate']),
                'lingkarPerut' => intval($dataKunjungan['lingkarPerut']),
                'statusPulang' => '3', // Berobat Jalan
                'kdDokter' => $dataKunjungan['kdDokter'],
                'kdDiag1' => $dataKunjungan['kdDiag1'],
                'kdDiag2' => $dataKunjungan['kdDiag2'],
                'kdDiag3' => $dataKunjungan['kdDiag3'],
                'kdAlergi' => $dataKunjungan['KdAlergiMakanan'],
                'kdAlergiUdara' => $dataKunjungan['KdAlergiUdara'],
                'kdAlergiObat' => $dataKunjungan['KdAlergiObat'],
                'kdPrognosa' => $dataKunjungan['KdPrognosa'],
                'terapi' => $dataKunjungan['terapi'] ?? '-',
                'terapiNonFarmakologi' => $dataKunjungan['terapi_non_obat'],
                'bmhp' => $dataKunjungan['bmhp'],
                'suhu' => floatval($dataKunjungan['suhu'])
            ];

            Log::info('Data yang akan dikirim ke PCare:', [
                'request_data' => $requestData,
                'attempt' => $attempt
            ]);

            $pcare = new PCare\Kunjungan($this->config);
            $rawResponse = $pcare->store($requestData);

            Log::info('Response mentah dari PCare:', [
                'raw_response' => $rawResponse,
                'response_type' => gettype($rawResponse),
                'attempt' => $attempt
            ]);

            // Jika response adalah string, coba parse sebagai JSON
            if (is_string($rawResponse)) {
                try {
                    $response = json_decode($rawResponse, true, 512, JSON_THROW_ON_ERROR);
                    Log::info('Response setelah parsing JSON:', [
                        'parsed_response' => $response
                    ]);
                } catch (\JsonException $e) {
                    Log::error('Gagal parsing response JSON:', [
                        'error' => $e->getMessage(),
                        'raw_response' => $rawResponse
                    ]);
                    throw new \Exception('Gagal parsing response dari PCare: ' . $e->getMessage());
                }
            } else {
                $response = $rawResponse;
                Log::info('Response bukan string, menggunakan langsung:', [
                    'response' => $response
                ]);
            }

            // Validasi response
            if (!isset($response['metaData']['code'])) {
                Log::error('Response tidak valid:', [
                    'response' => $response
                ]);
                throw new \Exception('Response tidak valid dari PCare: metadata code tidak ditemukan');
            }

            Log::info('Metadata response:', [
                'code' => $response['metaData']['code'],
                'message' => $response['metaData']['message'] ?? 'No message'
            ]);

            // Cek apakah response sukses
            if ($response['metaData']['code'] == 201 || $response['metaData']['code'] == 200) {
                // Response sukses, coba dapatkan noKunjungan
                $noKunjungan = null;

                // Log seluruh response untuk debugging
                Log::info('Response sukses, mencari noKunjungan:', [
                    'full_response' => $response
                ]);

                // Coba ambil dari berbagai kemungkinan format response
                if (isset($response['response']['message']) && is_string($response['response']['message'])) {
                    // Coba ekstrak noKunjungan dari message jika berformat "Kunjungan #12345678 berhasil disimpan"
                    if (preg_match('/Kunjungan #(\d+)/', $response['response']['message'], $matches)) {
                        $noKunjungan = $matches[1];
                    } else {
                        $noKunjungan = $response['response']['message'];
                    }
                } elseif (isset($response['response']['noKunjungan'])) {
                    $noKunjungan = $response['response']['noKunjungan'];
                } elseif (isset($response['response']) && is_string($response['response'])) {
                    if (preg_match('/Kunjungan #(\d+)/', $response['response'], $matches)) {
                        $noKunjungan = $matches[1];
                    } else {
                        $noKunjungan = $response['response'];
                    }
                }

                Log::info('Hasil ekstraksi noKunjungan:', [
                    'noKunjungan' => $noKunjungan
                ]);

                // Jika tidak ada noKunjungan, throw exception dengan detail
                if (empty($noKunjungan)) {
                    Log::error('Nomor kunjungan tidak ditemukan:', [
                        'response' => $response
                    ]);
                    throw new \Exception('Nomor kunjungan tidak ditemukan dalam response BPJS. Response: ' . json_encode($response));
                }

                // Format response sesuai spesifikasi
                return [
                    'success' => true,
                    'data' => [
                        'noKunjungan' => $noKunjungan
                    ],
                    'metaData' => [
                        'message' => 'Data kunjungan berhasil disimpan',
                        'code' => $response['metaData']['code']
                    ],
                    'response_bpjs' => $response // Tambahkan response asli dari BPJS
                ];
            }

            // Jika response tidak sukses
            Log::error('Response tidak sukses dari PCare:', [
                'response' => $response
            ]);
            throw new \Exception($response['metaData']['message'] ?? 'Gagal mengirim data ke PCare: ' . json_encode($response));

        } catch (\Exception $e) {
            Log::error('Error saat mengirim data ke PCare (Percobaan ke-' . $attempt . ')', [
                'error' => $e->getMessage(),
                'data' => $dataKunjungan,
                'trace' => $e->getTraceAsString()
            ]);

            if ($attempt < $this->maxRetries) {
                $delaySeconds = ($this->retryDelay/1000);
                Log::info("Mencoba kembali dalam {$delaySeconds} detik... (Percobaan ke-" . ($attempt + 1) . ")");
                usleep($this->retryDelay * 1000);
                return $this->sendToPCare($dataKunjungan, $attempt + 1);
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'metaData' => [
                    'message' => $e->getMessage(),
                    'code' => 500
                ]
            ];
        }
    }

    public function create(Request $request)
    {
        try {
            Log::info('Memulai proses create kunjungan', [
                'no_rawat' => $request->no_rawat
            ]);

            DB::beginTransaction();
            try {
                // Ambil data reg_periksa
                $regPeriksa = DB::table('reg_periksa as rp')
                    ->leftJoin('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                    ->leftJoin('dokter as d', 'rp.kd_dokter', '=', 'd.kd_dokter')
                    ->leftJoin('maping_dokter_pcare as mdp', 'd.kd_dokter', '=', 'mdp.kd_dokter')
                    ->leftJoin('pemeriksaan_ralan as pr', 'rp.no_rawat', '=', 'pr.no_rawat')
                    ->select([
                        'rp.*', 'p.nm_pasien', 'mdp.kd_dokter_pcare',
                        'pr.tinggi', 'pr.berat', 'pr.tensi', 'pr.respirasi',
                        'pr.nadi', 'pr.suhu_tubuh', 'pr.lingkar_perut'
                    ])
                    ->where('rp.no_rawat', $request->no_rawat)
                    ->first();

                if (!$regPeriksa) {
                    throw new \Exception('Data reg_periksa tidak ditemukan');
                }

                // Parse tekanan darah
                $tensi = explode('/', $regPeriksa->tensi ?? '0/0');
                $sistole = $tensi[0] ?? '0';
                $diastole = $tensi[1] ?? '0';

                // Siapkan data kunjungan
                $dataKunjungan = [
                    'no_rawat' => $request->no_rawat,
                    'noKartu' => $request->data_kunjungan['noKartu'],
                    'tglDaftar' => $request->data_kunjungan['tglDaftar'],
                    'kdPoli' => $request->data_kunjungan['kdPoli'],
                    'keluhan' => $request->data_kunjungan['keluhan'],
                    'kdSadar' => $request->data_kunjungan['kdSadar'],
                    'sistole' => $sistole,
                    'diastole' => $diastole,
                    'beratBadan' => $regPeriksa->berat ?? 0,
                    'tinggiBadan' => $regPeriksa->tinggi ?? 0,
                    'respRate' => $regPeriksa->respirasi ?? 0,
                    'heartRate' => $regPeriksa->nadi ?? 0,
                    'lingkarPerut' => $regPeriksa->lingkar_perut ?? 0,
                    'tglPulang' => $request->data_kunjungan['tglPulang'],
                    'kdDokter' => $regPeriksa->kd_dokter_pcare,
                    'kdDiag1' => $request->data_kunjungan['kdDiag1'],
                    'kdDiag2' => $request->data_kunjungan['kdDiag2'] ?? null,
                    'kdDiag3' => $request->data_kunjungan['kdDiag3'] ?? null,
                    'KdAlergiMakanan' => $request->data_kunjungan['KdAlergiMakanan'],
                    'KdAlergiUdara' => $request->data_kunjungan['KdAlergiUdara'],
                    'KdAlergiObat' => $request->data_kunjungan['KdAlergiObat'],
                    'KdPrognosa' => $request->data_kunjungan['KdPrognosa'],
                    'terapi' => $request->data_kunjungan['terapi'],
                    'terapi_non_obat' => $request->data_kunjungan['terapi_non_obat'],
                    'bmhp' => $request->data_kunjungan['bmhp'],
                    'suhu' => $regPeriksa->suhu_tubuh ?? '36.5'
                ];

                // Kirim data ke PCare
                $response = $this->sendToPCare($dataKunjungan);

                // Ambil nomor kunjungan dari response
                $noKunjungan = null;
                if (isset($response['data']['noKunjungan'])) {
                    $noKunjungan = $response['data']['noKunjungan'];
                }

                // Update atau insert ke database lokal
                $dataToSave = [
                    'noKunjungan' => $noKunjungan,
                    'tglDaftar' => date('Y-m-d', strtotime(str_replace('-', '/', $dataKunjungan['tglDaftar']))),
                    'no_rkm_medis' => $regPeriksa->no_rkm_medis,
                    'nm_pasien' => $regPeriksa->nm_pasien,
                    'noKartu' => $dataKunjungan['noKartu'],
                    'kdPoli' => $dataKunjungan['kdPoli'],
                    'nmPoli' => 'POLI UMUM',
                    'keluhan' => $dataKunjungan['keluhan'],
                    'kdSadar' => $dataKunjungan['kdSadar'],
                    'nmSadar' => 'Compos Mentis',
                    'sistole' => $dataKunjungan['sistole'],
                    'diastole' => $dataKunjungan['diastole'],
                    'beratBadan' => $dataKunjungan['beratBadan'],
                    'tinggiBadan' => $dataKunjungan['tinggiBadan'],
                    'respRate' => $dataKunjungan['respRate'],
                    'heartRate' => $dataKunjungan['heartRate'],
                    'lingkarPerut' => $dataKunjungan['lingkarPerut'],
                    'terapi' => $dataKunjungan['terapi'],
                    'kdStatusPulang' => '3',
                    'nmStatusPulang' => 'Berobat Jalan',
                    'tglPulang' => date('Y-m-d', strtotime(str_replace('-', '/', $dataKunjungan['tglPulang']))),
                    'kdDokter' => $dataKunjungan['kdDokter'],
                    'nmDokter' => 'dr. BUDI',
                    'kdDiag1' => $dataKunjungan['kdDiag1'],
                    'nmDiag1' => 'Demam Tifoid',
                    'kdDiag2' => $dataKunjungan['kdDiag2'],
                    'nmDiag2' => null,
                    'kdDiag3' => $dataKunjungan['kdDiag3'],
                    'nmDiag3' => null,
                    'status' => $noKunjungan ? 'Terkirim' : 'Gagal',
                    'KdAlergiMakanan' => $dataKunjungan['KdAlergiMakanan'],
                    'NmAlergiMakanan' => 'TIDAK ADA ALERGI MAKANAN',
                    'KdAlergiUdara' => $dataKunjungan['KdAlergiUdara'],
                    'NmAlergiUdara' => 'TIDAK ADA ALERGI UDARA',
                    'KdAlergiObat' => $dataKunjungan['KdAlergiObat'],
                    'NmAlergiObat' => 'TIDAK ADA ALERGI OBAT',
                    'KdPrognosa' => $dataKunjungan['KdPrognosa'],
                    'NmPrognosa' => 'BAIK',
                    'terapi_non_obat' => $dataKunjungan['terapi_non_obat'],
                    'bmhp' => $dataKunjungan['bmhp']
                ];

                // Update jika sudah ada, insert jika belum
                DB::table('pcare_kunjungan_umum')
                    ->updateOrInsert(
                        ['no_rawat' => $request->no_rawat],
                        $dataToSave
                    );

                if ($noKunjungan) {
                    // Update status pendaftaran menjadi "Sudah Dilayani"
                    DB::table('pcare_pendaftaran')
                        ->where('no_rawat', $request->no_rawat)
                        ->update(['status' => 'Sudah Dilayani']);
                }

                DB::commit();

                Log::info('Proses create kunjungan selesai', [
                    'no_rawat' => $request->no_rawat,
                    'response' => $response
                ]);

                return response()->json($response);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error dalam create kunjungan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'response' => [
                    'field' => 'error',
                    'message' => $e->getMessage()
                ],
                'metaData' => [
                    'message' => 'Gagal mengirim data ke PCare',
                    'code' => 500
                ]
            ], 500);
        }
    }
} 
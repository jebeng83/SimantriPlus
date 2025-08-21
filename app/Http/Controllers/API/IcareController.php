<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\IcareTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\MapingDokterPcare;

class IcareController extends Controller
{
    use IcareTrait;

    /**
     * Mendapatkan data peserta berdasarkan nomor kartu
     * URL: {Base URL}/peserta/{Parameter 1}
     * Parameter 1: Nomor Kartu Peserta
     */
    public function getPeserta($noKartu)
    {
        try {
            // Perbaiki format nomor kartu (hapus non-digit, hapus leading zeros, padding hingga 13 digit)
            $noKartu = preg_replace('/[^0-9]/', '', $noKartu); // Hapus karakter non-digit
            
            // Jika lebih dari 13 digit, ambil 13 digit terakhir
            if (strlen($noKartu) > 13) {
                $noKartu = substr($noKartu, -13);
                Log::info('ICare Get Peserta - Nomor kartu terlalu panjang, dipotong menjadi 13 digit terakhir', [
                    'noKartu' => $noKartu
                ]);
            }
            
            $noKartuClean = ltrim($noKartu, '0'); // Hapus leading zeros
            $noKartu = str_pad($noKartuClean, 13, '0', STR_PAD_LEFT); // Padding hingga 13 digit
            
            // Log nomor kartu yang sudah diperbaiki
            Log::info('ICare Get Peserta Format Fix', [
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

            // Log request
            Log::info('ICare Get Peserta Request', [
                'noKartu' => $noKartu,
                'timestamp' => now()
            ]);

            // Debugging info - tampilkan semua variabel environment yang diperlukan
            Log::info('ICare Environment Variables', [
                'base_url' => env('BPJS_ICARE_BASE_URL'),
                'cons_id' => env('BPJS_ICARE_CONS_ID'),
                'user_key' => env('BPJS_ICARE_USER_KEY'),
                'username' => env('BPJS_ICARE_USER'),
                'has_password' => !empty(env('BPJS_ICARE_PASS')),
                'has_cons_pwd' => !empty(env('BPJS_ICARE_CONS_PWD'))
            ]);

            // Format endpoint untuk iCare
            $endpoint = "peserta/{$noKartu}";

            // Kirim request ke iCare
            $response = $this->requestIcare($endpoint);

            // Debug response
            Log::info('ICare Response Debug', [
                'response' => $response
            ]);

            // Cek response
            if (isset($response['metaData']) && $response['metaData']['code'] == 200) {
                // Format response sesuai dengan contoh
                $peserta = $response['response'];
                
                // Data yang ditampilkan dengan format yang sama
                $formattedData = [
                    ['No.Kartu', ': '.$peserta['noKartu']],
                    ['Nama', ': '.$peserta['nama']],
                    ['Hubungan Keluarga', ': '.$peserta['hubunganKeluarga']],
                    ['Jenis Kelamin', ': '.str_replace(['L', 'P'], ['Laki-Laki', 'Perempuan'], $peserta['sex'])],
                    ['Tanggal Lahir', ': '.$peserta['tglLahir']],
                    ['Mulai Aktif', ': '.$peserta['tglMulaiAktif']],
                    ['Akhir Berlaku', ': '.$peserta['tglAkhirBerlaku']],
                    ['Provider Umum', ':'],
                    ['       Kode Provider', ': '.$peserta['kdProviderPst']['kdProvider']],
                    ['       Nama Provider', ': '.$peserta['kdProviderPst']['nmProvider']],
                    ['Provider Gigi', ':'],
                    ['       Kode Provider', ': '.$peserta['kdProviderGigi']['kdProvider']],
                    ['       Nama Provider', ': '.$peserta['kdProviderGigi']['nmProvider']],
                    ['Kelas Tanggungan', ':'],
                    ['       Kode Kelas', ': '.$peserta['jnsKelas']['kode']],
                    ['       Nama Kelas', ': '.$peserta['jnsKelas']['nama']],
                    ['Jenis Peserta', ':'],
                    ['       Kode Jenis', ': '.$peserta['jnsPeserta']['kode']],
                    ['       Nama Jenis', ': '.$peserta['jnsPeserta']['nama']],
                    ['Golongan Darah', ': '.$peserta['golDarah']],
                    ['Nomor HP', ': '.$peserta['noHP']],
                    ['Nomor KTP', ': '.$peserta['noKTP']],
                    ['Peserta Prolanis', ': '.$peserta['pstProl']],
                    ['Peserta PRB', ': '.$peserta['pstPrb']],
                    ['Status', ': '.$peserta['ketAktif']],
                    ['Asuransi/COB', ':'],
                    ['       Kode Asuransi', ': '.$peserta['asuransi']['kdAsuransi']],
                    ['       Nama Asuransi', ': '.$peserta['asuransi']['nmAsuransi']],
                    ['       Nomer Asuransi', ': '.$peserta['asuransi']['noAsuransi']],
                    ['       COB', ': '.$peserta['asuransi']['cob']],
                    ['Tunggakan', ': '.$peserta['tunggakan']]
                ];

                $response['formattedData'] = $formattedData;
                return response()->json($response);
            }

            // Debug response error
            Log::warning('ICare Response Error', [
                'response' => $response,
                'endpoint' => $endpoint
            ]);

            return response()->json($response, 400);

        } catch (\Exception $e) {
            Log::error('ICare Get Peserta Error', [
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

            // Format endpoint untuk iCare
            $endpoint = "peserta/nik/{$nik}";

            // Kirim request ke iCare
            $response = $this->requestIcare($endpoint);

            // Cek response
            if (isset($response['metaData']) && $response['metaData']['code'] == 200) {
                return response()->json($response);
            }

            return response()->json($response, 400);

        } catch (\Exception $e) {
            Log::error('ICare Get Peserta By NIK Error', [
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
     * Mendapatkan riwayat peserta
     */
    public function getRiwayatPeserta($noKartu)
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

            // Format endpoint untuk iCare
            $endpoint = "peserta/{$noKartu}/riwayat";

            // Kirim request ke iCare
            $response = $this->requestIcare($endpoint);

            // Cek response
            if (isset($response['metaData']) && $response['metaData']['code'] == 200) {
                return response()->json($response);
            }

            return response()->json($response, 400);

        } catch (\Exception $e) {
            Log::error('ICare Get Riwayat Peserta Error', [
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
     * Mendapatkan URL riwayat pasien BPJS ICare
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateIcare(Request $request)
    {
        try {
            // Validasi request
            $request->validate([
                'param' => 'required'
            ]);

            $noKartu = $request->input('param');
            
            // Prioritaskan pengambilan kode dokter dari tabel maping_dokter_pcare
            $kodeDokter = null;
            
            // Coba ambil kode dokter dari request
            $kodeDokterRequest = $request->input('kodedokter');
            
            if (!empty($kodeDokterRequest)) {
                // Cek apakah kode dokter ada di tabel mapping
                $dokterMapping = MapingDokterPcare::where('kd_dokter', $kodeDokterRequest)->first();
                
                if ($dokterMapping) {
                    $kodeDokter = $dokterMapping->kd_dokter_pcare;
                    Log::info('Kode dokter diambil dari request dan ditemukan di mapping', [
                        'kd_dokter' => $kodeDokterRequest,
                        'kd_dokter_pcare' => $kodeDokter
                    ]);
                }
            }
            
            // Jika belum ada, gunakan kode dari BPJS_ICARE_USER
            if (empty($kodeDokter)) {
                $userBPJS = env('BPJS_ICARE_USER');
                
                if (!empty($userBPJS) && strpos($userBPJS, '-') !== false) {
                    // Format username BPJS biasanya: username-kodedokter
                    $parts = explode('-', $userBPJS);
                    if (isset($parts[1]) && !empty($parts[1])) {
                        // Cari kode dokter dari tabel mapping
                        $dokterMapping = MapingDokterPcare::where('kd_dokter', $parts[1])->first();
                        
                        if ($dokterMapping) {
                            $kodeDokter = $dokterMapping->kd_dokter_pcare;
                            Log::info('Kode dokter diambil dari BPJS_ICARE_USER dan ditemukan di mapping', [
                                'user_bpjs' => $userBPJS,
                                'kd_dokter' => $parts[1],
                                'kd_dokter_pcare' => $kodeDokter
                            ]);
                        } else {
                            // Jika tidak ditemukan di mapping, gunakan kode dari username
                            $kodeDokter = $parts[1];
                            Log::info('Kode dokter diambil dari BPJS_ICARE_USER (tidak ada di mapping)', [
                                'user_bpjs' => $userBPJS,
                                'kd_dokter_pcare' => $kodeDokter
                            ]);
                        }
                    }
                }
            }
            
            // Jika masih belum ada, ambil default dari SISWO (berdasarkan gambar tabel)
            if (empty($kodeDokter)) {
                // Cari kode dokter SISWO dari tabel mapping
                $dokterMapping = MapingDokterPcare::where('kd_dokter', '102')->first();
                
                if ($dokterMapping) {
                    $kodeDokter = $dokterMapping->kd_dokter_pcare;
                    Log::info('Kode dokter diambil dari default SISWO di mapping', [
                        'kd_dokter' => '102',
                        'kd_dokter_pcare' => $kodeDokter
                    ]);
                } else {
                    // Default ke 510429 (kode SISWO dari gambar tabel)
                    $kodeDokter = '510429';
                    Log::info('Kode dokter menggunakan default SISWO', [
                        'kd_dokter_pcare' => $kodeDokter
                    ]);
                }
            }

            // Log request untuk debug
            Log::info('BPJS iCare Input', [
                'raw_input' => $request->all(),
                'final_kode_dokter' => $kodeDokter
            ]);

            // Format endpoint untuk iCare - sesuai dokumentasi
            $endpoint = "validate";

            // Data yang akan dikirim
            $data = [
                'param' => $noKartu,
                'kodedokter' => $kodeDokter
            ];

            // Log request
            Log::info('ICare Validate Request', [
                'url' => env('BPJS_ICARE_BASE_URL') . '/' . $endpoint,
                'data' => $data,
                'timestamp' => now(),
                'config' => [
                    'BPJS_ICARE_BASE_URL' => env('BPJS_ICARE_BASE_URL'),
                    'BPJS_ICARE_CONS_ID' => env('BPJS_ICARE_CONS_ID'),
                    'BPJS_ICARE_USER_KEY' => env('BPJS_ICARE_USER_KEY'),
                    'has_pwd' => !empty(env('BPJS_ICARE_CONS_PWD')),
                    'has_pass' => !empty(env('BPJS_ICARE_PASS')),
                ]
            ]);

            // Kirim request ke iCare
            $response = $this->requestIcare($endpoint, 'POST', $data);

            // Debug response
            Log::info('ICare Validate Response', [
                'response' => $response
            ]);

            // Return response
            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('ICare Validate Error', [
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
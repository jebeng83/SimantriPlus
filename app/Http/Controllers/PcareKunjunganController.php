<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\PcareTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PcareKunjunganController extends Controller
{
    use PcareTrait;

    /**
     * Display the kunjungan data page
     */
    public function index()
    {
        return view('Pcare.kunjungan.index');
    }

    /**
     * Show specific kunjungan data
     */
    public function show($noRawat)
    {
        try {
            // Get kunjungan data from database or API
            $kunjungan = $this->getKunjunganData($noRawat);
            
            if (!$kunjungan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data kunjungan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $kunjungan
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting kunjungan data: ' . $e->getMessage(), [
                'noRawat' => $noRawat,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kirim ulang data kunjungan ke BPJS PCare
     */
    public function kirimUlang(Request $request, $noRawat)
    {
        try {
            // Validate the request
            $request->validate([
                'alasan' => 'required|string|max:255'
            ]);

            // Get kunjungan data
            $kunjungan = $this->getKunjunganData($noRawat);
            
            if (!$kunjungan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data kunjungan tidak ditemukan'
                ], 404);
            }

            // Prepare data for PCare API
            $pcareData = $this->preparePcareKunjunganData($kunjungan);
            
            // Send to PCare API
            $response = $this->sendKunjunganToPcare($pcareData);
            
            if ($response && $response['success']) {
                // Update status in database
                $this->updateKunjunganStatus($noRawat, 'sent', $response);
                
                // Log success
                Log::info('PCare kunjungan kirim ulang berhasil', [
                    'no_rawat' => $noRawat,
                    'noKunjungan' => $response['noKunjungan'],
                    'message' => $response['message']
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Data kunjungan berhasil dikirim ulang ke BPJS PCare',
                    'noKunjungan' => $response['noKunjungan'],
                    'data' => $response
                ]);
            } else {
                // Handle error response
                $errorMessage = 'Gagal mengirim data ke BPJS PCare';
                if ($response && isset($response['message'])) {
                    $errorMessage = $response['message'];
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => $response
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Error kirim ulang kunjungan: ' . $e->getMessage(), [
                'noRawat' => $noRawat,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kirim ulang batch data kunjungan
     */
    public function kirimUlangBatch(Request $request)
    {
        try {
            $request->validate([
                'no_rawat' => 'required|array',
                'no_rawat.*' => 'required|string',
                'alasan' => 'required|string|max:255'
            ]);

            $results = [];
            $successCount = 0;
            $failCount = 0;

            foreach ($request->no_rawat as $noRawat) {
                try {
                    // Get kunjungan data
                    $kunjungan = $this->getKunjunganData($noRawat);
                    
                    if (!$kunjungan) {
                        $results[] = [
                            'no_rawat' => $noRawat,
                            'success' => false,
                            'message' => 'Data tidak ditemukan'
                        ];
                        $failCount++;
                        continue;
                    }

                    // Prepare and send data
                    $pcareData = $this->preparePcareKunjunganData($kunjungan);
                    $response = $this->sendKunjunganToPcare($pcareData);
                    
                    if ($response && $response['success']) {
                        $this->updateKunjunganStatus($noRawat, 'sent', $response);
                        $results[] = [
                            'no_rawat' => $noRawat,
                            'success' => true,
                            'message' => 'Berhasil dikirim',
                            'noKunjungan' => $response['noKunjungan']
                        ];
                        $successCount++;
                    } else {
                        $errorMessage = 'Gagal dikirim ke PCare';
                        if ($response && isset($response['message'])) {
                            $errorMessage = $response['message'];
                        }
                        
                        $results[] = [
                            'no_rawat' => $noRawat,
                            'success' => false,
                            'message' => $errorMessage
                        ];
                        $failCount++;
                    }

                } catch (\Exception $e) {
                    $results[] = [
                        'no_rawat' => $noRawat,
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ];
                    $failCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Batch selesai. Berhasil: {$successCount}, Gagal: {$failCount}",
                'summary' => [
                    'total' => count($request->no_rawat),
                    'success' => $successCount,
                    'failed' => $failCount
                ],
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Error kirim ulang batch: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kunjungan data from database
     */
    private function getKunjunganData($noRawat)
    {
        try {
            $kunjungan = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->leftJoin('maping_dokter_pcare', 'dokter.kd_dokter', '=', 'maping_dokter_pcare.kd_dokter')
                ->where('reg_periksa.no_rawat', $noRawat)
                ->select(
                    'reg_periksa.*',
                    'pasien.nm_pasien',
                    'pasien.no_peserta',
                    'pasien.no_ktp',
                    'pasien.tgl_lahir',
                    'pasien.jk',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter',
                    'maping_dokter_pcare.kd_dokter_pcare'
                )
                ->first();
                
            return $kunjungan;
        } catch (\Exception $e) {
            Log::error('Error getting kunjungan from database: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Prepare kunjungan data for PCare API sesuai format BPJS
     */
    private function preparePcareKunjunganData($kunjungan)
    {
        // Ambil data pemeriksaan
        $pemeriksaanData = DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $kunjungan->no_rawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();
        
        // Ambil data diagnosa utama
        $diagnosaData = DB::table('diagnosa_pasien')
            ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
            ->where('diagnosa_pasien.no_rawat', $kunjungan->no_rawat)
            ->where('diagnosa_pasien.prioritas', '1')
            ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
            ->first();
        
        // Ambil mapping poli PCare
        $mappingPoli = DB::table('maping_poliklinik_pcare')
            ->where('kd_poli_rs', $kunjungan->kd_poli)
            ->first();
        
        $kdPoliPcare = $mappingPoli ? $mappingPoli->kd_poli_pcare : '001';
        
        // Persiapkan data vital signs
        $sistole = 120;
        $diastole = 80;
        if ($pemeriksaanData && !empty($pemeriksaanData->tensi) && strpos($pemeriksaanData->tensi, '/') !== false) {
            $tensiParts = explode('/', $pemeriksaanData->tensi);
            $sistole = (int)trim($tensiParts[0]) ?: 120;
            $diastole = (int)trim($tensiParts[1]) ?: 80;
        }
        
        // Ambil data resep obat
        $terapiObatData = DB::table('resep_obat')
            ->join('resep_dokter', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
            ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
            ->where('resep_obat.no_rawat', $kunjungan->no_rawat)
            ->select('databarang.nama_brng', 'resep_dokter.jml', 'resep_dokter.aturan_pakai')
            ->get();
        
        $terapiObatString = 'Tidak Ada';
        if ($terapiObatData->isNotEmpty()) {
            $terapiObatArray = [];
            foreach ($terapiObatData as $obat) {
                $terapiObatArray[] = $obat->nama_brng . ' ' . $obat->jml . ' [' . $obat->aturan_pakai . ']';
            }
            $terapiObatString = implode(', ', $terapiObatArray);
        }
        
        // Format sesuai BPJS yang berhasil (HTTP 201)
        return [
            'noKunjungan' => '', // Gunakan string kosong instead of null
            'noKartu' => (string)$kunjungan->no_peserta,
            'tglDaftar' => date('d-m-Y', strtotime($kunjungan->tgl_registrasi)),
            'kdPoli' => (string)$kdPoliPcare,
            'keluhan' => (string)($pemeriksaanData->keluhan ?? 'Tidak Ada'),
            'kdSadar' => '04', // Compos Mentis
            'sistole' => (int)$sistole,
            'diastole' => (int)$diastole,
            'beratBadan' => (float)($pemeriksaanData->berat ?? 50),
            'tinggiBadan' => (float)($pemeriksaanData->tinggi ?? 170),
            'respRate' => (int)($pemeriksaanData->respirasi ?? 20),
            'heartRate' => (int)($pemeriksaanData->nadi ?? 80),
            'lingkarPerut' => (float)($pemeriksaanData->lingkar_perut ?? 0),
            'kdStatusPulang' => '3', // Sesuai format BPJS: "3"
            'tglPulang' => date('d-m-Y'),
            'kdDokter' => (string)($kunjungan->kd_dokter_pcare ?? ''),
            'kdDiag1' => (string)($diagnosaData->kd_penyakit ?? 'Z00.0'),
            'kdDiag2' => '', // Gunakan string kosong instead of null
            'kdDiag3' => '', // Gunakan string kosong instead of null
            'kdPoliRujukInternal' => '', // Gunakan string kosong instead of null
            'rujukLanjut' => '', // Gunakan string kosong instead of null
            'kdTacc' => -1, // Sesuai format BPJS: -1
            'alasanTacc' => '', // Gunakan string kosong instead of null
            'anamnesa' => (string)($pemeriksaanData->keluhan ?? 'Tidak Ada'), // Sama dengan keluhan
            'alergiMakan' => '00',
            'alergiUdara' => '00',
            'alergiObat' => '00',
            'kdPrognosa' => '02', // Baik
            'terapiObat' => (string)$terapiObatString,
            'terapiNonObat' => (string)($pemeriksaanData->instruksi ?? 'Edukasi Kesehatan'),
            'bmhp' => 'Tidak Ada',
            'suhu' => (string)($pemeriksaanData->suhu_tubuh ?? '36.5')
        ];
    }

    /**
     * Send kunjungan data to PCare API
     */
    private function sendKunjunganToPcare($data)
    {
        try {
            Log::info('Sending kunjungan to PCare', ['data' => $data]);
            
            $endpoint = '/kunjungan/v1';
            $response = $this->requestPcare($endpoint, 'POST', $data);
            
            Log::info('PCare response received', ['response' => $response]);
            
            if ($response && isset($response['metaData'])) {
                $metaData = $response['metaData'];
                
                Log::info('PCare metaData', ['metaData' => $metaData]);
                
                // Handle successful response (201 Created)
                if ($metaData['code'] == '201' && isset($response['response'])) {
                    // Response sudah didekripsi otomatis oleh requestPcare
                    $responseData = $response['response'];
                    
                    Log::info('PCare response data', ['responseData' => $responseData]);
                    
                    if (is_array($responseData) && count($responseData) > 0) {
                        $kunjunganResponse = $responseData[0];
                        if (isset($kunjunganResponse['noKunjungan'])) {
                            Log::info('PCare Kunjungan berhasil dikirim', [
                                'noKunjungan' => $kunjunganResponse['noKunjungan'],
                                'message' => $metaData['message']
                            ]);
                            return [
                                'success' => true,
                                'noKunjungan' => $kunjunganResponse['noKunjungan'],
                                'message' => $metaData['message']
                            ];
                        }
                    }
                }
                
                // Handle error responses
                Log::error('PCare Kunjungan error', [
                    'code' => $metaData['code'],
                    'message' => $metaData['message']
                ]);
                
                return [
                    'success' => false,
                    'code' => $metaData['code'],
                    'message' => $metaData['message']
                ];
            }
            
            Log::error('PCare response invalid', ['response' => $response]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error sending to PCare: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update kunjungan status in database
     */
    private function updateKunjunganStatus($noRawat, $status, $response = null)
    {
        try {
            // This should be implemented based on your database structure
            DB::table('reg_periksa')
                ->where('no_rawat', $noRawat)
                ->update([
                    'status_pcare' => $status,
                    'response_pcare' => json_encode($response),
                    'updated_at' => now()
                ]);
        } catch (\Exception $e) {
            Log::error('Error updating kunjungan status: ' . $e->getMessage());
        }
    }
}
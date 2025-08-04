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
            
            if ($response && isset($response['metaData']) && $response['metaData']['code'] == 201) {
                // Update status in database
                $this->updateKunjunganStatus($noRawat, 'sent', $response);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Data kunjungan berhasil dikirim ulang ke BPJS PCare',
                    'data' => $response
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim data ke BPJS PCare',
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
                    
                    if ($response && isset($response['metaData']) && $response['metaData']['code'] == 201) {
                        $this->updateKunjunganStatus($noRawat, 'sent', $response);
                        $results[] = [
                            'no_rawat' => $noRawat,
                            'success' => true,
                            'message' => 'Berhasil dikirim'
                        ];
                        $successCount++;
                    } else {
                        $results[] = [
                            'no_rawat' => $noRawat,
                            'success' => false,
                            'message' => 'Gagal dikirim ke PCare'
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
        // This should be implemented based on your database structure
        // Example implementation:
        try {
            $kunjungan = DB::table('reg_periksa')
                ->where('no_rawat', $noRawat)
                ->first();
                
            return $kunjungan;
        } catch (\Exception $e) {
            Log::error('Error getting kunjungan from database: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Prepare kunjungan data for PCare API
     */
    private function preparePcareKunjunganData($kunjungan)
    {
        // This should be implemented based on PCare API requirements
        // Example implementation:
        return [
            'noKunjungan' => $kunjungan->no_rawat ?? '',
            'tglDaftar' => $kunjungan->tgl_registrasi ?? '',
            'kdPoli' => $kunjungan->kd_poli ?? '',
            // Add other required fields based on PCare API spec
        ];
    }

    /**
     * Send kunjungan data to PCare API
     */
    private function sendKunjunganToPcare($data)
    {
        try {
            // Use PCare trait to send data
            $endpoint = 'kunjungan';
            return $this->requestPcare($endpoint, 'POST', $data);
        } catch (\Exception $e) {
            Log::error('Error sending to PCare: ' . $e->getMessage());
            return null;
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
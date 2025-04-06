<?php

namespace App\Http\Antrol;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\BpjsTraits;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PanggilAntreanController extends Controller
{
    use BpjsTraits;

    /**
     * Update status antrean (hadir/tidak hadir)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function panggil(Request $request)
    {
        try {
            // Log permintaan
            Log::info('Permintaan panggil antrean', [
                'request' => $request->all()
            ]);

            // Validasi input
            $validator = Validator::make($request->all(), [
                'tanggalperiksa' => 'required|date_format:Y-m-d',
                'kodepoli' => 'required|string|max:10',
                'nomorkartu' => 'required|string|max:13',
                'status' => 'required|integer|in:1,2',
                'waktu' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'metadata' => [
                        'code' => 400,
                        'message' => $validator->errors()->first()
                    ]
                ], 400);
            }

            // Data yang akan dikirimkan ke BPJS
            $data = [
                'tanggalperiksa' => $request->tanggalperiksa,
                'kodepoli' => $request->kodepoli,
                'nomorkartu' => $request->nomorkartu,
                'status' => $request->status,
                'waktu' => $request->waktu
            ];

            // Kirim data ke API BPJS
            $endpoint = "/antrean/panggil";
            $response = $this->requestPostBpjs($endpoint, $data, 'mobilejkn');
            $responseData = $response instanceof \Illuminate\Http\JsonResponse ? 
                           $response->getData(true) : json_decode($response, true);
            
            Log::info('Respons dari BPJS Mobile JKN', [
                'response' => $responseData
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

            // Jika berhasil, update status antrean di database lokal
            $isSuccess = isset($standardResponse['metadata']['code']) && 
                       $standardResponse['metadata']['code'] == 200;
                       
            if ($isSuccess) {
                try {
                    // Cari data pasien berdasarkan nomor kartu BPJS
                    $pasien = DB::table('pasien')
                        ->where('no_peserta', $request->nomorkartu)
                        ->first();
                    
                    if ($pasien) {
                        // Cari registrasi periksa yang sesuai
                        $regPeriksa = DB::table('reg_periksa')
                            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                            ->join('maping_poliklinik_pcare', 'poliklinik.kd_poli', '=', 'maping_poliklinik_pcare.kd_poli_rs')
                            ->where('reg_periksa.no_rkm_medis', $pasien->no_rkm_medis)
                            ->where('reg_periksa.tgl_registrasi', $request->tanggalperiksa)
                            ->where('maping_poliklinik_pcare.kd_poli_pcare', $request->kodepoli)
                            ->first();
                        
                        if ($regPeriksa) {
                            // Update status di reg_periksa
                            $statusUpdate = ($request->status == 1) ? 'Berkas Diterima' : 'Batal';
                            
                            DB::table('reg_periksa')
                                ->where('no_rawat', $regPeriksa->no_rawat)
                                ->update([
                                    'stts' => $statusUpdate,
                                    'status_panggil' => $request->status,
                                    'waktu_panggil' => date('Y-m-d H:i:s', $request->waktu / 1000) // Konversi dari millisecond
                                ]);
                                
                            Log::info('Berhasil update status reg_periksa', [
                                'no_rawat' => $regPeriksa->no_rawat,
                                'status' => $statusUpdate
                            ]);
                            
                            // Log ke antrean_bpjs_log jika tabel ada
                            if (Schema::hasTable('antrean_bpjs_log')) {
                                DB::table('antrean_bpjs_log')->insert([
                                    'no_rawat' => $regPeriksa->no_rawat,
                                    'no_rkm_medis' => $pasien->no_rkm_medis,
                                    'status' => 'Panggilan: ' . ($request->status == 1 ? 'Hadir' : 'Tidak Hadir'),
                                    'response' => json_encode($standardResponse),
                                    'created_at' => now()
                                ]);
                            }
                        } else {
                            Log::warning('Registrasi periksa tidak ditemukan untuk panggilan antrean', [
                                'no_rkm_medis' => $pasien->no_rkm_medis,
                                'tanggal' => $request->tanggalperiksa,
                                'kodepoli' => $request->kodepoli
                            ]);
                        }
                    } else {
                        Log::warning('Pasien dengan nomor kartu BPJS tidak ditemukan', [
                            'nomorkartu' => $request->nomorkartu
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Gagal update status antrean di database lokal', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Kembalikan respons dengan format standar BPJS
            return response()->json($standardResponse, $isSuccess ? 200 : 400);

        } catch (\Exception $e) {
            Log::error('Error pada PanggilAntreanController::panggil', [
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
}

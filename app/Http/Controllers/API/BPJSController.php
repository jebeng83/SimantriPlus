<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\BpjsTraits;

class BPJSController extends Controller
{
    use BpjsTraits;
    public function icare(Request $request)
    {
        try {
            $input = $request->all();
            
            // Log input data
            Log::info('BPJS iCare Input', [
                'raw_input' => $input
            ]);
            
            // Format nomor kartu - hanya hapus spasi dan non-numeric
            $noKartu = preg_replace('/[^0-9]/', '', $input['param']);
            
            // Pastikan panjang 13 digit
            if (strlen($noKartu) !== 13) {
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Nomor kartu harus 13 digit'
                    ],
                    'response' => null
                ], 400);
            }

            // Mapping kode dokter internal ke kode dokter PCare
            $kodeDokterInternal = $input['kodedokter'];
            $kodeDokterPcare = DB::table('maping_dokter_pcare')
                ->where('kd_dokter', $kodeDokterInternal)
                ->value('kd_dokter_pcare');
            
            // Jika mapping tidak ditemukan, gunakan kode dokter default atau kembalikan error
            if (!$kodeDokterPcare) {
                Log::warning('BPJS Mapping Dokter Tidak Ditemukan', [
                    'kd_dokter_internal' => $kodeDokterInternal
                ]);
                
                return response()->json([
                    'metaData' => [
                        'code' => 400,
                        'message' => 'Kode dokter tidak valid atau tidak terdaftar di PCare'
                    ],
                    'response' => null
                ], 400);
            }
            
            // Log hasil mapping
            Log::info('BPJS Dokter Mapping', [
                'kd_dokter_internal' => $kodeDokterInternal,
                'kd_dokter_pcare' => $kodeDokterPcare
            ]);

            // Format request untuk PCare sesuai katalog
            // Sesuai dokumentasi, hanya perlu parameter 'param' untuk endpoint validate
            $data = [
                'param' => $noKartu
            ];
            
            // Simpan kode dokter PCare untuk digunakan pada endpoint lain jika diperlukan
            session(['kd_dokter_pcare' => $kodeDokterPcare]);
            
            // Log final request data
            Log::info('BPJS PCare Request Data', [
                'final_data' => $data,
                'kd_dokter_pcare' => $kodeDokterPcare, // Log kode dokter PCare terpisah
                'url' => env('BPJS_ICARE_BASE_URL')
            ]);
            
            // Kirim request ke BPJS - gunakan endpoint yang benar sesuai katalog
            $response = $this->requestPostBpjs('api/pcare/validate', $data);
            
            // Log respons dari BPJS
            Log::info('BPJS Response', [
                'response' => $response
            ]);
            
            // Pastikan format respons sesuai dengan katalog
            if ($response->getStatusCode() == 200) {
                $responseData = json_decode($response->getContent(), true);
                
                // Log respons data
                Log::info('BPJS Response Data', [
                    'data' => $responseData
                ]);
                
                // Jika respons berhasil, kembalikan dengan format yang benar
                return response()->json($responseData);
            }
            
            // Jika respons gagal, kembalikan respons asli
            return $response;
            
        } catch (\Exception $e) {
            Log::error('BPJS iCare Error', [
                'message' => $e->getMessage(),
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
}

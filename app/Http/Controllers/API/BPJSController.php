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
                    'metadata' => [
                        'code' => 400,
                        'message' => 'Nomor kartu harus 13 digit'
                    ],
                    'response' => null
                ], 400);
            }

            // Jika kode dokter ada dalam request, lakukan mapping
            if (isset($input['kodedokter']) && !empty($input['kodedokter'])) {
                $kodeDokterInternal = $input['kodedokter'];
                $kodeDokterPcare = DB::table('maping_dokter_pcare')
                    ->where('kd_dokter', $kodeDokterInternal)
                    ->value('kd_dokter_pcare');
                
                // Jika mapping tidak ditemukan, log warning saja tapi tetap lanjutkan
                if (!$kodeDokterPcare) {
                    Log::warning('BPJS Mapping Dokter Tidak Ditemukan', [
                        'kd_dokter_internal' => $kodeDokterInternal
                    ]);
                }
            }
            
            // Format request untuk iCare
            $data = [
                'param' => $noKartu
            ];
            
            // Kirim request ke BPJS iCare
            $response = $this->requestPostBpjs('api/icare/validate', $data, 'icare');
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('BPJS iCare Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'metadata' => [
                    'code' => 500,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ],
                'response' => null
            ], 500);
        }
    }

    public function getPeserta($noKartu)
    {
        try {
            // Perbaiki format nomor kartu (hapus non-digit, hapus leading zeros, padding hingga 13 digit)
            $noKartu = preg_replace('/[^0-9]/', '', $noKartu); // Hapus karakter non-digit
            
            // Jika lebih dari 13 digit, ambil 13 digit terakhir
            if (strlen($noKartu) > 13) {
                $noKartu = substr($noKartu, -13);
                Log::info('BPJS Get Peserta - Nomor kartu terlalu panjang, dipotong menjadi 13 digit terakhir', [
                    'noKartu' => $noKartu
                ]);
            }
            
            $noKartuClean = ltrim($noKartu, '0'); // Hapus leading zeros
            $noKartu = str_pad($noKartuClean, 13, '0', STR_PAD_LEFT); // Padding hingga 13 digit
            
            // Log nomor kartu yang sudah diperbaiki
            Log::info('BPJS Get Peserta Format Fix', [
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

            // Kirim request ke BPJS PCare
            $response = $this->requestGetBpjs('peserta/' . $noKartu, 'pcare');
            
            return $response;

        } catch (\Exception $e) {
            Log::error('BPJS PCare Get Peserta Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'no_kartu' => $noKartu ?? null
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

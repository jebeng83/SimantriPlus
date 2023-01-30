<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    public function getRiwayatPemeriksaan(Request $request)
    {
        $noRM = $request->get('no_rm');
        try{

            $data = DB::table('reg_periksa')
                    ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                    ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                    ->where('no_rkm_medis', $noRM)
                    ->where('reg_periksa.stts', 'Sudah')
                    ->select('reg_periksa.tgl_registrasi', 'reg_periksa.no_rawat', 'dokter.nm_dokter', 
                            'reg_periksa.status_lanjut', 'poliklinik.nm_poli', 'reg_periksa.no_reg', 'reg_periksa.status_lanjut')
                    ->orderBy('reg_periksa.tgl_registrasi', 'desc')
                    ->get();

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);

        }catch(\Illuminate\Database\QueryException $ex){
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ]);
        }
    }

    public function getPemeriksaan(Request $request)
    {
        $tmp = $request->get('no_rawat');
        $noRawat = str_replace('-', '/', $tmp);
        $status = $request->get('status');

        try{

            if($status == 'Ralan'){
                $data = DB::table('pemeriksaan_ralan')
                        ->where('no_rawat', $noRawat)
                        ->first();

                return response()->json([
                            'status' => 'success',
                            'data' => $data,
                            'no_rawat' => $noRawat,
                            'status_lanjut' => $status
                        ]);
            }else{
                $data = DB::table('pemeriksaan_ranap')
                        ->where('no_rawat', $noRawat)
                        ->first();

                        return response()->json([
                            'status' => 'success',
                            'data' => $data,
                            'no_rawat' => $noRawat,
                            'status_lanjut' => $status
                        ]);
            }

        }catch(\Illuminate\Database\QueryException $ex){

            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => $ex->getMessage()
            ]);
        }
    }
}

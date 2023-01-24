<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class RadiologiController extends Controller
{
    use EnkripsiData;

    public function getPermintaanRadiologi($noRawat)
    {
        $noRawat = $this->decryptData($noRawat);
        try{

            $data = DB::table('hasil_radiologi')
                        ->where('no_rawat', $noRawat)
                        ->get();

            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Data permintaan radiologi berhasil diambil',
                'data' => $data
            ]);
            
        }catch(\Illuminate\Database\QueryException $ex){
            return response()->json([
                'status' => 'gagal',
                'pesan' => $ex->getMessage()
            ]);
        }
    }
}

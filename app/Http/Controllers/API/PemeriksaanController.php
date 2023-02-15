<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class PemeriksaanController extends Controller
{
    use EnkripsiData;

    public function getPemeriksaan($noRawat)
    {
        $noRawat = $this->decryptData($noRawat);

        try{

            $maxTgl = DB::table('pemeriksaan_ranap')
                            ->where('no_rawat', $noRawat)
                            ->max('tgl_perawatan');

            $maxJam = DB::table('pemeriksaan_ranap')
                            ->where('no_rawat', $noRawat)
                            ->where('tgl_perawatan', $maxTgl)
                            ->max('jam_rawat');

            $data = DB::table('pemeriksaan_ranap')
                        ->where('no_rawat', $noRawat)
                        ->where('tgl_perawatan', $maxTgl)
                        ->where('jam_rawat', $maxJam)
                        ->first();

            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Data pemeriksaan berhasil diambil',
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
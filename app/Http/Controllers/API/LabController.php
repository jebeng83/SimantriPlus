<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class LabController extends Controller
{
    use EnkripsiData;
    public function getPerawatanLab(Request $request)
    {
        $q = $request->get('q');
        $que = '%'.$q.'%';
        $obat = DB::table('jns_perawatan_lab')
                    ->where('status', '1')
                    ->where(function($query) use ($que) {
                        $query->where('kd_jenis_prw', 'like', $que)
                              ->orWhere('nm_perawatan', 'like', $que);
                    })
                    ->selectRaw('kd_jenis_prw AS id, nm_perawatan AS text')
                    ->get();
        return response()->json($obat, 200);
    }

    public function postPermintaanLab(Request $request, $noRawat)
    {
        $$input = $request->all();
        $klinis = $$input['klinis'];
        $info = $input['info'];
        $jnsPemeriksaan = $input['jenis_pemeriksaan'];
        $noRawat = $this->decryptData($noRawat);

        try{
            $getNumber = DB::table('permintaan_lab')
                            ->where('tgl_permintaan', date('Y-m-d'))
                            ->selectRaw('ifnull(MAX(CONVERT(RIGHT(noorder,4),signed)),0) as no')
                            ->first();

            $lastNumber = substr($getNumber->no, 0, 4);
            $getNextNumber = sprintf('%04s', ($lastNumber + 1));
            $noOrder = 'PL'.date('dmy').$getNextNumber;

            DB::table('permintaan_lab')
                    ->insert([
                        'noorder' => $noOrder,
                        'no_rawat' => $noRawat,
                        'tgl_permintaan' => date('Y-m-d'),
                        'jam_permintaan' => date('H:i:s'),
                        'dokter_perujuk' => session()->get('username'),
                        'diagnosa_klinis' => $klinis,
                        'informasi_tambahan' => $info,
                        'status' => 'ralan'
                    ]);

            foreach($jnsPemeriksaan as $pemeriksaan){
                DB::table('permintaan_pemeriksaan_lab')
                        ->insert([
                            'noorder' => $noOrder,
                            'kd_jenis_prw' => $pemeriksaan,
                            'stts_bayar' => 'Belum'
                        ]);
            }

            return response()->json(['status' => 'sukses', 'message' => 'Permintaan lab berhasil disimpan'], 200);

        }catch(\Illuminate\Database\QueryException $ex){
            return response()->json(['status' => 'gagal', 'message' => $ex->getMessage()], 200);
        }


    }
}

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

    public function getPerawatanRadiologi(Request $request)
    {
        $q = $request->get('q');
        $que = '%'.$q.'%';
        $obat = DB::table('jns_perawatan_radiologi')
                    ->where('status', '1')
                    ->where(function($query) use ($que) {
                        $query->where('kd_jenis_prw', 'like', $que)
                              ->orWhere('nm_perawatan', 'like', $que);
                    })
                    ->selectRaw('kd_jenis_prw AS id, nm_perawatan AS text')
                    ->get();
        return response()->json($obat, 200);
    }

    public function postPermintaanRadiologi(Request $request, $noRawat)
    {
        $input = $request->all();
        $klinis = $input['klinis'];
        $info = $input['info'];
        $jnsPemeriksaan = $input['jns_pemeriksaan'];
        $noRawat = $this->decryptData($noRawat);

        try{
            DB::beginTransaction();
            $getNumber = DB::table('permintaan_radiologi')
                            ->where('tgl_permintaan', date('Y-m-d'))
                            ->selectRaw('ifnull(MAX(CONVERT(RIGHT(noorder,4),signed)),0) as no')
                            ->first();

            $lastNumber = substr($getNumber->no, 0, 4);
            $getNextNumber = sprintf('%04s', ($lastNumber + 1));
            $noOrder = 'PL'.date('Ymd').$getNextNumber;

            DB::table('permintaan_radiologi')
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
                DB::table('permintaan_pemeriksaan_radiologi')
                        ->insert([
                            'noorder' => $noOrder,
                            'kd_jenis_prw' => $pemeriksaan,
                            'stts_bayar' => 'Belum'
                        ]);
            }

            DB::commit();
            return response()->json(['status' => 'sukses', 'message' => 'Permintaan lab berhasil disimpan'], 200);

        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            return response()->json(['status' => 'gagal', 'message' => $ex->getMessage()], 200);
        }
    }

    public function hapusPermintaanRadiologi($noOrder)
    {
        try{
            DB::beginTransaction();

            DB::table('permintaan_radiologi')
                    ->where('noorder', $noOrder)
                    ->delete();

            // DB::table('permintaan_pemeriksaan_lab')
            //         ->where('noorder', $noOrder)
            //         ->delete();

            DB::commit();
            return response()->json(['status' => 'sukses', 'message' => 'Permintaan lab berhasil dihapus'], 200);
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            return response()->json(['status' => 'gagal', 'message' => $ex->getMessage()], 200);
        }
    }
}

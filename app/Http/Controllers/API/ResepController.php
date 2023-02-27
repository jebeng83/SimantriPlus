<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class ResepController extends Controller
{
    use EnkripsiData;

    public function getObatRanap(Request $request, $bangsal)
    {
        $q = $request->get('q');
        $que = '%'.$q.'%';

        $depo = DB::table('set_depo_ranap')
                    ->where('kd_bangsal', $bangsal)
                    ->first();

        $obat = DB::table('databarang')
                    ->join('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                    ->where('status', '1')
                    ->where('gudangbarang.stok', '>', '0')
                    ->where('gudangbarang.kd_bangsal', $depo->kd_depo)
                    ->where(function($query) use ($que) {
                        $query->where('databarang.kode_brng', 'like', $que)
                              ->orWhere('databarang.nama_brng', 'like', $que);
                    })
                    ->selectRaw('gudangbarang.kode_brng AS id, databarang.nama_brng AS text')
                    ->get();
        return response()->json($obat, 200);
    }

    public function getObatRalan(Request $request, $poli)
    {
        $q = $request->get('q');
        $que = '%'.$q.'%';

        $depo = DB::table('set_depo_ralan')
                    ->where('kd_poli', $poli)
                    ->first();

        $obat = DB::table('databarang')
                    ->join('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                    ->where('status', '1')
                    ->where('gudangbarang.stok', '>', '0')
                    ->where('gudangbarang.kd_bangsal', $depo->kd_bangsal)
                    ->where(function($query) use ($que) {
                        $query->where('databarang.kode_brng', 'like', $que)
                              ->orWhere('databarang.nama_brng', 'like', $que);
                    })
                    ->selectRaw('gudangbarang.kode_brng AS id, databarang.nama_brng AS text')
                    ->get();
        return response()->json($obat, 200);
    }

    public function getDataObat(Request $request ,$kdObat)
    {
        $input = $request->all();
        $status = $input['status'];
        $kode = $input['kode'];
        $bangsal = "";
        if($status == 'ralan'){
            $db = DB::table('set_depo_ralan')->where('kd_poli', $kode)->first();
            $bangsal = $db->kd_bangsal;
        }else{
            $db = DB::table('set_depo_ranap')->where('kd_bangsal', $kode)->first();
            $bangsal = $db->kd_depo;
        }
        $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $kdObat)->where('kd_bangsal', $bangsal)->max('tanggal');
        $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $kdObat)->where('tanggal', $maxTgl)->where    ('kd_bangsal', $bangsal)->max('jam');
        $data = DB::table('databarang')
            ->join('riwayat_barang_medis', 'databarang.kode_brng', '=', 'riwayat_barang_medis.kode_brng')
            ->where('databarang.kode_brng', $kdObat)
            ->where('riwayat_barang_medis.tanggal', $maxTgl)
            ->where('riwayat_barang_medis.jam', $maxJam)
            ->select('databarang.*', 'riwayat_barang_medis.stok_akhir')
            ->first();

        return response()->json($data);
    }

    public function postResep(Request $request, $noRawat)
    {
        $dokter = session()->get('username');
        $resObat = $request->get('obat');
        $resJml = $request->get('jumlah');
        $resAturan = $request->get('aturan_pakai');
        $status = $request->get('status');
        $kode = $request->get('kode');
        $noRawat = $this->decryptData($noRawat);
        $bangsal = "";

        DB::beginTransaction();
        try{

            if($status == 'Ralan'){
                $iter = $request->get('iter');
                if($iter != '-'){
                    DB::table('resep_iter')->upsert(
                        [
                        'no_rawat'=> $noRawat,
                        'catatan_iter'=> $iter,
                        ],
                        ['no_rawat'],
                        ['catatan_iter']
                    );
                }
                $db = DB::table('set_depo_ralan')->where('kd_poli', $kode)->first();
                $bangsal = $db->kd_bangsal;
            }else{
                $db = DB::table('set_depo_ranap')->where('kd_bangsal', $kode)->first();
                $bangsal = $db->kd_depo;
            }

            for ($i=0; $i < count($resObat); $i++){
                $obat = $resObat[$i];
                $jml = $resJml[$i];
                $aturan = $resAturan[$i];

                $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', $bangsal)->max('tanggal');
                $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('tanggal', $maxTgl)->where('kd_bangsal',  $bangsal)->max('jam');
                $maxStok = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', $bangsal)->where('tanggal', $maxTgl)->where('jam', $maxJam)->max('stok_akhir');

                if($maxStok < $jml){
                    continue;
                    // if(empty($obat)){
                    //     return response()->json([
                    //         'status' => 'gagal',
                    //         'pesan' => 'Obat tidak boleh kosong'
                    //     ]);
                    // }else{
                    //     $dataBarang = DB::table('databarang')->where('kode_brng', $obat)->first();
                    //     return response()->json([
                    //         'status' => 'gagal',
                    //         'pesan' => 'Stok obat '.$dataBarang->nama_brng.' kosong'
                    //     ]);
                    // }
                    
                }
                
                $resep = DB::table('resep_obat')->where('no_rawat', $noRawat)->where('tgl_peresepan', date('Y-m-d'))->first();
                if(!empty($resep)){
                    if($resep->tgl_perawatan != '0000-00-00'){
                        return response()->json([
                            'status' => 'gagal',
                            'pesan' => 'Resep obat sudah tervalidasi'
                        ]);
                    }
                }
                $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%'.date('Y-m-d').'%')->orWhere('tgl_peresepan', 'like', '%'.date('Y-m-d').'%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
                $maxNo = substr($no->resep, 0, 4);
                $nextNo = sprintf('%04s', ($maxNo + 1));
                $tgl = date('Ymd');
                $noResep = $tgl.''.$nextNo;

                if($resep){
                    DB::table('resep_dokter')->insert([
                        'no_resep' => $resep->no_resep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                    ]);
                }else{
                    DB::table('resep_obat')->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',
                        'jam' => '00:00:00',
                        'no_rawat' => $noRawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => $tgl,
                        'jam_peresepan' => date('H:i:s'),
                        'status' => $status,
                    ]);
                    DB::table('resep_dokter')->insert([
                        'no_resep' => $noResep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Input resep berhasil'
            ]);
        }catch (\Illuminate\Database\QueryException $ex){
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
                'pesan' => $ex->getMessage()
            ]);
        }
    }

    public function postResepRanap(Request $request, $noRawat)
    {
        $dokter = session()->get('username');
        $resObat = $request->get('obat');
        $resJml = $request->get('jumlah');
        $resAturan = $request->get('aturan_pakai');
        $status = $request->get('status');
        $kode = $request->get('kode');
        $noRawat = $this->decryptData($noRawat);
        $bangsal = "";

        
        try{
            DB::beginTransaction();
            $db = DB::table('set_depo_ranap')->where('kd_bangsal', $kode)->first();
            $bangsal = $db->kd_depo;

            $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%'.date('Y-m-d').'%')->orWhere('tgl_peresepan', 'like', '%'.date('Y-m-d').'%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
            $maxNo = substr($no->resep, 0, 4);
            $nextNo = sprintf('%04s', ($maxNo + 1));
            $tgl = date('Ymd');
            $noResep = $tgl.''.$nextNo;

            for ($i=0; $i < count($resObat); $i++){
                $obat = $resObat[$i];
                $jml = $resJml[$i];
                $aturan = $resAturan[$i];

                $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', $bangsal)->max('tanggal');
                $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('tanggal', $maxTgl)->where('kd_bangsal',  $bangsal)->max('jam');
                $maxStok = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', $bangsal)->where('tanggal', $maxTgl)->where('jam', $maxJam)->max('stok_akhir');

                if($maxStok < $jml){
                    continue;
                    // if(empty($obat)){
                    //     return response()->json([
                    //         'status' => 'gagal',
                    //         'pesan' => 'Obat tidak boleh kosong'
                    //     ]);
                    // }else{
                    //     $dataBarang = DB::table('databarang')->where('kode_brng', $obat)->first();
                    //     return response()->json([
                    //         'status' => 'gagal',
                    //         'pesan' => 'Stok obat '.$dataBarang->nama_brng.' kosong'
                    //     ]);
                    // }
                    
                }
                
                $maxTglResep = DB::table('resep_obat')->where('no_rawat', $noRawat)->where('tgl_peresepan', date('Y-m-d'))->max('jam_peresepan');
                $resep = DB::table('resep_obat')->where('no_rawat', $noRawat)->where('tgl_peresepan', date('Y-m-d'))->where('jam_peresepan', $maxTglResep)->first();

                if(!empty($resep) && $resep->tgl_perawatan != '0000-00-00'){
                    //resep sudah divalidasi

                    DB::table('resep_obat')->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',
                        'jam' => '00:00:00',
                        'no_rawat' => $noRawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => $tgl,
                        'jam_peresepan' => date('H:i:s'),
                        'status' => $status,
                    ]);

                    DB::table('resep_dokter')->insert([
                        'no_resep' => $noResep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                    ]);

                }else if(empty($resep)){
                    //resep belum ada

                    DB::table('resep_obat')->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',
                        'jam' => '00:00:00',
                        'no_rawat' => $noRawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => $tgl,
                        'jam_peresepan' => date('H:i:s'),
                        'status' => $status,
                    ]);

                    DB::table('resep_dokter')->insert([
                        'no_resep' => $noResep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                    ]);

                }else{
                    //resep sudah ada dan belum divalidasi

                    DB::table('resep_dokter')->insert([
                        'no_resep' => $resep->no_resep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Input resep berhasil'
            ]);

        }catch (\Illuminate\Database\QueryException $ex){
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
                'pesan' => $ex->getMessage()
            ]);
        }
    }

    public function postResepRacikan(Request $request, $noRawat)
    {
        $input = $request->all();
        $namaRacikan = $input['nama_racikan'];
        $aturanPakai = $input['aturan_racikan'];
        $jumlahRacikan = $input['jumlah_racikan'];
        $metodeRacikan = $input['metode_racikan'];
        $keteranganRacikan = $input['keterangan_racikan'];

        $kdObat = $input['kd_obat'];
        $p1 = $input['p1'];
        $p2 = $input['p2'];
        $kandungan = $input['kandungan'];
        $jml = $input['jml'];

        $no_rawat = $this->decryptData($noRawat);
        $dokter = session()->get('username');
        $status = $request->get('status');
        $kode = $request->get('kode');

        $request->validate([
            'nama_racikan' => 'required',
            'aturan_racikan' => 'required',
            'jumlah_racikan' => 'required',
            'metode_racikan' => 'required',
            'keterangan_racikan' => 'required',
            'kd_obat' => 'required',
            'p1' => 'required',
            'p2' => 'required',
            'kandungan' => 'required',
            'jml' => 'required',
        ]);

        // if($validate){
        //     return response()->json(['status'=>'gagal', 'message'=>'Data tidak boleh kosong', 'data' => $input]);
        // }
        DB::beginTransaction();
        try{
            $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%'.date('Y-m-d').'%')->orWhere('tgl_peresepan', 'like', '%'.date('Y-m-d').'%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
            $maxNo = substr($no->resep, 0, 4);
            $nextNo = sprintf('%04s', ($maxNo + 1));
            $tgl = date('Ymd');
            $noResep = $tgl.''.$nextNo;

            $cek = DB::table('resep_obat')
                    ->join('resep_dokter_racikan', 'resep_obat.no_resep', '=', 'resep_dokter_racikan.no_resep')
                    ->where('resep_obat.no_rawat', $no_rawat)->where('resep_obat.tgl_peresepan', date('Y-m-d'))
                    ->select('resep_obat.no_resep', 'resep_obat.tgl_perawatan')
                    ->first();

            if (!empty($cek) && $cek->tgl_perawatan != '0000-00-00') {
                $noRacik = DB::table('resep_dokter_racikan')->where('no_resep', $cek->no_resep)->max('no_racik');
                $nextNoRacik = $noRacik + 1;
                $insert = DB::table('resep_dokter_racikan')
                                ->insert([
                                    'no_resep' => $cek->no_resep,
                                    'no_racik' => $nextNoRacik,
                                    'nama_racik' => $namaRacikan,
                                    'kd_racik' => $metodeRacikan,
                                    'jml_dr' => $jumlahRacikan,
                                    'aturan_pakai' => $aturanPakai,
                                    'keterangan' => $keteranganRacikan,
                                ]);
                if($insert){
                    return response()->json(['status'=>'sukses', 'message'=>'Racikan berhasil ditambahkan']);
                }
            }else{
                $insert = DB::table('resep_obat')
                                ->insert([
                                    'no_resep' => $noResep,
                                    'tgl_perawatan' => '0000-00-00',
                                    'jam' => '00:00:00',
                                    'no_rawat' => $no_rawat,
                                    'kd_dokter' => $dokter,
                                    'tgl_peresepan' => date('Y-m-d'),
                                    'jam_peresepan' => date('H:i:s'),
                                    'status' => $status,
                                    'tgl_penyerahan' => '0000-00-00',
                                    'jam_penyerahan' => '00:00:00',
                                ]);
                if($insert){
                    $insert = DB::table('resep_dokter_racikan')
                                ->insert([
                                    'no_resep' => $noResep,
                                    'no_racik' => '1',
                                    'nama_racik' => $namaRacikan,
                                    'kd_racik' => $metodeRacikan,
                                    'jml_dr' => $jumlahRacikan,
                                    'aturan_pakai' => $aturanPakai,
                                    'keterangan' => $keteranganRacikan,
                                ]);
                    if($insert){
                        for($i=0; $i < count($kdObat); $i++){
                            DB::table('resep_dokter_racikan_detail')->insert([
                                'no_resep' => $noResep,
                                'no_racik' => '1',
                                'kode_brng' => $kdObat[$i],
                                'p1' => $p1[$i],
                                'p2' => $p2[$i],
                                'kandungan' => $kandungan[$i],
                                'jml' => $jml[$i],
                            ]);
                        }
                        DB::commit();
                        return response()->json(['status'=>'sukses', 'message'=>'Racikan berhasil ditambahkan']);
                    }
                }else{
                    DB::rollBack();
                    return response()->json(['status'=>'gagal', 'message'=>'Racikan gagal ditambahkan']);
                }
            }

            
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            return response()->json(['status' => 'gagal', 'message' => $ex->getMessage()]);
        }

    }

    public function postResepRacikanRanap(Request $request, $noRawat)
    {
        $input = $request->all();
        $namaRacikan = $input['nama_racikan'];
        $aturanPakai = $input['aturan_racikan'];
        $jumlahRacikan = $input['jumlah_racikan'];
        $metodeRacikan = $input['metode_racikan'];
        $keteranganRacikan = $input['keterangan_racikan'];

        $kdObat = $input['kd_obat'];
        $p1 = $input['p1'];
        $p2 = $input['p2'];
        $kandungan = $input['kandungan'];
        $jml = $input['jml'];

        $no_rawat = $this->decryptData($noRawat);
        $dokter = session()->get('username');

        $request->validate([
            'nama_racikan' => 'required',
            'aturan_racikan' => 'required',
            'jumlah_racikan' => 'required',
            'metode_racikan' => 'required',
            'keterangan_racikan' => 'required',
            'kd_obat' => 'required',
            'p1' => 'required',
            'p2' => 'required',
            'kandungan' => 'required',
            'jml' => 'required',
        ]);

        // if($validate){
        //     return response()->json(['status'=>'gagal', 'message'=>'Data tidak boleh kosong', 'data' => $input]);
        // }

        try{
            $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%'.date('Y-m-d').'%')->orWhere('tgl_peresepan', 'like', '%'.date('Y-m-d').'%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
            $maxNo = substr($no->resep, 0, 4);
            $nextNo = sprintf('%04s', ($maxNo + 1));
            $tgl = date('Ymd');
            $noResep = $tgl.''.$nextNo;

            $cek = DB::table('resep_obat')
                    ->join('resep_dokter_racikan', 'resep_obat.no_resep', '=', 'resep_dokter_racikan.no_resep')
                    ->where('resep_obat.no_rawat', $no_rawat)->where('resep_obat.tgl_peresepan', date('Y-m-d'))
                    ->select('resep_obat.no_resep')
                    ->first();

            if (!empty($cek)) {
                $noRacik = DB::table('resep_dokter_racikan')->where('no_resep', $cek->no_resep)->max('no_racik');
                $nextNoRacik = $noRacik + 1;
                $insert = DB::table('resep_dokter_racikan')
                                ->insert([
                                    'no_resep' => $cek->no_resep,
                                    'no_racik' => $nextNoRacik,
                                    'nama_racik' => $namaRacikan,
                                    'kd_racik' => $metodeRacikan,
                                    'jml_dr' => $jumlahRacikan,
                                    'aturan_pakai' => $aturanPakai,
                                    'keterangan' => $keteranganRacikan,
                                ]);
                if($insert){
                    return response()->json(['status'=>'sukses', 'message'=>'Racikan berhasil ditambahkan']);
                }
            }else{
                $insert = DB::table('resep_obat')
                                ->insert([
                                    'no_resep' => $noResep,
                                    'tgl_perawatan' => '0000-00-00',
                                    'jam' => '00:00:00',
                                    'no_rawat' => $no_rawat,
                                    'kd_dokter' => $dokter,
                                    'tgl_peresepan' => date('Y-m-d'),
                                    'jam_peresepan' => date('H:i:s'),
                                    'status' => 'ranap',
                                    'tgl_penyerahan' => '0000-00-00',
                                    'jam_penyerahan' => '00:00:00',
                                ]);
                if($insert){
                    $insert = DB::table('resep_dokter_racikan')
                                ->insert([
                                    'no_resep' => $noResep,
                                    'no_racik' => '1',
                                    'nama_racik' => $namaRacikan,
                                    'kd_racik' => $metodeRacikan,
                                    'jml_dr' => $jumlahRacikan,
                                    'aturan_pakai' => $aturanPakai,
                                    'keterangan' => $keteranganRacikan,
                                ]);
                    if($insert){
                        for($i=0; $i < count($kdObat); $i++){
                            DB::table('resep_dokter_racikan_detail')->insert([
                                'no_resep' => $noResep,
                                'no_racik' => '1',
                                'kode_brng' => $kdObat[$i],
                                'p1' => $p1[$i],
                                'p2' => $p2[$i],
                                'kandungan' => $kandungan[$i],
                                'jml' => $jml[$i],
                            ]);
                        }
                        
                        return response()->json(['status'=>'sukses', 'message'=>'Racikan berhasil ditambahkan']);
                    }
                }else{
                    return response()->json(['status'=>'gagal', 'message'=>'Racikan gagal ditambahkan']);
                }
            }
        }catch(\Illuminate\Database\QueryException $ex){
            return response()->json(['status' => 'gagal', 'message' => $ex->getMessage()]);
        }

    }

    public function hapusObat($noResep, $kdObat)
    {
        try{
            DB::table('resep_dokter')->where('no_resep', $noResep)->where('kode_brng', $kdObat)->delete();
            return response()->json(['status'=> 'sukses', 'pesan'=> 'Obat berhasil dihapus']);
        }catch (\Illuminate\Database\QueryException $ex){
            return response()->json(['status'=> 'gagal', 'pesan'=> $ex->getMessage()]);
        }
    }
}

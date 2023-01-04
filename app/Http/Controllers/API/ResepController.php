<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class ResepController extends Controller
{
    use EnkripsiData;
    public function getDataObat($kdObat)
    {
        $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $kdObat)->where('kd_bangsal', 'DPF')->max('tanggal');
        $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $kdObat)->where('tanggal', $maxTgl)->where    ('kd_bangsal', 'DPF')->max('jam');
        $data = DB::table('databarang')
            ->join('riwayat_barang_medis', 'databarang.kode_brng', '=', 'riwayat_barang_medis.kode_brng')
            ->where('databarang.kode_brng', $kdObat)
            ->where('riwayat_barang_medis.tanggal', $maxTgl)
            ->where('riwayat_barang_medis.jam', $maxJam)
            ->select('databarang.*', 'riwayat_barang_medis.stok_akhir')
            ->first();

        return response()->json($data);
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
                                    'tgl_perawatan' => date('Y-m-d'),
                                    'jam' => date('H:i:s'),
                                    'no_rawat' => $no_rawat,
                                    'kd_dokter' => $dokter,
                                    'tgl_peresepan' => date('Y-m-d'),
                                    'jam_peresepan' => date('H:i:s'),
                                    'status' => 'ralan',
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
                                    'tgl_perawatan' => date('Y-m-d'),
                                    'jam' => date('H:i:s'),
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

    // public function hapusObatRacikanRanap($noResep, $noRacikan)
    // {
    //     try{
    //         $delete = DB::table('resep_dokter_racikan')->where('no_resep', $noResep)->where('no_racik', $noRacikan)->delete();
    //         return response()->json(['status'=> 'sukses', 'pesan'=> 'Obat berhasil dihapus']);
    //     }catch (\Illuminate\Database\QueryException $ex){
    //         return response()->json(['status'=> 'gagal', 'pesan'=> $ex->getMessage()]);
    //     }
    // }
}

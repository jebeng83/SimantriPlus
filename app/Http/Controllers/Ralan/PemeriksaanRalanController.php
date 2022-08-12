<?php

namespace App\Http\Controllers\Ralan;
use Illuminate\Support\Facades\Crypt;
use DB;
use App\Http\Controllers\Controller;
use Request;

class PemeriksaanRalanController extends Controller
{
    public $dokter, $noRawat, $noRM; 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('loginauth');
        $this->middleware('decrypt')->except('getObat');
        $this->dokter = session()->get('username');
        $this->noRawat = Request::get('no_rawat');
        $this->noRM = Request::get('no_rm');
    }

    public function index()
    {
        $dokter = session()->get('username');
        $noRawat = Request::get('no_rawat');
        $noRM = Request::get('no_rm');
        return view('ralan.pemeriksaan-ralan');
    }

    public function hapusObat($noResep, $kdObat)
    {
        try{
            $delete = DB::table('resep_dokter')->where('no_resep', $noResep)->where('kode_brng', $kdObat)->delete();
            return response()->json(['status'=> 'sukses', 'pesan'=> 'Obat berhasil dihapus']);
        }catch (\Illuminate\Database\QueryException $ex){
            return response()->json(['status'=> 'gagal', 'pesan'=> $ex->getMessage()]);
        }
    }

    public function postResepRacikan($noRawat)
    {
        $namaRacikan = Request::get('nama_racikan');
        $aturanPakai = Request::get('aturan_pakai');
        $jumlahRacikan = Request::get('jumlah_racikan');
        $metodeRacikan = Request::get('metode_racikan');
        $keteranganRacikan = Request::get('keterangan_racikan');

        try{
            // for($i=0; $i < )
        }catch(\Illuminate\Database\QueryException $ex){
            return response()->json(['status'=> 'gagal', 'pesan'=> $ex->getMessage()]);
        }
    }

    public function getCopyResep($noResep)
    {
        $data = DB::table('resep_dokter')
                    ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                    ->where('resep_dokter.no_resep', $noResep)
                    ->select('databarang.nama_brng', 'resep_dokter.jml', 'resep_dokter.aturan_pakai', 'resep_dokter.kode_brng')
                    ->get();
        return response()->json($data);
    }

    public function postResumMedis($noRawat)
    {
        $keluhan = Request::get('keluhan_utama');
        $diagnosa = Request::get('diagnosa_utama');
        $terapi = Request::get('terapi');
        $prosedur = Request::get('prosedur_utama');
        $dokter = session()->get('username');

        try{
            $cek = DB::table('resume_pasien')->where('no_rawat', $noRawat)->count('no_rawat');
            if($cek > 0){
                $update = DB::table('resume_pasien')->where('no_rawat', $noRawat)->update([
                    'keluhan_utama' => $keluhan,
                    'diagnosa_utama' => $diagnosa,
                    'obat_pulang' => $terapi,
                    'prosedur_utama' => $prosedur,
                ]);
                return response()->json(['status'=> 'sukses', 'pesan'=> 'Resume medis berhasil diperbarui']);
            }else{
                $insert = DB::table('resume_pasien')->insert([
                    'no_rawat' => $noRawat,
                    'kd_dokter' => $dokter,
                    'keluhan_utama' => $keluhan,
                    'diagnosa_utama' => $diagnosa,
                    'obat_pulang' => $terapi,
                    'prosedur_utama' => $prosedur,
                ]);
                return response()->json(['status'=> 'sukses', 'pesan'=> 'Resume medis berhasil ditambahkan']);
            }
        }catch (\Illuminate\Database\QueryException $ex){
            return response()->json(['status'=> 'gagal', 'pesan'=> $ex->getMessage()]);
        }
    }

    public function postCopyResep($noRawat)
    {
        $dokter = session()->get('username');
        $resObat = Request::get('obat');
        $resJml = Request::get('jumlah');
        $resAturan = Request::get('aturan_pakai');
        $no_rawat = $this->decryptData($noRawat);

        $resep = DB::table('resep_obat')->where('no_rawat', $no_rawat)->first();
        $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%'.date('Y-m-d').'%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
        $maxNo = substr($no->resep, 0, 4);
        $nextNo = sprintf('%04s', ($maxNo + 1));
        $tgl = date('Ymd');
        $noResep = $tgl.''.$nextNo;

        try{
            for ($i=0; $i < count($resObat); $i++){
                $obat = $resObat[$i];
                $jml = $resJml[$i];
                $aturan = $resAturan[$i];

                $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', 'DPF')->max('tanggal');
                $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('tanggal', $maxTgl)->where('kd_bangsal', 'DPF')->max('jam');
                $maxStok = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', 'DPF')->where('tanggal', $maxTgl)->where('jam', $maxJam)->max('stok_akhir');

                if($maxStok < 1){
                    $dataBarang = DB::table('databarang')->where('kode_brng', $obat)->first();
                    return response()->json([
                        'status' => 'gagal',
                        'pesan' => 'Stok obat '.$dataBarang->nama_brng ?? $obat.' kosong'
                    ]);
                }

                if($resep){
                    if(!empty($jml)){
                        DB::table('resep_dokter')->insert([
                            'no_resep' => $resep->no_resep,
                            'kode_brng' => $obat,
                            'jml' => $jml,
                            'aturan_pakai' => $aturan,
                        ]);
                    }
                }else{
                    DB::table('resep_obat')->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => $tgl,
                        'jam' => date('H:i:s'),
                        'no_rawat' => $no_rawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => $tgl,
                        'jam_peresepan' => date('H:i:s'),
                        'status' => 'Ralan',
                    ]);
                    if(!empty($jml)){
                        DB::table('resep_dokter')->insert([
                            'no_resep' => $noResep,
                            'kode_brng' => $obat,
                            'jml' => $jml,
                            'aturan_pakai' => $aturan,
                        ]);
                    }
                }
            }
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Input resep berhasil'
            ]);
        }catch (\Illuminate\Database\QueryException $ex){
            return response()->json([
                'status' => 'gagal',
                'pesan' => $ex->getMessage()
            ]);
        }
    }

    public function postResep($noRawat)
    {
        $dokter = session()->get('username');
        $resObat = Request::get('obat');
        $resJml = Request::get('jumlah');
        $resAturan = Request::get('aturan_pakai');
        $noRawat = $this->decryptData($noRawat);
        // $validate = Request::validate([
        //     'username' => 'required',
        //     'obat' => 'required',
        //     'jumlah' => 'required',
        //     'aturan_pakai' => 'required',
        // ]);
        // if ($validate->fails()) {    
        //     return response()->json($validate->messages(), Response::HTTP_BAD_REQUEST);
        // }
        try{
            for ($i=0; $i < count($resObat); $i++){
                $obat = $resObat[$i];
                $jml = $resJml[$i];
                $aturan = $resAturan[$i];

                $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', 'DPF')->max('tanggal');
                $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('tanggal', $maxTgl)->where('kd_bangsal', 'DPF')->max('jam');
                $maxStok = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', 'DPF')->where('tanggal', $maxTgl)->where('jam', $maxJam)->max('stok_akhir');

                if($maxStok < 1){
                    return response()->json([
                        'status' => 'gagal',
                        'pesan' => 'Stok obat '.$obat.' kosong'
                    ]);
                }
                $resep = DB::table('resep_obat')->where('no_rawat', $noRawat)->first();
                $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%'.date('Y-m-d').'%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
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
                        'tgl_perawatan' => $tgl,
                        'jam' => date('H:i:s'),
                        'no_rawat' => $noRawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => $tgl,
                        'jam_peresepan' => date('H:i:s'),
                        'status' => 'Ralan',
                    ]);
                    DB::table('resep_dokter')->insert([
                        'no_resep' => $noResep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                    ]);
                }
            }
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Input resep berhasil'
            ]);
        }catch (\Illuminate\Database\QueryException $ex){
            return response()->json([
                'status' => 'gagal',
                'pesan' => $ex->getMessage()
            ]);
        }
    }

    public static function getResepObat($noResep){
        $data = DB::table('resep_dokter')
                    ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                    ->where('resep_dokter.no_resep', $noResep)
                    ->select('databarang.nama_brng', 'resep_dokter.jml', 'resep_dokter.aturan_pakai')
                    ->get();
        
        return $data;
    }

    public static function getObat()
    {
        $q = Request::get('q');
        $que = '%'.$q.'%';
        $obat = DB::table('databarang')
                    ->join('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                    ->where('status', '1')
                    ->where('gudangbarang.stok', '>', '0')
                    ->where('gudangbarang.kd_bangsal', 'DPF')
                    ->where(function($query) use ($que) {
                        $query->where('databarang.kode_brng', 'like', $que)
                              ->orWhere('databarang.nama_brng', 'like', $que);
                    })
                    ->selectRaw('gudangbarang.kode_brng AS id, databarang.nama_brng AS text')
                    ->get();
        return response()->json($obat, 200);
    }

    public static function getPemeriksaanRalan($noRawat, $status)
    {
        if($status == 'Ralan'){
            $data = DB::table('pemeriksaan_ralan')
                        ->where('no_rawat', $noRawat)
                        ->first();
        }else{
            $data = DB::table('pemeriksaan_ranap')
                        ->where('no_rawat', $noRawat)
                        ->first();
        }
        return $data;
    }

    public static function getDiagnosa($noRawat)
    {
        $data = DB::table('diagnosa_pasien')
                    ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                    ->where('diagnosa_pasien.no_rawat', $noRawat)
                    ->select('penyakit.kd_penyakit', 'penyakit.nm_penyakit')
                    ->get();
        return $data;
    }

    public static function getPemeriksaanObstetri($noRawat)
    {
        $data = DB::table('pemeriksaan_obstetri_ralan')
                    ->where('no_rawat', $noRawat)
                    ->first();
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPemeriksaan(Request $request)
    {
        // return response()->json([
        //                 'status' => 'success',
        //                 'message' => Request::get('no_rawat')
        //             ], 200);
        $validate = Request::validate([
            'tensi' => 'required',
            'kesadaran' => 'required',
            'rtl' => 'required',
            'penilaian' => 'required',
            'instruksi' => 'required',
        ]);
        $cek = DB::table('pemeriksaan_ralan')
                    ->where('no_rawat', Request::get('no_rawat'))
                    ->count();
        $data = [
                    'no_rawat' => Request::get('no_rawat'),
                    'nip' => session()->get('username'),
                    'tgl_perawatan' => date('Y-m-d'),
                    'jam_rawat' => date('H:i:s'),
                    'suhu_tubuh' => Request::get('suhu'),
                    'tensi' => Request::get('tensi'),
                    'nadi' => Request::get('nadi'),
                    'respirasi' => Request::get('respirasi'),
                    'tinggi' => Request::get('tinggi'),
                    'berat' => Request::get('berat'),
                    'gcs' => Request::get('gcs'),
                    'kesadaran' => Request::get('kesadaran'),
                    'keluhan' => Request::get('keluhan'),
                    'pemeriksaan' => Request::get('pemeriksaan'),
                    'alergi' => Request::get('alergi'),
                    'imun_ke' => Request::get('imun'),
                    'rtl' => Request::get('rtl'),
                    'penilaian' => Request::get('penilaian'),
                    'instruksi' => Request::get('instruksi'),
                ];
        if($cek > 0){
            $insert = DB::table('pemeriksaan_ralan')
                        ->where('no_rawat', Request::get('no_rawat'))
                        ->update($data);
                    if($insert){
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Data berhasil disimpan'
                        ], 200);
                    }
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data gagal disimpan'
                    ], 500);
        }else{
            $insert = DB::table('pemeriksaan_ralan')
                        ->insert($data);
                    
            if($insert){
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil disimpan'
                ], 200);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal disimpan'
            ], 500);
        }
    }

    public function decryptData($data)
    {
        $data = Crypt::decrypt($data);
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

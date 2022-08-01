<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $kd_poli = session()->get('kd_poli');
        $kd_dokter = session()->get('username');
        $totalPasien = DB::table('pasien')->count();
        $pasienBulanIni = DB::table('pasien')->where('tgl_daftar', 'like', date('Y-m').'%')->count();
        $pasienPoliBulanIni = DB::table('reg_periksa')->where('tgl_registrasi', 'like', date('Y-m').'%')->where('kd_poli', $kd_poli)->where('stts', '<>', 'Belum')->count();
        $pasienPoliHariIni = DB::table('reg_periksa')->where('tgl_registrasi', 'like', date('Y-m-d').'%')->where('kd_poli', $kd_poli)->count();
        $pasienAktif = DB::table('reg_periksa')
                            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                            ->where('kd_dokter', $kd_dokter)
                            ->groupBy('no_rkm_medis')
                            ->orderBy('jumlah', 'desc')
                            ->selectRaw("reg_periksa.no_rkm_medis, pasien.nm_pasien, count(reg_periksa.no_rkm_medis) jumlah")
                            ->limit(10)->get();
        $pasienTerakhir = DB::table('reg_periksa')
                            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                            ->where('reg_periksa.kd_poli', $kd_poli)
                            ->where('tgl_registrasi', date('Y-m-d'))
                            ->orderBy('reg_periksa.jam_reg', 'desc')
                            ->select('reg_periksa.no_rawat', 'pasien.nm_pasien', 'reg_periksa.stts')
                            ->limit(10)
                            ->get();
        $headPasienAktif = ['No Rekam Medis', 'Nama Pasien', 'Jumlah'];
        $headPasienTerakhir = ['No Rawat', 'Nama Pasien', 'Status'];
        return view('home',[
            'totalPasien' => $totalPasien,
            'pasienBulanIni' => $pasienBulanIni,
            'pasienPoliBulanIni' => $pasienPoliBulanIni,
            'pasienPoliHariIni' => $pasienPoliHariIni,
            'pasienAktif' => array_values($pasienAktif->toArray()),
            'headPasienAktif' => $headPasienAktif,
            'headPasienTerakhir' => $headPasienTerakhir,
            'pasienTerakhir' => array_values($pasienTerakhir->toArray()),
            'poliklinik' => $this->getPoliklinik($kd_poli),
        ]);
    }

    private function getPoliklinik($kd_poli)
    {
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        return $poli->nm_poli;
    }    
}

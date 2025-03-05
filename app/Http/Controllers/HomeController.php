<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginauth')->except(['logout']);
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
        
        // Jika kd_poli atau kd_dokter tidak ada dalam session, gunakan nilai default
        if (!$kd_poli) {
            $kd_poli = '-';
        }
        
        if (!$kd_dokter) {
            $kd_dokter = '-';
        }
        
        $totalPasien = DB::table('pasien')->count();
        $pasienBulanIni = DB::table('pasien')->where('tgl_daftar', 'like', date('Y-m').'%')->count();
        $pasienPoliBulanIni = DB::table('reg_periksa')->where('tgl_registrasi', 'like', date('Y-m').'%')->where('kd_poli', $kd_poli)->where('stts', '<>', 'Belum')->count();
        $pasienPoliHariIni = DB::table('reg_periksa')->where('tgl_registrasi', 'like', date('Y-m-d').'%')->where('kd_poli', $kd_poli)->count();
        
        // Pastikan kd_dokter valid sebelum melakukan query
        if ($kd_dokter != '-') {
            $pasienAktif = DB::table('reg_periksa')
                                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                                ->where('kd_dokter', $kd_dokter)
                                ->groupBy('no_rkm_medis')
                                ->orderBy('jumlah', 'desc')
                                ->selectRaw("reg_periksa.no_rkm_medis, pasien.nm_pasien, count(reg_periksa.no_rkm_medis) jumlah")
                                ->limit(10)->get();
        } else {
            $pasienAktif = collect([]);
        }
        
        // Pastikan kd_poli valid sebelum melakukan query
        if ($kd_poli != '-') {
            $pasienTerakhir = DB::table('reg_periksa')
                                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                                ->where('reg_periksa.kd_poli', $kd_poli)
                                ->where('tgl_registrasi', date('Y-m-d'))
                                ->orderBy('reg_periksa.jam_reg', 'desc')
                                ->select('reg_periksa.no_rawat', 'pasien.nm_pasien', 'reg_periksa.stts')
                                ->limit(10)
                                ->get();
        } else {
            $pasienTerakhir = collect([]);
        }
        
        $headPasienAktif = ['No Rekam Medis', 'Nama Pasien', 'Jumlah'];
        $headPasienTerakhir = ['No Rawat', 'Nama Pasien', 'Status'];
        
        // Dapatkan statistik kunjungan jika kd_dokter valid
        $statistikKunjungan = ($kd_dokter != '-') ? $this->statistikKunjungan($kd_dokter) : collect([]);
        
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
            'statistikKunjungan' => $statistikKunjungan,
            'nm_dokter' => $this->getDokter($kd_dokter),
        ]);
    }

    private function getPoliklinik($kd_poli)
    {
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        if ($poli) {
            return $poli->nm_poli;
        }
        return 'Poliklinik tidak ditemukan';
    }
    
    private function getDokter($kd_dokter)
    {
        $dokter = DB::table('pegawai')->where('nik', $kd_dokter)->first();
        if ($dokter) {
            return $dokter->nama;
        }
        return 'Dokter tidak ditemukan';
    }
    
    public function statistikKunjungan($kd_dokter)
    {
        $data = DB::table('reg_periksa')
                    ->where('kd_dokter', $kd_dokter)
                    ->where('tgl_registrasi', 'like', date('Y').'-%')
                    ->selectRaw("MONTHNAME (tgl_registrasi) as bulan, COUNT(DISTINCT  no_rawat) as jumlah")
                    ->groupByRaw("MONTH(tgl_registrasi)")
                    ->get();
        return $data;
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}

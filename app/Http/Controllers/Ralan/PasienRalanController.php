<?php

namespace App\Http\Controllers\Ralan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use DB;
use Request;

class PasienRalanController extends Controller
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
        $tanggal = Request::get('tanggal') ?? date('Y-m-d');
        $heads = ['No. Reg', 'Nama Pasien', 'No Rawat', 'Telp', 'Dokter', 'Status'];
        $data = DB::table('reg_periksa')
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
                    ->where('reg_periksa.kd_poli', $kd_poli)
                    ->where('tgl_registrasi', $tanggal)
                    ->where('reg_periksa.kd_dokter', $kd_dokter)
                    ->orderBy('reg_periksa.jam_reg', 'desc')
                    ->select('reg_periksa.no_reg', 'pasien.nm_pasien', 'reg_periksa.no_rawat', 'pasien.no_tlp', 'dokter.nm_dokter', 'reg_periksa.stts', 'pasien.no_rkm_medis')
                    ->get();
        return view('ralan.pasien-ralan',[
            'nm_poli' => $this->getPoliklinik($kd_poli),
            'heads' => $heads,
            'data' => $data,
            'tanggal' => $tanggal,
        ]);
    }

    private function getPoliklinik($kd_poli)
    {
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        return $poli->nm_poli;
    }
    
    public static function encryptData($data)
    {
        $data = Crypt::encrypt($data);
        return $data;
    }
}

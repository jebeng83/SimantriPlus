<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    public function index(Request $request)
    {
        $kd_dokter = session()->get('username');
        $kd_poli = session()->get('kd_poli');
        $tglMulai = $request->tglMulai ?? Carbon::now()->format('Y-m-d');
        $tglAkhir = $request->tglAkhir ?? Carbon::now()->format('Y-m-d');
        $data = DB::table('booking_registrasi')
                    ->join('pasien', 'pasien.no_rkm_medis', '=', 'booking_registrasi.no_rkm_medis')
                    ->join('poliklinik', 'poliklinik.kd_poli', '=', 'booking_registrasi.kd_poli')
                    ->join('penjab', 'booking_registrasi.kd_pj', '=', 'penjab.kd_pj')
                    ->select('booking_registrasi.*', 'pasien.nm_pasien', 'poliklinik.nm_poli', 'pasien.no_tlp', 'pasien.alamat', 'penjab.png_jawab')
                    ->whereBetween('booking_registrasi.tanggal_periksa', [$tglMulai, $tglAkhir])
                    ->where('booking_registrasi.kd_dokter', $kd_dokter)
                    ->where('booking_registrasi.kd_dokter', $kd_dokter)
                    ->orderBy('booking_registrasi.no_reg', 'asc')
                    ->get();

        $heads = ['Antrian', 'Nama', 'No. RM', 'Tgl Periksa', 'No. Tlp', 'Alamat', 'Jns bayar'];
        $nmPoli = $this->getPoliklinik($kd_poli);

        return view('booking.index', compact('data', 'heads', 'tglMulai', 'tglAkhir', 'nmPoli'));
    }

    private function getPoliklinik($kd_poli)
    {
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        return $poli->nm_poli;
    }
}

<?php

namespace App\Http\Controllers\Ranap;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasienRanapController extends Controller
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
        $kd_dokter = session()->get('username');
        $heads = ['Nama', 'No. RM', 'Kamar', 'Tanggal Masuk', 'Cara Bayar', 'Lama Dirawat'];

        // Tampilkan semua pasien yang masih dirawat (kamar_inap.stts_pulang = "-")
        $data = DB::table('kamar_inap')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->join('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->where('kamar_inap.stts_pulang', '-')
            ->orderBy('bangsal.nm_bangsal', 'asc') // Urutkan berdasarkan kamar
            ->select(
                'pasien.nm_pasien',
                'reg_periksa.no_rkm_medis',
                'bangsal.nm_bangsal',
                'kamar_inap.kd_kamar',
                'kamar_inap.tgl_masuk',
                'penjab.png_jawab',
                'reg_periksa.no_rawat',
                'bangsal.kd_bangsal'
            )
            ->get();

        // Hitung lama dirawat untuk masing-masing pasien
        foreach ($data as $row) {
            $tglMasuk = Carbon::parse($row->tgl_masuk);
            $now = Carbon::now();
            $lamaDirawat = $tglMasuk->diffInDays($now);
            $row->lama_dirawat = $lamaDirawat;
        }

        // Dapatkan nama dokter yang login
        $dokterLogin = null;
        if ($kd_dokter) {
            $dokterLogin = DB::table('dokter')->where('kd_dokter', $kd_dokter)->first();
        }

        // Hitung statistik untuk cara bayar
        $bpjsCount = collect($data)->where('png_jawab', 'BPJS')->count();
        $umumCount = collect($data)->where('png_jawab', 'UMUM')->count();
        $lainCount = collect($data)->whereNotIn('png_jawab', ['BPJS', 'UMUM'])->count();

        return view('ranap.pasien-ranap', [
            'heads' => $heads,
            'data' => $data,
            'bpjs_count' => $bpjsCount,
            'umum_count' => $umumCount,
            'lain_count' => $lainCount,
            'dokter_login' => $dokterLogin
        ]);
    }

    /**
     * Encrypt data for security
     *
     * @param mixed $data
     * @return string
     */
    public static function encryptData($data)
    {
        return Crypt::encrypt($data);
    }
}

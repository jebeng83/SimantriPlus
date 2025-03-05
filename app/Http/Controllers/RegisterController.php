<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RegisterController extends Controller
{
    public function index()
    {
        // $register = DB::table('reg_periksa')
        //     ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        //     ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
        //     ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        //     ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
        //     ->where('tgl_registrasi', date('Y-m-d'))
        //     ->where('stts', 'Belum')
        //     ->select('reg_periksa.*', 'pasien.nm_pasien', 'dokter.nm_dokter', 'poliklinik.nm_poli', 'penjab.png_jawab', 'pasien.no_tlp', 'pasien.jk')
        //     ->get();

        // $heads = ['No. Reg', 'No. Rawat', 'Tanggal', 'Jam', 'Dokter', 'No. RM', 'Pasien', 'JK', 'Umur', 'Poliklinik', 'Jenis Bayar', 'Penanggung Jawab', 'Alamat PJ', 'Hubungan PJ', 'Biaya Registrasi', 'Status', 'No. Telp', 'Stts Rawat', 'Stts Poli', 'Status Bayar'];

        return view('register.index', [
            // 'register' => $register,
            // 'heads' => $heads,
        ]);
    }

    public function getPasien(Request $request)
    {
        $q = $request->get('q');
        $limit = $request->get('limit', 5);
        $isPreload = $request->get('preload', false);
        
        // Jika ini adalah permintaan preload, gunakan cache
        if ($isPreload) {
            return Cache::remember('pasien_preload', 3600, function () use ($limit) {
                return DB::table('pasien')
                    ->orderBy('no_rkm_medis', 'desc') // Menggunakan no_rkm_medis sebagai pengganti updated_at
                    ->limit($limit)
                    ->selectRaw("no_rkm_medis as id, CONCAT(no_ktp, ' - ', nm_pasien) as text, no_ktp, kelurahanpj")
                    ->get();
            });
        }
        
        // Jika q kosong, kembalikan data terbaru
        if (empty($q)) {
            return DB::table('pasien')
                ->orderBy('no_rkm_medis', 'desc') // Menggunakan no_rkm_medis sebagai pengganti updated_at
                ->limit($limit)
                ->selectRaw("no_rkm_medis as id, CONCAT(no_ktp, ' - ', nm_pasien) as text, no_ktp, kelurahanpj")
                ->get();
        }
        
        // Cache key berdasarkan query dan limit
        $cacheKey = 'pasien_search_' . md5($q . $limit);
        
        // Coba ambil dari cache dulu
        return Cache::remember($cacheKey, 300, function () use ($q, $limit) {
            $que = '%' . $q . '%';
            return DB::table('pasien')
                ->where('nm_pasien', 'like', $que)
                ->orWhere('no_rkm_medis', 'like', $que)
                ->orWhere('no_ktp', 'like', $que)
                ->orWhere('no_peserta', 'like', $que)
                ->orWhere('alamat', 'like', $que)
                ->selectRaw("no_rkm_medis as id, CONCAT(no_ktp, ' - ', nm_pasien) as text, no_ktp, kelurahanpj")
                ->limit($limit)
                ->get();
        });
    }

    public function getDokter(Request $request)
    {
        $q = $request->get('q');
        $limit = $request->get('limit', 5);
        $isPreload = $request->get('preload', false);
        
        // Jika ini adalah permintaan preload, gunakan cache
        if ($isPreload) {
            return Cache::remember('dokter_preload', 3600, function () use ($limit) {
                return DB::table('dokter')
                    ->orderBy('nm_dokter', 'asc')
                    ->limit($limit)
                    ->selectRaw("kd_dokter as id, CONCAT(kd_dokter, ' - ', nm_dokter) as text")
                    ->get();
            });
        }
        
        // Jika q kosong, kembalikan data terbaru
        if (empty($q)) {
            return DB::table('dokter')
                ->orderBy('nm_dokter', 'asc')
                ->limit($limit)
                ->selectRaw("kd_dokter as id, CONCAT(kd_dokter, ' - ', nm_dokter) as text")
                ->get();
        }
        
        // Cache key berdasarkan query dan limit
        $cacheKey = 'dokter_search_' . md5($q . $limit);
        
        // Coba ambil dari cache dulu
        return Cache::remember($cacheKey, 300, function () use ($q, $limit) {
            $que = '%' . $q . '%';
            return DB::table('dokter')
                ->where('nm_dokter', 'like', $que)
                ->orWhere('kd_dokter', 'like', $que)
                ->selectRaw("kd_dokter as id, CONCAT(kd_dokter, ' - ', nm_dokter) as text")
                ->limit($limit)
                ->get();
        });
    }
}

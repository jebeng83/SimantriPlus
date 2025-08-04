<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RegisterController extends Controller
{
    public function index()
    {
        // Ambil data poliklinik untuk dropdown filter
        $poliklinik = DB::table('poliklinik')
            ->select('kd_poli', 'nm_poli')
            ->orderBy('nm_poli')
            ->get();

        // Hitung statistik pasien hari ini
        $today = date('Y-m-d');
        $totalPasien = DB::table('reg_periksa')
            ->where('tgl_registrasi', $today)
            ->where('stts', 'Belum')
            ->count();

        $belumPeriksa = DB::table('reg_periksa')
            ->where('tgl_registrasi', $today)
            ->where('stts', 'Belum')
            ->count();

        return view('register.index', [
            'poliklinik' => $poliklinik,
            'totalPasien' => $totalPasien,
            'belumPeriksa' => $belumPeriksa,
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

    public function getStats(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $kdPoli = $request->get('kd_poli');

        $query = DB::table('reg_periksa')
            ->where('tgl_registrasi', $date)
            ->where('stts', 'Belum');

        if ($kdPoli) {
            $query->where('kd_poli', $kdPoli);
        }

        $totalPasien = $query->count();
        $belumPeriksa = $query->count(); // Sama karena sudah filter stts = 'Belum'

        return response()->json([
            'totalPasien' => $totalPasien,
            'belumPeriksa' => $belumPeriksa
        ]);
    }

    public function getPoliklinik(Request $request)
    {
        $q = $request->get('q');
        $limit = $request->get('limit', 10);
        
        $query = DB::table('poliklinik')
            ->select('kd_poli as id', 'nm_poli as text')
            ->orderBy('nm_poli');

        if (!empty($q)) {
            $que = '%' . $q . '%';
            $query->where('nm_poli', 'like', $que)
                  ->orWhere('kd_poli', 'like', $que);
        }

        return $query->limit($limit)->get();
    }
}

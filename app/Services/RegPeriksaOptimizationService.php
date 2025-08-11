<?php

namespace App\Services;

use App\Models\RegPeriksa;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class RegPeriksaOptimizationService
{
    /**
     * Cache duration constants (in seconds)
     */
    const CACHE_STATISTICS = 300; // 5 minutes
    const CACHE_QUICK_STATS = 120; // 2 minutes
    const CACHE_DETAILED_STATS = 600; // 10 minutes
    
    /**
     * Get optimized query builder for reg_periksa with proper indexing
     *
     * @param string|null $tanggal
     * @param string|null $poliklinik
     * @param string|null $dokter
     * @return Builder
     */
    public function getOptimizedQuery($tanggal = null, $poliklinik = null, $dokter = null): Builder
    {
        $tanggal = $tanggal ?: Carbon::today()->format('Y-m-d');
        
        $query = RegPeriksa::query()
            ->select([
                // Optimized select - hanya kolom yang diperlukan
                'reg_periksa.no_rawat',
                'reg_periksa.no_reg',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_dokter',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.biaya_reg',
                'reg_periksa.p_jawab',
                'reg_periksa.almt_pj',
                'reg_periksa.hubunganpj',
                // Kolom dari joined tables
                'pasien.nm_pasien',
                'pasien.no_tlp',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.no_peserta',
                'pasien.no_ktp',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'penjab.png_jawab'
            ])
            // Optimized joins dengan proper order
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            // Filter conditions dalam urutan optimal untuk composite index
            ->where('reg_periksa.stts', 'Belum')
            ->where('reg_periksa.tgl_registrasi', $tanggal);
            
        // Apply additional filters
        if ($poliklinik) {
            $query->where('reg_periksa.kd_poli', $poliklinik);
        }
        
        if ($dokter) {
            $query->where('reg_periksa.kd_dokter', $dokter);
        }
        
        return $query;
    }
    
    /**
     * Get cached total pasien hari ini
     *
     * @param string|null $tanggal
     * @return int
     */
    public function getTotalPasienHariIni($tanggal = null): int
    {
        $tanggal = $tanggal ?: Carbon::today()->format('Y-m-d');
        $cacheKey = "total_pasien_hari_ini_{$tanggal}";
        
        return Cache::remember($cacheKey, self::CACHE_STATISTICS, function () use ($tanggal) {
            return RegPeriksa::where('tgl_registrasi', $tanggal)->count();
        });
    }
    
    /**
     * Get cached total pasien belum periksa
     *
     * @param string|null $tanggal
     * @return int
     */
    public function getTotalPasienBelumPeriksa($tanggal = null): int
    {
        $tanggal = $tanggal ?: Carbon::today()->format('Y-m-d');
        $cacheKey = "total_pasien_belum_periksa_{$tanggal}";
        
        return Cache::remember($cacheKey, self::CACHE_QUICK_STATS, function () use ($tanggal) {
            return RegPeriksa::where('tgl_registrasi', $tanggal)
                             ->where('stts', 'Belum')
                             ->count();
        });
    }
    
    /**
     * Get cached statistik per poliklinik
     *
     * @param string|null $tanggal
     * @return \Illuminate\Support\Collection
     */
    public function getStatistikPoliklinik($tanggal = null)
    {
        $tanggal = $tanggal ?: Carbon::today()->format('Y-m-d');
        $cacheKey = "statistik_poliklinik_{$tanggal}";
        
        return Cache::remember($cacheKey, self::CACHE_DETAILED_STATS, function () use ($tanggal) {
            return DB::table('reg_periksa')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('reg_periksa.tgl_registrasi', $tanggal)
                ->select(
                    'poliklinik.kd_poli',
                    'poliklinik.nm_poli',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN reg_periksa.stts = "Belum" THEN 1 ELSE 0 END) as belum_periksa'),
                    DB::raw('SUM(CASE WHEN reg_periksa.stts = "Sudah" THEN 1 ELSE 0 END) as sudah_periksa')
                )
                ->groupBy('poliklinik.kd_poli', 'poliklinik.nm_poli')
                ->orderBy('poliklinik.nm_poli')
                ->get();
        });
    }
    
    /**
     * Get cached statistik per dokter
     *
     * @param string|null $tanggal
     * @param string|null $poliklinik
     * @return \Illuminate\Support\Collection
     */
    public function getStatistikDokter($tanggal = null, $poliklinik = null)
    {
        $tanggal = $tanggal ?: Carbon::today()->format('Y-m-d');
        $cacheKey = "statistik_dokter_{$tanggal}" . ($poliklinik ? "_{$poliklinik}" : '');
        
        return Cache::remember($cacheKey, self::CACHE_DETAILED_STATS, function () use ($tanggal, $poliklinik) {
            $query = DB::table('reg_periksa')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('reg_periksa.tgl_registrasi', $tanggal)
                ->select(
                    'dokter.kd_dokter',
                    'dokter.nm_dokter',
                    'poliklinik.nm_poli',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN reg_periksa.stts = "Belum" THEN 1 ELSE 0 END) as belum_periksa'),
                    DB::raw('SUM(CASE WHEN reg_periksa.stts = "Sudah" THEN 1 ELSE 0 END) as sudah_periksa')
                )
                ->groupBy('dokter.kd_dokter', 'dokter.nm_dokter', 'poliklinik.nm_poli');
                
            if ($poliklinik) {
                $query->where('reg_periksa.kd_poli', $poliklinik);
            }
            
            return $query->orderBy('dokter.nm_dokter')->get();
        });
    }
    
    /**
     * Clear all related caches
     *
     * @param string|null $tanggal
     * @return void
     */
    public function clearAllCaches($tanggal = null): void
    {
        $tanggal = $tanggal ?: Carbon::today()->format('Y-m-d');
        
        $cacheKeys = [
            "total_pasien_hari_ini_{$tanggal}",
            "total_pasien_belum_periksa_{$tanggal}",
            "statistik_poliklinik_{$tanggal}",
            "statistik_dokter_{$tanggal}"
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        // Clear cache untuk semua poliklinik juga
        $polikliniks = DB::table('poliklinik')->pluck('kd_poli');
        foreach ($polikliniks as $kd_poli) {
            Cache::forget("statistik_dokter_{$tanggal}_{$kd_poli}");
        }
    }
    
    /**
     * Get query execution plan untuk debugging
     *
     * @param Builder $query
     * @return array
     */
    public function getQueryExecutionPlan(Builder $query): array
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        
        // Replace bindings in SQL for EXPLAIN
        foreach ($bindings as $binding) {
            $sql = preg_replace('/\?/', "'$binding'", $sql, 1);
        }
        
        return DB::select("EXPLAIN $sql");
    }
    
    /**
     * Log slow query untuk monitoring
     *
     * @param string $query
     * @param array $bindings
     * @param float $time
     * @return void
     */
    public function logSlowQuery(string $query, array $bindings, float $time): void
    {
        if ($time > 1000) { // Log jika lebih dari 1 detik
            Log::warning('Slow Query Detected in RegPeriksaOptimizationService', [
                'sql' => $query,
                'bindings' => $bindings,
                'time' => $time . 'ms',
                'timestamp' => Carbon::now()->toDateTimeString()
            ]);
        }
    }
}
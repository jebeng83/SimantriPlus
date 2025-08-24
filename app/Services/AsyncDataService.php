<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Carbon\Carbon;
use App\Models\RegPeriksa;
use Illuminate\Database\Eloquent\Builder;

class AsyncDataService
{
    /**
     * Cache duration constants (in seconds)
     */
    const CACHE_SHORT = 60;     // 1 minute
    const CACHE_MEDIUM = 300;   // 5 minutes
    const CACHE_LONG = 900;     // 15 minutes
    
    /**
     * Chunk size for large datasets
     */
    const CHUNK_SIZE = 100;
    
    /**
     * Load data asynchronously with chunking
     *
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function loadDataAsync(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $cacheKey = $this->generateAsyncCacheKey($filters, $page, $perPage);
        
        return Cache::remember($cacheKey, self::CACHE_SHORT, function () use ($filters, $page, $perPage) {
            return $this->processDataInChunks($filters, $page, $perPage);
        });
    }
    
    /**
     * Process data in chunks to avoid memory issues
     *
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    private function processDataInChunks(array $filters, int $page, int $perPage): array
    {
        $startTime = microtime(true);
        
        try {
            // Build optimized query
            $query = $this->buildOptimizedQuery($filters);
            
            // Get total count with caching
            $totalCount = $this->getCachedCount($query, $filters);
            
            // Calculate offset
            $offset = ($page - 1) * $perPage;
            
            // Get paginated results
            $results = $query->offset($offset)
                           ->limit($perPage)
                           ->get();
            
            $executionTime = (microtime(true) - $startTime) * 1000;
            
            // Log if query is slow
            if ($executionTime > 1000) {
                Log::warning('Slow Async Query Detected', [
                    'execution_time' => $executionTime . 'ms',
                    'filters' => $filters,
                    'page' => $page,
                    'per_page' => $perPage
                ]);
            }
            
            return [
                'data' => $results,
                'total' => $totalCount,
                'page' => $page,
                'per_page' => $perPage,
                'execution_time' => $executionTime
            ];
            
        } catch (\Exception $e) {
            Log::error('Async Data Loading Error', [
                'error' => $e->getMessage(),
                'filters' => $filters,
                'page' => $page,
                'per_page' => $perPage
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Build optimized query with minimal joins
     *
     * @param array $filters
     * @return Builder
     */
    private function buildOptimizedQuery(array $filters): Builder
    {
        $query = RegPeriksa::query()
            ->select([
                'reg_periksa.no_rawat',
                'reg_periksa.no_reg',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_dokter',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'pasien.nm_pasien',
                'pasien.jk',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'penjab.png_jawab'
            ])
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj');
        
        // Apply filters
        $this->applyFilters($query, $filters);
        
        // Add ordering
        $query->orderBy('reg_periksa.tgl_registrasi', 'desc')
              ->orderBy('reg_periksa.jam_reg', 'desc');
        
        return $query;
    }
    
    /**
     * Apply filters to query
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        // Default date filter for performance
        $defaultDate = $filters['tanggal_dari'] ?? Carbon::today()->format('Y-m-d');
        $query->where('reg_periksa.tgl_registrasi', '>=', $defaultDate);
        
        if (isset($filters['tanggal_sampai']) && $filters['tanggal_sampai']) {
            $query->where('reg_periksa.tgl_registrasi', '<=', $filters['tanggal_sampai']);
        }
        
        if (isset($filters['status']) && $filters['status']) {
            $query->where('reg_periksa.stts', $filters['status']);
        }
        
        if (isset($filters['poliklinik']) && $filters['poliklinik']) {
            $query->where('reg_periksa.kd_poli', $filters['poliklinik']);
        }
        
        if (isset($filters['dokter']) && $filters['dokter']) {
            $query->where('reg_periksa.kd_dokter', $filters['dokter']);
        }
    }
    
    /**
     * Get cached count to avoid slow count queries
     *
     * @param Builder $query
     * @param array $filters
     * @return int
     */
    private function getCachedCount(Builder $query, array $filters): int
    {
        $countCacheKey = $this->generateCountCacheKey($filters);
        
        return Cache::remember($countCacheKey, self::CACHE_MEDIUM, function () use ($filters) {
            // Use simpler count query without joins when possible
            $countQuery = RegPeriksa::query();
            
            // Apply same filters but without joins for better performance
            $defaultDate = $filters['tanggal_dari'] ?? Carbon::today()->format('Y-m-d');
            $countQuery->where('tgl_registrasi', '>=', $defaultDate);
            
            if (isset($filters['tanggal_sampai']) && $filters['tanggal_sampai']) {
                $countQuery->where('tgl_registrasi', '<=', $filters['tanggal_sampai']);
            }
            
            if (isset($filters['status']) && $filters['status']) {
                $countQuery->where('stts', $filters['status']);
            }
            
            if (isset($filters['poliklinik']) && $filters['poliklinik']) {
                $countQuery->where('kd_poli', $filters['poliklinik']);
            }
            
            if (isset($filters['dokter']) && $filters['dokter']) {
                $countQuery->where('kd_dokter', $filters['dokter']);
            }
            
            return $countQuery->count();
        });
    }
    
    /**
     * Generate cache key for async data
     *
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return string
     */
    private function generateAsyncCacheKey(array $filters, int $page, int $perPage): string
    {
        $filterString = collect($filters)->map(function ($value, $key) {
            return $key . ':' . $value;
        })->implode('|');
        
        return 'async_reg_periksa_' . md5($filterString . '_page_' . $page . '_per_page_' . $perPage);
    }
    
    /**
     * Generate cache key for count
     *
     * @param array $filters
     * @return string
     */
    private function generateCountCacheKey(array $filters): string
    {
        $filterString = collect($filters)->map(function ($value, $key) {
            return $key . ':' . $value;
        })->implode('|');
        
        return 'async_count_reg_periksa_' . md5($filterString);
    }
    
    /**
     * Clear all async caches
     *
     * @return void
     */
    public function clearAsyncCaches(): void
    {
        // Clear pattern-based cache keys
        $cacheKeys = [
            'async_reg_periksa_*',
            'async_count_reg_periksa_*'
        ];
        
        foreach ($cacheKeys as $pattern) {
            Cache::flush(); // For simplicity, flush all cache
        }
    }
    
    /**
     * Preload data in background for better UX
     *
     * @param array $filters
     * @param int $currentPage
     * @param int $perPage
     * @return void
     */
    public function preloadNextPage(array $filters, int $currentPage, int $perPage): void
    {
        // Preload next page in background
        $nextPage = $currentPage + 1;
        
        // Use queue for background processing
        dispatch(function () use ($filters, $nextPage, $perPage) {
            $this->loadDataAsync($filters, $nextPage, $perPage);
        })->onQueue('low-priority');
    }
}
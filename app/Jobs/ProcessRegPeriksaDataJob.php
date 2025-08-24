<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\RegPeriksa;
use Carbon\Carbon;

class ProcessRegPeriksaDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $page;
    protected $perPage;
    protected $cacheKey;

    /**
     * Create a new job instance.
     *
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @param string $cacheKey
     */
    public function __construct(array $filters, int $page, int $perPage, string $cacheKey)
    {
        $this->filters = $filters;
        $this->page = $page;
        $this->perPage = $perPage;
        $this->cacheKey = $cacheKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $startTime = microtime(true);
            
            // Process data in background
            $data = $this->processDataInBackground();
            
            // Cache the results
            Cache::put($this->cacheKey, $data, 300); // 5 minutes
            
            $executionTime = (microtime(true) - $startTime) * 1000;
            
            Log::info('Background data processing completed', [
                'cache_key' => $this->cacheKey,
                'execution_time' => $executionTime . 'ms',
                'page' => $this->page,
                'per_page' => $this->perPage
            ]);
            
        } catch (\Exception $e) {
            Log::error('Background data processing failed', [
                'error' => $e->getMessage(),
                'cache_key' => $this->cacheKey,
                'filters' => $this->filters
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Process data with optimized query
     *
     * @return array
     */
    private function processDataInBackground(): array
    {
        // Use raw SQL for better performance
        $sql = $this->buildOptimizedRawSQL();
        
        // Execute with pagination
        $offset = ($this->page - 1) * $this->perPage;
        $results = DB::select($sql . " LIMIT {$this->perPage} OFFSET {$offset}");
        
        // Get count separately with simpler query
        $countSql = $this->buildCountSQL();
        $countResult = DB::select($countSql);
        $totalCount = $countResult[0]->total ?? 0;
        
        return [
            'data' => $results,
            'total' => $totalCount,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'processed_at' => Carbon::now()->toDateTimeString()
        ];
    }
    
    /**
     * Build optimized raw SQL query
     *
     * @return string
     */
    private function buildOptimizedRawSQL(): string
    {
        $sql = "
            SELECT 
                rp.no_rawat,
                rp.no_reg,
                rp.tgl_registrasi,
                rp.jam_reg,
                rp.no_rkm_medis,
                rp.kd_dokter,
                rp.kd_poli,
                rp.kd_pj,
                rp.stts,
                p.nm_pasien,
                p.jk,
                d.nm_dokter,
                pol.nm_poli,
                pj.png_jawab
            FROM reg_periksa rp
            FORCE INDEX (idx_reg_periksa_composite)
            INNER JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
            INNER JOIN dokter d ON rp.kd_dokter = d.kd_dokter
            INNER JOIN poliklinik pol ON rp.kd_poli = pol.kd_poli
            INNER JOIN penjab pj ON rp.kd_pj = pj.kd_pj
            WHERE 1=1
        ";
        
        // Apply filters
        $defaultDate = $this->filters['tanggal_dari'] ?? Carbon::today()->format('Y-m-d');
        $sql .= " AND rp.tgl_registrasi >= '{$defaultDate}'";
        
        if (isset($this->filters['tanggal_sampai']) && $this->filters['tanggal_sampai']) {
            $sql .= " AND rp.tgl_registrasi <= '{$this->filters['tanggal_sampai']}'";
        }
        
        if (isset($this->filters['status']) && $this->filters['status']) {
            $sql .= " AND rp.stts = '{$this->filters['status']}'";
        }
        
        if (isset($this->filters['poliklinik']) && $this->filters['poliklinik']) {
            $sql .= " AND rp.kd_poli = '{$this->filters['poliklinik']}'";
        }
        
        if (isset($this->filters['dokter']) && $this->filters['dokter']) {
            $sql .= " AND rp.kd_dokter = '{$this->filters['dokter']}'";
        }
        
        $sql .= " ORDER BY rp.tgl_registrasi DESC, rp.jam_reg DESC";
        
        return $sql;
    }
    
    /**
     * Build optimized count SQL
     *
     * @return string
     */
    private function buildCountSQL(): string
    {
        $sql = "
            SELECT COUNT(*) as total
            FROM reg_periksa rp
            FORCE INDEX (idx_reg_periksa_tgl_registrasi)
            WHERE 1=1
        ";
        
        // Apply same filters but without joins
        $defaultDate = $this->filters['tanggal_dari'] ?? Carbon::today()->format('Y-m-d');
        $sql .= " AND rp.tgl_registrasi >= '{$defaultDate}'";
        
        if (isset($this->filters['tanggal_sampai']) && $this->filters['tanggal_sampai']) {
            $sql .= " AND rp.tgl_registrasi <= '{$this->filters['tanggal_sampai']}'";
        }
        
        if (isset($this->filters['status']) && $this->filters['status']) {
            $sql .= " AND rp.stts = '{$this->filters['status']}'";
        }
        
        if (isset($this->filters['poliklinik']) && $this->filters['poliklinik']) {
            $sql .= " AND rp.kd_poli = '{$this->filters['poliklinik']}'";
        }
        
        if (isset($this->filters['dokter']) && $this->filters['dokter']) {
            $sql .= " AND rp.kd_dokter = '{$this->filters['dokter']}'";
        }
        
        return $sql;
    }
}
<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\RegPeriksa;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Support\Facades\Date;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use App\Models\Poliklinik;
use App\Models\Dokter;
use Carbon\Carbon;
use App\Models\AntreanBpjsLog;
use App\Services\RegPeriksaOptimizationService;
use App\Services\AsyncDataService;
use App\Jobs\ProcessRegPeriksaDataJob;
class RegPeriksaTable extends DataTableComponent
{
    protected $model = RegPeriksa::class;
    
    public $tanggalFilter;
    public $poliklinikFilter;
    
    // Filter Lock Properties
    public $isFilterLocked = false;
    public $persistedFilters = [];
    
    protected $listeners = [
        'filterByPoliklinik', 
        'refreshDatatable' => 'refreshData', 
        'registrationSuccess' => 'refreshData', 
        'resetFilters', 
        'toggleFilterLock', 
        'loadPersistedFilters', 
        'getFilterLockStatus',
        'initializeComponent' => 'handleComponentInitialization'
    ];
    
    protected $optimizationService;
    protected $asyncDataService;

    public function mount()
    {
        // Load filter lock status from session
        $this->isFilterLocked = session('reg_periksa_filter_lock', false);
        
        // Component mounted - filter lock status loaded from session
        Log::info('RegPeriksaTable mounted', [
            'isFilterLocked' => $this->isFilterLocked,
            'session_id' => session()->getId()
        ]);
        
        // Load persisted filters if filter is locked
        if ($this->isFilterLocked) {
            $this->loadPersistedFilters();
            Log::info('Loaded persisted filters', ['filters' => $this->persistedFilters]);
        } else {
            // Set default tanggal ke hari ini jika filter tidak terkunci
            $this->tanggalFilter = Carbon::today()->format('Y-m-d');
        }
        
        // Initialize optimization service
        $this->initializeOptimizationService();
        
        // Emit initial filter lock status after mount
        $this->dispatchBrowserEvent('component-mounted', [
            'isFilterLocked' => $this->isFilterLocked
        ]);
    }
    
    /**
     * Pastikan optimization service terinisialisasi
     */
    private function initializeOptimizationService()
    {
        if (!$this->optimizationService) {
            $this->optimizationService = new RegPeriksaOptimizationService();
        }
        
        if (!$this->asyncDataService) {
            $this->asyncDataService = new AsyncDataService();
        }
    }

    public function configure(): void
    {
        $this->setPrimaryKey('no_rawat');
        $this->setPerPageAccepted([5, 10, 25, 50]);
        $this->setPerPage(10);
        $this->setDefaultSort('tgl_registrasi', 'desc');
        
        // Configure filters with proper layout and buttons
        $this->setFiltersEnabled();
        $this->setFiltersVisibilityEnabled();
        $this->setFilterPillsEnabled();
        $this->setFilterLayoutSlideDown();
        $this->setFilterSlideDownDefaultStatusEnabled();
        
        // Optimize pagination for large datasets
        $this->setPaginationMethod('simple');
        $this->setUseHeaderAsFooterEnabled();
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Tanggal Dari')
                ->config([
                    'placeholder' => 'Pilih tanggal mulai',
                    'allowInput' => true,
                    'disabled' => false,
                ])
                ->setFilterDefaultValue(Carbon::today()->format('Y-m-d'))
                ->filter(function (Builder $builder, string $value) {
                    if ($value) {
                        $builder->where('reg_periksa.tgl_registrasi', '>=', $value);
                    }
                }),
            DateFilter::make('Tanggal Sampai')
                ->config([
                    'placeholder' => 'Pilih tanggal akhir',
                    'allowInput' => true,
                    'disabled' => false,
                ])
                ->setFilterDefaultValue(Carbon::today()->format('Y-m-d'))
                ->filter(function (Builder $builder, string $value) {
                    if ($value) {
                        $builder->where('reg_periksa.tgl_registrasi', '<=', $value);
                    }
                }),
            SelectFilter::make('Status')
                ->options([
                    '' => 'Semua Status',
                    'Belum' => 'Belum Periksa',
                    'Berkas Diterima' => 'Berkas Diterima',
                    'Sudah' => 'Sudah Periksa',
                    'Batal' => 'Batal',
                    'Dirujuk' => 'Dirujuk',
                ])
                ->filter(function (Builder $builder, $value) {
                    if ($value) {
                        $builder->where('reg_periksa.stts', $value);
                    }
                }),
            SelectFilter::make('Poliklinik')
                ->options($this->getPoliklinikOptions())
                ->filter(function (Builder $builder, $value) {
                    if ($value) {
                        $builder->where('reg_periksa.kd_poli', $value);
                    }
                }),
            SelectFilter::make('Dokter')
                ->options($this->getDokterOptions())
                ->filter(function (Builder $builder, $value) {
                    if ($value) {
                        $builder->where('reg_periksa.kd_dokter', $value);
                    }
                })
        ];
    }

    public function builder(): Builder
    {
        // Use optimization service for async loading and caching
        $this->initializeOptimizationService();
        
        // Get base query with optimizations
        $query = RegPeriksa::query()
            ->select([
                // Minimal essential columns for initial load
                'reg_periksa.no_rawat',
                'reg_periksa.no_reg',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_dokter',
                'reg_periksa.kd_poli',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                // Essential display columns with aliases
                'pasien.nm_pasien',
                'pasien.jk',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'penjab.png_jawab'
            ])
            // Optimized joins with proper index usage
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj');
            
        // Apply default date filter for performance
        $defaultDate = $this->getAppliedFilterWithValue('tanggal_dari') ?? Carbon::today()->format('Y-m-d');
        if ($defaultDate) {
            $query->where('reg_periksa.tgl_registrasi', '>=', $defaultDate);
        }
        
        // Add ordering with index-friendly approach
        $query->orderBy('reg_periksa.tgl_registrasi', 'desc')
              ->orderBy('reg_periksa.jam_reg', 'desc');
              
        return $query;
    }

    public function refreshData()
    {
        // Pastikan optimization service terinisialisasi
        $this->initializeOptimizationService();
        
        // Clear cache ketika refresh data menggunakan optimization service
        $this->optimizationService->clearAllCaches();
        
        // Force refresh the component
        $this->resetPage();
        $this->emit('$refresh');
    }
    
    /**
     * Override getRowsProperty untuk implementasi async loading dengan job queue
     */
    public function getRowsProperty()
    {
        $cacheKey = $this->generateCacheKey();
        
        // Try to get from cache first
        $cachedData = Cache::get($cacheKey);
        
        if ($cachedData) {
            return collect($cachedData['data']);
        }
        
        // If not in cache, dispatch background job
        $this->dispatchBackgroundJob($cacheKey);
        
        // Return minimal placeholder data while processing
        return $this->getPlaceholderData();
    }
    
    /**
     * Dispatch background job for data processing
     */
    private function dispatchBackgroundJob(string $cacheKey): void
    {
        $jobCacheKey = "job_dispatched_{$cacheKey}";
        
        // Prevent duplicate job dispatch
        if (!Cache::has($jobCacheKey)) {
            ProcessRegPeriksaDataJob::dispatch(
                $this->getAppliedFilters(),
                $this->page,
                $this->getPerPage(),
                $cacheKey
            )->onQueue('data-processing');
            
            // Mark job as dispatched for 1 minute
            Cache::put($jobCacheKey, true, 60);
        }
    }
    
    /**
     * Get placeholder data while background processing
     */
    private function getPlaceholderData()
    {
        // Return empty collection with loading indicator
        return collect([]);
    }
    
    /**
     * Override getTotalRowCountProperty untuk optimasi count query
     */
    public function getTotalRowCountProperty()
    {
        // Cache count query untuk menghindari slow query
        $countCacheKey = $this->generateCountCacheKey();
        
        return Cache::remember($countCacheKey, 120, function () {
            // Gunakan query yang lebih sederhana untuk count
            $query = RegPeriksa::query();
            
            // Apply filters yang sama seperti di builder
            $defaultDate = $this->getAppliedFilterWithValue('tanggal_dari') ?? Carbon::today()->format('Y-m-d');
            if ($defaultDate) {
                $query->where('tgl_registrasi', '>=', $defaultDate);
            }
            
            // Apply filter lainnya jika ada
            if ($this->hasFilter('tanggal_sampai')) {
                $endDate = $this->getAppliedFilterWithValue('tanggal_sampai');
                if ($endDate) {
                    $query->where('tgl_registrasi', '<=', $endDate);
                }
            }
            
            if ($this->hasFilter('status')) {
                $status = $this->getAppliedFilterWithValue('status');
                if ($status) {
                    $query->where('stts', $status);
                }
            }
            
            if ($this->hasFilter('poliklinik')) {
                $poli = $this->getAppliedFilterWithValue('poliklinik');
                if ($poli) {
                    $query->where('kd_poli', $poli);
                }
            }
            
            if ($this->hasFilter('dokter')) {
                $dokter = $this->getAppliedFilterWithValue('dokter');
                if ($dokter) {
                    $query->where('kd_dokter', $dokter);
                }
            }
            
            return $query->count();
        });
    }
    
    /**
     * Generate cache key untuk rows dengan job queue support
     */
    private function generateCacheKey(): string
    {
        $filters = $this->getAppliedFilters();
        $page = $this->page;
        $perPage = $this->getPerPage();
        
        return 'reg_periksa_data_' . md5(serialize([
            'filters' => $filters,
            'page' => $page,
            'per_page' => $perPage
        ]));
    }
    
    /**
     * Generate cache key untuk count
     */
    private function generateCountCacheKey(): string
    {
        $filters = collect($this->getAppliedFilters())->map(function ($value, $key) {
            return $key . ':' . $value;
        })->implode('|');
        
        return 'reg_periksa_count_' . md5($filters);
    }
    
    /**
     * Clear cache ketika ada perubahan filter dan save filters jika locked
     */
    public function updatedFilters()
    {
        // Clear cache ketika filter berubah
        $this->clearFilterCache();
        $this->resetPage();
        
        // Save filters if locked
        if ($this->isFilterLocked) {
            $this->saveCurrentFilters();
        }
    }
    
    /**
     * Clear filter-related cache
     */
    private function clearFilterCache()
    {
        // Clear cache untuk rows dan count
        Cache::forget($this->generateCacheKey());
        Cache::forget($this->generateCountCacheKey());
        
        // Clear optimization service cache juga
        $this->initializeOptimizationService();
        $this->optimizationService->clearAllCaches();
    }
    
    public function hapus($no_rawat)
    {
        try {
            RegPeriksa::where('no_rawat', $no_rawat)->delete();
            
            // Clear cache setelah hapus data
            $this->optimizationService->clearAllCaches();
            
            $this->refreshData();
            $this->emit('refreshDatatable');
            session()->flash('success', 'Data registrasi berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data registrasi: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk mendapatkan timestamp dalam format milliseconds
     * yang diperlukan oleh BPJS (UTC timezone)
     * 
     * @param string|null $datetime Format Y-m-d H:i:s, default waktu sekarang
     * @return int Timestamp dalam milliseconds (UTC)
     */
    private function getTimestampMillis($datetime = null)
    {
        if (empty($datetime)) {
            // Gunakan waktu sekarang dalam UTC
            $carbon = Carbon::now('UTC');
        } else {
            // Parse datetime dan convert ke UTC
            $carbon = Carbon::parse($datetime)->utc();
        }
        
        // Return timestamp dalam milliseconds
        return (int)($carbon->timestamp * 1000);
    }
    
    /**
     * Get total pasien hari ini dengan caching menggunakan optimization service
     */
    public function getTotalPasienHariIni()
    {
        return $this->optimizationService->getTotalPasienHariIni();
    }
    
    /**
     * Get total pasien belum periksa dengan caching menggunakan optimization service
     */
    public function getTotalPasienBelumPeriksa()
    {
        return $this->optimizationService->getTotalPasienBelumPeriksa();
    }
    
    /**
     * Get statistik poliklinik dengan caching menggunakan optimization service
     */
    public function getStatistikPoliklinik()
    {
        return $this->optimizationService->getStatistikPoliklinik();
    }
    
    /**
     * Get statistik dokter dengan caching menggunakan optimization service
     */
    public function getStatistikDokter($poliklinik = null)
    {
        return $this->optimizationService->getStatistikDokter(null, $poliklinik);
    }
    
    /**
     * Clear cache ketika ada perubahan data menggunakan optimization service
     */
    public function clearStatistikCache()
    {
        $this->optimizationService->clearAllCaches();
    }

    public function updateStatusAntreanBPJS($no_rawat, $status)
    {
        try {
            // Ambil data registrasi
            $regPeriksa = RegPeriksa::with(['pasien', 'poliklinik'])
                ->where('no_rawat', $no_rawat)
                ->first();

            if (!$regPeriksa) {
                session()->flash('error', 'Data registrasi tidak ditemukan.');
                return;
            }

            // Cek apakah pasien menggunakan BPJS
            if ($regPeriksa->kd_pj !== 'BPJ') {
                session()->flash('error', 'Pasien tidak menggunakan BPJS.');
                return;
            }

            // Ambil mapping kode poli BPJS
            $mappingPoli = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_rs', $regPeriksa->kd_poli)
                ->select('kd_poli_pcare')
                ->first();

            if (!$mappingPoli) {
                session()->flash('error', 'Mapping poliklinik BPJS tidak ditemukan untuk kode poli: ' . $regPeriksa->kd_poli);
                return;
            }

            // Siapkan data untuk API BPJS
            $requestData = [
                'tanggalperiksa' => $regPeriksa->tgl_registrasi,
                'kodepoli' => $mappingPoli->kd_poli_pcare,
                'nomorkartu' => $regPeriksa->pasien->no_peserta ?? '',
                'status' => $status, // 1 = Hadir, 2 = Tidak Hadir
                'waktu' => $this->getTimestampMillis() // timestamp dalam millisecond
            ];

            // Panggil API BPJS
            $response = $this->callBPJSAPI($requestData);

            // Log ke antrean_bpjs_log table
            $responseWithStatus = $response['data'] ?? $response;
            $responseWithStatus['success'] = $response['success'];
            if (!$response['success']) {
                $responseWithStatus['error_message'] = $response['message'];
            }
            
            $logData = [
                'no_rawat' => $no_rawat,
                'no_rkm_medis' => $regPeriksa->no_rkm_medis,
                'status' => $status == 1 ? 'Hadir' : 'Tidak Hadir',
                'response' => json_encode($responseWithStatus)
            ];
            
            AntreanBpjsLog::logActivity($logData);

            // Emit event dengan detail respons BPJS untuk logging di frontend
            $this->emit('bpjsResponseReceived', [
                'success' => $response['success'],
                'status_text' => $status == 1 ? 'Hadir' : 'Tidak Hadir',
                'no_rawat' => $no_rawat,
                'patient_name' => $regPeriksa->pasien->nm_pasien ?? 'Unknown',
                'response_data' => $responseWithStatus,
                'request_data' => $requestData,
                'timestamp' => now()->toDateTimeString()
            ]);

            if ($response['success']) {
                $statusText = $status == 1 ? 'Hadir' : 'Tidak Hadir';
                session()->flash('success', "Status antrean BPJS berhasil diupdate menjadi: {$statusText}");
                $this->emit('refreshDatatable');
            } else {
                session()->flash('error', 'Gagal mengupdate status antrean BPJS: ' . $response['message']);
            }
        } catch (\Exception $e) {
            // Error updating BPJS queue status
            
            // Log error ke antrean_bpjs_log table
             if (isset($regPeriksa)) {
                 $logData = [
                     'no_rawat' => $no_rawat,
                     'no_rkm_medis' => $regPeriksa->no_rkm_medis,
                     'status' => 'Error',
                     'response' => json_encode([
                         'error' => $e->getMessage(),
                         'success' => false,
                         'error_message' => $e->getMessage()
                     ])
                 ];
                 
                 AntreanBpjsLog::logActivity($logData);
             }
            
            session()->flash('error', 'Terjadi kesalahan saat mengupdate status antrean BPJS.');
        }
    }

    public function batalAntreanBPJS($no_rawat, $alasan = 'Dibatalkan oleh petugas')
    {
        try {
            // Ambil data registrasi
            $regPeriksa = RegPeriksa::with(['pasien', 'poliklinik'])
                ->where('no_rawat', $no_rawat)
                ->first();

            if (!$regPeriksa) {
                session()->flash('error', 'Data registrasi tidak ditemukan.');
                return;
            }

            // Cek apakah pasien menggunakan BPJS
            if ($regPeriksa->kd_pj !== 'BPJ') {
                session()->flash('error', 'Pasien tidak menggunakan BPJS.');
                return;
            }

            // Ambil mapping kode poli BPJS
            $mappingPoli = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_rs', $regPeriksa->kd_poli)
                ->select('kd_poli_pcare')
                ->first();

            if (!$mappingPoli) {
                session()->flash('error', 'Mapping poliklinik BPJS tidak ditemukan untuk kode poli: ' . $regPeriksa->kd_poli);
                return;
            }

            // Siapkan data untuk API BPJS
            $requestData = [
                'tanggalperiksa' => $regPeriksa->tgl_registrasi,
                'kodepoli' => $mappingPoli->kd_poli_pcare,
                'nomorkartu' => $regPeriksa->pasien->no_peserta ?? '',
                'alasan' => $alasan
            ];

            // Panggil API BPJS untuk batal antrean
            $response = $this->callBPJSBatalAPI($requestData);

            // Log ke antrean_bpjs_log table
            $responseWithStatus = $response['data'] ?? $response;
            $responseWithStatus['success'] = $response['success'];
            if (!$response['success']) {
                $responseWithStatus['error_message'] = $response['message'];
            }
            
            $logData = [
                'no_rawat' => $no_rawat,
                'no_rkm_medis' => $regPeriksa->no_rkm_medis,
                'status' => 'Batal Antrean',
                'response' => json_encode($responseWithStatus)
            ];
            
            AntreanBpjsLog::logActivity($logData);

            // Emit event dengan detail respons BPJS untuk logging di frontend
            $this->emit('bpjsResponseReceived', [
                'success' => $response['success'],
                'status_text' => 'Batal Antrean',
                'no_rawat' => $no_rawat,
                'patient_name' => $regPeriksa->pasien->nm_pasien ?? 'Unknown',
                'response_data' => $responseWithStatus,
                'request_data' => $requestData,
                'timestamp' => now()->toDateTimeString()
            ]);

            if ($response['success']) {
                session()->flash('success', 'Antrean BPJS berhasil dibatalkan.');
                $this->emit('refreshDatatable');
            } else {
                session()->flash('error', 'Gagal membatalkan antrean BPJS: ' . $response['message']);
            }
        } catch (\Exception $e) {
            // Error cancelling BPJS queue
            
            // Log error ke antrean_bpjs_log table
             if (isset($regPeriksa)) {
                 $logData = [
                     'no_rawat' => $no_rawat,
                     'no_rkm_medis' => $regPeriksa->no_rkm_medis,
                     'status' => 'Error Batal',
                     'response' => json_encode([
                         'error' => $e->getMessage(),
                         'success' => false,
                         'error_message' => $e->getMessage()
                     ])
                 ];
                 
                 AntreanBpjsLog::logActivity($logData);
             }
            
            session()->flash('error', 'Terjadi kesalahan saat membatalkan antrean BPJS.');
        }
    }

    private function callBPJSAPI($requestData)
    {
        try {
            // Gunakan WsBPJSController yang sudah ada untuk konsistensi
            $controller = new \App\Http\Controllers\API\WsBPJSController();
            
            // Siapkan request object
            $request = new \Illuminate\Http\Request();
            $request->merge([
                'tanggalperiksa' => $requestData['tanggalperiksa'],
                'kodepoli' => $requestData['kodepoli'],
                'nomorkartu' => $requestData['nomorkartu'],
                'status' => $requestData['status'],
                'waktu' => $requestData['waktu']
            ]);
            
            // Panggil method updateStatusAntrean dari WsBPJSController
            $response = $controller->updateStatusAntrean($request);
            
            // Ambil data dari response
            $responseData = $response->getData(true);
            
            // BPJS API Response logged for debugging
            
            // Parse response menggunakan format standar BPJS
            $metadata = $responseData['metadata'] ?? $responseData['metaData'] ?? null;
            
            $isSuccess = false;
            $message = 'Unknown response';
            
            if ($metadata && isset($metadata['code'])) {
                $isSuccess = $metadata['code'] == 200;
                $message = $metadata['message'] ?? ($isSuccess ? 'Success' : 'Error');
            }
            
            return [
                'success' => $isSuccess,
                'message' => $message,
                'data' => $responseData
            ];
            
        } catch (\Exception $e) {
            // BPJS API Error via WsBPJSController
            return [
                'success' => false,
                'message' => 'Gagal menghubungi API BPJS: ' . $e->getMessage()
            ];
        }
    }

    private function callBPJSBatalAPI($requestData)
    {
        try {
            // Gunakan WsBPJSController yang sudah ada untuk konsistensi
            $controller = new \App\Http\Controllers\API\WsBPJSController();
            
            // Siapkan request object
            $request = new \Illuminate\Http\Request();
            $request->merge([
                'tanggalperiksa' => $requestData['tanggalperiksa'],
                'kodepoli' => $requestData['kodepoli'],
                'nomorkartu' => $requestData['nomorkartu'],
                'alasan' => $requestData['alasan']
            ]);
            
            // Panggil method batalAntrean dari WsBPJSController
            $response = $controller->batalAntrean($request);
            
            // Ambil data dari response
            $responseData = $response->getData(true);
            
            // BPJS Batal Antrean API Response logged for debugging
            
            // Parse response menggunakan format standar BPJS
            $metadata = $responseData['metadata'] ?? $responseData['metaData'] ?? null;
            
            $isSuccess = false;
            $message = 'Unknown response';
            
            if ($metadata && isset($metadata['code'])) {
                $isSuccess = $metadata['code'] == 200;
                $message = $metadata['message'] ?? ($isSuccess ? 'Success' : 'Error');
            }
            
            return [
                'success' => $isSuccess,
                'message' => $message,
                'data' => $responseData
            ];
            
        } catch (\Exception $e) {
            // BPJS Batal Antrean API Error via WsBPJSController
            return [
                'success' => false,
                'message' => 'Gagal menghubungi API BPJS: ' . $e->getMessage()
            ];
        }
    }

    public function columns(): array
    {
        return [
            Column::make("No.Reg", "no_reg")
                ->sortable()
                ->searchable(),
            Column::make("Tanggal", "tgl_registrasi")
                ->sortable()
                ->format(function ($value, $row, Column $column) {
                    return Carbon::parse($value)->format('d/m/Y');
                }),
            Column::make("Jam", "jam_reg")
                ->sortable()
                ->format(function ($value, $row, Column $column) {
                    return Carbon::parse($value)->format('H:i');
                }),
            Column::make("No. RM", "no_rkm_medis")
                ->searchable()
                ->sortable(),
            Column::make("Pasien", "pasien.nm_pasien")
                ->searchable()
                ->sortable()
                ->format(function ($value, $row, Column $column) {
                    $noRawat = isset($row->no_rawat) 
                        ? \App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rawat) 
                        : '';
                    $noRM = isset($row->no_rkm_medis) 
                        ? \App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rkm_medis) 
                        : '';
                    $url = route('ralan.pemeriksaan', ['no_rawat' => $noRawat, 'no_rm' => $noRM]);
                    return '<a href="' . $url . '" class="text-primary font-weight-bold" style="text-decoration: none; cursor: pointer;" title="Klik untuk pemeriksaan">' . $value . '</a>';
                })
                ->html(),
            Column::make("JK", "pasien.jk")
                ->sortable()
                ->format(function ($value, $row, Column $column) {
                    return $value == 'L' ? 'Laki-laki' : 'Perempuan';
                }),
            Column::make("Umur", "no_rawat")
                ->format(function ($value, $row, Column $column) {
                    if (isset($row->umurdaftar) && isset($row->sttsumur)) {
                        return $row->umurdaftar . ' ' . $row->sttsumur;
                    }
                    // Hitung umur dari tanggal lahir jika tersedia
                    if (isset($row->tgl_lahir)) {
                        $birthDate = Carbon::parse($row->tgl_lahir);
                        $age = $birthDate->age;
                        return $age . ' Tahun';
                    }
                    return '-';
                }),
            Column::make("Poliklinik", "poliklinik.nm_poli")
                ->sortable()
                ->searchable(),
            Column::make("Dokter", "dokter.nm_dokter")
                ->sortable()
                ->searchable(),
            Column::make("Jenis Bayar", "penjab.png_jawab")
                ->sortable()
                ->format(function ($value, $row, Column $column) {
                    // Cek apakah menggunakan BPJS (termasuk PBI dan NON PBI)
                    $isBpjs = in_array($row->kd_pj, ['BPJ', 'A14', 'A15']) || 
                             strtolower($value) == 'bpjs kesehatan' ||
                             stripos($value, 'bpjs') !== false;
                    
                    $badgeClass = $isBpjs ? 'badge-success' : 'badge-primary';
                    return '<span class="badge ' . $badgeClass . '">' . $value . '</span>';
                })
                ->html(),
            Column::make("Status", "stts")
                ->sortable()
                ->format(function ($value, $row, Column $column) {
                    $badgeClass = $value == 'Belum' ? 'badge-warning' : 'badge-success';
                    return '<span class="badge ' . $badgeClass . '">' . $value . '</span>';
                })
                ->html(),
            Column::make("Aksi", "no_rawat")
                ->format(function ($value, $row, Column $column) {
                    return view('livewire.registrasi.menu', ['row' => $row]);
                })
                ->html(),
        ];
    }

    /**
     * Get poliklinik options for filter dropdown
     */
    public function getPoliklinikOptions()
    {
        $polikliniks = DB::table('poliklinik')
            ->where('status', '1')
            ->select('kd_poli', 'nm_poli')
            ->orderBy('nm_poli')
            ->get();

        $options = ['' => 'Semua Poliklinik'];
        foreach ($polikliniks as $poliklinik) {
            $options[$poliklinik->kd_poli] = $poliklinik->nm_poli;
        }
        
        return $options;
    }
    
    /**
     * Get dokter options for filter dropdown
     */
    public function getDokterOptions()
    {
        $dokters = DB::table('dokter')
            ->where('status', '1')
            ->select('kd_dokter', 'nm_dokter')
            ->get();

        $options = ['' => 'Semua Dokter'];
        foreach ($dokters as $dokter) {
            $options[$dokter->kd_dokter] = $dokter->nm_dokter;
        }

        return $options;
    }

    public function filterByPoliklinik($kdPoli)
    {
        $this->poliklinikFilter = $kdPoli;
        $this->setFilter('poliklinik', $kdPoli);
    }

    public function setFilter($filterName, $value)
    {
        Log::info('Setting filter', ['filterName' => $filterName, 'value' => $value]);
        
        // Use Livewire Tables API to set filters properly
        try {
            // Set filter using the correct Livewire Tables method
            $this->setFilterValue($filterName, $value);
            
            // Handle component-specific filters
            if ($filterName === 'poliklinik' || $filterName === 'Poliklinik') {
                $this->poliklinikFilter = $value;
            }
            
            Log::info('Filter set successfully', ['filterName' => $filterName, 'value' => $value]);
            
        } catch (\Exception $e) {
            Log::warning('Failed to set filter using setFilterValue', [
                'filterName' => $filterName, 
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            
            // Handle component-specific filters directly
            if ($filterName === 'poliklinik' || $filterName === 'Poliklinik') {
                $this->poliklinikFilter = $value;
            }
        }
        
        // Reset halaman ke 1 saat filter berubah
        $this->setPage(1);
    }

    public function resetFilters()
    {
        // Jika filter terkunci, jangan reset
        if ($this->isFilterLocked) {
            $this->emit('filterLockActive', 'Filter sedang terkunci. Buka kunci terlebih dahulu untuk mereset filter.');
            return;
        }
        
        // Reset semua filter ke nilai default
        $this->tanggalFilter = Carbon::today()->format('Y-m-d');
        $this->poliklinikFilter = null;
        
        // Reset semua filter di Livewire Tables ke nilai default menggunakan method yang benar
        $this->setFilter('Tanggal Dari', Carbon::today()->format('Y-m-d'));
        $this->setFilter('Tanggal Sampai', Carbon::today()->format('Y-m-d'));
        $this->setFilter('Status', '');
        $this->setFilter('Poliklinik', '');
        $this->setFilter('Dokter', '');
        
        // Reset halaman ke 1
        $this->resetPage();
        
        // Refresh data
        $this->refreshData();
        
        // Clear persisted filters
        $this->clearPersistedFilters();
    }
    
    /**
     * Toggle filter lock status
     */
    public function toggleFilterLock()
    {
        $this->isFilterLocked = !$this->isFilterLocked;
        
        Log::info('🔄 Toggling filter lock', ['newStatus' => $this->isFilterLocked]);
        
        // Save lock status to session
        session(['reg_periksa_filter_lock' => $this->isFilterLocked]);
        
        if ($this->isFilterLocked) {
            // Save current filters when locking
            $this->saveCurrentFilters();
            
            Log::info('🔒 Filter locked and saved to session');
            
            $this->emit('filterLockToggled', [
                'locked' => true,
                'message' => 'Filter berhasil dikunci. Filter akan dipertahankan setelah refresh halaman.'
            ]);
        } else {
            // Clear persisted filters when unlocking
            $this->clearPersistedFilters();
            
            Log::info('🔓 Filter unlocked and cleared from session');
            
            $this->emit('filterLockToggled', [
                'locked' => false,
                'message' => 'Filter lock dibuka. Filter akan direset ke default setelah refresh halaman.'
            ]);
        }
        
        // Emit event to update UI
        $this->emit('filterLockUpdated', $this->isFilterLocked);
        
        // Dispatch browser event for frontend
        $this->dispatchBrowserEvent('filter-lock-changed', [
            'isLocked' => $this->isFilterLocked,
            'message' => $this->isFilterLocked ? 'Filter dikunci' : 'Filter dibuka'
        ]);
    }
    
    /**
     * Get current filter lock status
     */
    public function getFilterLockStatus()
    {
        Log::info('Getting filter lock status', ['isFilterLocked' => $this->isFilterLocked]);
        $this->emit('filterLockUpdated', $this->isFilterLocked);
    }
    
    /**
     * Handle component initialization from frontend
     */
    public function handleComponentInitialization()
    {
        Log::info('🚀 Component initialization triggered');
        
        $isFilterLocked = session('reg_periksa_filter_lock', false);
        $sessionFilters = session('reg_periksa_filters', []);
        
        Log::info('📊 Filter lock status', [
            'isFilterLocked' => $isFilterLocked,
            'sessionFilters' => $sessionFilters
        ]);
        
        $this->isFilterLocked = $isFilterLocked;
        
        if ($isFilterLocked && !empty($sessionFilters)) {
            Log::info('🔒 Filter is locked, loading persisted filters');
            $this->loadPersistedFilters();
            
            // Force refresh the component with new filters
            $this->emit('$refresh');
            
            // Dispatch browser event with message
            $this->dispatchBrowserEvent('filters-loaded', [
                'message' => 'Filter yang tersimpan berhasil dimuat',
                'persistedFilters' => $sessionFilters
            ]);
        } else {
            Log::info('🔓 Filter not locked, setting default date filters');
            // Set default date filters to today
            $today = now()->format('Y-m-d');
            
            // Use setFilter method to properly set filters
            $this->setFilter('Tanggal Dari', $today);
            $this->setFilter('Tanggal Sampai', $today);
            $this->setFilter('Status', '');
            $this->setFilter('Poliklinik', '');
            $this->setFilter('Dokter', '');
            
            $this->tanggalFilter = $today;
            
            // Reset pagination and clear cache
            $this->resetPage();
            $this->clearFilterCache();
        }
        
        // Dispatch component mounted event
        $this->dispatchBrowserEvent('component-mounted', [
            'isFilterLocked' => $isFilterLocked
        ]);
        
        Log::info('✅ Component initialization completed');
    }
    
    /**
     * Apply filters from session data
     */
    private function applyFiltersFromSession()
    {
        $sessionFilters = session('reg_periksa_filters', []);
        
        foreach ($sessionFilters as $filterName => $filterValue) {
            if (!empty($filterValue)) {
                Log::info('🎯 Applying session filter', ['filter' => $filterName, 'value' => $filterValue]);
                
                // Use setFilter method instead of direct property access
                $this->setFilter($filterName, $filterValue);
                
                Log::info('✅ Filter applied successfully', ['filter' => $filterName]);
            }
        }
    }
    
    /**
     * Save current filter values to session
     */
    public function saveCurrentFilters()
    {
        // Get current filter values using proper Livewire Tables methods
        $currentFilters = [];
        
        try {
            // Try to get applied filters using Livewire Tables API
            $appliedFilters = $this->getAppliedFilters();
            
            $currentFilters = [
                'Tanggal Dari' => $appliedFilters['Tanggal Dari'] ?? $this->getAppliedFilterWithValue('Tanggal Dari') ?? null,
                'Tanggal Sampai' => $appliedFilters['Tanggal Sampai'] ?? $this->getAppliedFilterWithValue('Tanggal Sampai') ?? null,
                'Status' => $appliedFilters['Status'] ?? $this->getAppliedFilterWithValue('Status') ?? null,
                'Poliklinik' => $appliedFilters['Poliklinik'] ?? $this->getAppliedFilterWithValue('Poliklinik') ?? $this->poliklinikFilter,
                'Dokter' => $appliedFilters['Dokter'] ?? $this->getAppliedFilterWithValue('Dokter') ?? null,
                'tanggalFilter' => $this->tanggalFilter,
                'poliklinikFilter' => $this->poliklinikFilter,
            ];
            
        } catch (\Exception $e) {
            Log::warning('Failed to get applied filters, using fallback method', ['error' => $e->getMessage()]);
            
            // Fallback method using component properties
            $currentFilters = [
                'Tanggal Dari' => $this->getAppliedFilterWithValue('Tanggal Dari'),
                'Tanggal Sampai' => $this->getAppliedFilterWithValue('Tanggal Sampai'),
                'Status' => $this->getAppliedFilterWithValue('Status'),
                'Poliklinik' => $this->poliklinikFilter,
                'Dokter' => $this->getAppliedFilterWithValue('Dokter'),
                'tanggalFilter' => $this->tanggalFilter,
                'poliklinikFilter' => $this->poliklinikFilter,
            ];
        }
        
        // Remove null values to keep session clean
        $currentFilters = array_filter($currentFilters, function($value) {
            return $value !== null && $value !== '';
        });
        
        Log::info('💾 Saving current filters to session', ['filters' => $currentFilters]);
        
        session(['reg_periksa_filters' => $currentFilters]);
        $this->persistedFilters = $currentFilters;
        
        Log::info('✅ Filters saved to session successfully');
    }
    
    /**
     * Load persisted filters from session
     */
    public function loadPersistedFilters()
    {
        $savedFilters = session('reg_periksa_filters', []);
        
        Log::info('🔄 Loading persisted filters from session', ['savedFilters' => $savedFilters]);
        
        if (!empty($savedFilters)) {
            $this->persistedFilters = $savedFilters;
            
            // Apply saved filters using setFilter method for proper handling
            if (isset($savedFilters['Tanggal Dari']) && $savedFilters['Tanggal Dari']) {
                Log::info('🎯 Applying Tanggal Dari filter', ['value' => $savedFilters['Tanggal Dari']]);
                $this->setFilter('Tanggal Dari', $savedFilters['Tanggal Dari']);
            }
            if (isset($savedFilters['Tanggal Sampai']) && $savedFilters['Tanggal Sampai']) {
                Log::info('🎯 Applying Tanggal Sampai filter', ['value' => $savedFilters['Tanggal Sampai']]);
                $this->setFilter('Tanggal Sampai', $savedFilters['Tanggal Sampai']);
            }
            if (isset($savedFilters['Status']) && $savedFilters['Status']) {
                Log::info('🎯 Applying Status filter', ['value' => $savedFilters['Status']]);
                $this->setFilter('Status', $savedFilters['Status']);
            }
            if (isset($savedFilters['Poliklinik']) && $savedFilters['Poliklinik']) {
                Log::info('🎯 Applying Poliklinik filter', ['value' => $savedFilters['Poliklinik']]);
                $this->setFilter('Poliklinik', $savedFilters['Poliklinik']);
                $this->poliklinikFilter = $savedFilters['Poliklinik'];
            }
            if (isset($savedFilters['Dokter']) && $savedFilters['Dokter']) {
                Log::info('🎯 Applying Dokter filter', ['value' => $savedFilters['Dokter']]);
                $this->setFilter('Dokter', $savedFilters['Dokter']);
            }
            
            // Apply component filters
            if (isset($savedFilters['tanggalFilter'])) {
                $this->tanggalFilter = $savedFilters['tanggalFilter'];
            }
            if (isset($savedFilters['poliklinikFilter'])) {
                $this->poliklinikFilter = $savedFilters['poliklinikFilter'];
            }
            
            // Force table to rebuild with new filters
            $this->resetPage();
            $this->clearFilterCache();
            
            Log::info('✅ Filters applied successfully', [
                'tanggalFilter' => $this->tanggalFilter,
                'poliklinikFilter' => $this->poliklinikFilter,
                'appliedFilters' => $this->getAppliedFilters()
            ]);
        } else {
            Log::info('📋 No persisted filters found');
        }
    }
    
    /**
     * Clear persisted filters from session
     */
    public function clearPersistedFilters()
    {
        session()->forget('reg_periksa_filters');
        $this->persistedFilters = [];
    }
    
}

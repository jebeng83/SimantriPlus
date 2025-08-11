<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\RegPeriksa;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Poliklinik;
use App\Models\Dokter;
use Carbon\Carbon;
use App\Models\AntreanBpjsLog;
use App\Services\RegPeriksaOptimizationService;

class RegPeriksaTable extends DataTableComponent
{
    protected $model = RegPeriksa::class;
    
    public $tanggalFilter;
    public $poliklinikFilter;
    
    protected $listeners = ['filterByPoliklinik', 'refreshDatatable' => 'refreshData', 'registrationSuccess' => 'refreshData'];
    
    protected $optimizationService;

    public function mount()
    {
        // Set default tanggal ke hari ini
        $this->tanggalFilter = Carbon::today()->format('Y-m-d');
        
        // Initialize optimization service
        $this->initializeOptimizationService();
    }
    
    /**
     * Pastikan optimization service terinisialisasi
     */
    private function initializeOptimizationService()
    {
        if (!$this->optimizationService) {
            $this->optimizationService = new RegPeriksaOptimizationService();
        }
    }

    public function configure(): void
    {
        $this->setPrimaryKey('no_rawat');
        $this->setFilterLayoutSlideDown();
        $this->setPerPageAccepted([5, 10, 25, 50, 100]);
        $this->setPerPage(10);
        $this->setDefaultSort('tgl_registrasi', 'desc');
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Tanggal Registrasi')
                ->config([
                    'placeholder' => 'Hari Ini: ' . Carbon::today()->format('d/m/Y'),
                    'allowInput' => false,
                    'disabled' => true,
                ])
                ->setFilterDefaultValue(Carbon::today()->format('Y-m-d'))
                ->filter(function (Builder $builder, string $value) {
                    // Paksa selalu menggunakan tanggal hari ini
                    $this->tanggalFilter = Carbon::today()->format('Y-m-d');
                    $builder->where('reg_periksa.tgl_registrasi', Carbon::today()->format('Y-m-d'));
                }),
            SelectFilter::make('Poliklinik')
                ->setFilterPillTitle('Poli')
                ->setFilterPillValues([
                    '' => 'Semua Poliklinik',
                ])
                ->filter(function (Builder $builder, $value) {
                    if ($value) {
                        $builder->where('reg_periksa.kd_poli', $value);
                    }
                }),
            SelectFilter::make('Dokter')
                ->setFilterPillTitle('Dokter')
                ->setFilterPillValues([
                    '' => 'Semua Dokter',
                ])
                ->filter(function (Builder $builder, $value) {
                    if ($value) {
                        $builder->where('reg_periksa.kd_dokter', $value);
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        // Pastikan optimization service terinisialisasi
        $this->initializeOptimizationService();
        
        // Gunakan optimization service untuk query yang sudah dioptimasi
        $tanggal = $this->tanggalFilter ?: Carbon::today()->format('Y-m-d');
        
        return $this->optimizationService->getOptimizedQuery(
            $tanggal,
            $this->poliklinikFilter,
            null // dokter filter bisa ditambahkan nanti jika diperlukan
        );
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
            Log::error('Error updating BPJS queue status: ' . $e->getMessage());
            
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
            Log::error('Error cancelling BPJS queue: ' . $e->getMessage());
            
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
            
            // Log response untuk debugging
            Log::info('BPJS API Response via WsBPJSController: ', [
                'request_data' => $requestData,
                'response' => $responseData,
                'response_structure' => [
                    'has_metadata' => isset($responseData['metadata']),
                    'has_metaData' => isset($responseData['metaData']),
                    'response_keys' => array_keys($responseData ?? [])
                ]
            ]);
            
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
            Log::error('BPJS API Error via WsBPJSController: ' . $e->getMessage());
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
            
            // Log response untuk debugging
            Log::info('BPJS Batal Antrean API Response via WsBPJSController: ', [
                'request_data' => $requestData,
                'response' => $responseData,
                'response_structure' => [
                    'has_metadata' => isset($responseData['metadata']),
                    'has_metaData' => isset($responseData['metaData']),
                    'response_keys' => array_keys($responseData ?? [])
                ]
            ]);
            
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
            Log::error('BPJS Batal Antrean API Error via WsBPJSController: ' . $e->getMessage());
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

    public function filterByPoliklinik($kdPoli)
    {
        $this->poliklinikFilter = $kdPoli;
        $this->setFilter('poliklinik', $kdPoli);
    }

    public function setFilter($filterName, $value)
    {
        if ($filterName === 'poliklinik') {
            $this->poliklinikFilter = $value;
            // Reset halaman ke 1 saat filter berubah
            $this->setPage(1);
        }
    }
}

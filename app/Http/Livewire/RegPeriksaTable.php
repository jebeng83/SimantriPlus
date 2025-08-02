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
use App\Models\Poliklinik;
use App\Models\Dokter;
use Carbon\Carbon;
use App\Models\AntreanBpjsLog;

class RegPeriksaTable extends DataTableComponent
{
    protected $model = RegPeriksa::class;
    
    public $tanggalFilter;

    public function mount()
    {
        // Set default tanggal ke hari ini
        $this->tanggalFilter = Carbon::today()->format('Y-m-d');
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
                    'placeholder' => 'Pilih Tanggal',
                    'allowInput' => true,
                ])
                ->setFilterDefaultValue(Carbon::today()->format('Y-m-d'))
                ->filter(function (Builder $builder, string $value) {
                    $this->tanggalFilter = $value;
                    $builder->where('reg_periksa.tgl_registrasi', $value);
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
        $query = RegPeriksa::query()
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('reg_periksa.stts', 'Belum')
            ->select(
                'reg_periksa.*', 
                'pasien.nm_pasien', 
                'pasien.no_tlp', 
                'pasien.jk',
                'pasien.tgl_lahir',
                'dokter.nm_dokter', 
                'poliklinik.nm_poli', 
                'penjab.png_jawab'
            );
            
        // Filter default ke hari ini
        if (!$this->tanggalFilter) {
            $query->where('reg_periksa.tgl_registrasi', Carbon::today()->format('Y-m-d'));
        }
        
        return $query;
    }

    public function hapus($no_rawat)
    {
        try {
            RegPeriksa::where('no_rawat', $no_rawat)->delete();
            $this->emit('refreshDatatable');
            session()->flash('success', 'Data registrasi berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data registrasi: ' . $e->getMessage());
        }
    }
    
    public function getTotalPasienHariIni()
    {
        $tanggal = $this->tanggalFilter ?? Carbon::today()->format('Y-m-d');
        return RegPeriksa::where('tgl_registrasi', $tanggal)->count();
    }
    
    public function getTotalPasienBelumPeriksa()
    {
        $tanggal = $this->tanggalFilter ?? Carbon::today()->format('Y-m-d');
        return RegPeriksa::where('tgl_registrasi', $tanggal)
                         ->where('stts', 'Belum')
                         ->count();
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

            // Siapkan data untuk API BPJS
            $requestData = [
                'tanggalperiksa' => $regPeriksa->tgl_registrasi,
                'kodepoli' => $regPeriksa->kd_poli,
                'nomorkartu' => $regPeriksa->pasien->no_peserta ?? '',
                'status' => $status, // 1 = Hadir, 2 = Tidak Hadir
                'waktu' => now()->timestamp * 1000 // timestamp dalam millisecond
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

    private function callBPJSAPI($requestData)
    {
        try {
            // Ambil konfigurasi BPJS Antrean V2
            $baseUrl = config('bpjs.antrean.base_url');
            $authUrl = config('bpjs.antrean.auth_url');
            $username = config('bpjs.antrean.username');
            $password = config('bpjs.antrean.password');
            $consId = config('bpjs.antrean.cons_id');
            $consPwd = config('bpjs.antrean.cons_pwd');
            $userKey = config('bpjs.antrean.user_key');
            $user = config('bpjs.antrean.user');
            $pass = config('bpjs.antrean.pass');

            // Validasi konfigurasi
            if (empty($baseUrl) || empty($consId) || empty($userKey) || empty($consPwd)) {
                throw new \Exception('Konfigurasi BPJS Antrean V2 tidak lengkap. Periksa file .env');
            }

            // Generate timestamp dan signature untuk BPJS V2
            $timestamp = time();
            $signature = hash_hmac('sha256', $consId . '&' . $timestamp, $consPwd, true);
            $encodedSignature = base64_encode($signature);

            $url = "{$baseUrl}/antrean/panggil";

            $headers = [
                'X-cons-id' => $consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $encodedSignature,
                'user_key' => $userKey,
                'Content-Type' => 'application/json'
            ];

            $client = new \GuzzleHttp\Client();
            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $requestData,
                'timeout' => 30
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            // Log response untuk debugging
            Log::info('BPJS API V2 Response: ', [
                'url' => $url,
                'response' => $responseData
            ]);

            // Cek response berdasarkan struktur yang ada
            $isSuccess = false;
            if (isset($responseData['metadata']['code']) && $responseData['metadata']['code'] == 200) {
                $isSuccess = true;
            } elseif (isset($responseData['metaData']['code']) && $responseData['metaData']['code'] == 200) {
                $isSuccess = true;
            }

            return [
                'success' => $isSuccess,
                'message' => $responseData['metadata']['message'] ?? $responseData['metaData']['message'] ?? 'Unknown response',
                'data' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error('BPJS API V2 Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal menghubungi API BPJS V2: ' . $e->getMessage()
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
                ->sortable(),
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
                    $badgeClass = strtolower($value) == 'bpjs kesehatan' ? 'badge-success' : 'badge-primary';
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
}

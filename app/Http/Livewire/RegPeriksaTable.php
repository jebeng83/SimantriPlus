<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\RegPeriksa;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Support\Facades\Date;
use App\Models\Poliklinik;
use App\Models\Dokter;
use Carbon\Carbon;

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
        $this->setTableRowUrl(function($row) {
            return route('ralan.pemeriksaan', ['no_rawat' => $row->no_rawat]);
        });
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

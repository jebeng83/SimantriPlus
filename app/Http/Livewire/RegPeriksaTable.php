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

class RegPeriksaTable extends DataTableComponent
{
    protected $model = RegPeriksa::class;

    public function configure(): void
    {
        $this->setPrimaryKey('no_rawat');
        $this->setFilterLayoutSlideDown();
        $this->setPerPageAccepted([5, 10, 25, 50, 100]);
        $this->setPerPage(5);
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Tgl Registrasi')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('tgl_registrasi', $value);
                }),
            SelectFilter::make('Poliklinik')
                ->options(
                    Poliklinik::query()
                        ->where('status', '1')
                        ->get()
                        ->keyBy('kd_poli')
                        ->map(function ($poliklinik) {
                            return $poliklinik->nm_poli;
                        })->toArray()
                )
                ->filter(function (Builder $builder, $value) {
                    $builder->where('reg_periksa.kd_poli', $value);
                }),
            SelectFilter::make('Dokter')
                ->options(
                    Dokter::query()
                        ->where('status', '1')
                        ->get()
                        ->keyBy('kd_dokter')
                        ->map(function ($dokter) {
                            return $dokter->nm_dokter;
                        })->toArray()
                )->filter(function (Builder $builder, $value) {
                    $builder->where('reg_periksa.kd_dokter', $value);
                }),
        ];
    }

    public function builder(): Builder
    {
        return RegPeriksa::query()
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->where('stts', 'Belum')
            ->orderBy('tgl_registrasi', 'desc')
            ->select('reg_periksa.*', 'pasien.nm_pasien', 'dokter.nm_dokter', 'poliklinik.nm_poli', 'penjab.png_jawab', 'pasien.no_tlp', 'pasien.jk');
    }

    public function hapus($no_rawat)
    {
        RegPeriksa::where('no_rawat', $no_rawat)->delete();
        $this->emit('refreshDatatable');
    }

    public function columns(): array
    {
        return [
            Column::make("No.Reg", "no_reg")
                ->sortable(),
            Column::make("No.Rawat", "no_rawat")
                ->searchable()
                ->sortable(),
            Column::make("Tanggal", "tgl_registrasi")
                ->sortable(),
            Column::make("Dokter", "dokter.nm_dokter")
                ->sortable(),
            Column::make("No. RM", "no_rkm_medis")
                ->searchable()
                ->sortable(),
            Column::make("Pasien", "pasien.nm_pasien")
                ->searchable()
                ->sortable(),
            Column::make("JK", "pasien.jk")
                ->sortable(),
            Column::make("Umur", "no_rawat")
                ->format(function ($value, $row, Column $column) {
                    return $row->umurdaftar . '  ' . $row->sttsumur;
                })
                ->sortable(),
            Column::make("Poliklinik", "poliklinik.nm_poli")
                ->sortable(),
            Column::make("Jenis Bayar", "penjab.png_jawab")
                ->sortable(),
            Column::make("Penanggung Jawab", "p_jawab")
                ->sortable(),
            Column::make("Alamat P.J.", "almt_pj")
                ->sortable(),
            Column::make("Hubungan P.J.", "hubunganpj")
                ->sortable(),
            Column::make("Aksi", "no_rawat")
                ->format(function ($value, $row, Column $column) {
                    return view('livewire.registrasi.menu', ['row' => $row]);
                })
                ->html(),
        ];
    }
}

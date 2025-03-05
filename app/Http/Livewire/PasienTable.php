<?php

namespace App\Http\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Pasien;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class PasienTable extends DataTableComponent
{
    protected $model = Pasien::class;
    
    // Properti untuk pencarian
    public $searchName = '';
    public $searchRM = '';
    public $searchAddress = '';
    
    // Listener untuk event
    protected $listeners = [
        'searchPasien' => 'handleSearch',
        'resetSearch' => 'resetSearch',
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('no_rkm_medis')
            ->setTableRowUrl(function ($row) {
                return route('pasien.edit', $row->no_rkm_medis);
            })
            ->setTableRowUrlTarget(function ($row) {
                return '_self';
            });
        $this->setFilterPillsEnabled();
        $this->setPerPageAccepted([5, 10, 25, 50, 100]);
        $this->setPerPage(10);
    }
    
    public function filters(): array
    {
        return [
            TextFilter::make('alamat', 'Alamat')
                ->filter(function (Builder $query, $value) {
                    return $query->where('alamat', 'like', '%' . $value . '%');
                }),
        ];
    }

    public function query(): Builder
    {
        $query = Pasien::query();
        
        // Terapkan filter pencarian jika ada
        if (!empty($this->searchName)) {
            $query->where('nm_pasien', 'like', '%' . $this->searchName . '%');
        }
        
        if (!empty($this->searchRM)) {
            $query->where('no_rkm_medis', 'like', '%' . $this->searchRM . '%');
        }
        
        if (!empty($this->searchAddress)) {
            $query->where('alamat', 'like', '%' . $this->searchAddress . '%');
        }
        
        return $query->orderBy('tgl_daftar', 'desc');
    }
    
    // Fungsi untuk menangani event pencarian
    public function handleSearch($searchParams)
    {
        $this->searchName = $searchParams['name'] ?? '';
        $this->searchRM = $searchParams['rm'] ?? '';
        $this->searchAddress = $searchParams['address'] ?? '';
        
        // Kirim jumlah hasil pencarian ke JavaScript
        $count = $this->getFilteredModelCount();
        $this->dispatchBrowserEvent('searchResults', ['count' => $count]);
    }
    
    // Fungsi untuk reset pencarian
    public function resetSearch()
    {
        $this->searchName = '';
        $this->searchRM = '';
        $this->searchAddress = '';
        
        // Kirim jumlah hasil pencarian ke JavaScript
        $count = $this->getFilteredModelCount();
        $this->dispatchBrowserEvent('searchResults', ['count' => $count]);
    }
    
    // Fungsi untuk mendapatkan jumlah hasil pencarian
    protected function getFilteredModelCount(): int
    {
        return $this->query()->count();
    }

    public function columns(): array
    {
        return [
            Column::make("No rkm pasien", "no_rkm_medis")
                ->sortable()
                ->searchable(),
            Column::make("Nama", "nm_pasien")
                ->sortable()
                ->searchable(),
            Column::make("No. KTP/SIM", "no_ktp")
                ->sortable()
                ->searchable(),
            Column::make("No. KK", "no_kk")
                ->sortable()
                ->searchable(),
            Column::make("No. Peserta", "no_peserta")
                ->sortable()
                ->searchable(),
            Column::make("No. Telp", "no_tlp")
                ->sortable()
                ->searchable(),
            Column::make('Tgl. Lahir', 'tgl_lahir')
                ->sortable(),
            Column::make('Alamat', 'alamat')
                ->sortable()
                 ->searchable(),
            Column::make('Stts Nikah', 'stts_nikah')
                ->sortable(),
            Column::make('Status', 'status')
                ->sortable()
                 ->searchable(),
            Column::make('Posyandu', 'data_posyandu')
                ->sortable()
                 ->searchable(),
            Column::make('Aksi')
                ->label(
                    function ($row, Column $column) {
                        return view('pasien.partials.action-buttons', ['pasien' => $row]);
                    }
                ),
        ];
    }
    
}

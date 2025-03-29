<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pasien;
use Illuminate\Support\Facades\DB;

class PasienTableSearch extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public $searchName = '';
    public $searchRM = '';
    public $searchAddress = '';
    public $perPage = 20;
    
    protected $listeners = ['refresh' => '$refresh'];
    
    public function updatingSearchName()
    {
        $this->resetPage();
    }
    
    public function updatingSearchRM()
    {
        $this->resetPage();
    }
    
    public function updatingSearchAddress()
    {
        $this->resetPage();
    }
    
    public function search()
    {
        // Pencarian akan otomatis dilakukan saat render()
    }
    
    public function resetFilters()
    {
        $this->searchName = '';
        $this->searchRM = '';
        $this->searchAddress = '';
        $this->resetPage();
    }
    
    public function mount()
    {
        // Tidak perlu melakukan apa-apa, data akan otomatis dimuat di render()
    }
    
    public function render()
    {
        $query = Pasien::query();
        
        if (!empty($this->searchName)) {
            $query->where('nm_pasien', 'like', '%' . $this->searchName . '%');
        }
        
        if (!empty($this->searchRM)) {
            $query->where('no_rkm_medis', 'like', '%' . $this->searchRM . '%')
            ->orWhere('no_ktp', 'like', '%' . $this->searchRM . '%')
            ->orWhere('no_peserta', 'like', '%' . $this->searchRM . '%')
            ->orWhere('no_tlp', 'like', '%' . $this->searchRM . '%');

        }

        if (!empty($this->searchAddress)) {
            $query->where('alamat', 'like', '%' . $this->searchAddress . '%')
            ->orWhere('kelurahanpj', 'like', '%' . $this->searchAddress . '%')
            ->orWhere('data_posyandu', 'like', '%' . $this->searchAddress . '%');
        }
        
        $pasien = $query->orderBy('tgl_daftar', 'desc')->paginate($this->perPage);
        
        return view('livewire.pasien-table-search', [
            'pasien' => $pasien
        ]);
    }
}

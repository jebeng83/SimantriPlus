<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Pasien;
use Illuminate\Support\Facades\DB;

class PasienTableSearch extends Component
{
    public $searchName = '';
    public $searchRM = '';
    public $searchAddress = '';
    public $results = [];
    public $resultCount = 0;
    
    protected $listeners = ['refresh' => '$refresh', 'refreshPasienList' => 'resetSearch'];
    
    public function mount()
    {
        $this->resetSearch();
    }
    
    public function search()
    {
        $query = Pasien::query();
        
        if (!empty($this->searchName)) {
            $query->where('nm_pasien', 'like', '%' . $this->searchName . '%');
        }
        
        if (!empty($this->searchRM)) {
            $query->where('no_rkm_medis', 'like', '%' . $this->searchRM . '%');
        }
        
        if (!empty($this->searchAddress)) {
            $query->where('alamat', 'like', '%' . $this->searchAddress . '%');
        }
        
        $this->results = $query->orderBy('tgl_daftar', 'desc')->limit(100)->get();
        $this->resultCount = $this->results->count();
        
        $this->dispatchBrowserEvent('searchResults', ['count' => $this->resultCount]);
    }
    
    public function resetSearch()
    {
        $this->searchName = '';
        $this->searchRM = '';
        $this->searchAddress = '';
        
        $this->results = Pasien::orderBy('tgl_daftar', 'desc')->limit(100)->get();
        $this->resultCount = $this->results->count();
        
        $this->dispatchBrowserEvent('searchResults', ['count' => $this->resultCount]);
    }
    
    public function render()
    {
        return view('livewire.pasien-table-search');
    }
}

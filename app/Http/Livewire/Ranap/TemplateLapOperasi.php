<?php

namespace App\Http\Livewire\Ranap;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class TemplateLapOperasi extends Component
{
    // use WithPagination;
    public $search, $datas;
    public $readyToLoad = false;

    public function mount()
    {
        $this->search = '';
        $this->datas = [];
    }

    public function loadDatas()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        return view('livewire.ranap.template-lap-operasi');
    }

    public function updatedSearch()
    {
        $this->datas = DB::table('template_laporan_operasi')
                    ->where('nama_operasi', 'like', '%'.$this->search.'%')
                    ->limit(5)
                    ->get();
    }
}

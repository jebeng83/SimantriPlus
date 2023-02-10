<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RiwayatPemeriksaan extends Component
{
    use SwalResponse;
    public $isCollapse = true, $noRm;

    public function mount($noRm)
    {
        $this->noRm = $noRm;
    }
    
    public function render()
    {
        return view('livewire.ralan.riwayat-pemeriksaan');
    }
}

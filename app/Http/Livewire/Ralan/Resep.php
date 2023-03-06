<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Resep extends Component
{
    use SwalResponse;
    public $isCollapsed = true, $noRawat, $noRm, $swal = 'swal:resep', $poli, $jmlForm = 1, $form = [];

    public function mount($noRawat, $noRm)
    {
        $this->noRawat = $noRawat;
        $this->noRm = $noRm;
        $this->poli = session()->get('kd_poli');
        $this->dispatchBrowserEvent('poli-id', ['poli' => $this->poli]);
    }

    public function render()
    {
        return view('livewire.ralan.resep');
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function tambahForm()
    {
        $this->jmlForm++;
        $this->emit('tambahForm', ['jml' => $this->jmlForm]);
    }

    public function kurangiForm()
    {
        if($this->jmlForm > 1){
            $this->jmlForm--;
        }
    }
}

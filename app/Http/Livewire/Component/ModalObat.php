<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class ModalObat extends Component
{
    public $noRawat;

    public function boot($noRawat)
    {
        $this->noRawat = $noRawat;
    }

    public function render()
    {
        return view('livewire.component.modal-obat');
    }

    public function openModal()
    {
        $this->dispatchBrowserEvent('openModalObat');
    }
}

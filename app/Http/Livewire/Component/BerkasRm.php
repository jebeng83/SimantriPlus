<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class BerkasRm extends Component
{
    public $isLoading = true;
    public $rm;
    public $berkas = [];

    public function render()
    {
        return view('livewire.component.berkas-rm');
    }

    public function placeholder()
    {
        return <<<'HTML'
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            HTML;
    }

    public function getBerkas()
    {
        return DB::table('berkas_digital_perawatan')
            ->whereRaw(
                "no_rawat IN (SELECT no_rawat FROM reg_periksa WHERE no_rkm_medis = :noRM) AND lokasi_file <> :file AND (kode = :kode OR kode = :lab)",
                ['noRM' => $this->rm, 'file' => 'pages/upload/', 'kode' => 'B00', 'lab' => 'B05']
            )
            ->orderBy('no_rawat', 'desc')
            ->get();
    }

    public function updatedRm()
    {
        $this->isLoading = true;
        $this->berkas = $this->getBerkas();
        $this->isLoading = false;
    }
}

<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class RiwayatOperasi extends Component
{
    public $noRawat, $readyToLoad = false;
    protected $listeners = ['refreshRiwayatOperasi' => '$refresh'];

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
    }

    public function loadDatas()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        return view('livewire.component.riwayat-operasi', [
            'operasi' => $this->readyToLoad ? DB::table('laporan_operasi_detail')
                            ->join('dokter', 'laporan_operasi_detail.kd_dokter_bedah', '=', 'dokter.kd_dokter')
                            ->where('no_rawat', $this->noRawat)
                            ->select('laporan_operasi_detail.*', 'dokter.nm_dokter')
                            ->get() : []
        ]);
    }
}

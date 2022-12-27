<?php

namespace App\View\Components\Ralan;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class PenilaianAwalMedisBayi extends Component
{
    protected $noRawat;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        $this->noRawat = $noRawat;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.penilaian-awal-medis-bayi', [
            'data' => $this->getPenilaianAwalMedisBayi($this->noRawat),
        ]);
    }

    public function getPenilaianAwalMedisBayi()
    {
        return DB::table('penilaian_awal_medis_ralan_anak')
            ->where('no_rawat', $this->noRawat)
            ->first();
    }
}

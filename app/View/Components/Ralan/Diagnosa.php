<?php

namespace App\View\Components\Ralan;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class Diagnosa extends Component
{
    public $noRawat, $noRM, $diagnosa, $prioritas;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat, $noRm)
    {
        $this->noRawat = $noRawat;
        $this->noRM = $noRm;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.diagnosa', [
            'diagnosa' => DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $this->noRawat)
                ->get(),
            'noRawat' => $this->noRawat,
            'noRm' => $this->noRM,
        ]);
    }
}

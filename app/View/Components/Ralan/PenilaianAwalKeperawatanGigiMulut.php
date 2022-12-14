<?php

namespace App\View\Components\Ralan;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class PenilaianAwalKeperawatanGigiMulut extends Component
{
    public $no_rawat;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        $this->no_rawat = $noRawat;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.penilaian-awal-keperawatan-gigi-mulut',[
            'data' => $this->getPenilaianAwalKeperawatanGigiMulut(),
        ]);
    }

    public function getPenilaianAwalKeperawatanGigiMulut()
    {
        $penilaianAwalKeperawatanGigiMulut = DB::table('penilaian_awal_keperawatan_gigi')
            ->where('no_rawat', $this->no_rawat)
            ->first();

        return $penilaianAwalKeperawatanGigiMulut;
    }
}

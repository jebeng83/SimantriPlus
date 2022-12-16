<?php

namespace App\View\Components\Ralan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class PenilaianAwalKeperawatanBayi extends Component
{
    public $noRawat;
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
        return view('components.ralan.penilaian-awal-keperawatan-bayi',[
            'data' => $this->getPenilaianAwalKeperawatanBayi(),
        ]);
    }

    public function getPenilaianAwalKeperawatanBayi()
    {
        return DB::table('penilaian_awal_keperawatan_ralan_bayi')
            ->where('no_rawat', $this->noRawat)
            ->first();
    }
}

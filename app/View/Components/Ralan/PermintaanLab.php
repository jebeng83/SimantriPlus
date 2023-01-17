<?php

namespace App\View\Components\ralan;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class PermintaanLab extends Component
{
    use EnkripsiData;
    public $noRawat, $encrypNoRawat;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->encrypNoRawat = $this->encryptData($this->noRawat);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.permintaan-lab',[
            'pemeriksaan' => $this->getPemeriksaanLab($this->noRawat),
            'encrypNoRawat' => $this->encrypNoRawat
        ]);
    }

    public function getPemeriksaanLab($noRawat)
    {
        return DB::table('reg_periksa')
                    ->join('permintaan_lab', 'reg_periksa.no_rawat', '=', 'permintaan_lab.no_rawat')
                    ->join('permintaan_pemeriksaan_lab', 'permintaan_lab.noorder', '=', 'permintaan_pemeriksaan_lab.noorder')
                    ->where('reg_periksa.no_rawat', $noRawat)
                    ->get();
    }
}

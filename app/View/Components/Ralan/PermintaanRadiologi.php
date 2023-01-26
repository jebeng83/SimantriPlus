<?php

namespace App\View\Components\ralan;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class PermintaanRadiologi extends Component
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
        return view('components.ralan.permintaan-radiologi',[
            'pemeriksaan' => $this->getPemeriksaanRad($this->noRawat),
            'encrypNoRawat' => $this->encrypNoRawat
        ]);
    }

    public function getPemeriksaanRad($noRawat)
    {
        return DB::table('permintaan_radiologi')
                    ->where('no_rawat', $noRawat)
                    ->get();
    }

    public static function getDetailPemeriksaan($noOrder)
    {
        return DB::table('permintaan_pemeriksaan_radiologi')
                    ->join('jns_perawatan_radiologi', 'permintaan_pemeriksaan_radiologi.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
                    ->where('permintaan_pemeriksaan_radiologi.noorder', $noOrder)
                    ->select('jns_perawatan_radiologi.*')
                    ->get();
    }
}

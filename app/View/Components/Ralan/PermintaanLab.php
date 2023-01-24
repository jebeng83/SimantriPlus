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
        return DB::table('permintaan_lab')
                    ->where('no_rawat', $noRawat)
                    ->get();
    }

    public static function getDetailPemeriksaan($noOrder)
    {
        return DB::table('permintaan_pemeriksaan_lab')
                    ->join('jns_perawatan_lab', 'permintaan_pemeriksaan_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
                    ->where('permintaan_pemeriksaan_lab.noorder', $noOrder)
                    ->select('jns_perawatan_lab.*')
                    ->get();
    }
}

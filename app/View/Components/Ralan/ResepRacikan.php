<?php

namespace App\View\Components\Ralan;

use Illuminate\View\Component;
use App\Traits\EnkripsiData;
use Illuminate\Support\Facades\DB;
use Request;

class ResepRacikan extends Component
{
    public $dokter, $noRM, $noRawat, $encryptNoRawat, $encryptNoRM, $dataMetodeRacik;
    use EnkripsiData;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->noRawat = Request::get('no_rawat');
        $this->noRM = Request::get('no_rm');
        $this->encryptNoRawat = $this->encryptData($this->noRawat);
        $this->encryptNoRM = $this->encryptData($this->noRM);
        $this->dokter = session()->get('username');
        $this->dataMetodeRacik = DB::table('metode_racik')
                                ->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.resep-racikan',[
            'no_rawat' => $this->noRawat,
            'encryptNoRawat' => $this->encryptNoRawat,
            'encryptNoRM' => $this->encryptNoRM,
            'dataMetodeRacik' => $this->dataMetodeRacik,
            'resepRacikan' => $this->getResepRacikan($this->noRawat, session()->get('username')),
        ]);
    }

    public function getResepRacikan($noRawat, $kdDokter)
    {
        $data = DB::table('resep_dokter_racikan')
                    ->join('resep_obat', 'resep_dokter_racikan.no_resep', '=', 'resep_obat.no_resep')
                    ->join('metode_racik', 'resep_dokter_racikan.kd_racik', '=', 'metode_racik.kd_racik')
                    ->where([
                        ['resep_obat.no_rawat', '=', $noRawat], 
                        ['resep_obat.kd_dokter', '=', $kdDokter]
                    ])
                    ->select('resep_dokter_racikan.*', 'resep_obat.tgl_peresepan', 'resep_obat.jam_peresepan', 'metode_racik.nm_racik')
                    ->get();
        return $data;
    }
}

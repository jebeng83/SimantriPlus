<?php

namespace App\View\Components\ralan;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class Pasien extends Component
{
    public $data;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        $this->data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('catatan_pasien', 'reg_periksa.no_rkm_medis', '=', 'catatan_pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('personal_pasien', 'pasien.no_rkm_medis', '=', 'personal_pasien.no_rkm_medis')
            ->where('reg_periksa.no_rawat', $noRawat)
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'reg_periksa.kd_poli',
                'catatan_pasien.catatan',
                'personal_pasien.gambar',
                'pasien.*',
            )
            ->first();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.pasien')->with('data', $this->data)->with('dokter', session()->get('username'));
    }
}

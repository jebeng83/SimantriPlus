<?php

namespace App\View\Components\Ralan;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;
use Request;

class RujukInternal extends Component
{
    use EnkripsiData;
    public $noRawat, $encryptNoRawat;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        $this->encryptNoRawat = $this->encryptData($noRawat);
        $this->noRawat = $noRawat;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.rujuk-internal', [
            'encryptNoRawat' => $this->encryptNoRawat,
            'data' => $this->getRujukan($this->noRawat, session('username'), session('kd_poli')),
        ]);
    }

    public function getRujukan($noRawat, $kdDokter, $kdPoli)
    {
        $rm = Request::get('no_rm');
        return DB::table('rujukan_internal_poli')
                    ->join('poliklinik', 'poliklinik.kd_poli', '=', 'rujukan_internal_poli.kd_poli')
                    ->join('dokter', 'dokter.kd_dokter', '=', 'rujukan_internal_poli.kd_dokter')
                    ->join('rujukan_internal_poli_detail', 'rujukan_internal_poli_detail.no_rawat', '=', 'rujukan_internal_poli.no_rawat')
                    ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'rujukan_internal_poli.no_rawat')
                    ->where('reg_periksa.no_rkm_medis', $rm)
                    // ->where('rujukan_internal_poli.kd_dokter', $kdDokter)
                    // ->where('rujukan_internal_poli.kd_poli', $kdPoli)
                    ->select('rujukan_internal_poli.no_rawat', 'poliklinik.nm_poli', 'dokter.nm_dokter', 'rujukan_internal_poli_detail.konsul', 'rujukan_internal_poli_detail.pemeriksaan', 'rujukan_internal_poli_detail.diagnosa', 'rujukan_internal_poli_detail.saran')
                    ->get();
    }
    
}

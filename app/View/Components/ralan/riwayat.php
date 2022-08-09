<?php

namespace App\View\Components\ralan;
use Illuminate\Support\Facades\Crypt;
use DB;
use Illuminate\View\Component;

class riwayat extends Component
{
    public $data;
    public $heads;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        $param = $this->decryptData($noRawat);
        $pasien = DB::table('reg_periksa')
                            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                            ->where('reg_periksa.no_rawat', $param)
                            ->select('reg_periksa.no_rkm_medis')
                            ->first();
        $this->data = DB::table('reg_periksa')
                                    ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                                    ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                                    ->where('no_rkm_medis', $pasien->no_rkm_medis)
                                    ->select('reg_periksa.tgl_registrasi', 'reg_periksa.no_rawat', 'dokter.nm_dokter', 
                                            'reg_periksa.status_lanjut', 'poliklinik.nm_poli', 'reg_periksa.no_reg')
                                    ->orderBy('reg_periksa.tgl_registrasi', 'desc')
                                    ->limit(5)
                                    ->get();
        $this->heads = ['Tanggal', 'No. Rawat', 'Dokter', 'Keluhan', 'Diagnosa', 'Obstetri'];                        
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.riwayat',['data' => $this->data, 'heads' => $this->heads]);
    }

    public function decryptData($data)
    {
        $data = Crypt::decrypt($data);
        return $data;
    }

}

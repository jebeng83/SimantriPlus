<?php

namespace App\View\Components\Ranap;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class RiwayatRanap extends Component
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
        $pasien = $this->getPasien($noRawat);
        $this->data = $this->getRiwayatPemeriksaan($pasien->no_rkm_medis);
        $this->heads = ['No. Rawat', 'Dokter', 'Keluhan', 'Diagnosa'];                        
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ranap.riwayat-ranap',['data' => $this->data, 'heads' => $this->heads]);
    }

    public function getPasien($noRawat)
    {
        $data = DB::table('reg_periksa')
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->where('reg_periksa.no_rawat', $noRawat)
                    ->select('reg_periksa.no_rkm_medis')
                    ->first();

        return $data;
    }

    public function getRiwayatPemeriksaan($noRM)
    {
        $data = DB::table('reg_periksa')
                    ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                    ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                    ->where('no_rkm_medis', $noRM)
                    ->where('reg_periksa.stts', 'Sudah')
                    ->select('reg_periksa.tgl_registrasi', 'reg_periksa.no_rawat', 'dokter.nm_dokter', 
                            'reg_periksa.status_lanjut', 'poliklinik.nm_poli', 'reg_periksa.no_reg')
                    ->orderBy('reg_periksa.no_rawat', 'desc')
                    ->get();
                    
        return $data;
    }
}

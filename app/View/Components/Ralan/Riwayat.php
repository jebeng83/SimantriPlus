<?php

namespace App\View\Components\ralan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class Riwayat extends Component
{
    public $data, $noRawat;
    public $heads;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        $this->noRawat = $noRawat;
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
        return view('components.ralan.riwayat',[
            'data' => $this->data, 
            'heads' => $this->heads,
            // 'pemeriksaan' => $this->getPemeriksaanRalan($this->noRawat),
            // 'diagnosa' => $this->getDiagnosa($this->noRawat),
            // 'berkasLab' => $this->berkasLab($this->noRawat),
            // 'detailLab' => $this->detailLab($this->noRawat),
            // 'berkasRadiologi' => $this->berkasRadiologi($this->noRawat),
            // 'hasilRadiologi' => $this->hasilRadiologi($this->noRawat)
        ]);
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

    public function getPemeriksaanRalan($noRawat)
    {
        return DB::table('pemeriksaan_ralan')
                    ->where('no_rawat', $noRawat)
                    ->first();
    }

    public function berkasLab($noRawat)
    {
        return DB::table('berkas_digital_perawatan')
                    ->where('no_rawat', $noRawat)
                    ->where('kode', 'B05')
                    ->get();
    }

    public function detailLab($noRawat)
    {
        return DB::table('detail_periksa_lab')
                    ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template')
                    ->where('detail_periksa_lab.no_rawat', $noRawat)
                    ->select('template_laboratorium.Pemeriksaan', 'detail_periksa_lab.nilai', 'template_laboratorium.satuan', 'detail_periksa_lab.nilai_rujukan', 'detail_periksa_lab.keterangan')
                    ->get();
    }

    public function berkasRadiologi($noRawat)
    {
        return DB::table('gambar_radiologi')
                    ->where('no_rawat', $noRawat)
                    ->get();
    }

    public function hasilRadiologi($noRawat)
    {
        return DB::table('hasil_radiologi')
                    ->where('no_rawat', $noRawat)
                    ->get();
    }

    public static function getDiagnosa($noRawat)
    {
        $data = DB::table('diagnosa_pasien')
                    ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                    ->where('diagnosa_pasien.no_rawat', $noRawat)
                    ->select('penyakit.kd_penyakit', 'penyakit.nm_penyakit')
                    ->get();
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
                    ->orderBy('reg_periksa.tgl_registrasi', 'desc')
                    ->get();
                    
        return $data;
    }
}

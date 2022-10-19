<?php

namespace App\View\Components\ralan;

use App\Traits\EnkripsiData;
use Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;


class Resume extends Component
{
    use EnkripsiData;
    public $noRawat, $encrypNoRawat, $noRm, $kel, $diagnosa, $prosedur, $terapi;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->noRawat = Request::get('no_rawat');
        $this->encrypNoRawat = $this->encryptData($this->noRawat);
        $this->noRm = Request::get('no_rm');
        $this->kel = DB::table('pemeriksaan_ralan')->where('no_rawat', $this->noRawat)->select('keluhan')->first();
        $this->diagnosa = DB::table('resume_pasien')
                            ->join('reg_periksa', 'resume_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                            ->where('pasien.no_rkm_medis',$this->noRm)
                            ->first();
        $this->prosedur = DB::table('prosedur_pasien')
                                ->join('icd9', 'prosedur_pasien.kode', '=', 'icd9.kode')
                                ->where('prosedur_pasien.no_rawat', $this->noRawat)
                                ->where('prosedur_pasien.prioritas', '1')
                                ->where('prosedur_pasien.status', 'Ralan')
                                ->select('icd9.deskripsi_panjang')
                                ->first();
        $this->terapi = DB::table('resep_dokter')
                            ->join('resep_obat', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
                            ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'resep_obat.no_rawat')
                            ->where('resep_obat.no_rawat', $this->noRawat)
                            ->where('reg_periksa.status_lanjut', 'Ralan')
                            ->selectRaw("GROUP_CONCAT( databarang.nama_brng,'-', resep_dokter.jml SEPARATOR '\r\n') AS nama_brng")
                            ->first();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.resume',[
            'kel' => $this->kel, 
            'diagnosa' => $this->diagnosa, 
            'prosedur' => $this->prosedur, 
            'terapi' => $this->terapi,
            'encrypNoRawat' => $this->encrypNoRawat,
        ]);
    }

    public function getKeluhanUtama()
    {
        $data = DB::table('pemeriksaan_ralan')->where('no_rawat', $this->noRawat)->select('keluhan')->first();
        return $data ?? null;
    }

    public function getDiagnosaUtama()
    {
        $data = DB::table('resume_pasien')
                    ->join('reg_periksa', 'resume_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                    ->where('pasien.no_rkm_medis',$this->noRm)
                    ->first();
        return $data ?? null;
    }

    public function getProsedurUtama()
    {
        $data = DB::table('prosedur_pasien')
                    ->join('icd9', 'prosedur_pasien.kode', '=', 'icd9.kode')
                    ->where('prosedur_pasien.no_rawat', $this->noRawat)
                    ->where('prosedur_pasien.prioritas', '1')
                    ->where('prosedur_pasien.status', 'Ralan')
                    ->select('icd9.deskripsi_panjang')
                    ->first();
        return $data ?? null;
    }

    public function getTerapi()
    {
        $data = DB::table('resep_dokter')
                    ->join('resep_obat', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
                    ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                    ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'resep_obat.no_rawat')
                    ->where('resep_obat.no_rawat', $this->noRawat)
                    ->where('reg_periksa.status_lanjut', 'Ralan')
                    ->selectRaw("GROUP_CONCAT( databarang.nama_brng,'-', resep_dokter.jml SEPARATOR '\r\n') AS nama_brng")
                    ->first();
        return $data ?? null;
    }
}

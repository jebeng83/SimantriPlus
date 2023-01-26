<?php

namespace App\View\Components\Ranap;

use Illuminate\View\Component;
use App\Traits\EnkripsiData;
use Illuminate\Support\Facades\DB;
use Request;

class ResepRanap extends Component
{
    use EnkripsiData;
    public $heads, $riwayatPeresepan, $resep, $dokter, $noRM, $noRawat, $encryptNoRawat, $encryptNoRM, $dataMetodeRacik, $bangsal;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->noRawat = Request::get('no_rawat');
        $this->noRM = Request::get('no_rm');
        $this->bangsal = Request::get('bangsal');
        $this->encryptNoRawat = $this->encryptData($this->noRawat);
        $this->encryptNoRM = $this->encryptData($this->noRM);
        $this->dokter = session()->get('username');
        $this->heads = ['Nomor Resep', 'Tanggal','Detail Resep', 'Aksi'];
        $this->riwayatPeresepan = DB::table('reg_periksa')
                                ->join('resep_obat', 'reg_periksa.no_rawat', '=', 'resep_obat.no_rawat')
                                ->where('resep_obat.kd_dokter', $this->dokter)
                                ->where('reg_periksa.no_rkm_medis', $this->noRM)
                                ->where('reg_periksa.status_lanjut', 'Ranap')
                                ->orderBy('resep_obat.tgl_peresepan', 'desc')
                                ->select('resep_obat.no_resep', 'resep_obat.tgl_peresepan', 'resep_obat.jam_peresepan')
                                ->limit(5)
                                ->get();

        $this->resep = DB::table('resep_dokter')
                        ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                        ->join('resep_obat', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
                        ->where('resep_obat.no_rawat', $this->noRawat)
                        ->where('resep_obat.kd_dokter', $this->dokter)
                        ->where('resep_obat.tgl_peresepan', date('Y-m-d'))
                        ->select('resep_dokter.no_resep', 'resep_dokter.kode_brng', 'resep_dokter.jml', 'databarang.nama_brng', 'resep_dokter.aturan_pakai', 'resep_dokter.no_resep', 'databarang.nama_brng', 'resep_obat.tgl_peresepan', 'resep_obat.jam_peresepan')
                        ->get();

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
        return view('components.ranap.resep-ranap',[
            'heads' => $this->heads, 
            'riwayatPeresepan' => $this->riwayatPeresepan, 
            'resep' => $this->resep,
            'no_rawat' => $this->noRawat,
            'encryptNoRawat' => $this->encryptNoRawat,
            'encryptNoRM' => $this->encryptNoRM,
            'dataMetodeRacik' => $this->dataMetodeRacik,
            'bangsal' => $this->bangsal,
            'resepRacikan' => $this->getResepRacikan($this->noRawat, session()->get('username')),
        ]);
    }

    public static function getResepObat($noResep){
        $data = DB::table('resep_dokter')
                    ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                    ->where('resep_dokter.no_resep', $noResep)
                    ->select('databarang.nama_brng', 'resep_dokter.jml', 'resep_dokter.aturan_pakai')
                    ->get();
        
        return $data;
    }

    public static function getDetailRacikan($noResep)
    {
        return DB::table('resep_dokter_racikan_detail')
                    ->join('databarang', 'resep_dokter_racikan_detail.kode_brng', '=', 'databarang.kode_brng')
                    ->where('resep_dokter_racikan_detail.no_resep', $noResep)
                    ->select('databarang.nama_brng', 'resep_dokter_racikan_detail.*')
                    ->get();
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

    // public function encryptData($data)
    // {
    //     $data = Crypt::encrypt($data);
    //     return $data;
    // }
}

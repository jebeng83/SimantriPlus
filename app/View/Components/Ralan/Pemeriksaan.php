<?php

namespace App\View\Components\ralan;

use App\Traits\EnkripsiData;
use Request; 
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class Pemeriksaan extends Component
{
    use EnkripsiData;
    public $noRawat, $encryptNoRawat, $data;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        $this->noRawat = $noRawat;
        
        // Sanitasi input untuk mencegah masalah encoding
        if (!is_string($this->noRawat)) {
            $this->noRawat = (string)$this->noRawat;
        }
        
        // Bersihkan karakter non-printable
        $this->noRawat = preg_replace('/[[:^print:]]/', '', $this->noRawat);

        $this->encryptNoRawat = $this->encryptData($noRawat);
        $this->data = $this->getRiwayat(Request::get('no_rm'));
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $pemeriksaan = $this->data->firstWhere('no_rawat', '=', $this->noRawat);
        $alergi =  $this->data->whereNotNull('alergi')->where('alergi', '!=', '-')->implode('alergi', ', ');
        return view('components.ralan.pemeriksaan', [
            'pemeriksaan' => $pemeriksaan,
            'encryptNoRawat' => $this->encryptNoRawat,
            'alergi' => $alergi,
        ]);
    }

    public function getRiwayat($noRM)
    {
        // Sanitasi input
        if (!is_string($noRM)) {
            $noRM = (string)$noRM;
        }
        
        // Bersihkan karakter non-printable
        $cleanNoRM = preg_replace('/[[:^print:]]/', '', $noRM);
        
        $data = DB::table('pemeriksaan_ralan')
                            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'pemeriksaan_ralan.no_rawat')
                            ->where('reg_periksa.no_rkm_medis', $cleanNoRM)
                            ->select('pemeriksaan_ralan.*')
                            ->orderBy('pemeriksaan_ralan.tgl_perawatan', 'DESC')
                            ->get();
        return $data;
    }

    public function getAlergi()
    {
    
    }
}

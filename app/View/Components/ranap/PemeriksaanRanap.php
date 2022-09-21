<?php

namespace App\View\Components\Ranap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Crypt;

class PemeriksaanRanap extends Component
{
    public $noRawat, $noRM, $heads, $riwayat, $pemeriksaan, $encryptNoRawat;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        $this->noRM = Request::get('no_rm');
        $this->encryptNoRawat = $this->encryptData($noRawat);
        $this->noRawat = $noRawat;
        $this->heads = ['Tgl', 'Jam', 'Keluhan', 'Pemeriksaan', 'Penilaian', 'Suhu', 'tensi', 'Nadi', 'Aksi'];
        $this->riwayat = $this->getRiwayat($this->noRM);
        // $this->pemeriksaan = DB::table('pemeriksaan_ranap')->where('no_rawat', $this->noRawat)->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ranap.pemeriksaan-ranap', [
            'no_rawat' => $this->noRawat,
            'heads' => $this->heads,
            'riwayat' => $this->riwayat,
            'no_rm' => $this->noRM,
            'encryptNoRawat' => $this->encryptNoRawat,
        ]);
    }

    public function getRiwayat($noRM)
    {
        $data = DB::table('pemeriksaan_ranap')
                            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'pemeriksaan_ranap.no_rawat')
                            ->where('reg_periksa.no_rkm_medis', $noRM)
                            ->select('pemeriksaan_ranap.*')
                            ->orderBy('pemeriksaan_ranap.tgl_perawatan', 'DESC')
                            ->get();
        return $data;
    }

    public function encryptData($data)
    {
        $data = Crypt::encrypt($data);
        return $data;
    }
}

<?php

namespace App\View\Components\ralan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request; 
use Illuminate\View\Component;
use DB;

class pemeriksaan extends Component
{
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
        $pemeriksaan = DB::table('pemeriksaan_ralan')->where('no_rawat', $this->noRawat)->first();
        return view('components.ralan.pemeriksaan', [
            'pemeriksaan' => $pemeriksaan,
            'encryptNoRawat' => $this->encryptNoRawat,
        ]);
    }

    public function encryptData($data)
    {
        $data = Crypt::encrypt($data);
        return $data;
    }
}

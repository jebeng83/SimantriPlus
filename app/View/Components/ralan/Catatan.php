<?php

namespace App\View\Components\Ralan;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class Catatan extends Component
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
        return view('components.ralan.catatan',[
            'noRawat' => $this->noRawat,
            'data' => $this->catatan(),
        ]);
    }

    public function catatan()
    {
        return DB::table('catatan_perawatan')
            ->where('no_rawat', $this->noRawat)
            ->first();
    }
}

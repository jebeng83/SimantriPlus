<?php

namespace App\Http\Controllers\Ralan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class RujukInternalPasien extends Controller
{
    use EnkripsiData;
    public $noRawat, $noRM, $encryptNoRawat;
    public function __construct(Request $request)
    {
        $this->middleware('loginauth');
        $this->encryptNoRawat = $request->get('no_rawat');
        $this->noRawat = $this->decryptData($request->get('no_rawat'));
        $this->noRM = $this->decryptData($request->get('no_rm'));
    }
    
    public function index()
    {
        return view('ralan.rujuk-internal-pasien',[
            'noRawat' => $this->noRawat, 'noRM' => $this->noRM, 'rujukan' => $this->getRujukanInternal($this->noRawat), 'encryptNoRawat' => $this->encryptNoRawat,
        ]);
    }

    public function getRujukanInternal($noRawat)
    {
        return DB::table('rujukan_internal_poli')
                    ->join('rujukan_internal_poli_detail','rujukan_internal_poli.no_rawat', '=', 'rujukan_internal_poli_detail.no_rawat')
                    ->join('poliklinik','rujukan_internal_poli.kd_poli', '=', 'poliklinik.kd_poli')
                    ->join('dokter','rujukan_internal_poli.kd_dokter', '=', 'dokter.kd_dokter')
                    ->where('rujukan_internal_poli.no_rawat', $noRawat)
                    ->select('rujukan_internal_poli.no_rawat', 'dokter.nm_dokter', 'poliklinik.nm_poli', 'rujukan_internal_poli_detail.konsul', 'rujukan_internal_poli_detail.pemeriksaan','rujukan_internal_poli_detail.diagnosa','rujukan_internal_poli_detail.saran')
                    ->first();
    }
}

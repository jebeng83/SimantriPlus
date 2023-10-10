<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\BpjsTraits;

class BPJSController extends Controller
{
    use BpjsTraits;
    public function icare(Request $request)
    {
        $input = $request->all();
        $dokter = DB::table('maping_dokter_dpjpvclaim')
            ->where('kd_dokter', $input['kodedokter'])
            ->first();
        $data['param'] = $input['param'];
        $data['kodedokter'] = intval($dokter->kd_dokter_bpjs);
        $response = $this->requestPostBpjs('api/rs/validate', $data);
        return $response;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlamatController extends Controller
{
    public function getPropinsi(Request $request)
    {
        $q = $request->q;
        $data = DB::table('propinsi')->where('nm_prop', 'like', '%' . $q . '%')->get();
        return response()->json($data);
    }

    public function getKabupaten(Request $request)
    {
        $q = $request->q;
        $data = DB::table('kabupaten')->where('nm_kab', 'like', '%' . $q . '%')->get();
        return response()->json($data);
    }

    public function getKecamatan(Request $request)
    {
        $q = $request->q;
        $data = DB::table('kecamatan')->where('nm_kec', 'like', '%' . $q . '%')->get();
        return response()->json($data);
    }

    public function getKelurahan(Request $request)
    {
        $q = $request->q;
        $data = DB::table('kelurahan')->where('nm_kel', 'like', '%' . $q . '%')->get();
        return response()->json($data);
    }
}

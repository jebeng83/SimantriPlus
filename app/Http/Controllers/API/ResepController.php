<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepController extends Controller
{
    public function getDataObat($kdObat)
    {
        $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $kdObat)->where('kd_bangsal', 'DPF')->max('tanggal');
        $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $kdObat)->where('tanggal', $maxTgl)->where    ('kd_bangsal', 'DPF')->max('jam');
        $data = DB::table('databarang')
            ->join('riwayat_barang_medis', 'databarang.kode_brng', '=', 'riwayat_barang_medis.kode_brng')
            ->where('databarang.kode_brng', $kdObat)
            ->where('riwayat_barang_medis.tanggal', $maxTgl)
            ->where('riwayat_barang_medis.jam', $maxJam)
            ->select('databarang.*', 'riwayat_barang_medis.stok_akhir')
            ->first();

        return response()->json($data);
    }

    public function postResep($noRawat)
    {
        
    }
}

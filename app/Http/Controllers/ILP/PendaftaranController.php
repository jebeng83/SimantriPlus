<?php

namespace App\Http\Controllers\ILP;

use App\Models\Dokter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    /**
     * Show the ILP pendaftaran page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $kd_dokter = session()->get('username') ?? '';
        
        // Ambil data dokter untuk dropdown
        $dokter = DB::table('dokter')
                    ->join('pegawai', 'dokter.kd_dokter', '=', 'pegawai.nik')
                    ->select('dokter.kd_dokter', 'pegawai.nama')
                    ->get();
        
        return view('ilp.pendaftaran', [
            'nm_dokter' => $this->getDokter($kd_dokter),
            'dokter' => $dokter
        ]);
    }
    
    private function getDokter($kd_dokter)
    {
        $dokter = DB::table('pegawai')->where('nik', $kd_dokter)->first();
        return $dokter ? $dokter->nama : 'Dokter';
    }
}
<?php

namespace App\Http\Controllers\ILP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SkriningPkg;
use App\Models\Pegawai;
use Session;
use Carbon\Carbon;

class DashboardCKGController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    /**
     * Show the CKG dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $kd_dokter = session()->get('username');
        
        // Ambil filter periode (default: bulan)
        $periode_filter = $request->input('periode', 'bulan');
        
        // Hitung data jumlah entri per pegawai berdasarkan filter periode
        $data_entri_pegawai = $this->getDataEntriPegawai($periode_filter);
        
        // Jika permintaan AJAX, kembalikan data dalam format JSON
        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'entri_pegawai' => $data_entri_pegawai
                ],
                'periode_filter' => $periode_filter,
                'message' => 'Data berhasil dimuat'
            ]);
        }
        
        return view('ilp.dashboard_ckg', [
            'nm_dokter' => $this->getDokter($kd_dokter),
            'data_entri_pegawai' => $data_entri_pegawai,
            'periode_filter' => $periode_filter,
        ]);
    }
    
    /**
     * Ambil data jumlah entri per pegawai berdasarkan periode
     * 
     * @param string $periode
     * @return array
     */
    private function getDataEntriPegawai($periode = 'bulan')
    {
        // Tentukan rentang tanggal berdasarkan periode
        $tanggal_mulai = $this->getTanggalMulai($periode);
        $tanggal_akhir = Carbon::now()->endOfDay();
        
        // Query untuk mendapatkan data entri per pegawai
        $query = DB::table('skrining_pkg as sp')
            ->join('pegawai as p', 'sp.id_petugas_entri', '=', 'p.nik')
            ->select(
                'p.nik',
                'p.nama as nama_pegawai',
                DB::raw('COUNT(sp.id_pkg) as jumlah_entri')
            )
            ->whereNotNull('sp.id_petugas_entri')
            ->whereBetween('sp.updated_at', [$tanggal_mulai, $tanggal_akhir])
            ->groupBy('p.nik', 'p.nama')
            ->orderBy('jumlah_entri', 'desc')
            ->get();
        
        return $query->toArray();
    }
    
    /**
     * Tentukan tanggal mulai berdasarkan periode
     * 
     * @param string $periode
     * @return Carbon
     */
    private function getTanggalMulai($periode)
    {
        switch ($periode) {
            case 'hari':
                return Carbon::today()->startOfDay();
            case 'minggu':
                return Carbon::now()->startOfWeek();
            case 'bulan':
                return Carbon::now()->startOfMonth();
            case 'tahun':
                return Carbon::now()->startOfYear();
            default:
                return Carbon::now()->startOfMonth();
        }
    }
    
    /**
     * Ambil nama user berdasarkan username yang login
     * 
     * @param string $username
     * @return string
     */
    private function getDokter($username)
    {
        // Coba cari di tabel pegawai terlebih dahulu
        $pegawai = DB::table('pegawai')
            ->where('nik', $username)
            ->first();
            
        if ($pegawai) {
            return $pegawai->nama;
        }
        
        // Jika tidak ditemukan di pegawai, coba cari di tabel dokter
        $dokter = DB::table('dokter')
            ->where('kd_dokter', $username)
            ->first();
            
        return $dokter ? $dokter->nm_dokter : 'User';
    }
}
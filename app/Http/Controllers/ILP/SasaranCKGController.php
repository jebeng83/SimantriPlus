<?php

namespace App\Http\Controllers\ILP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pasien;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SasaranCKGController extends Controller
{
    public function index()
    {
        // Mendapatkan tanggal hari ini
        $today = Carbon::now()->format('m-d');
        
        // Query untuk mendapatkan pasien yang berulang tahun hari ini
        $pasienUlangTahun = DB::table('pasien')
            ->select(
                'no_rkm_medis',
                'nm_pasien',
                'tgl_lahir',
                'no_tlp',
                'alamatpj',
                'kelurahanpj',
                'data_posyandu'
            )
            ->whereRaw("DATE_FORMAT(tgl_lahir, '%m-%d') = ?", [$today])
            ->get();

        return view('ilp.sasaran_ckg', [
            'pasienUlangTahun' => $pasienUlangTahun,
            'title' => 'Sasaran CKG'
        ]);
    }
    
    public function detail($noRekamMedis)
    {
        \Log::info('Detail method called for: ' . $noRekamMedis);
        
        $pasien = DB::table('pasien')
            ->select(
                'no_rkm_medis',
                'nm_pasien',
                'tgl_lahir',
                'no_tlp',
                'alamatpj',
                'kelurahanpj',
                'namakeluarga',
                'data_posyandu'
            )
            ->where('no_rkm_medis', $noRekamMedis)
            ->first();
            
        \Log::info('Patient data retrieved: ', (array) $pasien);
            
        return response()->json($pasien);
    }
    
    public function kirimWA($noRekamMedis)
    {
        \Log::info('KirimWA method called for: ' . $noRekamMedis);
        
        $pasien = DB::table('pasien')
            ->select('nm_pasien', 'no_tlp')
            ->where('no_rkm_medis', $noRekamMedis)
            ->first();
            
        \Log::info('Patient data for WA: ', (array) $pasien);
            
        if (!$pasien || empty($pasien->no_tlp)) {
            \Log::warning('Phone number not found for: ' . $noRekamMedis);
            return response()->json(['status' => 'error', 'message' => 'Nomor telepon tidak ditemukan'], 404);
        }
        
        $nomorTelepon = $pasien->no_tlp;
        // Pastikan format nomor telepon benar
        if (substr($nomorTelepon, 0, 1) === '0') {
            $nomorTelepon = '62' . substr($nomorTelepon, 1);
        }
        
        $pesan = "Selamat Ulang Tahun " . $pasien->nm_pasien . "! Kami dari SIMKES_KHANZA mengucapkan selamat ulang tahun dan semoga selalu sehat. Jangan lupa untuk melakukan pemeriksaan kesehatan rutin ya.";
        
        $url = "https://wa.me/" . $nomorTelepon . "?text=" . urlencode($pesan);
        
        \Log::info('WhatsApp URL generated: ' . $url);
        
        return response()->json(['status' => 'success', 'url' => $url]);
    }
} 
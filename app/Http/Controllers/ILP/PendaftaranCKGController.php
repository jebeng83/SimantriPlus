<?php

namespace App\Http\Controllers\ILP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PendaftaranCKGController extends Controller
{
    /**
     * Menampilkan halaman pendaftaran CKG
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Log untuk debugging
        Log::info('Halaman pendaftaran CKG diakses');
        
        // Filter data jika ada
        $tanggal_awal = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $status = $request->input('status');
        
        // Query dasar
        $query = DB::table('skrining_pkg')
            ->select('id_pkg', 'nik', 'nama_lengkap', 'tanggal_lahir', 'umur', 'jenis_kelamin', 
                     'no_handphone', 'no_rkm_medis', 'tanggal_skrining', 'status');
                     
        // Terapkan filter jika ada
        if ($tanggal_awal) {
            $query->whereDate('tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
        
        // Ambil data
        $data_pendaftaran = $query->orderBy('tanggal_skrining', 'desc')->get();
        
        // Log hasil query untuk debugging
        Log::info('Jumlah data pendaftaran CKG: ' . count($data_pendaftaran));
        if (count($data_pendaftaran) > 0) {
            Log::info('Data pertama: ', (array) $data_pendaftaran[0]);
        }

        return view('ilp.pendaftaran_ckg', compact('data_pendaftaran'));
    }

    /**
     * Menampilkan detail pendaftaran CKG
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request)
    {
        $id = $request->input('id');
        
        // Log untuk debugging
        Log::info('Detail CKG dipanggil dengan ID: ' . $id);
        
        // Mengambil data detail pendaftaran dengan join ke tabel pasien
        $detail = DB::table('skrining_pkg')
            ->leftJoin('pasien', 'skrining_pkg.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->select(
                'skrining_pkg.*',
                'pasien.pekerjaan',
                'pasien.alamatpj',
                'pasien.kelurahanpj',
                'pasien.kecamatanpj',
                'pasien.kabupatenpj'
            )
            ->where('id_pkg', $id)
            ->first();
            
        if (!$detail) {
            Log::error('Data CKG tidak ditemukan untuk ID: ' . $id);
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
        
        // Log hasil query untuk debugging
        Log::info('Data detail CKG ditemukan: ', (array) $detail);
        
        // Mengembalikan view partial untuk detail
        return view('ilp.partials.detail_ckg', compact('detail'));
    }

    /**
     * Memperbarui status pendaftaran CKG
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        
        // Update status di database
        $updated = DB::table('skrining_pkg')
            ->where('id_pkg', $id)
            ->update(['status' => $status]);
            
        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui']);
        } else {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status'], 500);
        }
    }
}
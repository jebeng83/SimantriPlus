<?php

namespace App\Http\Controllers\ILP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Pegawai;

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
            ->leftJoin('pasien', 'skrining_pkg.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->select(
                'skrining_pkg.id_pkg',
                'skrining_pkg.nik',
                'skrining_pkg.nama_lengkap',
                'skrining_pkg.tanggal_lahir',
                'skrining_pkg.umur',
                'skrining_pkg.jenis_kelamin',
                'skrining_pkg.no_handphone',
                'skrining_pkg.no_rkm_medis',
                'skrining_pkg.tanggal_skrining',
                'skrining_pkg.status',
                'skrining_pkg.kunjungan_sehat',
                'pasien.no_peserta'
            );
                     
        // Terapkan filter jika ada
        if ($tanggal_awal) {
            $query->whereDate('tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        if ($status !== null && $status !== '') {
            $query->where('skrining_pkg.status', $status);
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
        
        // Mengambil data detail pendaftaran dengan join ke tabel pasien dan pegawai
        $detail = DB::table('skrining_pkg')
            ->leftJoin('pasien', 'skrining_pkg.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('pegawai', 'skrining_pkg.id_petugas_entri', '=', 'pegawai.nik')
            ->select(
                'skrining_pkg.*',
                'pasien.pekerjaan',
                'pasien.alamatpj',
                'pasien.kelurahanpj',
                'pasien.kecamatanpj',
                'pasien.kabupatenpj',
                'pasien.no_peserta',
                'pegawai.nama as petugas_entry_nama'
            )
            ->where('id_pkg', $id)
            ->first();
            
        if (!$detail) {
            Log::error('Data CKG tidak ditemukan untuk ID: ' . $id);
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
        
        // Log hasil query untuk debugging
        Log::info('Data detail CKG ditemukan: ', (array) $detail);
        
        // Ambil data pegawai aktif untuk dropdown petugas entry
        $pegawai_aktif = Pegawai::where('stts_aktif', 'Aktif')
            ->select('nik', 'nama')
            ->orderBy('nama')
            ->get();
        
        // Mengembalikan view partial untuk detail
        return view('ilp.partials.detail_ckg', compact('detail', 'pegawai_aktif'));
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
        $kunjunganSehat = $request->input('kunjungan_sehat', null);
        
        // Ambil NIK petugas dari session
        $nikPetugas = session('username'); // NIK petugas dari session
        $nikPetugasEntri = null;
        
        // Jika status akan diubah menjadi selesai, validasi NIK petugas
        if ($status == '1' && $nikPetugas) {
            $pegawai = DB::table('pegawai')
                ->where('nik', $nikPetugas)
                ->where('stts_aktif', 'Aktif')
                ->first();
                
            if ($pegawai) {
                $nikPetugasEntri = $pegawai->nik; // Gunakan NIK, bukan ID
            }
        }
        
        // Cek apakah data sudah memiliki status selesai
        $currentData = DB::table('skrining_pkg')
            ->where('id_pkg', $id)
            ->first();
            
        if (!$currentData) {
            return response()->json([
                'success' => false, 
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        
        // Jika status sudah selesai (1) dan mencoba diubah lagi menjadi selesai
        // KECUALI jika hanya update kunjungan_sehat tanpa mengubah status
        if ($currentData->status == '1' && $status == '1' && $kunjunganSehat === null) {
            return response()->json([
                'success' => false, 
                'message' => 'Data sudah dalam status selesai. Tidak dapat diubah kembali.'
            ], 400);
        }
        
        // Jika data sudah selesai dan hanya ingin update kunjungan_sehat, izinkan
        if ($currentData->status == '1' && $status == '1' && $kunjunganSehat !== null) {
            // Hanya update kunjungan_sehat, tidak perlu validasi petugas entry lagi
            $dataUpdate = ['kunjungan_sehat' => (string) $kunjunganSehat === '1' ? '1' : '0'];
            
            try {
                $updated = DB::table('skrining_pkg')
                    ->where('id_pkg', $id)
                    ->update($dataUpdate);
                
                if ($updated) {
                    return response()->json(['success' => true, 'message' => 'Status kunjungan sehat berhasil diperbarui']);
                } else {
                    return response()->json(['success' => false, 'message' => 'Gagal memperbarui status kunjungan sehat'], 500);
                }
            } catch (\Exception $e) {
                Log::error('Error updating kunjungan_sehat: ' . $e->getMessage());
                return response()->json([
                    'success' => false, 
                    'message' => 'Terjadi kesalahan saat memperbarui status kunjungan sehat.'
                ], 500);
            }
        }
        
        // Validasi: Petugas entry wajib diisi ketika status diubah menjadi selesai (status = 1)
        if ($status == '1' && (empty($nikPetugasEntri) || $nikPetugasEntri === null)) {
            return response()->json([
                'success' => false, 
                'message' => 'Petugas Entry tidak ditemukan. Pastikan Anda sudah login dengan benar.'
            ], 400);
        }
        
        $dataUpdate = ['status' => $status];
        if ($kunjunganSehat !== null) {
            $dataUpdate['kunjungan_sehat'] = (string) $kunjunganSehat === '1' ? '1' : '0';
        }
        
        // Gunakan NIK pegawai untuk kolom id_petugas_entri
        if ($nikPetugasEntri !== null) {
            $dataUpdate['id_petugas_entri'] = $nikPetugasEntri;
        }
        
        try {
            $updated = DB::table('skrining_pkg')
                ->where('id_pkg', $id)
                ->update($dataUpdate);
            
            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui']);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal memperbarui status'], 500);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error updating CKG status: ' . $e->getMessage());
            
            // Handle foreign key constraint violation
            if (strpos($e->getMessage(), 'foreign key constraint') !== false || 
                strpos($e->getMessage(), 'pkg_ibfk_2') !== false) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Petugas Entry tidak valid. Silakan pilih petugas yang terdaftar dalam sistem.'
                ], 400);
            }
            
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan database. Silakan coba lagi atau hubungi administrator.'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error updating CKG status: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'
            ], 500);
        }
    }

    /**
     * Cek status processing untuk record tertentu
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkProcessingStatus(Request $request)
    {
        // Hapus status processing yang sudah expired
        DB::table('ckg_processing_status')
            ->where('expires_at', '<=', now())
            ->delete();
        
        // Ambil semua record yang sedang diproses
        $processing_records = DB::table('ckg_processing_status')
            ->where('expires_at', '>', now())
            ->pluck('id_pkg')
            ->toArray();
            
        return response()->json([
            'processing_records' => $processing_records
        ]);
    }

    /**
     * Set status processing untuk record tertentu
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function setProcessing(Request $request)
    {
        $id = $request->input('id');
        $userSession = session()->getId();
        
        try {
            // Hapus status processing yang sudah expired
            DB::table('ckg_processing_status')
                ->where('expires_at', '<=', now())
                ->delete();
            
            // Cek apakah sudah ada yang memproses
            $existing = DB::table('ckg_processing_status')
                ->where('id_pkg', $id)
                ->where('expires_at', '>', now())
                ->first();
                
            if ($existing && $existing->user_session !== $userSession) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Record sedang diproses oleh user lain'
                ]);
            }
            
            // Set atau update status processing
            DB::table('ckg_processing_status')
                ->updateOrInsert(
                    ['id_pkg' => $id],
                    [
                        'user_session' => $userSession,
                        'expires_at' => now()->addMinutes(30),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
                
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Error setting processing status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    /**
     * Release status processing untuk record tertentu
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function releaseProcessing(Request $request)
    {
        $id = $request->input('id');
        $userSession = session()->getId();
        
        try {
            // Hapus status processing hanya jika milik user session yang sama
            $deleted = DB::table('ckg_processing_status')
                ->where('id_pkg', $id)
                ->where('user_session', $userSession)
                ->delete();
                
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Error releasing processing status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }
}
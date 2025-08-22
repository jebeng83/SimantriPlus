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
        $nama_sekolah = $request->input('nama_sekolah');
        $kelas = $request->input('kelas');
        
        // Query dasar
        $query = DB::table('skrining_pkg')
            ->leftJoin('pasien', 'skrining_pkg.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('data_siswa_sekolah', 'skrining_pkg.no_rkm_medis', '=', 'data_siswa_sekolah.no_rkm_medis')
            ->leftJoin('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
            ->leftJoin('data_kelas', 'data_siswa_sekolah.id_kelas', '=', 'data_kelas.id_kelas')
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
                'pasien.no_peserta',
                'data_sekolah.nama_sekolah',
                'data_kelas.kelas'
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
        
        if ($nama_sekolah !== null && $nama_sekolah !== '') {
            $query->where('data_sekolah.id_sekolah', $nama_sekolah);
        }
        
        if ($kelas !== null && $kelas !== '') {
            $query->where('data_kelas.id_kelas', $kelas);
        }
        
        // Ambil data
        $data_pendaftaran = $query->orderBy('tanggal_skrining', 'desc')->get();
        
        // Ambil data untuk dropdown filter
        $daftar_sekolah = DB::table('data_sekolah')
            ->orderBy('nama_sekolah')
            ->get(['id_sekolah', 'nama_sekolah']);
            
        $daftar_kelas = DB::table('data_kelas')
            ->orderBy('kelas')
            ->get(['id_kelas', 'kelas']);
        
        // Log hasil query untuk debugging
        Log::info('Jumlah data pendaftaran CKG: ' . count($data_pendaftaran));
        if (count($data_pendaftaran) > 0) {
            Log::info('Data pertama: ', (array) $data_pendaftaran[0]);
        }

        return view('ilp.pendaftaran_ckg', compact('data_pendaftaran', 'daftar_sekolah', 'daftar_kelas'));
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
            return response()->json([
                'error' => 'Data CKG Tidak Ditemukan',
                'message' => 'Data CKG dengan ID ' . $id . ' tidak ditemukan di sistem. Pastikan ID yang dimasukkan benar.',
                'suggestion' => 'Silakan periksa kembali ID CKG atau hubungi administrator jika masalah berlanjut.'
            ], 404);
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
     * Menampilkan detail siswa sekolah CKG
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function detailSekolah(Request $request)
    {
        $id = $request->input('id');
        
        // Log untuk debugging
        Log::info('Detail CKG Sekolah dipanggil dengan ID: ' . $id);
        
        // Mengambil data detail siswa dengan join ke tabel yang diperlukan
        $detail = DB::table('data_siswa_sekolah')
            ->leftJoin('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
            ->leftJoin('jenis_sekolah', 'data_sekolah.id_jenis_sekolah', '=', 'jenis_sekolah.id')
            ->leftJoin('data_kelas', 'data_siswa_sekolah.id_kelas', '=', 'data_kelas.id_kelas')
            // Join untuk kelurahan, kecamatan, kabupaten tidak diperlukan karena field sudah berisi nama langsung
            ->leftJoin('skrining_siswa_sd', 'data_siswa_sekolah.no_rkm_medis', '=', 'skrining_siswa_sd.no_rkm_medis')
            ->leftJoin('skrining_pkg', 'data_siswa_sekolah.no_rkm_medis', '=', 'skrining_pkg.no_rkm_medis')
            ->leftJoin('users', 'skrining_pkg.id_petugas_entri', '=', 'users.id')
            ->leftJoin('pegawai', 'skrining_pkg.id_petugas_entri', '=', 'pegawai.nik')
            ->select(
                'data_siswa_sekolah.*',
                'pasien.no_rkm_medis as no_pasien',
                'pasien.no_ktp',
                'pasien.no_peserta',
                'pasien.tgl_lahir',
                'pasien.nm_pasien as nama_siswa',
                'pasien.alamatpj as alamat_siswa',
                'pasien.kelurahanpj as kelurahan_siswa',
                'pasien.kecamatanpj as kecamatan_siswa',
                'pasien.kabupatenpj as kabupaten_siswa',
                'data_siswa_sekolah.jenis_disabilitas',
                'data_siswa_sekolah.no_whatsapp',
                'data_siswa_sekolah.nik_ortu',
                'data_siswa_sekolah.nama_ortu',
                'data_siswa_sekolah.tanggal_lahir as tanggal_lahir_ortu',
                'data_siswa_sekolah.jenis_kelamin as jenis_kelamin_ortu',
                'data_siswa_sekolah.status as status_ortu',
                'data_sekolah.nama_sekolah',
                'jenis_sekolah.nama as jenis_sekolah',
                'data_kelas.kelas as nama_kelas',
                'skrining_siswa_sd.created_at as tanggal_skrining',
                'users.name as petugas_skrining',
                'skrining_siswa_sd.berat_badan',
                'skrining_siswa_sd.tinggi_badan',
                'skrining_siswa_sd.imt',
                'skrining_siswa_sd.kategori_status_gizi as status_gizi',
                'skrining_siswa_sd.sistole as tekanan_darah',
                'skrining_siswa_sd.sistole',
                'skrining_siswa_sd.diastole',
                'skrining_siswa_sd.visus_mata_kanan as visus_od',
                'skrining_siswa_sd.visus_mata_kiri as visus_os',
                'skrining_siswa_sd.selaput_mata_kanan as kelainan_mata',
                'skrining_siswa_sd.gangguan_telingga_kanan as pendengaran_kanan',
                'skrining_siswa_sd.gangguan_telingga_kiri as pendengaran_kiri',
                'skrining_siswa_sd.gangguan_telingga_kanan as kelainan_telinga',
                'skrining_siswa_sd.gangguan_telingga_kanan',
                'skrining_siswa_sd.gangguan_telingga_kiri',
                'skrining_siswa_sd.serumen_kanan',
                'skrining_siswa_sd.serumen_kiri',
                'skrining_siswa_sd.infeksi_telingga_kanan',
                'skrining_siswa_sd.infeksi_telingga_kiri',
                'skrining_siswa_sd.selaput_mata_kanan',
                'skrining_siswa_sd.selaput_mata_kiri',
                'skrining_siswa_sd.visus_mata_kanan',
                'skrining_siswa_sd.visus_mata_kiri',
                'skrining_siswa_sd.kacamata',
                'skrining_siswa_sd.kebugaran_jasmani',
                'skrining_siswa_sd.gigi_karies',
                'skrining_siswa_sd.hasil_gds',
                'skrining_siswa_sd.pemeriksaan_hb',
                'skrining_siswa_sd.imunisasi_bcg as status_imunisasi',
                'skrining_siswa_sd.kebugaran_jantung as kesimpulan',
                'skrining_siswa_sd.kebugaran_jantung as tindak_lanjut',
                'skrining_siswa_sd.kebugaran_jantung as status_skrining',
                'skrining_siswa_sd.sering_bangun_sd',
                'skrining_siswa_sd.sering_haus_sekolah',
                'skrining_siswa_sd.sering_lapar',
                'skrining_siswa_sd.berat_turun_sekolah',
                'skrining_siswa_sd.sering_ngompol_sekolah',
                'skrining_siswa_sd.riwayat_dm_sd',
                'skrining_siswa_sd.gejala_cemas_khawatir',
                'skrining_siswa_sd.gejala_cemas_berfikir_lebih',
                'skrining_siswa_sd.gejala_cemas_sulit_konsentrasi',
                'skrining_siswa_sd.depresi_anak_sedih',
                'skrining_siswa_sd.depresi_anak_tidaksuka',
                'skrining_siswa_sd.depresi_anak_capek',
                'skrining_siswa_sd.menstruasi',
                'skrining_siswa_sd.haid_pertama',
                'skrining_siswa_sd.keputihan',
                'skrining_siswa_sd.gatal_kemaluan_puteri',
                'skrining_siswa_sd.gatal_kemaluan_putra',
                'skrining_siswa_sd.nyeri_bak_bab',
                'skrining_siswa_sd.luka_penis_dubur',
                'skrining_siswa_sd.malaria_gejala',
                'skrining_siswa_sd.malaria_sakit',
                'skrining_siswa_sd.malaria_tempat',
                'skrining_siswa_sd.aktivitas_fisik_jumlah',
                'skrining_siswa_sd.aktifitas_fisik_waktu',
                'skrining_siswa_sd.kebugaran_tulang',
                'skrining_siswa_sd.kebugaran_jantung',
                'skrining_siswa_sd.kebugaran_asma',
                'skrining_siswa_sd.kebugaran_pingsan',
                'skrining_siswa_sd.tropis_bercak',
                'skrining_siswa_sd.tropis_koreng',
                'skrining_siswa_sd.merokok_aktif_sd',
                'skrining_siswa_sd.jenis_rokok_sd',
                'skrining_siswa_sd.jumlah_rokok_sd',
                'skrining_siswa_sd.lama_rokok_sd',
                'skrining_siswa_sd.terpapar_rokok_sd',
                'skrining_siswa_sd.talasemia_1',
                'skrining_siswa_sd.talasemia_2',
                'skrining_siswa_sd.imunisasi_hepatitis',
                'skrining_siswa_sd.imunisasi_bcg',
                'skrining_siswa_sd.imunisasi_opv1',
                'skrining_siswa_sd.imunisasi_dpt1',
                'skrining_siswa_sd.imunisasi_opv2',
                'skrining_siswa_sd.imunisasi_dpt2',
                'skrining_siswa_sd.imunisasi_opv3',
                'skrining_siswa_sd.imunisasi_dpt3',
                'skrining_siswa_sd.imunisasi_opv4',
                'skrining_siswa_sd.imunisasi_ipv',
                'skrining_siswa_sd.imunisasi_campak1',
                'skrining_siswa_sd.imunisasi_dpt4',
                'skrining_siswa_sd.imunisasi_campak2',
                'skrining_siswa_sd.tes_hepatitis_sekolah',
                'skrining_siswa_sd.keluarga_hepatitis_sekolah',
                'skrining_siswa_sd.tranfusi_darah_sekolah',
                'skrining_siswa_sd.cucidarah_sekolah',
                'skrining_siswa_sd.tbc_batuk_lama',
                'skrining_siswa_sd.tbc_bb_turun',
                'skrining_siswa_sd.tbc_demam',
                'skrining_siswa_sd.tbc_lesu',
                'skrining_siswa_sd.tbc_kontak',
                'skrining_pkg.id_pkg',
                'skrining_pkg.id_petugas_entri',
                'skrining_pkg.status',
                'pegawai.nama as petugas_entry_nama'
            )
            ->where('skrining_pkg.id_pkg', $id)
            ->first();
            
        if (!$detail) {
            // Cek apakah data ada di skrining_pkg tapi bukan siswa sekolah
            $pkgData = DB::table('skrining_pkg')->where('id_pkg', $id)->first();
            
            if ($pkgData) {
                Log::error('Data dengan ID ' . $id . ' ditemukan di skrining_pkg tapi tidak ada di data_siswa_sekolah. no_rkm_medis: ' . $pkgData->no_rkm_medis);
                return response()->json([
                    'error' => 'Bukan Data Siswa Sekolah',
                    'message' => 'Data dengan ID ' . $id . ' adalah data skrining umum, bukan data siswa sekolah. Silakan gunakan tombol "Detail CKG" untuk melihat data ini.',
                    'suggestion' => 'Gunakan tombol "Detail CKG" (bukan "Detail CKG Sekolah") untuk melihat data skrining umum.'
                ], 400);
            } else {
                Log::error('Data tidak ditemukan untuk ID: ' . $id);
                return response()->json([
                    'error' => 'Data Tidak Ditemukan',
                    'message' => 'Data dengan ID ' . $id . ' tidak ditemukan di sistem.',
                    'suggestion' => 'Silakan periksa kembali ID atau refresh halaman untuk memperbarui data.'
                ], 404);
            }
        }
        
        // Log hasil query untuk debugging
        Log::info('Data detail siswa sekolah ditemukan: ', (array) $detail);
        
        // Update status menjadi "Sedang Diproses" (status = '2') ketika detail dibuka
        if ($detail->status != '1') { // Jika belum selesai
            DB::table('skrining_pkg')
                ->where('id_pkg', $id)
                ->update([
                    'status' => '2', // Sedang Diproses
                    'updated_at' => now()
                ]);
            
            // Update status di object detail untuk ditampilkan
            $detail->status = '2';
            
            Log::info('Status diupdate menjadi Sedang Diproses untuk ID: ' . $id);
        }
        
        // Ambil data pegawai aktif untuk dropdown petugas entry
        $pegawai_aktif = Pegawai::where('stts_aktif', 'Aktif')
            ->select('nik', 'nama')
            ->orderBy('nama')
            ->get();
        
        // Mengembalikan view partial untuk detail sekolah
        return view('ilp.partials.detail_ckg_sekolah', compact('detail', 'pegawai_aktif'));
    }

    /**
     * Memperbarui petugas entry untuk siswa sekolah
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePetugasEntrySekolah(Request $request)
    {
        $id = $request->input('id');
        $id_petugas_entri = $request->input('id_petugas_entri');
        
        try {
            // Update petugas entry di tabel skrining_pkg
            $updated = DB::table('skrining_pkg')
                ->where('id_pkg', $id)
                ->update([
                    'id_petugas_entri' => $id_petugas_entri,
                    'updated_at' => now()
                ]);
                
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Petugas entry berhasil diperbarui'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan atau tidak ada perubahan'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating petugas entry: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui petugas entry'
            ]);
        }
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
                'error' => 'Data CKG Tidak Ditemukan',
                'message' => 'Data CKG dengan ID ' . $id . ' tidak ditemukan di sistem.',
                'suggestion' => 'Silakan periksa kembali ID CKG atau refresh halaman untuk memperbarui data.'
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
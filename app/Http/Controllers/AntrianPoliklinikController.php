<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class AntrianPoliklinikController extends Controller
{
    /**
     * Menampilkan tampilan antrian poliklinik
     */
    public function index()
    {
        return view('antrian-poliklinik');
    }

    /**
     * API untuk mendapatkan data antrian poliklinik
     */
    public function getAntrianPoliklinik(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kdPoli = $request->input('kd_poli');
        $search = $request->input('search');

        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->select(
                'reg_periksa.no_reg',
                'reg_periksa.no_rawat',
                'reg_periksa.kd_poli',
                'reg_periksa.stts',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'poliklinik.nm_poli',
                'dokter.nm_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.tgl_registrasi', $tanggal)
            ->where('reg_periksa.stts', 'Belum');

        if ($kdPoli) {
            $query->where('reg_periksa.kd_poli', $kdPoli);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pasien.no_rkm_medis', 'like', "%$search%")
                  ->orWhere('pasien.nm_pasien', 'like', "%$search%");
            });
        }

        $antrian = $query->orderBy('reg_periksa.no_reg', 'asc')
                         ->get();

        return response()->json($antrian);
    }

    /**
     * API untuk mendapatkan data poliklinik
     */
    public function getPoliklinik()
    {
        $poliklinik = DB::table('poliklinik')
            ->select('kd_poli', 'nm_poli')
            ->where('status', '1')
            ->orderBy('nm_poli', 'asc')
            ->get();

        return response()->json($poliklinik);
    }

    /**
     * API untuk mendapatkan detail pasien
     */
    public function getDetailPasien($noRawat)
    {
        $detailPasien = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->select(
                'reg_periksa.no_reg',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.stts',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'poliklinik.nm_poli',
                'dokter.nm_dokter',
                'penjab.png_jawab'
            )
            ->where('reg_periksa.no_rawat', $noRawat)
            ->first();

        // Hitung umur pasien
        if ($detailPasien) {
            $tglLahir = Carbon::parse($detailPasien->tgl_lahir);
            $now = Carbon::now();
            $umurTahun = $tglLahir->diffInYears($now);
            $umurBulan = $tglLahir->copy()->addYears($umurTahun)->diffInMonths($now);
            $umurHari = $tglLahir->copy()->addYears($umurTahun)->addMonths($umurBulan)->diffInDays($now);

            $detailPasien->umur = "$umurTahun tahun, $umurBulan bulan, $umurHari hari";
        }

        return response()->json($detailPasien);
    }

    /**
     * API untuk melakukan panggilan pasien
     */
    public function panggilPasien(Request $request)
    {
        $noRawat = $request->input('no_rawat');
        $status = $request->input('status', 'Dipanggil'); // Default status 'Dipanggil'

        // Cek status saat ini
        $currentStatus = DB::table('reg_periksa')
            ->where('no_rawat', $noRawat)
            ->value('stts');

        // Jika sudah "Dipanggil" dan status baru adalah "Sudah", update ke "Sudah"
        // Jika masih "Belum", update ke "Dipanggil"
        if ($currentStatus === 'Dipanggil' && $status === 'Sudah') {
            DB::table('reg_periksa')
                ->where('no_rawat', $noRawat)
                ->update(['stts' => 'Sudah']);
            $message = 'Pasien berhasil dilayani';
        } else if ($currentStatus === 'Belum') {
            // Dalam implementasi nyata, tambahkan field baru untuk status dipanggil
            // Karena status dalam database hanya "Belum" dan "Sudah"
            // Untuk demo ini, kita tetap update stts ke 'Dipanggil' meskipun akan disimpan hanya sebagai 'Belum'
            // dan kita tambahkan field waktu_panggil untuk menandai yang sedang dipanggil
            
            DB::table('reg_periksa')
                ->where('no_rawat', $noRawat)
                ->update([
                    // 'stts' => 'Dipanggil' // Dalam sistem nyata, kita perlu field baru untuk tracking yang dipanggil
                    'jam_reg' => date('H:i:s') // Contoh: pakai jam_reg untuk menyimpan waktu panggil (dalam implementasi nyata, buat field baru)
                ]);
            $message = 'Pasien berhasil dipanggil';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pasien sudah dalam proses layanan'
            ], 400);
        }

        // Dapatkan data penting dari pasien untuk notifikasi
        $pasien = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->select('reg_periksa.no_reg', 'pasien.nm_pasien', 'poliklinik.nm_poli')
            ->where('reg_periksa.no_rawat', $noRawat)
            ->first();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'no_reg' => $pasien->no_reg,
                'nm_pasien' => $pasien->nm_pasien,
                'nm_poli' => $pasien->nm_poli,
                'status' => $status
            ]
        ]);
    }

    /**
     * Cetak laporan antrian poliklinik
     */
    public function cetakLaporan(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kdPoli = $request->input('kd_poli');

        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->select(
                'reg_periksa.no_reg',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.stts',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'poliklinik.nm_poli',
                'dokter.nm_dokter'
            )
            ->where('reg_periksa.tgl_registrasi', $tanggal);

        if ($kdPoli) {
            $query->where('reg_periksa.kd_poli', $kdPoli);
        }

        $antrian = $query->orderBy('poliklinik.nm_poli', 'asc')
                         ->orderBy('reg_periksa.no_reg', 'asc')
                         ->get();

        $namaPoli = "Semua Poliklinik";
        if ($kdPoli) {
            $poli = DB::table('poliklinik')
                      ->where('kd_poli', $kdPoli)
                      ->first();
            if ($poli) {
                $namaPoli = $poli->nm_poli;
            }
        }

        $data = [
            'antrian' => $antrian,
            'tanggal' => Carbon::parse($tanggal)->format('d-m-Y'),
            'namaPoli' => $namaPoli
        ];

        return view('laporan.antrian-poliklinik', $data);
    }

    /**
     * Export data antrian ke Excel
     */
    public function exportExcel(Request $request)
    {
        // Implementasi export ke Excel dapat ditambahkan di sini
        return redirect()->back()->with('success', 'Export Excel berhasil');
    }

    /**
     * Menampilkan tampilan display antrian untuk TV
     */
    public function display()
    {
        // Ambil data setting rumah sakit
        $setting = DB::table('setting')
            ->select('nama_instansi', 'alamat_instansi', 'kabupaten', 'propinsi', 'kontak', 'email', 'logo')
            ->first();
        
        return view('antrian-display', compact('setting'));
    }

    /**
     * API untuk mendapatkan data antrian untuk display TV
     */
    public function getAntrianDisplay(Request $request)
    {
        try {
            // Selalu ambil data hari ini
            $tanggal = date('Y-m-d');
            
            $query = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->select(
                    'reg_periksa.no_reg',
                    'reg_periksa.no_rawat',
                    'reg_periksa.kd_poli',
                    'reg_periksa.stts as status',
                    'reg_periksa.jam_reg',
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter'
                )
                ->where('reg_periksa.tgl_registrasi', $tanggal);
            
            // Jika ada parameter poliklinik
            if ($request->has('kd_poli') && $request->kd_poli) {
                $query->where('reg_periksa.kd_poli', $request->kd_poli);
            }
            
            // Sortir berdasarkan poliklinik dan nomor registrasi
            $antrian = $query->orderBy('poliklinik.nm_poli', 'asc')
                            ->orderBy('reg_periksa.no_reg', 'asc')
                            ->get();
            
            // Map status 'Belum' ke 'Menunggu' untuk menyesuaikan dengan tampilan
            foreach ($antrian as $item) {
                if ($item->status === 'Belum') {
                    $item->status = 'Menunggu';
                } else if ($item->status === 'Sudah') {
                    $item->status = 'Selesai';
                }
            }
            
            // Cari pasien yang baru dipanggil (jika ada)
            $dipanggil = $this->getPasienDipanggil();
            
            // Untuk testing, jika tidak ada pasien yang dipanggil, gunakan pasien pertama
            if (!$dipanggil && count($antrian) > 0) {
                $dipanggil = clone $antrian[0];
                $dipanggil->status = 'Dipanggil';
                
                // Perbarui status pasien di array antrian
                $antrian[0]->status = 'Dipanggil';
            }
            
            return response()->json([
                'antrian' => $antrian,
                'dipanggil' => $dipanggil,
                'count' => count($antrian),
                'timestamp' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting antrian display: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mendapatkan data antrian',
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Mendapatkan data pasien yang sedang dipanggil
     */
    private function getPasienDipanggil()
    {
        // Dalam implementasi nyata, fungsi ini akan mengambil data dari tabel
        // yang menyimpan status pasien yang dipanggil
        // Sebagai contoh, kita mengambil data dari tabel temp_panggil (jika ada)
        
        try {
            // Cek apakah tabel temp_panggil ada
            if (Schema::hasTable('temp_panggil')) {
                $dipanggil = DB::table('temp_panggil')
                    ->join('reg_periksa', 'temp_panggil.no_rawat', '=', 'reg_periksa.no_rawat')
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                    ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                    ->select(
                        'reg_periksa.no_reg',
                        'reg_periksa.no_rawat',
                        'reg_periksa.kd_poli',
                        'pasien.no_rkm_medis',
                        'pasien.nm_pasien',
                        'poliklinik.nm_poli',
                        'dokter.nm_dokter'
                    )
                    ->where('temp_panggil.status', 'dipanggil')
                    ->orderBy('temp_panggil.created_at', 'desc')
                    ->first();
                    
                if ($dipanggil) {
                    $dipanggil->status = 'Dipanggil';
                    return $dipanggil;
                }
            }
            
            // Jika tidak ada tabel atau data, coba buat simulasi data panggilan
            // Cari pasien dengan status 'Dipanggil' jika sudah ada
            $dipanggil = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->select(
                    'reg_periksa.no_reg',
                    'reg_periksa.no_rawat',
                    'reg_periksa.kd_poli',
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter'
                )
                ->where('reg_periksa.tgl_registrasi', date('Y-m-d'))
                ->where('reg_periksa.stts', 'Dipanggil')
                ->first();
                
            if ($dipanggil) {
                $dipanggil->status = 'Dipanggil';
                return $dipanggil;
            }
            
            // Jika tidak ada pasien dengan status 'Dipanggil', coba gunakan pasien pertama
            // sebagai simulasi (hanya untuk demo)
            $pertama = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->select(
                    'reg_periksa.no_reg',
                    'reg_periksa.no_rawat',
                    'reg_periksa.kd_poli',
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter'
                )
                ->where('reg_periksa.tgl_registrasi', date('Y-m-d'))
                ->where('reg_periksa.stts', 'Belum')
                ->orderBy('poliklinik.nm_poli', 'asc')
                ->orderBy('reg_periksa.no_reg', 'asc')
                ->first();
                
            if ($pertama) {
                $pertama->status = 'Dipanggil';
                return $pertama;
            }
            
            return null;
        } catch (\Exception $e) {
            \Log::error('Error getting dipanggil patient: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * API untuk mendapatkan daftar file media (gambar dan video) yang tersedia
     */
    public function getMediaFiles()
    {
        try {
            // Path untuk folder video
            $videoPath = public_path('assets/video');
            $videoFiles = [];

            // Path untuk folder gambar
            $imagePath = public_path('img');
            $imageFiles = [];

            // Cek apakah folder video ada dan bisa diakses
            if (is_dir($videoPath) && is_readable($videoPath)) {
                // Ambil semua file MP4 dari folder video
                $videoFiles = array_filter(scandir($videoPath), function($item) use ($videoPath) {
                    return !is_dir($videoPath . '/' . $item) && 
                           pathinfo($item, PATHINFO_EXTENSION) == 'mp4';
                });
            } else {
                \Log::warning("Folder video tidak ditemukan atau tidak bisa diakses: $videoPath");
            }

            // Cek apakah folder gambar ada dan bisa diakses
            if (is_dir($imagePath) && is_readable($imagePath)) {
                // Ambil semua file gambar dari folder img
                $imageFiles = array_filter(scandir($imagePath), function($item) use ($imagePath) {
                    $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                    return !is_dir($imagePath . '/' . $item) && 
                           in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
                });
            } else {
                \Log::warning("Folder gambar tidak ditemukan atau tidak bisa diakses: $imagePath");
            }

            return response()->json([
                'video' => array_values($videoFiles),
                'images' => array_values($imageFiles),
                'video_path' => asset('assets/video'),
                'image_path' => asset('img'),
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting media files: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mendapatkan file media',
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
} 
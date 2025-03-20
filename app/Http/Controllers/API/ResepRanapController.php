<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\EnkripsiData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ResepRanapController extends Controller
{
    use EnkripsiData;

    public function getObatRanap($bangsal)
    {
        try {
            // Query untuk mendapatkan data obat berdasarkan bangsal
            // Menggunakan gudangbarang untuk melihat stok di bangsal tertentu
            $result = DB::table('databarang')
                ->join('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                ->where('gudangbarang.kd_bangsal', $bangsal)
                ->where('gudangbarang.stok', '>', 0)
                ->select('databarang.kode_brng as id', 'databarang.nama_brng as text')
                ->get();
                
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("Error getObatRanap: " . $e->getMessage());
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Method untuk menyimpan resep ranap
    public function postResepRanap($encryptNoRawat, Request $request)
    {
        try {
            // Dekripsi no_rawat
            $noRawat = $this->decryptData($encryptNoRawat);
            
            // Ambil data input
            $obat = $request->input('obat');
            $jumlah = $request->input('jumlah');
            $aturanPakai = $request->input('aturan_pakai');
            $dokter = $request->input('dokter');
            $kode = $request->input('kode');
            
            // Validasi data
            if (empty($obat) || empty($jumlah) || empty($aturanPakai)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Data resep tidak lengkap'
                ], 400);
            }
            
            // Buat nomor resep baru
            $tglPeresepan = date('Y-m-d');
            $jamPeresepan = date('H:i:s');
            $noResep = $this->generateNoResep();
            $depoFarmasi = $kode;
            
            // Simpan resep_obat dengan nilai default untuk field yang belum terisi
            DB::table('resep_obat')->insert([
                'no_resep' => $noResep,
                'tgl_peresepan' => $tglPeresepan,
                'jam_peresepan' => $jamPeresepan,
                'no_rawat' => $noRawat,
                'kd_dokter' => $dokter,
                'status' => 'ranap',
                'tgl_perawatan' => '0000-00-00',  // Nilai default untuk tgl_perawatan
                'jam' => '00:00:00'  // Nilai default untuk jam
            ]);
            
            // Simpan detail resep
            $berhasil = true;
            for ($i = 0; $i < count($obat); $i++) {
                if (!empty($obat[$i]) && !empty($jumlah[$i]) && !empty($aturanPakai[$i])) {
                    // Simpan jumlah obat langsung tanpa perhitungan kapasitas
                    $insert = DB::table('resep_dokter')->insert([
                        'no_resep' => $noResep,
                        'kode_brng' => $obat[$i],
                        'jml' => $jumlah[$i],
                        'aturan_pakai' => $aturanPakai[$i]
                    ]);
                    
                    if (!$insert) {
                        $berhasil = false;
                        Log::error("Gagal menyimpan detail resep untuk obat: " . $obat[$i]);
                    }
                }
            }
            
            // Tambahkan data ke tabel permintaan resep ranap
            try {
                // Cek apakah tabel permintaan_resep_ranap ada
                $tableExists = DB::getSchemaBuilder()->hasTable('permintaan_resep_ranap');
                
                if ($tableExists) {
                    DB::table('permintaan_resep_ranap')->insert([
                        'no_rawat' => $noRawat,
                        'tgl_permintaan' => $tglPeresepan,
                        'jam_permintaan' => $jamPeresepan, 
                        'no_resep' => $noResep,
                        'status' => 'Belum Terlayani',
                        'kd_bangsal' => $depoFarmasi
                    ]);
                } else {
                    Log::info("Tabel permintaan_resep_ranap tidak ditemukan, skip penyimpanan ke tabel tersebut");
                }
            } catch (\Exception $e) {
                Log::warning("Error saat menyimpan ke permintaan_resep_ranap: " . $e->getMessage() . " (Ini bukan error kritis, resep tetap tersimpan)");
                // Lanjutkan meskipun ada error, karena resep sudah tersimpan
            }
            
            // Log aktivitas
            Log::info("Resep ranap berhasil disimpan. No Resep: " . $noResep);
            
            // Ambil nama obat untuk response
            $resepDetail = DB::table('resep_dokter')
                ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                ->where('resep_dokter.no_resep', $noResep)
                ->select('resep_dokter.*', 'databarang.nama_brng')
                ->get();
            
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Resep berhasil disimpan',
                'no_resep' => $noResep,
                'tgl_peresepan' => $tglPeresepan,
                'jam_peresepan' => $jamPeresepan,
                'detail_resep' => $resepDetail
            ]);
        } catch (\Exception $e) {
            Log::error("Error saat menyimpan resep: " . $e->getMessage());
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function postResepRacikanRanap($encryptNoRawat, Request $request)
    {
        try {
            // Dekripsi no_rawat
            $noRawat = $this->decryptData($encryptNoRawat);
            
            // Ambil data input
            $namaRacikan = $request->input('nama_racikan');
            $metodeRacikan = $request->input('metode_racikan');
            $jumlahRacikan = $request->input('jumlah_racikan');
            $aturanRacikan = $request->input('aturan_racikan');
            $keteranganRacikan = $request->input('keterangan_racikan');
            $kdObat = $request->input('kd_obat');
            $p1 = $request->input('p1');
            $p2 = $request->input('p2');
            $kandungan = $request->input('kandungan');
            $jml = $request->input('jml');
            $dokter = $request->input('dokter');
            $depoFarmasi = $request->input('kode');
            
            // Validasi data
            if (empty($namaRacikan) || empty($metodeRacikan) || empty($jumlahRacikan)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Data racikan tidak lengkap'
                ], 400);
            }
            
            // Buat nomor resep baru
            $tglPeresepan = date('Y-m-d');
            $jamPeresepan = date('H:i:s');
            $noResep = $this->generateNoResep();
            
            // Simpan resep_obat
            DB::table('resep_obat')->insert([
                'no_resep' => $noResep,
                'tgl_peresepan' => $tglPeresepan,
                'jam_peresepan' => $jamPeresepan,
                'no_rawat' => $noRawat,
                'kd_dokter' => $dokter ?? session()->get('username'),
                'status' => 'ranap',
                'tgl_perawatan' => '0000-00-00',  // Nilai default untuk tgl_perawatan
                'jam' => '00:00:00'  // Nilai default untuk jam
            ]);
            
            // Simpan racikan
            $simpanRacikan = DB::table('resep_dokter_racikan')->insert([
                'no_resep' => $noResep,
                'no_racik' => 1,
                'nama_racik' => $namaRacikan,
                'kd_racik' => $metodeRacikan,
                'jml_dr' => $jumlahRacikan,
                'aturan_pakai' => $aturanRacikan,
                'keterangan' => $keteranganRacikan
            ]);
            
            // Simpan detail racikan
            $berhasil = true;
            for ($i = 0; $i < count($kdObat); $i++) {
                if (!empty($kdObat[$i])) {
                    $insert = DB::table('resep_dokter_racikan_detail')->insert([
                        'no_resep' => $noResep,
                        'no_racik' => 1,
                        'kode_brng' => $kdObat[$i],
                        'p1' => $p1[$i] ?? 1,
                        'p2' => $p2[$i] ?? 1,
                        'kandungan' => $kandungan[$i] ?? 1,
                        'jml' => $jml[$i]
                    ]);
                    
                    if (!$insert) {
                        $berhasil = false;
                        Log::error("Gagal menyimpan detail racikan untuk obat: " . $kdObat[$i]);
                    }
                }
            }
            
            // Tambahkan data ke tabel permintaan resep ranap
            try {
                // Cek apakah tabel permintaan_resep_ranap ada
                $tableExists = DB::getSchemaBuilder()->hasTable('permintaan_resep_ranap');
                
                if ($tableExists) {
                    DB::table('permintaan_resep_ranap')->insert([
                        'no_rawat' => $noRawat,
                        'tgl_permintaan' => $tglPeresepan,
                        'jam_permintaan' => $jamPeresepan, 
                        'no_resep' => $noResep,
                        'status' => 'Belum Terlayani',
                        'kd_bangsal' => $depoFarmasi
                    ]);
                } else {
                    Log::info("Tabel permintaan_resep_ranap tidak ditemukan, skip penyimpanan ke tabel tersebut");
                }
            } catch (\Exception $e) {
                Log::warning("Error saat menyimpan ke permintaan_resep_ranap: " . $e->getMessage() . " (Ini bukan error kritis, resep tetap tersimpan)");
                // Lanjutkan meskipun ada error, karena resep sudah tersimpan
            }
            
            // Log aktivitas
            Log::info("Resep racikan berhasil disimpan. No Resep: " . $noResep);
            
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Resep racikan berhasil disimpan',
                'no_resep' => $noResep
            ]);
        } catch (\Exception $e) {
            Log::error("Error saat menyimpan racikan: " . $e->getMessage());
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function hapusRacikan(Request $request)
    {
        try {
            $noResep = $request->input('no_resep');
            $noRacik = $request->input('no_racik');
            
            // Validasi data
            if (empty($noResep) || empty($noRacik)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Parameter tidak lengkap'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Hapus detail racikan
            DB::table('resep_dokter_racikan_detail')
                ->where('no_resep', $noResep)
                ->where('no_racik', $noRacik)
                ->delete();
                
            // Hapus racikan
            DB::table('resep_dokter_racikan')
                ->where('no_resep', $noResep)
                ->where('no_racik', $noRacik)
                ->delete();
                
            // Periksa apakah masih ada racikan atau resep untuk nomor resep ini
            $cekRacikan = DB::table('resep_dokter_racikan')
                ->where('no_resep', $noResep)
                ->count();
                
            $cekResep = DB::table('resep_dokter')
                ->where('no_resep', $noResep)
                ->count();
                
            // Jika tidak ada resep atau racikan lagi, hapus entri resep_obat
            if ($cekRacikan == 0 && $cekResep == 0) {
                DB::table('resep_obat')
                    ->where('no_resep', $noResep)
                    ->delete();
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Racikan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saat menghapus racikan: " . $e->getMessage());
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Method untuk mendapatkan data resep untuk di-copy
    public function getCopyResep($noResep, Request $request)
    {
        try {
            // Log untuk debugging
            Log::info("Mengambil data resep untuk copy dengan no_resep: " . $noResep);
            
            // Validasi nomor resep
            if (empty($noResep)) {
                Log::warning("Nomor resep kosong");
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Nomor resep tidak boleh kosong'
                ], 400);
            }
            
            // Periksa apakah resep ada
            $cekResep = DB::table('resep_obat')
                ->where('no_resep', $noResep)
                ->first();
                
            if (!$cekResep) {
                Log::warning("Resep dengan nomor {$noResep} tidak ditemukan");
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Resep tidak ditemukan'
                ], 404);
            }
            
            // Ambil data resep obat non-racikan
            $resepObat = DB::table('resep_dokter')
                ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                ->where('resep_dokter.no_resep', $noResep)
                ->select(
                    'resep_dokter.kode_brng',
                    'databarang.nama_brng',
                    'resep_dokter.jml',
                    DB::raw("COALESCE(resep_dokter.aturan_pakai, '') as aturan_pakai")
                )
                ->get();
                
            Log::info("Jumlah data resep obat: " . count($resepObat));
                
            // Jika tidak ada data resep obat, cek apakah ada racikan
            if ($resepObat->isEmpty()) {
                Log::info("Tidak ada data resep obat, memeriksa racikan");
                
                // Ambil data racikan
                $racikan = DB::table('resep_dokter_racikan')
                    ->where('no_resep', $noResep)
                    ->first();
                    
                if ($racikan) {
                    Log::info("Racikan ditemukan, mengambil detail racikan");
                    
                    // Ambil detail racikan
                    $detailRacikan = DB::table('resep_dokter_racikan_detail')
                        ->join('databarang', 'resep_dokter_racikan_detail.kode_brng', '=', 'databarang.kode_brng')
                        ->where('resep_dokter_racikan_detail.no_resep', $noResep)
                        ->select(
                            'resep_dokter_racikan_detail.kode_brng',
                            'databarang.nama_brng',
                            'resep_dokter_racikan_detail.jml',
                            DB::raw("COALESCE('{$racikan->aturan_pakai}', 'Sesuai Racikan') as aturan_pakai")
                        )
                        ->get();
                        
                    Log::info("Jumlah data detail racikan: " . count($detailRacikan));
                    
                    if ($detailRacikan->isEmpty()) {
                        Log::warning("Detail racikan kosong");
                        return response()->json([
                            'status' => 'gagal',
                            'message' => 'Detail racikan tidak ditemukan'
                        ], 404);
                    }
                        
                    return response()->json($detailRacikan);
                }
                
                // Jika tidak ada data sama sekali
                Log::warning("Tidak ada data resep obat maupun racikan");
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Tidak ada data resep yang dapat disalin'
                ], 404);
            }
            
            Log::info("Berhasil mengambil data resep obat");
            return response()->json($resepObat);
        } catch (\Exception $e) {
            Log::error("Error saat mengambil data copy resep: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            return response()->json([
                'status' => 'gagal',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Method untuk generate nomor resep
    private function generateNoResep()
    {
        $tanggal = date('Ymd');
        $query = DB::select("SELECT ifnull(MAX(CONVERT(RIGHT(no_resep,6),signed)),0) as max_id FROM resep_obat WHERE LEFT(no_resep,8) = ?", [$tanggal]);
        $lastId = $query[0]->max_id;
        $nextId = $lastId + 1;
        
        return $tanggal . sprintf('%06d', $nextId);
    }
    
    /**
     * Mendapatkan riwayat peresepan sesuai nomor rawat selama periode perawatan
     */
    public function getRiwayatPeresepan($encryptNoRawat)
    {
        try {
            Log::info("Menerima request riwayat peresepan dengan parameter: " . $encryptNoRawat);
            
            // Cek mode pengujian terlebih dahulu sebelum dekripsi
            $isTestMode = request()->has('test_mode') && request()->query('test_mode') == '1';
            $testNoRawat = request()->query('test_no_rawat');
            
            if ($isTestMode && $testNoRawat) {
                $noRawat = $testNoRawat;
                Log::info("Mode pengujian aktif, menggunakan nomor rawat: {$noRawat}");
            } else {
                // Dekripsi no_rawat jika bukan mode tes
                try {
                    $noRawat = $this->decryptData($encryptNoRawat);
                    Log::info("Berhasil mendekripsi no_rawat: {$noRawat} dari token: {$encryptNoRawat}");
                } catch (\Exception $e) {
                    Log::error("Gagal mendekripsi no_rawat: " . $e->getMessage());
                    Log::error("Token yang diterima: {$encryptNoRawat}");
                    return response()->json([
                        'status' => 'gagal',
                        'pesan' => 'Gagal mendekripsi nomor rawat: ' . $e->getMessage()
                    ], 400);
                }
            }
            
            // Log untuk debugging
            Log::info("Mengambil riwayat peresepan untuk no_rawat: " . $noRawat);
            
            // Verifikasi ulang no_rawat pada tabel reg_periksa untuk memastikan data valid
            $cekNoRawat = DB::table('reg_periksa')
                ->where('no_rawat', '=', trim($noRawat))
                ->first();
                
            if (!$cekNoRawat) {
                Log::warning("Data registrasi tidak ditemukan untuk no_rawat: " . $noRawat);
                
                // Coba ambil beberapa contoh no_rawat yang valid dari database untuk debugging
                $sampleRawat = DB::table('reg_periksa')
                    ->select('no_rawat')
                    ->limit(5)
                    ->get();
                    
                Log::info("Contoh nomor rawat yang valid di database: " . json_encode($sampleRawat));
                
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Data registrasi tidak ditemukan',
                    'no_rawat_dicari' => $noRawat,
                    'sample_valid' => $sampleRawat,
                    'is_test_mode' => $isTestMode,
                    'test_no_rawat' => $testNoRawat
                ], 404);
            }
            
            Log::info("Nomor rawat terverifikasi: " . $noRawat);
            
            // Ambil data resep obat berdasarkan nomor rawat (strict matching)
            // Pastikan tidak ada whitespace dengan menggunakan trim
            $noRawatTrim = trim($noRawat);
            $dataResep = DB::table('resep_obat')
                ->where('no_rawat', '=', $noRawatTrim) // Menggunakan perbandingan standar
                ->select('no_resep', 'tgl_peresepan', 'jam_peresepan', 'no_rawat')
                ->orderBy('tgl_peresepan', 'desc')
                ->orderBy('jam_peresepan', 'desc')
                ->get();
            
            Log::info("Query menggunakan no_rawat (trimmed): " . $noRawatTrim);
            Log::info("Jumlah resep yang ditemukan: " . count($dataResep));
            
            // Log nomor-nomor resep dan no_rawat yang ditemukan untuk verifikasi
            foreach ($dataResep as $index => $resep) {
                Log::info("Resep #{$index}: no_resep={$resep->no_resep}, no_rawat={$resep->no_rawat}, tanggal={$resep->tgl_peresepan}");
            }
                
            $riwayatResep = [];
            
            foreach ($dataResep as $resep) {
                // Double-check no_rawat untuk memastikan hanya resep untuk pasien ini yang diambil
                if (trim($resep->no_rawat) !== $noRawatTrim) {
                    Log::warning("Skipping resep {$resep->no_resep} karena no_rawat tidak cocok");
                    Log::warning("Expected: '{$noRawatTrim}', Actual: '{$resep->no_rawat}'");
                    continue;
                }
                
                // Cek apakah ada detail resep non-racikan
                $detailResep = DB::table('resep_dokter')
                    ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                    ->where('resep_dokter.no_resep', $resep->no_resep)
                    ->select(
                        'databarang.nama_brng',
                        'resep_dokter.jml',
                        'resep_dokter.aturan_pakai'
                    )
                    ->get();
                    
                // Cek apakah ada detail resep racikan
                $detailRacikan = [];
                $racikan = DB::table('resep_dokter_racikan')
                    ->where('no_resep', $resep->no_resep)
                    ->get();
                    
                foreach ($racikan as $r) {
                    $detailObatRacikan = DB::table('resep_dokter_racikan_detail')
                        ->join('databarang', 'resep_dokter_racikan_detail.kode_brng', '=', 'databarang.kode_brng')
                        ->where('resep_dokter_racikan_detail.no_resep', $resep->no_resep)
                        ->where('resep_dokter_racikan_detail.no_racik', $r->no_racik)
                        ->select(
                            'databarang.nama_brng',
                            'resep_dokter_racikan_detail.jml',
                            DB::raw("'{$r->aturan_pakai}' as aturan_pakai"),
                            DB::raw("'{$r->nama_racik}' as nama_racik")
                        )
                        ->get();
                        
                    $detailRacikan = array_merge($detailRacikan, $detailObatRacikan->toArray());
                }
                
                // Gabungkan detail resep
                $detailLengkap = [];
                
                // Tambahkan detail obat non-racikan
                foreach ($detailResep as $detail) {
                    $detailLengkap[] = [
                        'nama_brng' => $detail->nama_brng,
                        'jml' => $detail->jml,
                        'aturan_pakai' => $detail->aturan_pakai,
                        'racikan' => false
                    ];
                }
                
                // Tambahkan detail obat racikan
                foreach ($detailRacikan as $detail) {
                    $detailLengkap[] = [
                        'nama_brng' => $detail->nama_brng,
                        'jml' => $detail->jml,
                        'aturan_pakai' => $detail->aturan_pakai,
                        'racikan' => true,
                        'nama_racik' => $detail->nama_racik
                    ];
                }
                
                // Tambahkan ke riwayat jika ada detail resep
                if (count($detailLengkap) > 0) {
                    $riwayatResep[] = [
                        'no_resep' => $resep->no_resep,
                        'tgl_peresepan' => $resep->tgl_peresepan,
                        'jam_peresepan' => $resep->jam_peresepan,
                        'detail' => $detailLengkap
                    ];
                }
            }
            
            Log::info("Jumlah final riwayat resep yang dikembalikan: " . count($riwayatResep));
            
            return response()->json([
                'status' => 'sukses',
                'data' => $riwayatResep,
                'no_rawat' => $noRawat
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error saat mengambil riwayat peresepan: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 
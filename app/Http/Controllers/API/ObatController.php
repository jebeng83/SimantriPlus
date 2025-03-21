<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\EnkripsiData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ObatController extends Controller
{
    use EnkripsiData;
    
    /**
     * Mendapatkan detail obat berdasarkan kode obat
     */
    public function getObat($kodeObat, Request $request)
    {
        try {
            // Ambil parameter
            $status = $request->get('status');
            $kodeBangsal = $request->get('kode');
            
            // Query untuk mendapatkan detail obat
            $data = DB::table('databarang')
                ->where('kode_brng', $kodeObat)
                ->first();
                
            // Tambahkan informasi stok
            if ($data) {
                if ($status === 'ranap') {
                    // Ambil stok untuk rawat inap (gudang)
                    $stok = DB::table('gudangbarang')
                        ->where('kode_brng', $kodeObat)
                        ->where('kd_bangsal', $kodeBangsal)
                        ->first();
                        
                    $data->stok_akhir = $stok ? $stok->stok : 0;
                } else {
                    // Default stok
                    $data->stok_akhir = 0;
                }
                
                return response()->json($data);
            } else {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Obat tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error("Error saat mengambil obat: " . $e->getMessage());
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menghapus obat dari resep
     */
    public function hapusObat($noResep, $kodeObat, Request $request)
    {
        try {
            // Log input untuk debugging
            Log::info("hapusObat dipanggil dengan noResep: " . $noResep . ", kodeObat: " . $kodeObat);
            
            // Convert parameter jika ada strip atau karakter khusus
            $noResep = trim($noResep);
            $kodeObat = trim($kodeObat);
            
            // Tulis log untuk debugging yang lebih detail
            Log::info("Setelah trim - noResep: '{$noResep}', kodeObat: '{$kodeObat}'");
            
            // Cek apakah resep ada
            $resepInfo = DB::table('resep_obat')
                ->where('no_resep', $noResep)
                ->first();
                
            if (!$resepInfo) {
                Log::error("Resep dengan no_resep={$noResep} tidak ditemukan");
                
                // Coba cari dengan metode pencarian yang lebih fleksibel
                $alternatifResep = DB::table('resep_obat')
                    ->where('no_resep', 'LIKE', "%{$noResep}%")
                    ->first();
                    
                if ($alternatifResep) {
                    Log::info("Menemukan alternatif resep: no_resep={$alternatifResep->no_resep}");
                    $noResep = $alternatifResep->no_resep;
                    $resepInfo = $alternatifResep;
                } else {
                    return response()->json([
                        'status' => 'gagal',
                        'pesan' => 'Resep tidak ditemukan'
                    ], 404);
                }
            }
            
            // Periksa apakah resep sudah divalidasi
            if ($resepInfo && $resepInfo->tgl_perawatan != '0000-00-00' && $resepInfo->tgl_perawatan != null) {
                Log::warning("Resep dengan no_resep={$noResep} sudah tervalidasi, tgl_perawatan={$resepInfo->tgl_perawatan}");
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Resep sudah tervalidasi, silahkan hubungi farmasi untuk menghapus obat'
                ], 400);
            }
            
            // Periksa apakah obat ada
            $obat = DB::table('resep_dokter')
                ->where('no_resep', $noResep)
                ->where('kode_brng', $kodeObat)
                ->first();
                
            if (!$obat) {
                Log::warning("Obat tidak ditemukan dengan pencarian exact match, mencoba dengan alternatif");
                
                // Coba mencari dengan metode pencarian yang lebih fleksibel (case-insensitive)
                $alternatifObat = DB::table('resep_dokter')
                    ->whereRaw('LOWER(no_resep) = ?', [strtolower($noResep)])
                    ->whereRaw('LOWER(kode_brng) = ?', [strtolower($kodeObat)])
                    ->first();
                
                if ($alternatifObat) {
                    Log::info("Menemukan alternatif obat dengan case-insensitive: no_resep={$alternatifObat->no_resep}, kode_brng={$alternatifObat->kode_brng}");
                    $noResep = $alternatifObat->no_resep;
                    $kodeObat = $alternatifObat->kode_brng;
                } else {
                    // Coba cari dengan pattern matching
                    $patternObat = DB::table('resep_dokter')
                        ->where('no_resep', 'LIKE', "%{$noResep}%")
                        ->where('kode_brng', 'LIKE', "%{$kodeObat}%")
                        ->first();
                        
                    if ($patternObat) {
                        Log::info("Menemukan obat dengan pattern matching: no_resep={$patternObat->no_resep}, kode_brng={$patternObat->kode_brng}");
                        $noResep = $patternObat->no_resep;
                        $kodeObat = $patternObat->kode_brng;
                    } else {
                        // Coba cari semua obat dalam resep ini untuk debugging
                        $allObatInResep = DB::table('resep_dokter')
                            ->where('no_resep', $noResep)
                            ->get();
                            
                        Log::info("Semua obat dalam resep {$noResep}: " . json_encode($allObatInResep));
                        
                        Log::error("Obat tidak ditemukan setelah mencoba semua metode pencarian");
                        return response()->json([
                            'status' => 'gagal',
                            'pesan' => 'Obat tidak ditemukan dalam resep'
                        ], 404);
                    }
                }
            }
            
            // Hapus obat
            DB::beginTransaction();
            try {
                Log::info("Menghapus obat dari resep_dokter: no_resep={$noResep}, kode_brng={$kodeObat}");
                
                // Cek apakah obat sudah digunakan dalam pemberian obat
                // Tabel detail_pemberian_obat tidak memiliki kolom no_resep, tapi memiliki no_rawat dan kode_brng
                if ($resepInfo && isset($resepInfo->no_rawat)) {
                    $relatedRecords = DB::table('detail_pemberian_obat')
                        ->where('no_rawat', $resepInfo->no_rawat)
                        ->where('kode_brng', $kodeObat)
                        ->count();
                        
                    if ($relatedRecords > 0) {
                        Log::warning("Obat sudah digunakan dalam pemberian obat: no_rawat={$resepInfo->no_rawat}, kode_brng={$kodeObat}, jumlah={$relatedRecords}");
                        // Kita tetap lanjutkan penghapusan karena ini hanya peringatan
                    }
                }
                
                $result = DB::table('resep_dokter')
                    ->where('no_resep', $noResep)
                    ->where('kode_brng', $kodeObat)
                    ->delete();
                
                Log::info("Hasil penghapusan: {$result} baris terpengaruh");
                
                if ($result == 0) {
                    // Jika tidak ada baris yang terpengaruh, coba sekali lagi dengan pencarian case insensitive
                    Log::warning("Penghapusan pertama gagal, mencoba dengan case-insensitive");
                    $result = DB::table('resep_dokter')
                        ->whereRaw('LOWER(no_resep) = ?', [strtolower($noResep)])
                        ->whereRaw('LOWER(kode_brng) = ?', [strtolower($kodeObat)])
                        ->delete();
                        
                    Log::info("Hasil penghapusan kedua: {$result} baris terpengaruh");
                    
                    if ($result == 0) {
                        Log::error("Gagal menghapus obat dari database setelah mencoba semua metode");
                        throw new \Exception("Gagal menghapus obat dari database. Tidak ada baris yang terpengaruh.");
                    }
                }
                    
                // Periksa apakah masih ada obat untuk resep ini
                $obatTersisa = DB::table('resep_dokter')
                    ->where('no_resep', $noResep)
                    ->count();
                
                Log::info("Jumlah obat tersisa dalam resep: {$obatTersisa}");
                    
                // Jika tidak ada obat tersisa, hapus juga entry di resep_obat
                if ($obatTersisa === 0) {
                    $cekRacikan = DB::table('resep_dokter_racikan')
                        ->where('no_resep', $noResep)
                        ->count();
                    
                    Log::info("Jumlah racikan tersisa dalam resep: {$cekRacikan}");
                    
                    if ($cekRacikan === 0) {
                        Log::info("Menghapus resep_obat dengan no_resep: {$noResep}");
                        $deleteResep = DB::table('resep_obat')
                            ->where('no_resep', $noResep)
                            ->delete();
                        Log::info("Hasil penghapusan resep_obat: {$deleteResep} baris terpengaruh");
                    } else {
                        Log::info("Resep masih memiliki racikan, jadi resep_obat tidak dihapus");
                    }
                }
                
                DB::commit();
                
                Log::info("Transaksi commit berhasil, obat telah dihapus untuk no_resep: {$noResep} dan kode_brng: {$kodeObat}");
                return response()->json([
                    'status' => 'sukses',
                    'pesan' => 'Obat berhasil dihapus'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error saat transaksi database: " . $e->getMessage());
                throw $e; // Re-throw agar ditangkap oleh catch berikutnya
            }
        } catch (\Exception $e) {
            Log::error("Error lengkap saat menghapus obat: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan saat menghapus obat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mencari kode obat berdasarkan nama obat
     */
    public function cariKodeObat(Request $request)
    {
        try {
            // Validasi input
            if (!$request->has('nama_obat') || !is_array($request->input('nama_obat'))) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Parameter nama_obat diperlukan dan harus berupa array'
                ], 400);
            }
            
            $namaObat = $request->input('nama_obat');
            $hasilPencarian = [];
            
            Log::info("Mencari kode obat untuk " . count($namaObat) . " nama obat");
            
            // Cari kode untuk setiap nama obat
            foreach ($namaObat as $index => $nama) {
                // Trim nama obat untuk menghindari masalah whitespace
                $nama = trim($nama);
                
                // Periksa jika nama kosong
                if (empty($nama)) {
                    Log::warning("Nama obat pada indeks $index kosong");
                    continue;
                }
                
                // Cari obat dengan nama yang sama atau mirip
                $obat = DB::table('databarang')
                    ->where('nama_brng', 'LIKE', $nama)
                    ->first();
                
                if (!$obat) {
                    // Coba pencarian dengan substring
                    $obat = DB::table('databarang')
                        ->whereRaw('LOWER(nama_brng) LIKE ?', ['%' . strtolower($nama) . '%'])
                        ->first();
                }
                
                if ($obat) {
                    Log::info("Obat ditemukan: {$nama} -> {$obat->kode_brng}");
                    $hasilPencarian[] = $obat->kode_brng;
                } else {
                    Log::warning("Obat tidak ditemukan: {$nama}");
                    // Gunakan nama obat sebagai fallback jika tidak ditemukan
                    $hasilPencarian[] = "NOT_FOUND_" . $index;
                }
            }
            
            // Periksa apakah ada obat yang ditemukan
            if (empty($hasilPencarian)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Tidak ada obat yang ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Berhasil mencari kode obat',
                'data' => $hasilPencarian
            ]);
        } catch (\Exception $e) {
            Log::error("Error saat mencari kode obat: " . $e->getMessage());
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 
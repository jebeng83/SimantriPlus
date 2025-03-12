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
            
            // Simpan resep_obat
            DB::table('resep_obat')->insert([
                'no_resep' => $noResep,
                'tgl_peresepan' => $tglPeresepan,
                'jam_peresepan' => $jamPeresepan,
                'no_rawat' => $noRawat,
                'kd_dokter' => $dokter,
                'status' => 'ranap',
                'tgl_perawatan' => $tglPeresepan,
                'jam' => $jamPeresepan
            ]);
            
            // Simpan detail resep
            for ($i = 0; $i < count($obat); $i++) {
                if (!empty($obat[$i]) && !empty($jumlah[$i]) && !empty($aturanPakai[$i])) {
                    DB::table('resep_dokter')->insert([
                        'no_resep' => $noResep,
                        'kode_brng' => $obat[$i],
                        'jml' => $jumlah[$i],
                        'aturan_pakai' => $aturanPakai[$i]
                    ]);
                }
            }
            
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
                'tgl_perawatan' => $tglPeresepan,
                'jam' => $jamPeresepan
            ]);
            
            // Simpan racikan
            DB::table('resep_dokter_racikan')->insert([
                'no_resep' => $noResep,
                'no_racik' => 1,
                'nama_racik' => $namaRacikan,
                'kd_racik' => $metodeRacikan,
                'jml_dr' => $jumlahRacikan,
                'aturan_pakai' => $aturanRacikan,
                'keterangan' => $keteranganRacikan
            ]);
            
            // Simpan detail racikan
            for ($i = 0; $i < count($kdObat); $i++) {
                if (!empty($kdObat[$i])) {
                    DB::table('resep_dokter_racikan_detail')->insert([
                        'no_resep' => $noResep,
                        'no_racik' => 1,
                        'kode_brng' => $kdObat[$i],
                        'p1' => $p1[$i] ?? 1,
                        'p2' => $p2[$i] ?? 1,
                        'kandungan' => $kandungan[$i] ?? 1,
                        'jml' => $jml[$i]
                    ]);
                }
            }
            
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
} 
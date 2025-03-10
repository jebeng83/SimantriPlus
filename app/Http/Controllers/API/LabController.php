<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class LabController extends Controller
{
    use EnkripsiData;

    /**
     * Helper untuk mendekode no_rawat dengan aman
     * 
     * @param string $encodedValue
     * @return string
     */
    private function safeDecodeNoRawat($encodedValue)
    {
        try {
            // Coba dekripsi dengan metode standar
            $decodedValue = $this->decryptData($encodedValue);
            
            \Illuminate\Support\Facades\Log::info('safeDecodeNoRawat - Dekripsi standar berhasil', [
                'encoded' => $encodedValue,
                'decoded' => $decodedValue
            ]);
            
            return $decodedValue;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal mendekripsi no_rawat dengan metode standar: ' . $e->getMessage(), [
                'encoded_value' => $encodedValue
            ]);
            
            // Jika gagal, coba dengan metode alternatif (base64 decode)
            try {
                $base64Decoded = base64_decode($encodedValue);
                
                \Illuminate\Support\Facades\Log::info('safeDecodeNoRawat - Dekripsi base64 berhasil', [
                    'encoded' => $encodedValue,
                    'decoded' => $base64Decoded
                ]);
                
                return $base64Decoded;
            } catch (\Exception $e2) {
                \Illuminate\Support\Facades\Log::warning('Gagal mendekripsi no_rawat dengan base64: ' . $e2->getMessage());
            }
            
            // Jika semua metode gagal, coba cari di database berdasarkan pola tertentu
            try {
                $possibleDate = date('Y/m/d');
                $cekRawat = DB::table('reg_periksa')
                    ->where('no_rawat', 'like', $possibleDate . '%')
                    ->orderBy('jam_reg', 'desc')
                    ->first();
                
                if ($cekRawat) {
                    \Illuminate\Support\Facades\Log::info('safeDecodeNoRawat - Alternatif query berhasil', [
                        'encoded' => $encodedValue,
                        'found_no_rawat' => $cekRawat->no_rawat
                    ]);
                    
                    return $cekRawat->no_rawat;
                }
            } catch (\Exception $e3) {
                \Illuminate\Support\Facades\Log::warning('Gagal mencari alternatif no_rawat: ' . $e3->getMessage());
            }
            
            // Jika semua metode gagal, kembalikan nilai asli
            \Illuminate\Support\Facades\Log::warning('Mengembalikan nilai no_rawat asli karena semua metode dekripsi gagal');
            return $encodedValue;
        }
    }

    public function getPemeriksaanLab($noRawat)
    {
        $decodedNoRawat = $this->safeDecodeNoRawat($noRawat);
        
        try{
            $data = DB::table('detail_periksa_lab')
                    ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template')
                    ->where('detail_periksa_lab.no_rawat', $decodedNoRawat)
                    ->select('template_laboratorium.Pemeriksaan', 'detail_periksa_lab.nilai')
                    ->get();

            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Data pemeriksaan lab berhasil diambil',
                'data' => $data
            ]);
        }catch(\Illuminate\Database\QueryException $ex){
            \Illuminate\Support\Facades\Log::error('Error di getPemeriksaanLab: ' . $ex->getMessage(), [
                'no_rawat_original' => $noRawat,
                'decoded' => $decodedNoRawat
            ]);
            return response()->json([
                'status' => 'gagal',
                'pesan' => $ex->getMessage()
            ]);
        }
    }

    public function getPerawatanLab(Request $request)
    {
        $q = $request->get('q');
        $que = '%'.$q.'%';
        $obat = DB::table('jns_perawatan_lab')
                    ->where('status', '1')
                    ->where(function($query) use ($que) {
                        $query->where('kd_jenis_prw', 'like', $que)
                              ->orWhere('nm_perawatan', 'like', $que);
                    })
                    ->selectRaw('kd_jenis_prw AS id, nm_perawatan AS text')
                    ->get();
        return response()->json($obat, 200);
    }

    public function postPermintaanLab(Request $request, $noRawat)
    {
        $input = $request->all();
        $klinis = $input['klinis'] ?? '-';
        $info = $input['info'] ?? '-';
        $jnsPemeriksaan = $input['jns_pemeriksaan'] ?? [];
        $templates = $input['templates'] ?? []; // Data template yang dipilih
        
        \Illuminate\Support\Facades\Log::info('Menerima request permintaan lab', [
            'no_rawat' => $noRawat,
            'jenis_pemeriksaan' => count($jnsPemeriksaan),
            'data_input' => $input
        ]);
        
        // Validasi input
        if (empty($jnsPemeriksaan)) {
            \Illuminate\Support\Facades\Log::warning('Permintaan lab ditolak: tidak ada jenis pemeriksaan yang dipilih');
            return response()->json([
                'status' => 'gagal', 
                'message' => 'Pilih minimal satu jenis pemeriksaan.'
            ], 200);
        }
        
        // Dekode no_rawat dengan helper method yang lebih aman
        $decodedNoRawat = $this->safeDecodeNoRawat($noRawat);
        
        \Illuminate\Support\Facades\Log::info('No Rawat yang digunakan:', [
            'enkripsi' => $noRawat,
            'hasil_dekripsi' => $decodedNoRawat
        ]);
        
        // Verifikasi keberadaan no_rawat dalam database
        $cekRawat = DB::table('reg_periksa')
            ->where('no_rawat', $decodedNoRawat)
            ->first();
            
        if (!$cekRawat) {
            \Illuminate\Support\Facades\Log::error('Data registrasi tidak ditemukan', [
                'no_rawat_original' => $noRawat,
                'no_rawat_decoded' => $decodedNoRawat
            ]);
            
            // Coba mencari data pasien dengan format tanggal hari ini
            try {
                $todayFormat = date('Y/m/d');
                $cekRawatHariIni = DB::table('reg_periksa')
                    ->where('no_rawat', 'like', $todayFormat . '%')
                    ->where('kd_dokter', session()->get('username'))
                    ->orderBy('jam_reg', 'desc')
                    ->first();
                
                if ($cekRawatHariIni) {
                    \Illuminate\Support\Facades\Log::info('Menemukan data pasien hari ini sebagai alternatif', [
                        'no_rawat_alternatif' => $cekRawatHariIni->no_rawat
                    ]);
                    $decodedNoRawat = $cekRawatHariIni->no_rawat;
                } else {
                    return response()->json([
                        'status' => 'gagal', 
                        'message' => 'Data registrasi tidak ditemukan. Hubungi administrator.'
                    ], 200);
                }
            } catch(\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal mencari data alternatif: ' . $e->getMessage());
                return response()->json([
                    'status' => 'gagal', 
                    'message' => 'Data registrasi tidak ditemukan dan tidak ada alternatif. Hubungi administrator.'
                ], 200);
            }
        }
        
        try {
            DB::beginTransaction();
            // Buat nomor permintaan
            $getNumber = DB::table('permintaan_lab')
                ->where('tgl_permintaan', date('Y-m-d'))
                ->selectRaw('ifnull(MAX(CONVERT(RIGHT(noorder,4),signed)),0) as no')
                ->first();

            $lastNumber = isset($getNumber->no) ? substr($getNumber->no, 0, 4) : 0;
            $getNextNumber = sprintf('%04s', ($lastNumber + 1));
            $noOrder = 'PL'.date('Ymd').$getNextNumber;
            
            \Illuminate\Support\Facades\Log::info('Nomor Order dibuat:', [
                'noorder' => $noOrder
            ]);

            // Simpan permintaan lab
            DB::table('permintaan_lab')
                ->insert([
                    'noorder' => $noOrder,
                    'no_rawat' => $decodedNoRawat,
                    'tgl_permintaan' => date('Y-m-d'),
                    'jam_permintaan' => date('H:i:s'),
                    'dokter_perujuk' => session()->get('username'),
                    'diagnosa_klinis' => $klinis,
                    'informasi_tambahan' => $info,
                    'status' => 'ralan'
                ]);
            
            \Illuminate\Support\Facades\Log::info('Berhasil menyimpan permintaan lab', [
                'noorder' => $noOrder,
                'no_rawat' => $decodedNoRawat
            ]);

            // Simpan jenis pemeriksaan
            foreach($jnsPemeriksaan as $pemeriksaan) {
                DB::table('permintaan_pemeriksaan_lab')
                    ->insert([
                        'noorder' => $noOrder,
                        'kd_jenis_prw' => $pemeriksaan,
                        'stts_bayar' => 'Belum'
                    ]);
                    
                \Illuminate\Support\Facades\Log::info('Jenis pemeriksaan disimpan:', [
                    'noorder' => $noOrder,
                    'kd_jenis_prw' => $pemeriksaan
                ]);
            }
            
            // Simpan detail template yang dipilih
            if (!empty($templates)) {
                foreach($templates as $template) {
                    try {
                        DB::table('permintaan_detail_permintaan_lab')
                            ->insert([
                                'noorder' => $noOrder,
                                'kd_jenis_prw' => $template['kd_jenis_prw'],
                                'id_template' => $template['id_template'],
                                'stts_bayar' => 'Belum'
                            ]);
                        
                        \Illuminate\Support\Facades\Log::info('Template pemeriksaan disimpan:', [
                            'noorder' => $noOrder,
                            'kd_jenis_prw' => $template['kd_jenis_prw'],
                            'id_template' => $template['id_template']
                        ]);
                    } catch (\Exception $templateError) {
                        \Illuminate\Support\Facades\Log::warning('Gagal menyimpan template:', [
                            'error' => $templateError->getMessage(),
                            'template' => $template
                        ]);
                        // Lanjutkan meskipun ada error template
                    }
                }
            }

            DB::commit();
            \Illuminate\Support\Facades\Log::info('Transaksi permintaan lab berhasil commit');
            return response()->json(['status' => 'sukses', 'message' => 'Permintaan lab berhasil disimpan', 'noorder' => $noOrder], 200);

        } catch(\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error saat simpan permintaan lab: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'gagal', 'message' => $e->getMessage()], 200);
        }
    }

    public function hapusPermintaanLab($noOrder)
    {
        try{
            // Log aktivitas hapus permintaan lab
            \Illuminate\Support\Facades\Log::info('Mencoba hapus permintaan lab', [
                'noorder' => $noOrder
            ]);

            // Cek apakah permintaan lab ada
            $permintaanLab = DB::table('permintaan_lab')
                ->where('noorder', $noOrder)
                ->first();
                
            if (!$permintaanLab) {
                \Illuminate\Support\Facades\Log::warning('Permintaan lab tidak ditemukan saat akan dihapus', [
                    'noorder' => $noOrder
                ]);
                return response()->json([
                    'status' => 'gagal', 
                    'message' => 'Permintaan lab tidak ditemukan'
                ], 200);
            }

            DB::beginTransaction();

            // Hitung total data sebelum dihapus untuk debugging
            $detailCount = DB::table('permintaan_detail_permintaan_lab')
                ->where('noorder', $noOrder)
                ->count();
                
            $pemeriksaanCount = DB::table('permintaan_pemeriksaan_lab')
                ->where('noorder', $noOrder)
                ->count();
                
            \Illuminate\Support\Facades\Log::info('Data yang akan dihapus:', [
                'noorder' => $noOrder,
                'detail_count' => $detailCount,
                'pemeriksaan_count' => $pemeriksaanCount
            ]);

            // Hapus detail template terlebih dahulu
            try {
                DB::table('permintaan_detail_permintaan_lab')
                    ->where('noorder', $noOrder)
                    ->delete();
                
                \Illuminate\Support\Facades\Log::info('Detail template berhasil dihapus', [
                    'noorder' => $noOrder
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal menghapus detail template:', [
                    'noorder' => $noOrder,
                    'error' => $e->getMessage()
                ]);
                // Teruskan proses meskipun ada error pada tahap ini
            }

            // Hapus juga pemeriksaan terkait
            try {
                DB::table('permintaan_pemeriksaan_lab')
                    ->where('noorder', $noOrder)
                    ->delete();
                
                \Illuminate\Support\Facades\Log::info('Pemeriksaan lab berhasil dihapus', [
                    'noorder' => $noOrder
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal menghapus pemeriksaan lab:', [
                    'noorder' => $noOrder,
                    'error' => $e->getMessage()
                ]);
                // Teruskan proses meskipun ada error pada tahap ini
            }

            // Hapus permintaan lab
            DB::table('permintaan_lab')
                ->where('noorder', $noOrder)
                ->delete();
            
            \Illuminate\Support\Facades\Log::info('Permintaan lab berhasil dihapus', [
                'noorder' => $noOrder
            ]);

            DB::commit();
            return response()->json(['status' => 'sukses', 'message' => 'Permintaan lab berhasil dihapus'], 200);
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error saat hapus permintaan lab: ' . $ex->getMessage(), [
                'noorder' => $noOrder,
                'code' => $ex->getCode(),
                'trace' => $ex->getTraceAsString()
            ]);
            return response()->json(['status' => 'gagal', 'message' => $ex->getMessage()], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error Exception saat hapus permintaan lab: ' . $e->getMessage(), [
                'noorder' => $noOrder,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'gagal', 'message' => $e->getMessage()], 200);
        }
    }

    public function getTemplateByJenisPemeriksaan($kd_jenis_prw)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Request template laboratorium', [
                'kd_jenis_prw' => $kd_jenis_prw
            ]);
            
            // Periksa apakah jenis pemeriksaan valid
            $jenisPemeriksaan = DB::table('jns_perawatan_lab')
                ->where('kd_jenis_prw', $kd_jenis_prw)
                ->first();
                
            if (!$jenisPemeriksaan) {
                \Illuminate\Support\Facades\Log::warning('Jenis pemeriksaan tidak ditemukan', [
                    'kd_jenis_prw' => $kd_jenis_prw
                ]);
                
                return response()->json([
                    'status' => 'sukses',
                    'data' => []
                ]);
            }
            
            // Aktifkan query logging untuk debugging
            DB::enableQueryLog();
            
            // Ambil data template laboratorium berdasarkan jenis pemeriksaan
            $templates = DB::table('template_laboratorium as tl')
                ->where('tl.kd_jenis_prw', $kd_jenis_prw)
                ->select(
                    'tl.id_template', 
                    'tl.Pemeriksaan',
                    'tl.satuan',
                    'tl.nilai_rujukan_ld',
                    'tl.nilai_rujukan_la',
                    'tl.nilai_rujukan_pd',
                    'tl.nilai_rujukan_pa'
                )
                ->orderBy('tl.urut', 'asc')
                ->get();
                
            $queries = DB::getQueryLog();
            \Illuminate\Support\Facades\Log::info('Query template', [
                'queries' => $queries,
                'jumlah_template' => count($templates)
            ]);
            
            return response()->json([
                'status' => 'sukses',
                'data' => $templates
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengambil template lab: ' . $e->getMessage(), [
                'kd_jenis_prw' => $kd_jenis_prw,
                'error' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'gagal',
                'message' => $e->getMessage()
            ]);
        }
    }
}

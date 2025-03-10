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
        \Illuminate\Support\Facades\Log::info('LabController: Mencoba mendekode no_rawat: ' . $encodedValue);
        
        if (empty($encodedValue)) {
            return '';
        }
        
        // Coba dekripsi dengan cara biasa
        try {
            $decodedValue = $this->decryptData($encodedValue);
            \Illuminate\Support\Facades\Log::info('LabController: Hasil dekripsi layer 1: ' . $decodedValue);
            
            // Jika hasil dekripsi adalah base64 terenkode URL, dekode lagi
            if (strpos($decodedValue, '%') !== false) {
                $urlDecoded = urldecode($decodedValue);
                \Illuminate\Support\Facades\Log::info('LabController: Hasil URL decode: ' . $urlDecoded);
                
                // Coba base64 decode
                $base64Decoded = base64_decode($urlDecoded, true); // strict mode
                
                if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                    \Illuminate\Support\Facades\Log::info('LabController: No Rawat berhasil didekode (double layer): ' . $base64Decoded);
                    return $base64Decoded;
                }
            }
            
            // Validasi hasil dekripsi (harus memiliki format yang benar)
            if (preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $decodedValue)) {
                \Illuminate\Support\Facades\Log::info('LabController: No Rawat berhasil didekripsi metode standar: ' . $decodedValue);
                return $decodedValue;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('LabController: Gagal decrypt no_rawat [metode 1]: ' . $e->getMessage());
        }
        
        // Jika mengandung karakter % berarti URL encoded
        if (strpos($encodedValue, '%') !== false) {
            try {
                // Dekode URL dulu
                $urlDecoded = urldecode($encodedValue);
                \Illuminate\Support\Facades\Log::info('LabController: URL decode result: ' . $urlDecoded);
                
                // Coba base64 dekode
                $base64Decoded = base64_decode($urlDecoded, true); // strict mode
                
                if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                    \Illuminate\Support\Facades\Log::info('LabController: No Rawat berhasil didekode dengan url+base64: ' . $base64Decoded);
                    return $base64Decoded;
                }
                
                // Jika masih mengandung % setelah urldecode, coba lagi
                if (strpos($urlDecoded, '%') !== false) {
                    $doubleUrlDecoded = urldecode($urlDecoded);
                    $base64Decoded = base64_decode($doubleUrlDecoded, true);
                    
                    if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                        \Illuminate\Support\Facades\Log::info('LabController: No Rawat berhasil didekode dengan double-url+base64: ' . $base64Decoded);
                        return $base64Decoded;
                    }
                }
                
                // Cobalah menghapus %3D di akhir (=) secara manual jika ada
                if (substr($encodedValue, -3) === '%3D') {
                    $trimmedEncoded = substr($encodedValue, 0, -3);
                    $urlDecodedTrimmed = urldecode($trimmedEncoded);
                    
                    // Tambahkan padding jika perlu
                    $paddedBase64 = $urlDecodedTrimmed . str_repeat('=', 4 - (strlen($urlDecodedTrimmed) % 4));
                    $base64DecodedTrimmed = base64_decode($paddedBase64, true);
                    
                    if ($base64DecodedTrimmed !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64DecodedTrimmed)) {
                        \Illuminate\Support\Facades\Log::info('LabController: No Rawat berhasil didekode dengan trim+padding+base64: ' . $base64DecodedTrimmed);
                        return $base64DecodedTrimmed;
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('LabController: Gagal mendekode no_rawat [metode url]: ' . $e->getMessage());
            }
        }
        
        // Jika merupakan data base64 biasa, coba decode langsung
        if (preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $encodedValue)) {
            try {
                $directBase64Decoded = base64_decode($encodedValue, true);
                
                if ($directBase64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $directBase64Decoded)) {
                    \Illuminate\Support\Facades\Log::info('LabController: No Rawat berhasil didekode dengan direct base64: ' . $directBase64Decoded);
                    return $directBase64Decoded;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('LabController: Gagal mendekode no_rawat [metode direct base64]: ' . $e->getMessage());
            }
        }
        
        // Jika sampai di sini dan belum berhasil, coba cari no_rawat di database berdasarkan tanggal
        try {
            // Gunakan tanggal hari ini sebagai fallback
            $possibleDate = date('Y/m/d');
            
            $cekRawat = DB::table('reg_periksa')
                ->where('no_rawat', 'like', $possibleDate . '%')
                ->where('kd_dokter', session()->get('username'))
                ->orderBy('tgl_registrasi', 'desc')
                ->orderBy('jam_reg', 'desc')
                ->first();
                
            if ($cekRawat) {
                \Illuminate\Support\Facades\Log::info('LabController: Berhasil menemukan no_rawat berdasarkan tanggal: ' . $cekRawat->no_rawat);
                return $cekRawat->no_rawat;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('LabController: Gagal mencari no_rawat di database: ' . $e->getMessage());
        }
        
        \Illuminate\Support\Facades\Log::warning('LabController: Tidak berhasil mendekode no_rawat, mengembalikan nilai asli: ' . $encodedValue);
        return $encodedValue;
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
            'jenis_pemeriksaan' => count($jnsPemeriksaan)
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

            $lastNumber = substr($getNumber->no, 0, 4);
            $getNextNumber = sprintf('%04s', ($lastNumber + 1));
            $noOrder = 'PL'.date('Ymd').$getNextNumber;

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

            // Simpan jenis pemeriksaan
            foreach($jnsPemeriksaan as $pemeriksaan) {
                DB::table('permintaan_pemeriksaan_lab')
                    ->insert([
                        'noorder' => $noOrder,
                        'kd_jenis_prw' => $pemeriksaan,
                        'stts_bayar' => 'Belum'
                    ]);
            }
            
            // Simpan detail template yang dipilih
            if (!empty($templates)) {
                foreach($templates as $template) {
                    DB::table('permintaan_detail_permintaan_lab')
                        ->insert([
                            'noorder' => $noOrder,
                            'kd_jenis_prw' => $template['kd_jenis_prw'],
                            'id_template' => $template['id_template'],
                            'stts_bayar' => 'Belum'
                        ]);
                }
            }

            DB::commit();
            return response()->json(['status' => 'sukses', 'message' => 'Permintaan lab berhasil disimpan'], 200);

        } catch(\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error saat simpan permintaan lab: ' . $e->getMessage());
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

            DB::table('permintaan_lab')
                    ->where('noorder', $noOrder)
                    ->delete();

            // Hapus juga pemeriksaan terkait
            DB::table('permintaan_pemeriksaan_lab')
                    ->where('noorder', $noOrder)
                    ->delete();

            DB::commit();
            return response()->json(['status' => 'sukses', 'message' => 'Permintaan lab berhasil dihapus'], 200);
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error saat hapus permintaan lab: ' . $ex->getMessage(), [
                'noorder' => $noOrder,
                'code' => $ex->getCode()
            ]);
            return response()->json(['status' => 'gagal', 'message' => $ex->getMessage()], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error Exception saat hapus permintaan lab: ' . $e->getMessage());
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

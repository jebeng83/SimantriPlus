<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class PemeriksaanController extends Controller
{
    use EnkripsiData;

    public function getPemeriksaan($noRawat)
    {
        $noRawat = $this->decryptData($noRawat);

        try {
            // Cari data pemeriksaan terbaru untuk no_rawat ini
            $maxTgl = DB::table('pemeriksaan_ranap')
                ->where('no_rawat', $noRawat)
                ->max('tgl_perawatan');

            $maxJam = DB::table('pemeriksaan_ranap')
                ->where('no_rawat', $noRawat)
                ->where('tgl_perawatan', $maxTgl)
                ->max('jam_rawat');

            $data = DB::table('pemeriksaan_ranap')
                ->where('no_rawat', $noRawat)
                ->where('tgl_perawatan', $maxTgl)
                ->where('jam_rawat', $maxJam)
                ->first();

            // Jika tidak ada data, cari data kosong sebagai template
            if (!$data) {
                return response()->json([
                    'status' => 'sukses',
                    'pesan' => 'Data pemeriksaan kosong',
                    'data' => [
                        'kesadaran' => 'Compos Mentis',
                        'keluhan' => '',
                        'pemeriksaan' => 'KU : Composmentis, Baik 
Thorax : Cor S1-2 intensitas normal, reguler, bising (-)
Pulmo : SDV +/+ ST -/-
Abdomen : Supel, NT(-), peristaltik (+) normal.
EXT : Oedem -/-',
                        'penilaian' => '- ',
                        'suhu_tubuh' => '',
                        'berat' => '',
                        'tinggi' => '',
                        'tensi' => '',
                        'nadi' => '',
                        'respirasi' => '',
                        'instruksi' => 'Istirahat Cukup, PHBS ',
                        'alergi' => 'Tidak Ada',
                        'rtl' => 'Edukasi Kesehatan',
                        'gcs' => '',
                        'spo2' => '',
                        'evaluasi' => 'Evaluasi Keadaan Umum Tiap 6 Jam'
                    ]
                ]);
            }

            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Data pemeriksaan berhasil diambil',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Gagal mengambil data pemeriksaan'
            ], 500);
        }
    }

    public function getPegawai(Request $request)
    {
        $q = $request->get('q');
        $que = '%' . $q . '%';
        $pegawai = DB::table('petugas')
            ->where('status', '1')
            ->where('nama', 'like', $que)
            ->selectRaw('nip AS id, nama AS text')
            ->get();
        return response()->json($pegawai, 200);
    }
    
    /**
     * Mendapatkan riwayat pemeriksaan pasien berdasarkan no_rawat
     * Dengan opsi filter untuk menampilkan hanya data hari ini
     *
     * @param string $noRawat
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRiwayatPemeriksaan($noRawat, \Illuminate\Http\Request $request)
    {
        try {
            $decodedNoRawat = $this->decryptData($noRawat);
            
            // Dapatkan parameter filter dari request, default true (hanya hari ini)
            $filterToday = $request->has('today') ? filter_var($request->input('today'), FILTER_VALIDATE_BOOLEAN) : true;
            
            // Dapatkan nomor RM dari no_rawat
            $pasien = DB::table('reg_periksa')
                ->where('no_rawat', $decodedNoRawat)
                ->first();
                
            if (!$pasien) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pasien tidak ditemukan',
                    'data' => []
                ], 404);
            }
            
            $noRM = $pasien->no_rkm_medis;
            
            // Buat query dasar
            $query = DB::table('pemeriksaan_ranap')
                ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'pemeriksaan_ranap.no_rawat')
                ->where('reg_periksa.no_rkm_medis', $noRM);
            
            // Filter hanya data hari ini jika parameter filterToday true
            if ($filterToday) {
                $query->where('pemeriksaan_ranap.tgl_perawatan', date('Y-m-d'));
            }
            
            // Eksekusi query dengan urutan terbaru lebih dulu
            $riwayat = $query->select('pemeriksaan_ranap.*')
                ->orderByDesc('pemeriksaan_ranap.tgl_perawatan')
                ->orderByDesc('pemeriksaan_ranap.jam_rawat')
                ->get();
            
            // Tambahkan header untuk mencegah caching
            return response()->json([
                'status' => 'success',
                'message' => 'Riwayat pemeriksaan berhasil diambil',
                'data' => $riwayat,
                'filtered_today' => $filterToday,
                'timestamp' => now()->timestamp // Tambahkan timestamp untuk memastikan data terbaru
            ], 200)->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error mengambil riwayat pemeriksaan: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data riwayat',
                'data' => [],
                'error_details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500)->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }
    }
}

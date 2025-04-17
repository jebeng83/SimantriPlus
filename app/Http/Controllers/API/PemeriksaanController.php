<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;
use Illuminate\Support\Facades\Log;

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

    public function save(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            
            // Log data yang akan disimpan
            Log::info('Menyimpan data pemeriksaan', [
                'no_rawat' => $data['no_rawat'],
                'tgl_perawatan' => $data['tgl_perawatan']
            ]);

            // Data untuk pemeriksaan_ralan
            $pemeriksaanData = [
                'no_rawat' => substr($data['no_rawat'], 0, 17),
                'tgl_perawatan' => $data['tgl_perawatan'],
                'jam_rawat' => $data['jam_rawat'],
                'suhu_tubuh' => substr($data['suhu_tubuh'] ?? '', 0, 5),
                'tensi' => substr($data['tensi'] ?? '', 0, 8),
                'nadi' => substr($data['nadi'] ?? '', 0, 3),
                'respirasi' => substr($data['respirasi'] ?? '', 0, 3),
                'tinggi' => substr($data['tinggi'] ?? '', 0, 5),
                'berat' => substr($data['berat'] ?? '', 0, 5),
                'spo2' => substr($data['spo2'] ?? '', 0, 3),
                'gcs' => substr($data['gcs'] ?? '', 0, 10),
                'kesadaran' => $data['kesadaran'] ?? 'Compos Mentis',
                'keluhan' => substr($data['keluhan'] ?? '', 0, 2000),
                'pemeriksaan' => substr($data['pemeriksaan'] ?? '', 0, 2000),
                'alergi' => substr($data['alergi'] ?? '', 0, 80),
                'lingkar_perut' => substr($data['lingkar_perut'] ?? '', 0, 5),
                'rtl' => substr($data['rtl'] ?? '', 0, 2000),
                'penilaian' => substr($data['penilaian'] ?? '', 0, 2000),
                'instruksi' => substr($data['instruksi'] ?? '', 0, 2000),
                'evaluasi' => substr($data['evaluasi'] ?? '', 0, 2000),
                'nip' => substr($data['nip'] ?? '', 0, 20)
            ];

            // Cek apakah data sudah ada
            $existingData = DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $data['no_rawat'])
                ->where('tgl_perawatan', $data['tgl_perawatan'])
                ->first();

            if ($existingData) {
                // Update data yang sudah ada
                DB::table('pemeriksaan_ralan')
                    ->where('no_rawat', $data['no_rawat'])
                    ->where('tgl_perawatan', $data['tgl_perawatan'])
                    ->update($pemeriksaanData);
                
                Log::info('Data pemeriksaan_ralan berhasil diupdate', [
                    'no_rawat' => $data['no_rawat']
                ]);
            } else {
                // Insert data baru
                DB::table('pemeriksaan_ralan')->insert($pemeriksaanData);
                
                Log::info('Data pemeriksaan_ralan berhasil disimpan', [
                    'no_rawat' => $data['no_rawat']
                ]);
            }

            // Update status pasien menjadi 'Sudah'
            // DB::table('reg_periksa')
            //     ->where('no_rawat', $data['no_rawat'])
            //     ->update(['stts' => 'Sudah']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data pemeriksaan berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Gagal menyimpan data pemeriksaan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data pemeriksaan: ' . $e->getMessage()
            ], 500);
        }
    }
}

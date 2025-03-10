<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class ResumePasienController extends Controller
{
    use EnkripsiData;

    public function postResume(Request $request, $noRawat)
    {
        $keluhan = $request->get('keluhan_utama');
        $diagnosa = $request->get('diagnosa_utama');
        $terapi = $request->get('terapi');
        $prosedur = $request->get('prosedur_utama');
        $jalannyaPenyakit = $request->get('jalannya_penyakit');
        $pemeriksaanPenunjang = $request->get('pemeriksaan_penunjang');
        $hasilLaborat = $request->get('hasil_laborat');
        $kondisiPulang = $request->get('kondisi_pulang');
        $dokter = session()->get('username');
        $noRawat = $this->decryptData($noRawat);

        // $request->validate([
        //     'keluhan' => 'required',
        //     'diagnosa' => 'required',
        //     'terapi' => 'required',
        //     'prosedur' => 'required',
        //     'jalannya_penyakit' => 'required',
        //     'pemeriksaan_penunjang' => 'required',
        //     'hasil_laborat' => 'required',
        //     'kondisi_pulang' => 'required',
        // ]);

        try {
            DB::beginTransaction();
            $cek = DB::table('resume_pasien')->where('no_rawat', $noRawat)->count('no_rawat');
            if ($cek > 0) {
                DB::table('resume_pasien')->where('no_rawat', $noRawat)->update([
                    'keluhan_utama' => $keluhan,
                    'diagnosa_utama' => $diagnosa,
                    'obat_pulang' => $terapi,
                    'prosedur_utama' => $prosedur,
                    'jalannya_penyakit' => $jalannyaPenyakit,
                    'pemeriksaan_penunjang' => $pemeriksaanPenunjang,
                    'hasil_laborat' => $hasilLaborat,
                    'kondisi_pulang' => $kondisiPulang,
                ]);
                DB::commit();
                return response()->json([
                    'status' => 'sukses',
                    'pesan' => 'Resume medis berhasil diperbarui'
                ]);
            } else {
                DB::table('resume_pasien')->insert([
                    'no_rawat' => $noRawat,
                    'kd_dokter' => $dokter,
                    'keluhan_utama' => $keluhan,
                    'diagnosa_utama' => $diagnosa,
                    'obat_pulang' => $terapi,
                    'prosedur_utama' => $prosedur,
                    'jalannya_penyakit' => $jalannyaPenyakit,
                    'pemeriksaan_penunjang' => $pemeriksaanPenunjang,
                    'hasil_laborat' => $hasilLaborat,
                    'kondisi_pulang' => $kondisiPulang,
                ]);

                DB::commit();
                return response()->json([
                    'status' => 'sukses',
                    'pesan' => 'Resume medis berhasil ditambahkan'
                ]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
                'message' => $ex->getMessage()
            ]);
        }
    }

    public function getKeluhanUtama($noRawat)
    {
        $noRawat = $this->decryptData($noRawat);

        try {
            $cek = DB::table('reg_periksa')->where('no_rawat', $noRawat)->first();
            if ($cek->status_lanjut == 'Ralan') {
                $data = DB::table('pemeriksaan_ralan')->where('no_rawat', $noRawat)->select('keluhan')->first();
            } else {
                $data = DB::table('pemeriksaan_ranap')->where('no_rawat', $noRawat)->select('keluhan')->first();
            }
            return response()->json([
                'status' => 'sukses',
                'data' => $data->keluhan
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'status' => 'gagal',
                'message' => $ex->getMessage()
            ]);
        }
    }

    public function getDiagnosa(Request $request)
    {
        $q = $request->get('q');
        $que = '%' . $q . '%';

        $data = DB::table('penyakit')
            ->where('kd_penyakit', 'like', $que)
            ->orWhere('nm_penyakit', 'like', $que)
            ->get();
        return response()->json($data, 200);
    }

    public function getICD9(Request $request)
    {
        $q = $request->get('q');
        $que = '%' . $q . '%';

        $data = DB::table('icd9')
            ->where('kode', 'like', $que)
            ->orWhere('deskripsi_panjang', 'like', $que)
            ->orWhere('deskripsi_pendek', 'like', $que)
            ->get();
        return response()->json($data, 200);
    }

    public function simpanDiagnosa(Request $request)
    {
        $encryptedNoRawat = $request->get('noRawat');
        $noRM = $request->get('noRM');
        
        // Log seluruh parameter dan header untuk debugging
        \Illuminate\Support\Facades\Log::info('Semua parameter request di simpanDiagnosa', [
            'all_params' => $request->all(),
            'route_params' => $request->route()->parameters(),
            'noRawat' => $encryptedNoRawat,
            'noRM' => $noRM,
            'diagnosa' => $request->get('diagnosa'),
            'prioritas' => $request->get('prioritas'),
            'headers' => $request->headers->all()
        ]);
        
        try {
            // Validasi input dulu
            $this->validate($request, [
                'diagnosa' => 'required',
                'prioritas' => 'required',
            ], [
                'diagnosa.required' => 'Diagnosa tidak boleh kosong',
                'prioritas.required' => 'Prioritas tidak boleh kosong',
            ]);
            
            // Dekripsi dan sanitasi no_rawat
            // Jika noRawat tidak ada di request, coba ambil dari route parameter
            if (empty($encryptedNoRawat) && !empty($request->route('noRawat'))) {
                $encryptedNoRawat = $request->route('noRawat');
                \Illuminate\Support\Facades\Log::info('Menggunakan noRawat dari route parameter', ['route_noRawat' => $encryptedNoRawat]);
            }
            
            if (empty($encryptedNoRawat)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'No Rawat tidak ditemukan di request'
                ], 400);
            }
            
            $noRawat = $this->decryptData($encryptedNoRawat);
            
            // URL-decode jika perlu
            if (strpos($noRawat, '%') !== false) {
                $noRawat = urldecode($noRawat);
            }
            
            // Pastikan data yang kita terima adalah string yang valid
            if (!is_string($noRawat)) {
                $noRawat = (string)$noRawat;
            }
            
            // Hapus karakter non-printable dan URL encoding
            $cleanNoRawat = preg_replace('/[[:^print:]]/', '', $noRawat);
            
            // Jika hasil tidak sesuai format, coba decode dari base64
            if (!preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $cleanNoRawat)) {
                $base64Decoded = base64_decode($cleanNoRawat);
                if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                    $cleanNoRawat = $base64Decoded;
                } else {
                    // Coba tanpa URL encoding
                    $base64Decoded = base64_decode(str_replace('%3D', '=', $encryptedNoRawat));
                    if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                        $cleanNoRawat = $base64Decoded;
                    }
                }
            }
            
            // Log debug untuk tracking
            \Illuminate\Support\Facades\Log::info('Proses no_rawat di simpanDiagnosa', [
                'encrypted' => $encryptedNoRawat,
                'decoded' => $noRawat,
                'cleaned' => $cleanNoRawat
            ]);
            
            // Verifikasi format no_rawat
            if (!preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $cleanNoRawat)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Format nomor rawat tidak valid: ' . $cleanNoRawat
                ], 400);
            }
            
            // Verifikasi no_rawat ada di database
            $cekNoRawat = DB::table('reg_periksa')
                ->where(DB::raw('BINARY no_rawat'), $cleanNoRawat)
                ->first();
                
            if (!$cekNoRawat) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'No Rawat tidak ditemukan di database'
                ], 404);
            }
            
            // Gunakan no_rawat yang sudah dibersihkan
            $noRawat = $cleanNoRawat;
            
            // Jika noRM tidak ada di request, coba ambil dari reg_periksa
            if (empty($noRM)) {
                $noRM = $cekNoRawat->no_rkm_medis;
                \Illuminate\Support\Facades\Log::info('Menggunakan noRM dari database', ['db_noRM' => $noRM]);
            }
            
            // Cek status penyakit (baru/lama)
            $cek_status = DB::table('diagnosa_pasien')
                ->join('reg_periksa', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                ->where('diagnosa_pasien.kd_penyakit', $request->get('diagnosa'))
                ->where('reg_periksa.no_rkm_medis', $noRM)
                ->select('diagnosa_pasien.kd_penyakit')
                ->first();
                
            $status = $cek_status ? 'Lama' : 'Baru';
            
            // Cek apakah diagnosa sudah ada
            $cek = DB::table('diagnosa_pasien')
                ->where('kd_penyakit', $request->get('diagnosa'))
                ->where(DB::raw('BINARY no_rawat'), $noRawat)
                ->count();
                
            if ($cek > 0) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Sudah ada diagnosa yang sama'
                ], 409);
            }
            
            // Mulai transaksi database
            DB::beginTransaction();
            
            // Simpan diagnosa
            DB::table('diagnosa_pasien')->insert([
                'no_rawat' => $noRawat,
                'kd_penyakit' => $request->get('diagnosa'),
                'status' => 'Ralan',
                'prioritas' => $request->get('prioritas'),
                'status_penyakit' => $status,
            ]);
            
            // Commit transaksi
            DB::commit();
            
            // Log sukses
            \Illuminate\Support\Facades\Log::info('Diagnosa berhasil disimpan', [
                'no_rawat' => $noRawat,
                'kd_penyakit' => $request->get('diagnosa'),
                'prioritas' => $request->get('prioritas')
            ]);
            
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Diagnosa berhasil disimpan'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangani error validasi
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $ex) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            
            // Log detail error untuk debugging
            \Illuminate\Support\Facades\Log::error('Error saat menyimpan diagnosa: ' . $ex->getMessage(), [
                'file' => __FILE__,
                'line' => __LINE__,
                'trace' => $ex->getTraceAsString(),
                'no_rawat' => $noRawat ?? 'tidak tersedia',
                'sql' => $ex->getSql() ?? 'tidak tersedia',
                'bindings' => $ex->getBindings() ?? []
            ]);
            
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan database: ' . $ex->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            
            \Illuminate\Support\Facades\Log::error('Error umum saat menyimpan diagnosa: ' . $e->getMessage(), [
                'file' => __FILE__,
                'line' => __LINE__,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

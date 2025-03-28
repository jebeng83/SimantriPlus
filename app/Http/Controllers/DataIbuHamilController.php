<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @deprecated Gunakan App\Http\Controllers\ANC\DataIbuHamilController sebagai gantinya
 */
class OldDataIbuHamilController extends Controller
{
    public function index()
    {
        return view('anc.data-ibu-hamil');
    }

    public function getDataPasien($nik)
    {
        try {
            Log::info('Mencari data pasien dengan NIK: ' . $nik);
            
            // Cek apakah tabelnya ada
            try {
                $tableExists = DB::select("SHOW TABLES LIKE 'pasien'");
                if (empty($tableExists)) {
                    Log::error('Tabel pasien tidak ditemukan');
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tabel pasien tidak ditemukan'
                    ], 404);
                }
            } catch (\Exception $e) {
                Log::error('Error cek tabel: ' . $e->getMessage());
            }
            
            // Debug query
            $queryLog = DB::connection()->enableQueryLog();
            
            $pasien = DB::table('pasien')
                ->select([
                    'pasien.nm_pasien as nama',
                    'pasien.tgl_lahir',
                    'pasien.no_kk',
                    'pasien.no_peserta',
                    'pasien.alamat',
                    'pasien.kd_prop',
                    'pasien.kd_kab',
                    'pasien.kd_kec',
                    'pasien.kd_kel',
                    'pasien.data_posyandu',
                    'pasien.no_rkm_medis'
                ])
                ->where('no_ktp', $nik)
                ->first();
                
            Log::info('Query logs: ', DB::getQueryLog());
            DB::connection()->disableQueryLog();

            if (!$pasien) {
                Log::warning('Data pasien tidak ditemukan untuk NIK: ' . $nik);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pasien tidak ditemukan'
                ], 404);
            }
            
            // Ambil data propinsi
            $propinsi = DB::table('propinsi')
                ->where('kd_prop', $pasien->kd_prop)
                ->first();
                
            // Ambil data kabupaten
            $kabupaten = DB::table('kabupaten')
                ->where('kd_kab', $pasien->kd_kab)
                ->where('kd_prop', $pasien->kd_prop)
                ->first();
                
            // Ambil data kecamatan
            $kecamatan = DB::table('kecamatan')
                ->where('kd_kec', $pasien->kd_kec)
                ->where('kd_kab', $pasien->kd_kab)
                ->where('kd_prop', $pasien->kd_prop)
                ->first();
                
            // Ambil data kelurahan
            $kelurahan = DB::table('kelurahan')
                ->where('kd_kel', $pasien->kd_kel)
                ->where('kd_kec', $pasien->kd_kec)
                ->where('kd_kab', $pasien->kd_kab)
                ->where('kd_prop', $pasien->kd_prop)
                ->first();

            Log::info('Data pasien ditemukan:', ['pasien' => $pasien]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'nama' => $pasien->nama,
                    'no_rkm_medis' => $pasien->no_rkm_medis,
                    'tgl_lahir' => $pasien->tgl_lahir,
                    'nomor_kk' => $pasien->no_kk,
                    'no_jaminan_kesehatan' => $pasien->no_peserta,
                    'alamat' => $pasien->alamat,
                    'provinsi' => [
                        'kode' => $pasien->kd_prop,
                        'nama' => $propinsi ? $propinsi->nm_prop : 'Tidak Ada'
                    ],
                    'kabupaten' => [
                        'kode' => $pasien->kd_kab,
                        'nama' => $kabupaten ? $kabupaten->nm_kab : 'Tidak Ada'
                    ],
                    'kecamatan' => [
                        'kode' => $pasien->kd_kec,
                        'nama' => $kecamatan ? $kecamatan->nm_kec : 'Tidak Ada'
                    ],
                    'desa' => [
                        'kode' => $pasien->kd_kel,
                        'nama' => $kelurahan ? $kelurahan->nm_kel : 'Tidak Ada'
                    ],
                    'puskesmas' => 'KERJO',
                    'data_posyandu' => $pasien->data_posyandu
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mencari data pasien: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'nik' => 'required_without:belumMemilikiNIK',
                'kehamilan_ke' => 'required',
                'tgl_lahir' => 'required|date',
                'nomor_kk' => 'required',
                'nama' => 'required',
                'provinsi' => 'required',
                'kabupaten' => 'required',
                'kecamatan' => 'required',
                'desa' => 'required',
                'alamat_lengkap' => 'required',
                'kepemilikan_buku_kia' => 'required'
            ]);

            // Simpan data
            DB::table('data_ibu_hamil')->insert([
                'nik' => $request->nik,
                'kehamilan_ke' => $request->kehamilan_ke,
                'tgl_lahir' => $request->tgl_lahir,
                'nomor_kk' => $request->nomor_kk,
                'nama' => $request->nama,
                'berat_badan_sebelum_hamil' => $request->berat_badan_sebelum_hamil,
                'tinggi_badan' => $request->tinggi_badan,
                'lila' => $request->lila,
                'imt_sebelum_hamil' => $request->imt_sebelum_hamil,
                'status_gizi' => $request->status_gizi,
                'jumlah_janin' => $request->jumlah_janin,
                'jarak_kehamilan_tahun' => $request->jarak_kehamilan_tahun,
                'jarak_kehamilan_bulan' => $request->jarak_kehamilan_bulan,
                'hari_pertama_haid' => $request->hari_pertama_haid,
                'hari_perkiraan_lahir' => $request->hari_perkiraan_lahir,
                'golongan_darah' => $request->golongan_darah,
                'rhesus' => $request->rhesus,
                'riwayat_penyakit' => $request->riwayat_penyakit,
                'riwayat_alergi' => $request->riwayat_alergi,
                'kepemilikan_buku_kia' => $request->kepemilikan_buku_kia,
                'jaminan_kesehatan' => $request->jaminan_kesehatan,
                'no_jaminan_kesehatan' => $request->no_jaminan_kesehatan,
                'faskes_tk1' => $request->faskes_tk1,
                'faskes_rujukan' => $request->faskes_rujukan,
                'pendidikan' => $request->pendidikan,
                'pekerjaan' => $request->pekerjaan,
                'nama_suami' => $request->nama_suami,
                'nik_suami' => $request->nik_suami,
                'telp_suami' => $request->telp_suami,
                'provinsi' => $request->provinsi,
                'kabupaten' => $request->kabupaten,
                'kecamatan' => $request->kecamatan,
                'desa' => $request->desa,
                'puskesmas' => 'KERJO',
                'alamat_lengkap' => $request->alamat_lengkap,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan data: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
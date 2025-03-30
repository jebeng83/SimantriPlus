<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PemeriksaanAnc;

class TestAncController extends Controller
{
    /**
     * Test penyimpanan data pemeriksaan ANC ke database
     */
    public function testStore()
    {
        try {
            // Dapatkan data pasien yang sudah terdaftar untuk testing
            $pasien = DB::table('pasien')->first();
            if (!$pasien) {
                return response()->json(['error' => 'Tidak ada data pasien untuk testing'], 400);
            }

            // Dapatkan data rawat jalan yang sudah ada
            $regPeriksa = DB::table('reg_periksa')->first();
            if (!$regPeriksa) {
                return response()->json(['error' => 'Tidak ada data reg_periksa untuk testing'], 400);
            }

            // Dapatkan atau buat data ibu hamil
            $ibuHamil = DB::table('data_ibu_hamil')->where('no_rkm_medis', $pasien->no_rkm_medis)->first();
            $idHamil = null;

            if (!$ibuHamil) {
                // Generate ID Hamil dengan format IBU+tahun+bulan+nomor urut 3 digit
                $tahunBulan = date('Ym');
                $lastId = DB::table('data_ibu_hamil')
                    ->where('id_hamil', 'like', "IBU{$tahunBulan}%")
                    ->orderBy('id_hamil', 'desc')
                    ->value('id_hamil');
                
                if ($lastId) {
                    $lastNumber = (int) substr($lastId, -3);
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }
                
                $idHamil = 'IBU' . $tahunBulan . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                
                // Insert data ibu hamil baru
                DB::table('data_ibu_hamil')->insert([
                    'id_hamil' => $idHamil,
                    'no_rkm_medis' => $pasien->no_rkm_medis,
                    'nama' => $pasien->nm_pasien,
                    'hari_pertama_haid' => date('Y-m-d', strtotime('-12 weeks')),
                    'hari_perkiraan_lahir' => date('Y-m-d', strtotime('+28 weeks')),
                    'status' => 'Aktif',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $idHamil = $ibuHamil->id_hamil;
            }

            // Buat data pemeriksaan ANC baru
            $pemeriksaanAnc = new PemeriksaanAnc();
            $pemeriksaanAnc->no_rawat = $regPeriksa->no_rawat;
            $pemeriksaanAnc->no_rkm_medis = $pasien->no_rkm_medis;
            $pemeriksaanAnc->id_hamil = $idHamil;
            $pemeriksaanAnc->tanggal_anc = now();
            $pemeriksaanAnc->diperiksa_oleh = 'Dr. Testing';
            $pemeriksaanAnc->usia_kehamilan = 12;
            $pemeriksaanAnc->trimester = 1;
            $pemeriksaanAnc->kunjungan_ke = 1;
            $pemeriksaanAnc->berat_badan = 55.5;
            $pemeriksaanAnc->tinggi_badan = 160;
            $pemeriksaanAnc->lila = 24;
            $pemeriksaanAnc->imt = 21.7;
            $pemeriksaanAnc->kategori_imt = 'NORMAL';
            $pemeriksaanAnc->jumlah_janin = '1';
            $pemeriksaanAnc->tinggi_fundus = 12;
            $pemeriksaanAnc->taksiran_berat_janin = 155 * (12 - 13);
            $pemeriksaanAnc->denyut_jantung_janin = 140;
            $pemeriksaanAnc->presentasi = 'Kepala';
            $pemeriksaanAnc->presentasi_janin = 'Kepala';
            $pemeriksaanAnc->td_sistole = 120;
            $pemeriksaanAnc->td_diastole = 80;
            $pemeriksaanAnc->jumlah_fe = 30;
            $pemeriksaanAnc->dosis = 1;
            $pemeriksaanAnc->status_tt = 'TT1';
            $pemeriksaanAnc->keluhan_utama = 'Mual dan muntah';
            $pemeriksaanAnc->gravida = 1;
            $pemeriksaanAnc->partus = 0;
            $pemeriksaanAnc->abortus = 0;
            $pemeriksaanAnc->hidup = 0;
            $pemeriksaanAnc->materi = 'Nutrisi ibu hamil';
            $pemeriksaanAnc->rekomendasi = 'Istirahat cukup';
            $pemeriksaanAnc->konseling_menyusui = 'Ya';
            $pemeriksaanAnc->tanda_bahaya_kehamilan = 'Ya';
            $pemeriksaanAnc->tanda_bahaya_persalinan = 'Ya';
            $pemeriksaanAnc->konseling_phbs = 'Ya';
            $pemeriksaanAnc->konseling_gizi = 'Ya';
            $pemeriksaanAnc->konseling_ibu_hamil = 'Ya';
            $pemeriksaanAnc->keadaan_pulang = 'Baik';
            $pemeriksaanAnc->tindak_lanjut = 'Kunjungan ANC berikutnya';
            $pemeriksaanAnc->detail_tindak_lanjut = 'Datang untuk pemeriksaan rutin';
            $pemeriksaanAnc->tanggal_kunjungan_berikutnya = now()->addWeeks(4);
            
            // Penambahan field untuk informasi lab
            $pemeriksaanAnc->hasil_pemeriksaan_hb = 12.5;
            $pemeriksaanAnc->hasil_pemeriksaan_urine_protein = 'Negatif';
            $pemeriksaanAnc->lab = json_encode([
                'hb' => [
                    'checked' => true,
                    'nilai' => '12.5'
                ],
                'goldar' => [
                    'checked' => true,
                    'nilai' => 'A'
                ],
                'protein_urin' => [
                    'checked' => true,
                    'nilai' => 'Negatif'
                ]
            ]);
            
            $pemeriksaanAnc->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Data pemeriksaan ANC berhasil disimpan',
                'id_anc' => $pemeriksaanAnc->id_anc,
                'data' => $pemeriksaanAnc->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error menyimpan pemeriksaan ANC: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    /**
     * Memeriksa data pemeriksaan ANC berdasarkan ID
     */
    public function check($id)
    {
        try {
            $pemeriksaan = PemeriksaanAnc::where('id_anc', $id)->first();
            
            if (!$pemeriksaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pemeriksaan ANC dengan ID ' . $id . ' tidak ditemukan'
                ], 404);
            }
            
            // Ambil struktur tabel untuk validasi
            $columns = DB::getSchemaBuilder()->getColumnListing('pemeriksaan_anc');
            
            return response()->json([
                'success' => true,
                'message' => 'Data pemeriksaan ANC ditemukan',
                'data' => $pemeriksaan->toArray(),
                'table_structure' => [
                    'table_name' => 'pemeriksaan_anc',
                    'columns' => $columns
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error memeriksa data pemeriksaan ANC: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}

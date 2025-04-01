<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PartografSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Periksa apakah ada data ibu hamil
        $ibuHamilExists = DB::table('ibu_hamil')->exists();
        
        if (!$ibuHamilExists) {
            $this->command->info('Tidak ada data ibu hamil. Seeder tidak dapat berjalan.');
            return;
        }
        
        // Periksa apakah ada data di reg_periksa
        $regPeriksaExists = DB::table('reg_periksa')->exists();
        
        if (!$regPeriksaExists) {
            $this->command->info('Tidak ada data registrasi periksa. Seeder tidak dapat berjalan.');
            return;
        }
        
        // Ambil 3 ibu hamil pertama
        $ibuHamil = DB::table('ibu_hamil')->limit(3)->get();
        
        foreach ($ibuHamil as $index => $ibu) {
            // Cari satu data registrasi untuk ibu hamil ini
            $regPeriksa = DB::table('reg_periksa')
                ->where('no_rkm_medis', $ibu->no_rkm_medis)
                ->first();
            
            if (!$regPeriksa) {
                $this->command->info("Tidak ada data registrasi untuk ibu hamil {$ibu->nama}. Melewati.");
                continue;
            }
            
            // Cek apakah partograf sudah ada untuk ibu hamil ini
            $exists = DB::table('partograf')
                ->where('id_hamil', $ibu->id_hamil)
                ->exists();
                
            if (!$exists) {
                $tanggalPartograf = Carbon::now()->subDays(rand(1, 30));
                $idPartograf = 'PG' . date('ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                
                // Buat data partograf dasar sesuai dengan struktur tabel yang ada
                DB::table('partograf')->insert([
                    'id_partograf' => $idPartograf,
                    'no_rawat' => $regPeriksa->no_rawat,
                    'no_rkm_medis' => $ibu->no_rkm_medis,
                    'id_hamil' => $ibu->id_hamil,
                    'tanggal_partograf' => $tanggalPartograf,
                    'diperiksa_oleh' => 'Dr. Testing',
                    
                    // Bagian 1: Informasi Persalinan Awal
                    'paritas' => rand(0, 1) ? 'Primipara' : 'Multipara',
                    'onset_persalinan' => rand(0, 1) ? 'Spontan' : 'Induksi',
                    'waktu_pecah_ketuban' => rand(0, 1) ? $tanggalPartograf->copy()->subHours(rand(1, 5)) : null,
                    'faktor_risiko' => json_encode(['Hipertensi', 'Diabetes']),
                    
                    // Bagian 2: Supportive Care
                    'pendamping' => rand(0, 1) ? 'Ya' : 'Tidak',
                    'mobilitas' => rand(0, 1) ? 'Aktif' : 'Pasif',
                    'manajemen_nyeri' => rand(0, 1) ? 'Farmakologis' : 'Non-farmakologis',
                    'intake_cairan' => rand(50, 200) . ' ml',
                    
                    // Bagian 3: Informasi Janin
                    'denyut_jantung_janin' => rand(120, 160),
                    'kondisi_cairan_ketuban' => ['J', 'M', 'D', 'K'][rand(0, 3)],
                    'presentasi_janin' => rand(0, 1) ? 'Kepala' : 'Bokong',
                    'bentuk_kepala_janin' => 'Normal',
                    'caput_succedaneum' => rand(0, 1) ? 'Ya' : 'Tidak',
                    
                    // Bagian 4: Informasi Ibu
                    'nadi' => rand(60, 100),
                    'tekanan_darah_sistole' => rand(100, 140),
                    'tekanan_darah_diastole' => rand(60, 90),
                    'suhu' => rand(360, 375) / 10,
                    'urine_output' => rand(100, 500),
                    
                    // Bagian 5: Proses Persalinan
                    'frekuensi_kontraksi' => rand(1, 5),
                    'durasi_kontraksi' => rand(20, 60),
                    'dilatasi_serviks' => rand(1, 10),
                    'penurunan_posisi_janin' => rand(0, 5),
                    
                    // Bagian 6: Pengobatan
                    'obat_dan_dosis' => rand(0, 1) ? 'Oksitoksin 5 IU' : 'Antibiotik',
                    'cairan_infus' => rand(0, 1) ? 'NaCl 0.9%' : 'Ringer Laktat',
                    
                    // Bagian 7: Perencanaan
                    'tindakan_yang_direncanakan' => 'Pemantauan rutin',
                    'hasil_tindakan' => 'Pelaksanaan normal',
                    'keputusan_bersama' => 'Melanjutkan proses persalinan normal',
                    
                    // Data grafik
                    'grafik_kemajuan_persalinan_json' => json_encode([
                        'waktu' => [$tanggalPartograf->format('H:i')],
                        'pembukaan' => [rand(1, 10)],
                        'penurunan' => [rand(0, 5)]
                    ]),
                    
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                $this->command->info("Data partograf berhasil dibuat untuk ibu hamil {$ibu->nama}");
            } else {
                $this->command->info("Data partograf untuk ibu hamil {$ibu->nama} sudah ada.");
            }
        }
    }
}

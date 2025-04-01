<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IbuHamilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan ada data pasien
        $pasienExists = DB::table('pasien')->exists();
        
        if (!$pasienExists) {
            $this->command->info('Tidak ada data pasien. Seeder tidak dapat berjalan.');
            return;
        }
        
        // Ambil 5 pasien perempuan secara acak
        $pasien = DB::table('pasien')
            ->where('jk', 'P')
            ->limit(5)
            ->get();
        
        if ($pasien->isEmpty()) {
            $this->command->info('Tidak ada data pasien perempuan. Seeder tidak dapat berjalan.');
            return;
        }
        
        // Buat data ibu hamil
        foreach ($pasien as $index => $p) {
            // Cek apakah sudah ada data untuk pasien ini
            $exists = DB::table('ibu_hamil')
                ->where('no_rkm_medis', $p->no_rkm_medis)
                ->exists();
                
            if (!$exists) {
                $hpht = Carbon::now()->subMonths(rand(1, 8))->subDays(rand(0, 20));
                $hpl = (clone $hpht)->addMonths(9)->addDays(7);
                
                DB::table('ibu_hamil')->insert([
                    'no_rkm_medis' => $p->no_rkm_medis,
                    'nama' => $p->nm_pasien,
                    'usia' => Carbon::parse($p->tgl_lahir)->age,
                    'usia_kehamilan' => Carbon::parse($hpht)->diffInWeeks(Carbon::now()),
                    'tanggal_lahir' => $p->tgl_lahir,
                    'alamat' => $p->alamat,
                    'status_kehamilan' => 'Aktif',
                    'HPHT' => $hpht,
                    'HPL' => $hpl,
                    'catatan' => 'Data testing',
                    'created_by' => 'Seeder',
                    'updated_by' => 'Seeder',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                $this->command->info("Data ibu hamil #{$index} berhasil dibuat untuk pasien {$p->nm_pasien}");
            } else {
                $this->command->info("Data ibu hamil untuk pasien {$p->nm_pasien} sudah ada.");
            }
        }
    }
}

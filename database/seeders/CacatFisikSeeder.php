<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CacatFisikSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nama_cacat' => 'TIDAK ADA'],
            ['nama_cacat' => 'TUNA NETRA'],
            ['nama_cacat' => 'TUNA RUNGU'],
            ['nama_cacat' => 'TUNA WICARA'],
            ['nama_cacat' => 'TUNA DAKSA'],
            ['nama_cacat' => 'TUNA GRAHITA'],
            ['nama_cacat' => 'TUNA LARAS'],
            ['nama_cacat' => 'CACAT GANDA'],
            // Tambahkan data lain sesuai kebutuhan
        ];

        DB::table('cacat_fisik')->insert($data);
    }
} 
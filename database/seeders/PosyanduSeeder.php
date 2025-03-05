<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Posyandu;

class PosyanduSeeder extends Seeder
{
    public function run()
    {
        $posyandu = [
            [
                'nama_posyandu' => 'Posyandu Melati',
                'alamat' => 'Jl. Melati No. 1',
                'keterangan' => 'Posyandu Wilayah A'
            ],
            [
                'nama_posyandu' => 'Posyandu Mawar',
                'alamat' => 'Jl. Mawar No. 2',
                'keterangan' => 'Posyandu Wilayah B'
            ],
            // Tambahkan data posyandu lainnya
        ];

        foreach ($posyandu as $pos) {
            Posyandu::create($pos);
        }
    }
} 
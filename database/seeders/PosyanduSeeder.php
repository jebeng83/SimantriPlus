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
                'kode_posyandu' => 'PSY001',
                'nama_posyandu' => 'Posyandu Melati',
                'alamat' => 'Jl. Melati No. 1',
                'desa' => 'Desa Melati'
            ],
            [
                'kode_posyandu' => 'PSY002',
                'nama_posyandu' => 'Posyandu Mawar',
                'alamat' => 'Jl. Mawar No. 2',
                'desa' => 'Desa Mawar'
            ],
            // Tambahkan data posyandu lainnya
        ];

        foreach ($posyandu as $pos) {
            Posyandu::create($pos);
        }
    }
}
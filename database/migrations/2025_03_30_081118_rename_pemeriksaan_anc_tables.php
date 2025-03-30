<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Cadangkan tabel lama dengan mengubah namanya
        Schema::rename('pemeriksaan_anc', 'pemeriksaan_anc_old');
        
        // Ubah nama tabel baru menjadi tabel utama
        Schema::rename('pemeriksaan_anc_new', 'pemeriksaan_anc');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Kembalikan nama tabel
        Schema::rename('pemeriksaan_anc', 'pemeriksaan_anc_new');
        Schema::rename('pemeriksaan_anc_old', 'pemeriksaan_anc');
    }
};

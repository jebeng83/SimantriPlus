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
        Schema::table('skrining_pkg', function (Blueprint $table) {
            // Talasemia - Kolom untuk menyimpan data form talasemia
            $table->enum('riwayat_keluarga', ['Ya', 'Tidak'])
                  ->nullable()
                  ->comment('Riwayat keluarga menderita talasemia atau kelainan darah');
            $table->enum('pembawa_sifat', ['Ya', 'Tidak'])
                  ->nullable()
                  ->comment('Anggota keluarga sebagai pembawa sifat talasemia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skrining_pkg', function (Blueprint $table) {
            // Hapus kolom talasemia
            $table->dropColumn(['riwayat_keluarga', 'pembawa_sifat']);
        });
    }
};

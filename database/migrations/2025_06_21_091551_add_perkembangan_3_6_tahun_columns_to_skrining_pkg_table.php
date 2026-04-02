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
            // Perkembangan 3-6 Tahun - Kolom untuk menyimpan data form perkembangan anak usia 3-6 tahun
            $table->enum('gangguan_emosi', ['Ya', 'Tidak'])->nullable()->comment('Apakah anak memiliki gangguan emosi/perilaku seperti tantrum, menangis tanpa alasan, memukul/menggigit');
            $table->enum('hiperaktif', ['Ya', 'Tidak'])->nullable()->comment('Apakah anak memiliki kondisi hiperaktif seperti tidak bisa duduk tenang, selalu bergerak, emosi meledak-ledak');
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
            // Hapus kolom perkembangan 3-6 tahun
            $table->dropColumn([
                'gangguan_emosi',
                'hiperaktif'
            ]);
        });
    }
};

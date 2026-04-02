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
            // Drop columns that are no longer needed since form only has disability status question
            $table->dropColumn([
                'pendidikan_anak',
                'tinggal_dengan_ortu',
                'pengasuh_utama',
                'jumlah_saudara',
                'alamat_anak'
            ]);
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
            // Re-add the dropped columns
            $table->enum('pendidikan_anak', ['Belum sekolah', 'PAUD/TK', 'SD/MI', 'SMP/MTs', 'SMA/SMK/MA'])->nullable()->comment('Tingkat pendidikan anak');
            $table->enum('tinggal_dengan_ortu', ['Ya', 'Tidak'])->nullable()->comment('Apakah anak tinggal dengan orang tua');
            $table->enum('pengasuh_utama', ['Ibu kandung', 'Ayah kandung', 'Kakek/Nenek', 'Saudara', 'Lainnya'])->nullable()->comment('Pengasuh utama anak');
            $table->enum('jumlah_saudara', ['0 (anak tunggal)', '1 saudara', '2 saudara', '3 atau lebih'])->nullable()->comment('Jumlah saudara kandung');
            $table->text('alamat_anak')->nullable()->comment('Alamat tempat tinggal anak');
        });
    }
};
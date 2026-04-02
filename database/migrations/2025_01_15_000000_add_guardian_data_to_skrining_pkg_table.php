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
            // Tambahkan kolom data wali untuk anak di bawah 5 tahun
            $table->string('nik_wali', 25)->nullable()->after('no_handphone');
            $table->string('nama_wali', 100)->nullable()->after('nik_wali');
            $table->date('tanggal_lahir_wali')->nullable()->after('nama_wali');
            $table->enum('jenis_kelamin_wali', ['L', 'P'])->nullable()->after('tanggal_lahir_wali');
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
            $table->dropColumn([
                'nik_wali',
                'nama_wali', 
                'tanggal_lahir_wali',
                'jenis_kelamin_wali'
            ]);
        });
    }
};
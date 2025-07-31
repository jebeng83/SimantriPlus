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
        Schema::table('data_siswa_sekolah', function (Blueprint $table) {
            $table->enum('status_siswa', ['Aktif', 'Pindah', 'Lulus', 'Drop Out'])->default('Aktif')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_siswa_sekolah', function (Blueprint $table) {
            $table->dropColumn('status_siswa');
        });
    }
};

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
        Schema::table('data_ibu_hamil', function (Blueprint $table) {
            // Tambahkan kolom data_posyandu setelah kolom desa
            if (!Schema::hasColumn('data_ibu_hamil', 'data_posyandu')) {
                $table->string('data_posyandu')->after('desa')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_ibu_hamil', function (Blueprint $table) {
            // Hapus kolom data_posyandu jika ada
            if (Schema::hasColumn('data_ibu_hamil', 'data_posyandu')) {
                $table->dropColumn('data_posyandu');
            }
        });
    }
};

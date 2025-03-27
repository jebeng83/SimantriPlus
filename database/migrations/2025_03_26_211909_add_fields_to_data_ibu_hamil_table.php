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
            // Menambahkan kolom no_rkm_medis setelah kolom nik
            $table->string('no_rkm_medis')->after('nik')->nullable();
            
            // Menambahkan kolom status setelah kolom pekerjaan
            $table->enum('status', ['Hamil', 'Melahirkan', 'Abortus'])->after('pekerjaan')->default('Hamil');
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
            $table->dropColumn('no_rkm_medis');
            $table->dropColumn('status');
        });
    }
};

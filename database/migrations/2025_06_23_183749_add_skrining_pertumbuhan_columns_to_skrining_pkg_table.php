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
            // Kolom untuk Skrining Pertumbuhan - Balita dan Anak Prasekolah
            $table->decimal('berat_badan_balita', 5, 2)->nullable()->comment('Berat badan balita dalam kg');
            $table->integer('tinggi_badan_balita')->nullable()->comment('Tinggi badan balita dalam cm');
            $table->string('status_gizi_bb_u')->nullable()->comment('Status gizi BB/U (1-5 tahun)');
            $table->string('status_gizi_pb_u')->nullable()->comment('Status gizi PB/U atau TB/U (1-5 tahun)');
            $table->string('status_gizi_bb_pb')->nullable()->comment('Status gizi BB/PB atau BB/TB (1-5 tahun)');
            $table->string('hasil_imt_u')->nullable()->comment('Hasil IMT/U');
            $table->string('status_lingkar_kepala')->nullable()->comment('Status lingkar kepala: Normal, Makrosefali, Mikrosefali');
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
            // Drop kolom skrining pertumbuhan
            $table->dropColumn([
                'berat_badan_balita',
                'tinggi_badan_balita',
                'status_gizi_bb_u',
                'status_gizi_pb_u',
                'status_gizi_bb_pb',
                'hasil_imt_u',
                'status_lingkar_kepala'
            ]);
        });
    }
};

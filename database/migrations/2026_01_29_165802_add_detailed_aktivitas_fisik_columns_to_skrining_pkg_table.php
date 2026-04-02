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
            // Aktifitas Fisik 2: Tempat Kerja Sedang
            $table->string('aktivitas_fisik_2')->nullable()->after('frekuensi_olahraga_2')->comment('Aktifitas fisik sedang tempat kerja');
            $table->integer('aktivitas_fisik_2_hari')->nullable()->after('aktivitas_fisik_2');
            $table->integer('aktivitas_fisik_2_menit')->nullable()->after('aktivitas_fisik_2_hari');

            // Aktifitas Fisik 3: Perjalanan
            $table->string('aktivitas_fisik_3')->nullable()->after('aktivitas_fisik_2_menit')->comment('Aktifitas fisik perjalanan');
            $table->integer('aktivitas_fisik_3_hari')->nullable()->after('aktivitas_fisik_3');
            $table->integer('aktivitas_fisik_3_menit')->nullable()->after('aktivitas_fisik_3_hari');

            // Aktifitas Fisik 4: Olahraga Sedang
            $table->string('aktivitas_fisik_4')->nullable()->after('aktivitas_fisik_3_menit')->comment('Olahraga intensitas sedang');
            $table->integer('aktivitas_fisik_4_hari')->nullable()->after('aktivitas_fisik_4');
            $table->integer('aktivitas_fisik_4_menit')->nullable()->after('aktivitas_fisik_4_hari');

            // Aktifitas Fisik 5: Tempat Kerja Berat
            $table->string('aktivitas_fisik_5')->nullable()->after('aktivitas_fisik_4_menit')->comment('Aktifitas fisik berat tempat kerja');
            $table->integer('aktivitas_fisik_5_hari')->nullable()->after('aktivitas_fisik_5');
            $table->integer('aktivitas_fisik_5_menit')->nullable()->after('aktivitas_fisik_5_hari');

            // Aktifitas Fisik 6: Olahraga Berat
            $table->string('aktivitas_fisik_6')->nullable()->after('aktivitas_fisik_5_menit')->comment('Olahraga intensitas berat');
            $table->integer('aktivitas_fisik_6_hari')->nullable()->after('aktivitas_fisik_6');
            $table->integer('aktivitas_fisik_6_menit')->nullable()->after('aktivitas_fisik_6_hari');
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
                'aktivitas_fisik_2', 'aktivitas_fisik_2_hari', 'aktivitas_fisik_2_menit',
                'aktivitas_fisik_3', 'aktivitas_fisik_3_hari', 'aktivitas_fisik_3_menit',
                'aktivitas_fisik_4', 'aktivitas_fisik_4_hari', 'aktivitas_fisik_4_menit',
                'aktivitas_fisik_5', 'aktivitas_fisik_5_hari', 'aktivitas_fisik_5_menit',
                'aktivitas_fisik_6', 'aktivitas_fisik_6_hari', 'aktivitas_fisik_6_menit',
            ]);
        });
    }
};

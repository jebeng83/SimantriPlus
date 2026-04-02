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
            $table->integer('frekuensi_olahraga_1')->length(2)->nullable()->after('durasi_olahraga')->comment('Jumlah hari olahraga dalam seminggu');
            $table->integer('frekuensi_olahraga_2')->length(3)->nullable()->after('frekuensi_olahraga_1')->comment('Durasi olahraga dalam menit per hari');
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
            $table->dropColumn(['frekuensi_olahraga_1', 'frekuensi_olahraga_2']);
        });
    }
};

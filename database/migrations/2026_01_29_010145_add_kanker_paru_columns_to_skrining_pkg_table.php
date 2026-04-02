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
            $table->string('kanker_paru_1', 10)->nullable()->after('faktor_resiko_tb')->comment('Merokok setahun terakhir');
            $table->string('kanker_paru_2', 10)->nullable()->after('kanker_paru_1')->comment('Terpapar asap rokok');
            $table->string('kanker_paru_3', 10)->nullable()->after('kanker_paru_2')->comment('Riwayat TBC atau PPOK');
            $table->string('kanker_paru_4', 10)->nullable()->after('kanker_paru_3')->comment('Gejala batuk lama/berdarah/sesak dll');
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
            $table->dropColumn(['kanker_paru_1', 'kanker_paru_2', 'kanker_paru_3', 'kanker_paru_4']);
        });
    }
};

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
            $table->string('usia_ibu', 25)->nullable()->after('jumlah_janin');
            $table->string('jumlah_anak_hidup', 3)->nullable()->after('usia_ibu');
            $table->string('riwayat_keguguran', 3)->default('0')->after('jumlah_anak_hidup');
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
            $table->dropColumn('usia_ibu');
            $table->dropColumn('jumlah_anak_hidup');
            $table->dropColumn('riwayat_keguguran');
        });
    }
};

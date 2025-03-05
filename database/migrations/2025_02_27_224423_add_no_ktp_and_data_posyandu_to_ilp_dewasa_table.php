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
        Schema::table('ilp_dewasa', function (Blueprint $table) {
            $table->string('no_ktp', 20)->nullable()->after('no_rkm_medis');
            $table->string('data_posyandu', 100)->nullable()->after('no_ktp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ilp_dewasa', function (Blueprint $table) {
            $table->dropColumn('no_ktp');
            $table->dropColumn('data_posyandu');
        });
    }
};

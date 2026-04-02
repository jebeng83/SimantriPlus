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
            $table->string('kanker_usus_1', 10)->nullable()->after('hubungan_intim')->comment('Riwayat keluarga kanker usus');
            $table->string('kanker_usus_2', 10)->nullable()->after('kanker_usus_1')->comment('Status merokok untuk kanker usus');
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
            $table->dropColumn(['kanker_usus_1', 'kanker_usus_2']);
        });
    }
};

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
            $table->enum('status_pemeriksaan', ['Menunggu', 'Dalam Proses', 'Selesai', 'Sudah Diambil'])->nullable()->after('skilas');
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
            $table->dropColumn('status_pemeriksaan');
        });
    }
};

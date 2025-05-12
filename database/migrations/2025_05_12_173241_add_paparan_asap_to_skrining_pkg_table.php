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
            // Cek apakah kolom sudah ada
            if (!Schema::hasColumn('skrining_pkg', 'paparan_asap')) {
                $table->enum('paparan_asap', ['Ya', 'Tidak'])->nullable()->after('jumlah_rokok');
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
        Schema::table('skrining_pkg', function (Blueprint $table) {
            if (Schema::hasColumn('skrining_pkg', 'paparan_asap')) {
                $table->dropColumn('paparan_asap');
            }
        });
    }
};

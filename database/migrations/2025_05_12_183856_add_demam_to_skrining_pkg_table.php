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
            // Tambahkan kolom demam jika belum ada
            if (!Schema::hasColumn('skrining_pkg', 'demam')) {
                $table->enum('demam', ['Ya', 'Tidak'])->nullable()->after('batuk');
            }
            
            // Tambahkan kolom batuk jika belum ada
            if (!Schema::hasColumn('skrining_pkg', 'batuk')) {
                $table->enum('batuk', ['Ya', 'Tidak'])->nullable()->after('riwayat_merokok');
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
            // Hapus kolom demam jika ada
            if (Schema::hasColumn('skrining_pkg', 'demam')) {
                $table->dropColumn('demam');
            }
            
            // Hapus kolom batuk jika ada
            if (Schema::hasColumn('skrining_pkg', 'batuk')) {
                $table->dropColumn('batuk');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            // Hapus data yang ada terlebih dahulu
            DB::statement('DELETE FROM skrining_pkg');
            
            // Hapus kolom id_pkg lama jika ada
            if (Schema::hasColumn('skrining_pkg', 'id_pkg')) {
                $table->dropColumn('id_pkg');
            }
        });
        
        // Buat ulang kolom id_pkg dengan auto increment
        Schema::table('skrining_pkg', function (Blueprint $table) {
            $table->bigIncrements('id_pkg')->first();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Tidak perlu implementasi down karena berbahaya untuk menghapus primary key
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('skrining_pkg', function (Blueprint $table) {
            if (!Schema::hasColumn('skrining_pkg', 'jumlah_karies')) {
                $table->enum('jumlah_karies', ['Tidak ada', '1', '2', '3', '> 3'])
                    ->nullable()
                    ->after('goyang')
                    ->comment('Jumlah gigi karies');
            }
        });
    }

    public function down()
    {
        Schema::table('skrining_pkg', function (Blueprint $table) {
            if (Schema::hasColumn('skrining_pkg', 'jumlah_karies')) {
                $table->dropColumn('jumlah_karies');
            }
        });
    }
};


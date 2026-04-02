<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('skrining_pkg', function (Blueprint $table) {
            if (!Schema::hasColumn('skrining_pkg', 'kode_posyandu')) {
                $table->string('kode_posyandu', 100)->nullable()->after('no_handphone');
            }
            if (!Schema::hasColumn('skrining_pkg', 'petugas_entri')) {
                $table->string('petugas_entri', 100)->nullable()->after('kode_posyandu');
            }
            if (!Schema::hasColumn('skrining_pkg', 'status_petugas')) {
                $table->enum('status_petugas', ['Pegawai', 'Kader', 'Mahasiswa', 'Lainnya'])->nullable()->after('petugas_entri');
            }
        });
    }

    public function down()
    {
        Schema::table('skrining_pkg', function (Blueprint $table) {
            if (Schema::hasColumn('skrining_pkg', 'status_petugas')) {
                $table->dropColumn('status_petugas');
            }
            if (Schema::hasColumn('skrining_pkg', 'petugas_entri')) {
                $table->dropColumn('petugas_entri');
            }
            if (Schema::hasColumn('skrining_pkg', 'kode_posyandu')) {
                $table->dropColumn('kode_posyandu');
            }
        });
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('skrining_pkg', 'status_petugas')) {
            DB::statement("ALTER TABLE skrining_pkg MODIFY COLUMN status_petugas VARCHAR(100) NULL");
        }
    }

    public function down()
    {
        if (Schema::hasColumn('skrining_pkg', 'status_petugas')) {
            DB::statement("ALTER TABLE skrining_pkg MODIFY COLUMN status_petugas ENUM('Pegawai','Kader','Mahasiswa','Lainnya') NULL");
        }
    }
};


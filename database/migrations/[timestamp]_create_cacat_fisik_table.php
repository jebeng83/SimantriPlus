<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Cek apakah tabel sudah ada
        if (!Schema::hasTable('cacat_fisik')) {
            Schema::create('cacat_fisik', function (Blueprint $table) {
                $table->id();
                $table->string('nama_cacat');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('cacat_fisik');
    }
}; 
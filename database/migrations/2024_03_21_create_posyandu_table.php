<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Cek apakah tabel sudah ada
        if (!Schema::hasTable('data_posyandu')) {
            Schema::create('data_posyandu', function (Blueprint $table) {
                $table->id();
                $table->string('thn')->nullable();
                $table->string('kode_posyandu')->nullable();
                $table->string('nama_posyandu');
                $table->text('alamat')->nullable();
                $table->string('desa')->nullable();
                $table->string('no_telp')->nullable();
                $table->integer('jumlah_kader')->nullable();
                $table->integer('jumlah_kk')->nullable();
                $table->integer('jumlah_bumil')->nullable();
                $table->integer('jumlah_balita')->nullable();
                $table->integer('jumlah_pra_sekolah')->nullable();
                $table->integer('jumlah_remaja')->nullable();
                $table->integer('jumlah_produktif')->nullable();
                $table->integer('jumlah_lansia')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('data_posyandu');
    }
}; 
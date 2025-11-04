<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kegiatan_ukm', function (Blueprint $table) {
            // Primary key
            $table->bigIncrements('id');

            // Kode kegiatan: 4 digit numeric string (e.g. 0001, 0002)
            $table->char('kode', 4)->unique();

            // Nama/Deskripsi kegiatan
            $table->string('nama_kegiatan', 255)->nullable();
            $table->text('tujuan_kegiatan')->nullable();
            $table->text('sasaran_kegiatan')->nullable();

            // Pelaksana program (jabatan)
            $table->string('kd_jbtn', 20)->nullable();
            $table->string('jabatan', 100)->nullable();

            // Tahun pelaksanaan (wajib)
            $table->integer('tahun');

            // Optional timestamps (frontend hides these fields)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_ukm');
    }
};
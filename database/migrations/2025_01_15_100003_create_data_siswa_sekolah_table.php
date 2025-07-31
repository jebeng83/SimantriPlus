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
        Schema::create('data_siswa_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('nisn', 20)->nullable();
            $table->string('nama_siswa', 100);
            $table->string('nik', 16)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir', 100);
            $table->text('alamat');
            $table->string('nama_ayah', 100)->nullable();
            $table->string('nama_ibu', 100)->nullable();
            $table->string('no_telepon_ortu', 20)->nullable();
            $table->integer('id_sekolah')->unsigned();
            $table->integer('id_kelas')->unsigned();
            $table->date('tanggal_masuk');
            $table->enum('status', ['Aktif', 'Pindah', 'Lulus', 'Drop Out'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_siswa_sekolah');
    }
};
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
        Schema::create('data_kelas', function (Blueprint $table) {
            $table->id('id_kelas');
            $table->foreignId('sekolah_id')->constrained('data_sekolah', 'id_sekolah')->onDelete('cascade');
            $table->string('kelas', 50);
            $table->string('tingkat', 10); // 1, 2, 3, 4, 5, 6 untuk SD
            $table->string('wali_kelas', 100)->nullable();
            $table->integer('jumlah_siswa')->default(0);
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
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
        Schema::dropIfExists('data_kelas');
    }
};
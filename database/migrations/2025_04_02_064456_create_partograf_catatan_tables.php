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
        // Tabel untuk menyimpan catatan persalinan umum
        Schema::create('partograf_catatan', function (Blueprint $table) {
            $table->id();
            $table->string('id_hamil', 20)->index();
            $table->text('catatan')->nullable();
            $table->string('petugas', 100)->nullable();
            $table->timestamps();
        });

        // Tabel untuk data Denyut Jantung Janin (DJJ)
        Schema::create('partograf_djj', function (Blueprint $table) {
            $table->id();
            $table->string('id_hamil', 20)->index();
            $table->integer('jam');
            $table->integer('nilai');
            $table->timestamps();
        });

        // Tabel untuk data Dilatasi Serviks
        Schema::create('partograf_dilatasi', function (Blueprint $table) {
            $table->id();
            $table->string('id_hamil', 20)->index();
            $table->integer('jam');
            $table->integer('nilai');
            $table->string('ketuban', 10)->nullable();
            $table->timestamps();
        });

        // Tabel untuk data Ketuban
        Schema::create('partograf_ketuban', function (Blueprint $table) {
            $table->id();
            $table->string('id_hamil', 20)->index();
            $table->integer('jam');
            $table->string('kode', 10);
            $table->timestamps();
        });

        // Tabel untuk data Kontraksi
        Schema::create('partograf_kontraksi', function (Blueprint $table) {
            $table->id();
            $table->string('id_hamil', 20)->index();
            $table->integer('jam');
            $table->integer('nilai');
            $table->integer('durasi');
            $table->timestamps();
        });

        // Tabel untuk data Tekanan Darah
        Schema::create('partograf_tensi', function (Blueprint $table) {
            $table->id();
            $table->string('id_hamil', 20)->index();
            $table->integer('jam');
            $table->integer('sistole');
            $table->integer('diastole');
            $table->timestamps();
        });

        // Tabel untuk data Nadi
        Schema::create('partograf_nadi', function (Blueprint $table) {
            $table->id();
            $table->string('id_hamil', 20)->index();
            $table->integer('jam');
            $table->integer('nilai');
            $table->timestamps();
        });

        // Tabel untuk data Suhu
        Schema::create('partograf_suhu', function (Blueprint $table) {
            $table->id();
            $table->string('id_hamil', 20)->index();
            $table->integer('jam');
            $table->decimal('nilai', 4, 1);
            $table->timestamps();
        });

        // Tabel untuk data Volume Urine
        Schema::create('partograf_volume', function (Blueprint $table) {
            $table->id();
            $table->string('id_hamil', 20)->index();
            $table->integer('jam');
            $table->integer('nilai');
            $table->timestamps();
        });

        // Tabel untuk data Obat
        Schema::create('partograf_obat', function (Blueprint $table) {
            $table->id();
            $table->string('id_hamil', 20)->index();
            $table->integer('jam');
            $table->text('detail');
            $table->timestamps();
        });

        // Tabel gabungan untuk tanda vital (untuk tampilan di UI)
        Schema::create('partograf_tanda_vital', function (Blueprint $table) {
            $table->id();
            $table->string('id_hamil', 20)->index();
            $table->integer('jam');
            $table->integer('sistole')->nullable();
            $table->integer('diastole')->nullable();
            $table->integer('nadi')->nullable();
            $table->decimal('suhu', 4, 1)->nullable();
            $table->integer('volume')->nullable();
            $table->text('obat')->nullable();
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
        Schema::dropIfExists('partograf_catatan');
        Schema::dropIfExists('partograf_djj');
        Schema::dropIfExists('partograf_dilatasi');
        Schema::dropIfExists('partograf_ketuban');
        Schema::dropIfExists('partograf_kontraksi');
        Schema::dropIfExists('partograf_tensi');
        Schema::dropIfExists('partograf_nadi');
        Schema::dropIfExists('partograf_suhu');
        Schema::dropIfExists('partograf_volume');
        Schema::dropIfExists('partograf_obat');
        Schema::dropIfExists('partograf_tanda_vital');
    }
};

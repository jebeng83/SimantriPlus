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
        Schema::create('partograf_pemantauan_kala4', function (Blueprint $table) {
            $table->id();
            $table->string('id_catatan', 20)->index();
            $table->string('id_hamil', 20)->index();
            $table->integer('jam_ke');
            $table->string('waktu')->nullable();
            $table->string('tekanan_darah')->nullable();
            $table->integer('nadi')->nullable();
            $table->string('tinggi_fundus')->nullable();
            $table->string('kontraksi')->nullable();
            $table->string('kandung_kemih')->nullable();
            $table->string('perdarahan')->nullable();
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
        Schema::dropIfExists('partograf_pemantauan_kala4');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIbuHamilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('data_ibu_hamil')) {
            Schema::create('data_ibu_hamil', function (Blueprint $table) {
                $table->string('id_hamil')->primary();
                $table->string('no_rkm_medis');
                $table->string('nama');
                $table->integer('usia')->nullable();
                $table->integer('usia_kehamilan')->nullable();
                $table->date('tanggal_lahir')->nullable();
                $table->text('alamat')->nullable();
                $table->string('status_kehamilan')->default('Aktif');
                $table->date('HPHT')->nullable();
                $table->date('HPL')->nullable();
                $table->text('catatan')->nullable();
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_ibu_hamil');
    }
}

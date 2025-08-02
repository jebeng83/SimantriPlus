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
        Schema::create('antrean_bpjs_log', function (Blueprint $table) {
            $table->id();
            $table->string('no_rawat', 17);
            $table->string('no_rkm_medis', 15)->nullable();
            $table->string('status', 50);
            $table->text('response')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('antrean_bpjs_log');
    }
};

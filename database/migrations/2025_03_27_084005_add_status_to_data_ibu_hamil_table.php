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
        Schema::table('data_ibu_hamil', function (Blueprint $table) {
            $table->string('status')->after('pekerjaan')->default('Hamil');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_ibu_hamil', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

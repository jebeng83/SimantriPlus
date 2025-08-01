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
        Schema::table('data_kelas', function (Blueprint $table) {
            // Drop the sekolah_id column (no foreign key constraint exists)
            $table->dropColumn('sekolah_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_kelas', function (Blueprint $table) {
            // Add back the sekolah_id column
            $table->unsignedBigInteger('sekolah_id')->after('id_kelas');
            // Add back the foreign key constraint
            $table->foreign('sekolah_id')->references('id_sekolah')->on('data_sekolah')->onDelete('cascade');
        });
    }
};

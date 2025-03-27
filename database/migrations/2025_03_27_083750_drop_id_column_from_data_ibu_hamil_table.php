<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->dropColumn('id');
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
            // Tambahkan kolom id kembali
            $table->bigIncrements('id')->first();
            
            // Isi data id
            $counter = 1;
            $records = DB::table('data_ibu_hamil')->get();
            
            foreach ($records as $record) {
                DB::table('data_ibu_hamil')
                    ->where('id_hamil', $record->id_hamil)
                    ->update(['id' => $counter]);
                $counter++;
            }
        });
    }
};

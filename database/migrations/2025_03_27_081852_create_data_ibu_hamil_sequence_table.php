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
        // Pastikan tabel sequence belum ada
        if (!Schema::hasTable('data_ibu_hamil_sequence')) {
            Schema::create('data_ibu_hamil_sequence', function (Blueprint $table) {
                $table->id();
                $table->integer('last_number')->default(1);
                $table->timestamps();
            });
            
            // Hitung jumlah data yang ada di tabel data_ibu_hamil
            $count = DB::table('data_ibu_hamil')->count();
            
            // Set nilai awal sequence sebagai jumlah data yang ada + 1
            DB::table('data_ibu_hamil_sequence')->insert([
                'last_number' => max(1, $count), 
                'created_at' => now(), 
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_ibu_hamil_sequence');
    }
};

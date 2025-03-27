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
        // 1. Tambahkan kolom id_hamil
        Schema::table('data_ibu_hamil', function (Blueprint $table) {
            $table->string('id_hamil', 7)->nullable()->after('id');
        });
        
        // 2. Buat tabel sequence jika belum ada
        if (!Schema::hasTable('data_ibu_hamil_sequence')) {
            Schema::create('data_ibu_hamil_sequence', function (Blueprint $table) {
                $table->id();
                $table->integer('last_number')->default(1);
                $table->timestamps();
            });
            
            // Inisialisasi sequence
            DB::table('data_ibu_hamil_sequence')->insert([
                'last_number' => 1, 
                'created_at' => now(), 
                'updated_at' => now()
            ]);
        }
        
        // 3. Isi data id_hamil untuk semua record yang ada
        $counter = 1;
        $records = DB::table('data_ibu_hamil')->whereNull('id_hamil')->get();
        
        foreach ($records as $record) {
            $idHamil = 'H' . str_pad($counter, 6, '0', STR_PAD_LEFT);
            DB::table('data_ibu_hamil')
                ->where('id', $record->id)
                ->update(['id_hamil' => $idHamil]);
            $counter++;
        }
        
        // 4. Update sequence counter
        if ($counter > 1) {
            DB::table('data_ibu_hamil_sequence')
                ->where('id', 1)
                ->update(['last_number' => $counter - 1]);
        }
        
        // 5. Jadikan id_hamil sebagai primary key
        Schema::table('data_ibu_hamil', function (Blueprint $table) {
            // Pastikan id_hamil tidak null
            $table->string('id_hamil', 7)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 1. Hapus kolom id_hamil
        Schema::table('data_ibu_hamil', function (Blueprint $table) {
            $table->dropColumn('id_hamil');
        });
        
        // 2. Hapus tabel sequence
        Schema::dropIfExists('data_ibu_hamil_sequence');
    }
};

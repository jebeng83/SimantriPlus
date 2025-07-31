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
        // Cek apakah tabel setting sudah ada
        if (!Schema::hasTable('setting')) {
            Schema::create('setting', function (Blueprint $table) {
                $table->id();
                $table->string('variable')->unique();
                $table->text('value')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
            });
            
            // Insert default hospital settings
            DB::table('setting')->insert([
                [
                    'variable' => 'hospital_name',
                    'value' => 'RSUD GUNUNG TUA',
                    'description' => 'Nama Rumah Sakit',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'variable' => 'hospital_address',
                    'value' => 'Jl. Raya Gunung Tua, Kabupaten Padang Lawas Utara, Sumatera Utara',
                    'description' => 'Alamat Rumah Sakit',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'variable' => 'hospital_phone',
                    'value' => '(0634) 123456',
                    'description' => 'Nomor Telepon Rumah Sakit',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'variable' => 'hospital_email',
                    'value' => 'info@rsudgunungua.go.id',
                    'description' => 'Email Rumah Sakit',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
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
        Schema::dropIfExists('setting');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('skrining_pkg', function (Blueprint $table) {
            if (!Schema::hasColumn('skrining_pkg', 'posisi_ukur')) {
                $table->enum('posisi_ukur', ['Berdiri', 'Terlentang'])
                    ->nullable()
                    ->after('tinggi_badan_balita')
                    ->comment('Posisi saat pengukuran tinggi badan balita');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skrining_pkg', function (Blueprint $table) {
            if (Schema::hasColumn('skrining_pkg', 'posisi_ukur')) {
                $table->dropColumn('posisi_ukur');
            }
        });
    }
};


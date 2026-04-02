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
        Schema::table('skrining_pkg', function (Blueprint $table) {
            if (!Schema::hasColumn('skrining_pkg', 'imunisasi_inti')) {
                $table->enum('imunisasi_inti', ['Ya', 'Tidak'])
                    ->nullable()
                    ->comment('Apakah anak memperoleh imunisasi usia 0-24 bulan');
            }

            if (!Schema::hasColumn('skrining_pkg', 'imunisasi_lanjutan')) {
                $table->enum('imunisasi_lanjutan', ['Ya', 'Tidak'])
                    ->nullable()
                    ->after('imunisasi_inti')
                    ->comment('Apakah punya catatan/mengingat riwayat imunisasi');
            }

            // Q3 - Q20 (18 item): Sudah / Belum
            for ($i = 1; $i <= 18; $i++) {
                $col = "imunisasi_lanjutan_{$i}";
                if (!Schema::hasColumn('skrining_pkg', $col)) {
                    $table->enum($col, ['Sudah', 'Belum'])
                        ->nullable()
                        ->after($i === 1 ? 'imunisasi_lanjutan' : "imunisasi_lanjutan_" . ($i - 1));
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skrining_pkg', function (Blueprint $table) {
            $cols = ['imunisasi_inti', 'imunisasi_lanjutan'];
            for ($i = 1; $i <= 18; $i++) {
                $cols[] = "imunisasi_lanjutan_{$i}";
            }

            $existing = array_values(array_filter($cols, function ($c) {
                return Schema::hasColumn('skrining_pkg', $c);
            }));

            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }
};


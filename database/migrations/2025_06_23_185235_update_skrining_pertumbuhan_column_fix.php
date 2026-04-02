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
            // Check if gangguan_pertumbuhan column exists and drop it
            if (Schema::hasColumn('skrining_pkg', 'gangguan_pertumbuhan')) {
                $table->dropColumn('gangguan_pertumbuhan');
            }
            
            // Add status_lingkar_kepala column if it doesn't exist
            if (!Schema::hasColumn('skrining_pkg', 'status_lingkar_kepala')) {
                $table->string('status_lingkar_kepala')->nullable()->comment('Status lingkar kepala: Normal, Makrosefali, Mikrosefali');
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
            // Drop status_lingkar_kepala column if it exists
            if (Schema::hasColumn('skrining_pkg', 'status_lingkar_kepala')) {
                $table->dropColumn('status_lingkar_kepala');
            }
            
            // Add back gangguan_pertumbuhan column
            if (!Schema::hasColumn('skrining_pkg', 'gangguan_pertumbuhan')) {
                $table->enum('gangguan_pertumbuhan', ['Ya', 'Tidak'])->nullable()->comment('Gangguan pertumbuhan atau perkembangan');
            }
        });
    }
};

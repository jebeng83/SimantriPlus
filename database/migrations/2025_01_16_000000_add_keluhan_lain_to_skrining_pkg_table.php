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
            // Add keluhan_lain column if it doesn't exist
            if (!Schema::hasColumn('skrining_pkg', 'keluhan_lain')) {
                $table->text('keluhan_lain')->nullable()->after('updated_at');
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
            // Drop keluhan_lain column if it exists
            if (Schema::hasColumn('skrining_pkg', 'keluhan_lain')) {
                $table->dropColumn('keluhan_lain');
            }
        });
    }
};
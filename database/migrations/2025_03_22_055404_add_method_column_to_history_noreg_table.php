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
        Schema::table('history_noreg', function (Blueprint $table) {
            if (!Schema::hasColumn('history_noreg', 'method')) {
                $table->string('method', 50)->nullable()->after('kd_poli')->comment('Metode yang digunakan untuk generate no_reg');
            }
            
            if (!Schema::hasColumn('history_noreg', 'created_by')) {
                $table->string('created_by', 50)->nullable()->after('method')->comment('User yang membuat record');
            }
            
            if (!Schema::hasColumn('history_noreg', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('created_by')->useCurrent()->comment('Waktu pembuatan record');
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
        Schema::table('history_noreg', function (Blueprint $table) {
            if (Schema::hasColumn('history_noreg', 'method')) {
                $table->dropColumn('method');
            }
            
            if (Schema::hasColumn('history_noreg', 'created_by')) {
                $table->dropColumn('created_by');
            }
            
            if (Schema::hasColumn('history_noreg', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
};

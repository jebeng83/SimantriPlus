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
        // Add indexes for reg_periksa table to optimize joins and filters
        // Check if indexes exist before creating them
        
        // Add composite index for common filter combinations (most important)
        if (!$this->indexExists('reg_periksa', 'idx_reg_periksa_composite')) {
            Schema::table('reg_periksa', function (Blueprint $table) {
                $table->index(['tgl_registrasi', 'kd_poli', 'kd_dokter'], 'idx_reg_periksa_composite');
            });
        }
        
        // Add index for date filtering and ordering if not exists
        if (!$this->indexExists('reg_periksa', 'idx_reg_periksa_tgl_registrasi')) {
            Schema::table('reg_periksa', function (Blueprint $table) {
                $table->index('tgl_registrasi', 'idx_reg_periksa_tgl_registrasi');
            });
        }
        
        // Add index for status filtering if not exists
        if (!$this->indexExists('reg_periksa', 'idx_reg_periksa_stts')) {
            Schema::table('reg_periksa', function (Blueprint $table) {
                $table->index('stts', 'idx_reg_periksa_stts');
            });
        }
    }
    
    /**
     * Check if an index exists on a table
     */
    private function indexExists($table, $indexName)
     {
         $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
         return count($indexes) > 0;
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reg_periksa', function (Blueprint $table) {
            $table->dropIndex('idx_reg_periksa_no_rkm_medis');
            $table->dropIndex('idx_reg_periksa_kd_dokter');
            $table->dropIndex('idx_reg_periksa_kd_poli');
            $table->dropIndex('idx_reg_periksa_kd_pj');
            $table->dropIndex('idx_reg_periksa_tgl_registrasi');
            $table->dropIndex('idx_reg_periksa_stts');
            $table->dropIndex('idx_reg_periksa_composite');
        });
        
        Schema::table('pasien', function (Blueprint $table) {
            $table->dropIndex('idx_pasien_no_rkm_medis');
        });
        
        Schema::table('dokter', function (Blueprint $table) {
            $table->dropIndex('idx_dokter_kd_dokter');
        });
        
        Schema::table('poliklinik', function (Blueprint $table) {
            $table->dropIndex('idx_poliklinik_kd_poli');
        });
        
        Schema::table('penjab', function (Blueprint $table) {
            $table->dropIndex('idx_penjab_kd_pj');
        });
    }
};

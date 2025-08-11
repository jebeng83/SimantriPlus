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
        Schema::table('reg_periksa', function (Blueprint $table) {
            // Composite index untuk filter utama (stts + tgl_registrasi)
            if (!$this->indexExists('reg_periksa', 'idx_reg_periksa_stts_tgl')) {
                $table->index(['stts', 'tgl_registrasi'], 'idx_reg_periksa_stts_tgl');
            }
            
            // Composite index untuk filter dengan poliklinik
            if (!$this->indexExists('reg_periksa', 'idx_reg_periksa_stts_tgl_poli')) {
                $table->index(['stts', 'tgl_registrasi', 'kd_poli'], 'idx_reg_periksa_stts_tgl_poli');
            }
            
            // Composite index untuk filter dengan dokter
            if (!$this->indexExists('reg_periksa', 'idx_reg_periksa_stts_tgl_dokter')) {
                $table->index(['stts', 'tgl_registrasi', 'kd_dokter'], 'idx_reg_periksa_stts_tgl_dokter');
            }
            
            // Index untuk sorting
            if (!$this->indexExists('reg_periksa', 'idx_reg_periksa_tgl_jam')) {
                $table->index(['tgl_registrasi', 'jam_reg'], 'idx_reg_periksa_tgl_jam');
            }
        });
        
        // Pastikan foreign key indexes ada untuk JOIN operations
        Schema::table('reg_periksa', function (Blueprint $table) {
            // Index untuk JOIN dengan pasien (jika belum ada)
            if (!$this->indexExists('reg_periksa', 'idx_reg_periksa_no_rkm_medis')) {
                $table->index('no_rkm_medis', 'idx_reg_periksa_no_rkm_medis');
            }
            
            // Index untuk JOIN dengan dokter (jika belum ada)
            if (!$this->indexExists('reg_periksa', 'idx_reg_periksa_kd_dokter')) {
                $table->index('kd_dokter', 'idx_reg_periksa_kd_dokter');
            }
            
            // Index untuk JOIN dengan poliklinik (jika belum ada)
            if (!$this->indexExists('reg_periksa', 'idx_reg_periksa_kd_poli')) {
                $table->index('kd_poli', 'idx_reg_periksa_kd_poli');
            }
            
            // Index untuk JOIN dengan penjab (jika belum ada)
            if (!$this->indexExists('reg_periksa', 'idx_reg_periksa_kd_pj')) {
                $table->index('kd_pj', 'idx_reg_periksa_kd_pj');
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
        Schema::table('reg_periksa', function (Blueprint $table) {
            // Drop composite indexes (hanya jika ada)
            if ($this->indexExists('reg_periksa', 'idx_reg_periksa_stts_tgl')) {
                $table->dropIndex('idx_reg_periksa_stts_tgl');
            }
            if ($this->indexExists('reg_periksa', 'idx_reg_periksa_stts_tgl_poli')) {
                $table->dropIndex('idx_reg_periksa_stts_tgl_poli');
            }
            if ($this->indexExists('reg_periksa', 'idx_reg_periksa_stts_tgl_dokter')) {
                $table->dropIndex('idx_reg_periksa_stts_tgl_dokter');
            }
            if ($this->indexExists('reg_periksa', 'idx_reg_periksa_tgl_jam')) {
                $table->dropIndex('idx_reg_periksa_tgl_jam');
            }
            
            // Drop foreign key indexes (hanya jika dibuat oleh migration ini)
            if ($this->indexExists('reg_periksa', 'idx_reg_periksa_no_rkm_medis')) {
                $table->dropIndex('idx_reg_periksa_no_rkm_medis');
            }
            if ($this->indexExists('reg_periksa', 'idx_reg_periksa_kd_dokter')) {
                $table->dropIndex('idx_reg_periksa_kd_dokter');
            }
            if ($this->indexExists('reg_periksa', 'idx_reg_periksa_kd_poli')) {
                $table->dropIndex('idx_reg_periksa_kd_poli');
            }
            if ($this->indexExists('reg_periksa', 'idx_reg_periksa_kd_pj')) {
                $table->dropIndex('idx_reg_periksa_kd_pj');
            }
        });
    }
    
    /**
     * Check if index exists using raw SQL
     *
     * @param string $table
     * @param string $index
     * @return bool
     */
    private function indexExists($table, $index)
    {
        $connection = Schema::getConnection();
        $indexes = $connection->select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);
        
        return count($indexes) > 0;
    }
};
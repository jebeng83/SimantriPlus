<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah semua data sudah memiliki id_anc
        $nullCount = DB::table('pemeriksaan_anc')
            ->whereNull('id_anc')
            ->count();
            
        if ($nullCount > 0) {
            // Generate id_anc untuk data yang belum memiliki id_anc
            $pemeriksaanAnc = DB::table('pemeriksaan_anc')
                ->whereNull('id_anc')
                ->get();
                
            foreach ($pemeriksaanAnc as $pemeriksaan) {
                $id_anc = $this->generateIdAnc();
                DB::table('pemeriksaan_anc')
                    ->where('id', $pemeriksaan->id)
                    ->update(['id_anc' => $id_anc]);
            }
        }
        
        // Pastikan bahwa id_anc adalah not null
        Schema::table('pemeriksaan_anc', function (Blueprint $table) {
            if (Schema::hasColumn('pemeriksaan_anc', 'id_anc')) {
                $table->string('id_anc', 7)->nullable(false)->change();
            }
        });
        
        // Periksa apakah unique constraint sudah ada, jika belum maka tambahkan
        $hasUniqueConstraint = DB::select("SHOW INDEX FROM pemeriksaan_anc WHERE Key_name = 'pemeriksaan_anc_id_anc_unique'");
        
        if (count($hasUniqueConstraint) === 0) {
            // Unique constraint belum ada, tambahkan
            Schema::table('pemeriksaan_anc', function (Blueprint $table) {
                $table->unique('id_anc');
            });
        }
        
        // Tambahkan kolom-kolom yang dibutuhkan oleh form
        $this->addMissingColumns();
        
        // Mencoba mengubah primary key dengan cara yang lebih aman
        try {
            $isPrimaryKey = DB::select("SHOW KEYS FROM pemeriksaan_anc WHERE Key_name = 'PRIMARY'");
            
            if (count($isPrimaryKey) > 0) {
                // Periksa kolom apa yang menjadi primary key saat ini
                $currentPrimaryColumn = $isPrimaryKey[0]->Column_name;
                
                if ($currentPrimaryColumn !== 'id_anc') {
                    // Drop primary key yang ada dan tambahkan primary key baru
                    DB::statement('ALTER TABLE pemeriksaan_anc DROP PRIMARY KEY, ADD PRIMARY KEY (id_anc)');
                }
            } else {
                // Tidak ada primary key, tambahkan langsung
                DB::statement('ALTER TABLE pemeriksaan_anc ADD PRIMARY KEY (id_anc)');
            }
        } catch (\Exception $e) {
            // Jika gagal, log error tetapi tetap lanjutkan migrasi
            Log::error('Gagal mengubah primary key: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mencoba mengembalikan primary key ke id jika kolom id masih ada
        try {
            if (Schema::hasColumn('pemeriksaan_anc', 'id')) {
                $isPrimaryKey = DB::select("SHOW KEYS FROM pemeriksaan_anc WHERE Key_name = 'PRIMARY'");
                
                if (count($isPrimaryKey) > 0) {
                    // Drop primary key yang ada
                    DB::statement('ALTER TABLE pemeriksaan_anc DROP PRIMARY KEY');
                }
                
                // Tambahkan primary key pada kolom id
                DB::statement('ALTER TABLE pemeriksaan_anc ADD PRIMARY KEY (id)');
            }
        } catch (\Exception $e) {
            // Jika gagal, log error
            Log::error('Gagal mengembalikan primary key: ' . $e->getMessage());
        }
        
        // Kembalikan id_anc menjadi nullable
        Schema::table('pemeriksaan_anc', function (Blueprint $table) {
            if (Schema::hasColumn('pemeriksaan_anc', 'id_anc')) {
                // Cek apakah unique constraint ada
                $hasUniqueConstraint = DB::select("SHOW INDEX FROM pemeriksaan_anc WHERE Key_name = 'pemeriksaan_anc_id_anc_unique'");
                
                if (count($hasUniqueConstraint) > 0) {
                    // Hapus unique constraint jika ada
                    $table->dropUnique(['id_anc']);
                }
                
                $table->string('id_anc', 7)->nullable()->change();
            }
        });
    }
    
    /**
     * Generate ID ANC baru dengan format ANC+4 angka
     */
    private function generateIdAnc(): string
    {
        // Cari ID terakhir dengan prefix ANC
        $lastId = DB::table('pemeriksaan_anc')
            ->where('id_anc', 'like', 'ANC%')
            ->orderBy('id_anc', 'desc')
            ->value('id_anc');
            
        if ($lastId) {
            // Ambil angka dari ID terakhir
            $lastNumber = (int) substr($lastId, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // Format angka dengan leading zero
        return 'ANC' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Tambahkan kolom-kolom yang mungkin belum ada di tabel
     */
    private function addMissingColumns(): void
    {
        Schema::table('pemeriksaan_anc', function (Blueprint $table) {
            // Tambahkan kolom-kolom yang kurang
            if (!Schema::hasColumn('pemeriksaan_anc', 'keluhan_utama')) {
                $table->text('keluhan_utama')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'gravida')) {
                $table->integer('gravida')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'partus')) {
                $table->integer('partus')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'abortus')) {
                $table->integer('abortus')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'hidup')) {
                $table->integer('hidup')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'riwayat_penyakit')) {
                $table->text('riwayat_penyakit')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'status_gizi')) {
                $table->string('status_gizi', 50)->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'taksiran_berat_janin')) {
                $table->integer('taksiran_berat_janin')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'status_tt')) {
                $table->string('status_tt', 50)->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'tanggal_imunisasi')) {
                $table->date('tanggal_imunisasi')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'tanggal_lab')) {
                $table->date('tanggal_lab')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'lab')) {
                $table->text('lab')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'rujukan_ims')) {
                $table->text('rujukan_ims')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'tindak_lanjut')) {
                $table->string('tindak_lanjut')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'detail_tindak_lanjut')) {
                $table->text('detail_tindak_lanjut')->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'tanggal_kunjungan_berikutnya')) {
                $table->date('tanggal_kunjungan_berikutnya')->nullable();
            }
            
            // Tanda bahaya persalinan (jika belum ada)
            if (!Schema::hasColumn('pemeriksaan_anc', 'tanda_bahaya_persalinan')) {
                $table->enum('tanda_bahaya_persalinan', ['Ya', 'Tidak'])->nullable();
            }
            
            // Kolom untuk Konseling PHBS dan Konseling Gizi
            if (!Schema::hasColumn('pemeriksaan_anc', 'konseling_phbs')) {
                $table->enum('konseling_phbs', ['Ya', 'Tidak'])->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'konseling_gizi')) {
                $table->enum('konseling_gizi', ['Ya', 'Tidak'])->nullable();
            }
            
            if (!Schema::hasColumn('pemeriksaan_anc', 'konseling_ibu_hamil')) {
                $table->enum('konseling_ibu_hamil', ['Ya', 'Tidak'])->nullable();
            }
            
            // Tambahkan kolom presentasi jika belum ada
            if (!Schema::hasColumn('pemeriksaan_anc', 'presentasi')) {
                $table->string('presentasi', 50)->nullable();
            }
            
            // Tambahkan kolom denyut jantung janin jika belum ada
            if (!Schema::hasColumn('pemeriksaan_anc', 'denyut_jantung_janin')) {
                $table->integer('denyut_jantung_janin')->nullable();
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Melakukan perubahan pada tabel pemeriksaan_anc
        Schema::table('pemeriksaan_anc', function (Blueprint $table) {
            // Menghapus kolom id lama jika perubahan dilakukan pada tabel yang sudah ada
            if (Schema::hasColumn('pemeriksaan_anc', 'id')) {
                // Tambahkan kolom id_anc baru
                $table->string('id_anc', 7)->after('id')->nullable();
                
                // Buat indeks untuk id_anc
                $table->index('id_anc');
            }
            
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
        });
        
        // Generate id_anc untuk data yang sudah ada
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_anc', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan
            if (Schema::hasColumn('pemeriksaan_anc', 'id_anc')) {
                $table->dropIndex(['id_anc']);
                $table->dropColumn('id_anc');
            }
            
            // Kita tidak menghapus kolom lain yang ditambahkan
            // untuk mencegah kehilangan data yang sudah ada
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
};

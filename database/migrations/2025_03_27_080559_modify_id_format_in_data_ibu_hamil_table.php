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
        // Inisialisasi counter
        $counter = 1;
        
        // Dapatkan informasi kolom yang ada di tabel data_ibu_hamil
        $columns = Schema::getColumnListing('data_ibu_hamil');
        
        // Periksa apakah kolom id_hamil sudah ada
        if (in_array('id_hamil', $columns)) {
            // Tabel sudah menggunakan format baru, tidak perlu migrasi kolom ID
            // Lanjutkan hanya untuk memastikan tabel sequence ada
        } else {
            // Langkah 1: Buat tabel baru dengan struktur yang diinginkan
            Schema::create('data_ibu_hamil_new', function (Blueprint $table) {
                $table->string('id_hamil', 7)->primary();
                $table->string('nik');
                $table->string('no_rkm_medis');
                $table->string('kehamilan_ke');
                $table->date('tgl_lahir');
                $table->string('nomor_kk');
                $table->string('nama');
                $table->double('berat_badan_sebelum_hamil')->nullable();
                $table->double('tinggi_badan')->nullable();
                $table->double('lila')->nullable();
                $table->double('imt_sebelum_hamil')->nullable();
                $table->string('status_gizi')->nullable();
                $table->string('jumlah_janin')->nullable();
                $table->string('jarak_kehamilan_tahun')->nullable();
                $table->string('jarak_kehamilan_bulan')->nullable();
                $table->date('hari_pertama_haid')->nullable();
                $table->date('hari_perkiraan_lahir')->nullable();
                $table->string('golongan_darah')->nullable();
                $table->string('rhesus')->nullable();
                $table->text('riwayat_penyakit')->nullable();
                $table->text('riwayat_alergi')->nullable();
                $table->boolean('kepemilikan_buku_kia');
                $table->string('jaminan_kesehatan')->nullable();
                $table->string('no_jaminan_kesehatan')->nullable();
                $table->string('faskes_tk1')->nullable();
                $table->string('faskes_rujukan')->nullable();
                $table->string('pendidikan')->nullable();
                $table->string('pekerjaan')->nullable();
                $table->string('status');
                $table->string('nama_suami')->nullable();
                $table->string('nik_suami')->nullable();
                $table->string('telp_suami')->nullable();
                $table->string('provinsi');
                $table->string('kabupaten');
                $table->string('kecamatan');
                $table->string('puskesmas');
                $table->string('desa');
                $table->string('data_posyandu')->nullable();
                $table->text('alamat_lengkap');
                $table->string('rt')->nullable();
                $table->string('rw')->nullable();
                $table->timestamps();
            });

            // Langkah 2: Ambil data dari tabel lama dan masukkan ke tabel baru dengan format ID baru
            $oldRecords = DB::table('data_ibu_hamil')->get();
            
            foreach ($oldRecords as $record) {
                $idHamil = 'H' . str_pad($counter, 6, '0', STR_PAD_LEFT);
                
                // Konversi data record ke array dan ubah id menjadi id_hamil jika ada
                $recordArray = (array) $record;
                
                // Hapus primary key lama jika ada
                if (in_array('id', $columns)) {
                    unset($recordArray['id']);
                }
                
                // Tambahkan id_hamil baru
                $recordArray['id_hamil'] = $idHamil;
                
                DB::table('data_ibu_hamil_new')->insert($recordArray);
                $counter++;
            }

            // Langkah 3: Hapus tabel lama
            Schema::dropIfExists('data_ibu_hamil');

            // Langkah 4: Rename tabel baru menjadi nama tabel lama
            Schema::rename('data_ibu_hamil_new', 'data_ibu_hamil');
        }

        // Langkah 5: Buat tabel untuk sequence counter jika belum ada
        if (!Schema::hasTable('data_ibu_hamil_sequence')) {
            Schema::create('data_ibu_hamil_sequence', function (Blueprint $table) {
                $table->id();
                $table->integer('last_number')->default(1);
            });
            
            // Jika ada data, update sequence counter dengan nilai terakhir
            $count = DB::table('data_ibu_hamil_sequence')->count();
            if ($count == 0) {
                DB::table('data_ibu_hamil_sequence')->insert(['last_number' => max(1, $counter - 1)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Periksa apakah kolom id_hamil ada di tabel
        $columns = Schema::getColumnListing('data_ibu_hamil');
        if (!in_array('id_hamil', $columns)) {
            // Tabel masih menggunakan format lama, tidak perlu rollback
            return;
        }
        
        // Langkah 1: Buat tabel baru dengan struktur asli
        Schema::create('data_ibu_hamil_old', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->string('no_rkm_medis');
            $table->string('kehamilan_ke');
            $table->date('tgl_lahir');
            $table->string('nomor_kk');
            $table->string('nama');
            $table->double('berat_badan_sebelum_hamil')->nullable();
            $table->double('tinggi_badan')->nullable();
            $table->double('lila')->nullable();
            $table->double('imt_sebelum_hamil')->nullable();
            $table->string('status_gizi')->nullable();
            $table->string('jumlah_janin')->nullable();
            $table->string('jarak_kehamilan_tahun')->nullable();
            $table->string('jarak_kehamilan_bulan')->nullable();
            $table->date('hari_pertama_haid')->nullable();
            $table->date('hari_perkiraan_lahir')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('rhesus')->nullable();
            $table->text('riwayat_penyakit')->nullable();
            $table->text('riwayat_alergi')->nullable();
            $table->boolean('kepemilikan_buku_kia');
            $table->string('jaminan_kesehatan')->nullable();
            $table->string('no_jaminan_kesehatan')->nullable();
            $table->string('faskes_tk1')->nullable();
            $table->string('faskes_rujukan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('status');
            $table->string('nama_suami')->nullable();
            $table->string('nik_suami')->nullable();
            $table->string('telp_suami')->nullable();
            $table->string('provinsi');
            $table->string('kabupaten');
            $table->string('kecamatan');
            $table->string('puskesmas');
            $table->string('desa');
            $table->string('data_posyandu')->nullable();
            $table->text('alamat_lengkap');
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->timestamps();
        });

        // Langkah 2: Salin data dari tabel baru ke tabel lama
        $newRecords = DB::table('data_ibu_hamil')->get();
        
        foreach ($newRecords as $record) {
            // Konversi data record ke array dan ubah id_hamil menjadi id
            $recordArray = (array) $record;
            unset($recordArray['id_hamil']); // Hapus id_hamil
            
            DB::table('data_ibu_hamil_old')->insert($recordArray);
        }

        // Langkah 3: Hapus tabel sequence
        Schema::dropIfExists('data_ibu_hamil_sequence');

        // Langkah 4: Hapus tabel baru
        Schema::dropIfExists('data_ibu_hamil');

        // Langkah 5: Rename tabel lama menjadi nama tabel asli
        Schema::rename('data_ibu_hamil_old', 'data_ibu_hamil');
    }
};

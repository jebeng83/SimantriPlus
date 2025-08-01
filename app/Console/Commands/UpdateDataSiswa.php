<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataSiswaSekolah;
use App\Models\DataSekolah;
use App\Models\DataKelas;
use App\Models\Pasien;

class UpdateDataSiswa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'siswa:update 
                            {id : ID siswa yang akan diupdate}
                            {--sekolah= : ID sekolah baru}
                            {--kelas= : ID kelas baru}
                            {--status= : Status siswa (Aktif, Pindah, Lulus, Drop Out)}
                            {--show : Tampilkan data siswa sebelum update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update data siswa sekolah (sekolah, kelas, status)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $siswaId = $this->argument('id');
        $sekolahId = $this->option('sekolah');
        $kelasId = $this->option('kelas');
        $status = $this->option('status');
        $showData = $this->option('show');

        // Cari data siswa
        $siswa = DataSiswaSekolah::with(['sekolah', 'kelas', 'pasien'])->find($siswaId);
        
        if (!$siswa) {
            $this->error("Siswa dengan ID {$siswaId} tidak ditemukan!");
            return 1;
        }

        // Tampilkan data siswa jika diminta
        if ($showData) {
            $this->showSiswaData($siswa);
        }

        // Validasi dan update sekolah
        if ($sekolahId) {
            $sekolah = DataSekolah::find($sekolahId);
            if (!$sekolah) {
                $this->error("Sekolah dengan ID {$sekolahId} tidak ditemukan!");
                return 1;
            }
            $siswa->id_sekolah = $sekolahId;
            $this->info("Sekolah akan diubah ke: {$sekolah->nama_sekolah}");
        }

        // Validasi dan update kelas
        if ($kelasId) {
            $kelas = DataKelas::find($kelasId);
            if (!$kelas) {
                $this->error("Kelas dengan ID {$kelasId} tidak ditemukan!");
                return 1;
            }
            $siswa->id_kelas = $kelasId;
            $this->info("Kelas akan diubah ke: {$kelas->kelas}");
        }

        // Update status
        if ($status) {
            $validStatus = ['Aktif', 'Pindah', 'Lulus', 'Drop Out'];
            if (!in_array($status, $validStatus)) {
                $this->error("Status tidak valid! Gunakan: " . implode(', ', $validStatus));
                return 1;
            }
            $siswa->status_siswa = $status;
            $this->info("Status akan diubah ke: {$status}");
        }

        // Konfirmasi sebelum update
        if (!$this->confirm('Apakah Anda yakin ingin melakukan update?')) {
            $this->info('Update dibatalkan.');
            return 0;
        }

        // Simpan perubahan
        try {
            $siswa->save();
            $this->info('Data siswa berhasil diupdate!');
            
            // Tampilkan data setelah update
            $this->line('');
            $this->info('Data siswa setelah update:');
            $siswa->refresh();
            $this->showSiswaData($siswa);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Gagal mengupdate data siswa: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Tampilkan data siswa
     */
    private function showSiswaData($siswa)
    {
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $siswa->id],
                ['No. RM', $siswa->no_rkm_medis],
                ['NISN', $siswa->nisn ?? '-'],
                ['Nama', $siswa->pasien->nm_pasien ?? '-'],
                ['Jenis Kelamin', $siswa->jenis_kelamin],
                ['ID Sekolah', $siswa->id_sekolah ?? '-'],
                ['ID Kelas', $siswa->id_kelas ?? '-'],
                ['Status', $siswa->status_siswa],
                ['Tanggal Lahir', $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d-m-Y') : '-'],
            ]
        );
    }

    /**
     * Tampilkan daftar sekolah
     */
    public function showSekolahList()
    {
        $sekolah = DataSekolah::all();
        $this->table(
            ['ID', 'Nama Sekolah'],
            $sekolah->map(function($item) {
                return [$item->id_sekolah, $item->nama_sekolah];
            })->toArray()
        );
    }

    /**
     * Tampilkan daftar kelas
     */
    public function showKelasList()
    {
        $kelas = DataKelas::all();
        $this->table(
            ['ID', 'Kelas'],
            $kelas->map(function($item) {
                return [$item->id_kelas, $item->kelas];
            })->toArray()
        );
    }
}
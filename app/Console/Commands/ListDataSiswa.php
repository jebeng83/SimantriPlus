<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataSiswaSekolah;
use App\Models\DataSekolah;
use App\Models\DataKelas;

class ListDataSiswa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'siswa:list 
                            {--type=siswa : Tipe data yang akan ditampilkan (siswa, sekolah, kelas)}
                            {--limit=10 : Jumlah data yang ditampilkan}
                            {--sekolah= : Filter berdasarkan ID sekolah}
                            {--kelas= : Filter berdasarkan ID kelas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tampilkan daftar data siswa, sekolah, atau kelas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $type = $this->option('type');
        $limit = $this->option('limit');
        $sekolahFilter = $this->option('sekolah');
        $kelasFilter = $this->option('kelas');

        switch ($type) {
            case 'siswa':
                $this->showSiswaList($limit, $sekolahFilter, $kelasFilter);
                break;
            case 'sekolah':
                $this->showSekolahList($limit);
                break;
            case 'kelas':
                $this->showKelasList($limit, $sekolahFilter);
                break;
            default:
                $this->error('Tipe tidak valid! Gunakan: siswa, sekolah, atau kelas');
                return 1;
        }

        return 0;
    }

    /**
     * Tampilkan daftar siswa
     */
    private function showSiswaList($limit, $sekolahFilter = null, $kelasFilter = null)
    {
        $query = DataSiswaSekolah::with(['sekolah', 'kelas', 'pasien']);

        if ($sekolahFilter) {
            $query->where('id_sekolah', $sekolahFilter);
        }

        if ($kelasFilter) {
            $query->where('id_kelas', $kelasFilter);
        }

        $siswa = $query->limit($limit)->get();

        if ($siswa->isEmpty()) {
            $this->info('Tidak ada data siswa yang ditemukan.');
            return;
        }

        $this->info("Menampilkan {$siswa->count()} data siswa:");
        $this->table(
            ['ID', 'No. RM', 'NISN', 'Nama', 'JK', 'Sekolah', 'Kelas', 'Status'],
            $siswa->map(function($item) {
                return [
                    $item->id,
                    $item->no_rkm_medis,
                    $item->nisn ?? '-',
                    $item->pasien->nm_pasien ?? '-',
                    $item->jenis_kelamin,
                    'ID Sekolah: ' . ($item->id_sekolah ?? '-'),
                    'ID Kelas: ' . ($item->id_kelas ?? '-'),
                    $item->status_siswa
                ];
            })->toArray()
        );
    }

    /**
     * Tampilkan daftar sekolah
     */
    private function showSekolahList($limit)
    {
        $sekolah = DataSekolah::with('jenisSekolah')->limit($limit)->get();

        if ($sekolah->isEmpty()) {
            $this->info('Tidak ada data sekolah yang ditemukan.');
            return;
        }

        $this->info("Menampilkan {$sekolah->count()} data sekolah:");
        $this->table(
            ['ID', 'Nama Sekolah', 'Jenis Sekolah'],
            $sekolah->map(function($item) {
                return [
                    $item->id_sekolah,
                    $item->nama_sekolah,
                    $item->jenisSekolah->nama ?? '-'
                ];
            })->toArray()
        );
    }

    /**
     * Tampilkan daftar kelas
     */
    private function showKelasList($limit, $sekolahFilter = null)
    {
        $query = DataKelas::with('sekolah');

        if ($sekolahFilter) {
            $query->where('sekolah_id', $sekolahFilter);
        }

        $kelas = $query->limit($limit)->get();

        if ($kelas->isEmpty()) {
            $this->info('Tidak ada data kelas yang ditemukan.');
            return;
        }

        $this->info("Menampilkan {$kelas->count()} data kelas:");
        $this->table(
            ['ID', 'Kelas', 'Tingkat', 'Wali Kelas', 'Sekolah'],
            $kelas->map(function($item) {
                return [
                    $item->id_kelas,
                    $item->kelas,
                    $item->tingkat ?? '-',
                    $item->wali_kelas ?? '-',
                    'ID Sekolah: ' . ($item->id_sekolah ?? '-')
                ];
            })->toArray()
        );
    }
}
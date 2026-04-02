<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestPasienSimpan extends Command
{
    protected $signature = 'test:pasien-simpan {--nm=TEST} {--nik=9999999999999999} {--jk=L} {--tgl=2000-01-01}';
    protected $description = 'Uji simpan data pasien ke tabel pasien dengan penomoran aman';

    public function handle()
    {
        try {
            $columns = Schema::getColumnListing('pasien');
            $this->info('Kolom tabel pasien: ' . implode(', ', $columns));

            DB::beginTransaction();

            $lock = DB::select('SELECT GET_LOCK("pasien_no_rkm_medis_lock", 10) AS l');
            if (!$lock || (int)($lock[0]->l ?? 0) !== 1) {
                throw new \Exception('Gagal mendapatkan lock penomoran');
            }

            $lastRecord = DB::table('pasien')
                            ->orderByRaw('CAST(no_rkm_medis AS UNSIGNED) DESC')
                            ->first();
            $lastNumber = $lastRecord ? (int)$lastRecord->no_rkm_medis : 0;
            $nextNumber = $lastNumber + 1;
            $noRM = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // Ambil kode wilayah yang valid untuk memenuhi foreign key
            $kdProp = DB::table('propinsi')->select('kd_prop')->orderBy('kd_prop')->value('kd_prop');
            $kdKab = DB::table('kabupaten')->select('kd_kab')->orderBy('kd_kab')->value('kd_kab');
            $kdKec = DB::table('kecamatan')->select('kd_kec')->orderBy('kd_kec')->value('kd_kec');
            $kdKel = DB::table('kelurahan')->select('kd_kel')->orderBy('kd_kel')->value('kd_kel');
            $kdPj = DB::table('penjab')->select('kd_pj')->orderBy('kd_pj')->value('kd_pj');

            $template = [
                'no_rkm_medis' => $noRM,
                'nm_pasien' => strtoupper($this->option('nm')),
                'no_ktp' => $this->option('nik'),
                'jk' => $this->option('jk'),
                'tmp_lahir' => 'TEST',
                'tgl_lahir' => $this->option('tgl'),
                'nm_ibu' => 'TEST',
                'alamat' => 'ALAMAT',
                'gol_darah' => '-',
                'pekerjaan' => '-',
                'stts_nikah' => 'MENIKAH',
                'agama' => 'ISLAM',
                'tgl_daftar' => date('Y-m-d'),
                'no_tlp' => '081',
                'umur' => '0 Th 0 Bl 0 Hr',
                'pnd' => '-',
                'keluarga' => 'AYAH',
                'namakeluarga' => 'TEST',
                'kd_pj' => $kdPj ?? '-',
                'no_peserta' => '0000',
                'kd_prop' => $kdProp ?? '31',
                'kd_kab' => $kdKab ?? '3172',
                'kd_kec' => $kdKec ?? '317205',
                'kd_kel' => $kdKel ?? '317205',
                'pekerjaanpj' => '-',
                'alamatpj' => 'ALAMAT',
                'kelurahanpj' => 'KELURAHAN',
                'kecamatanpj' => 'KECAMATAN',
                'kabupatenpj' => 'KABUPATEN',
                'propinsipj' => 'PROPINSI',
                'perusahaan_pasien' => '-',
                'suku_bangsa' => 5,
                'bahasa_pasien' => 11,
                'cacat_fisik' => 5,
                'email' => 'test@example.com',
                'nip' => '0',
                'no_kk' => '0',
                'data_posyandu' => 'POSYANDU',
                'status' => 'Kepala Keluarga',
            ];

            $data = [];
            foreach ($template as $k => $v) {
                if (in_array($k, $columns)) {
                    $data[$k] = $v;
                }
            }

            DB::table('pasien')->insert($data);

            DB::commit();
            DB::select('SELECT RELEASE_LOCK("pasien_no_rkm_medis_lock") AS r');

            $this->info('Sukses simpan pasien dengan no_rkm_medis: ' . $noRM);
            return 0;
        } catch (\Exception $e) {
            DB::select('SELECT RELEASE_LOCK("pasien_no_rkm_medis_lock") AS r');
            DB::rollBack();
            $this->error('Gagal simpan pasien: ' . $e->getMessage());
            return 1;
        }
    }
}

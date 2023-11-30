<?php

namespace App\Http\Livewire\Component\AwalRalan;

use Illuminate\Support\Arr;
use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\DB;

class Form extends Component
{
    use LivewireAlert;
    public $no_rawat, $editMode = false;
    public $tanggal_awal, $informasi, $ket_informasi, $keluhan_utama, $penyakit_sekarang, $penyakit_keluarga, $penyakit_dahulu, $riwayat_pengobatan, $riwayat_alergi, $keadaan, $kesadaran, $gcs, $tb, $bb, $td, $nadi, $rr, $suhu, $spo2, $kepala, $abdomen, $gigi, $gental, $tht, $ekstremitas, $thoraks, $kulit, $ket_fisik, $ket_lokalis, $penunjang, $diagnosis, $tatalaksana, $konsul;
    protected $listeners = ['setNoRawatUmum' => 'setNoRawat', 'hapusMedisRalan' => 'hapus'];

    public function mount()
    {
        $this->tanggal_awal = Carbon::now()->format('Y-m-d H:i:s');
        $this->informasi = 'Autoanamnesis';
        $this->keadaan = 'Sehat';
        $this->kesadaran = 'Compos Mentis';
        $this->kepala = 'Normal';
        $this->abdomen = 'Normal';
        $this->gigi = 'Normal';
        $this->gental = 'Normal';
        $this->tht = 'Normal';
        $this->ekstremitas = 'Normal';
        $this->thoraks = 'Normal';
        $this->kulit = 'Normal';
    }

    public function render()
    {
        return view('livewire.component.awal-ralan.form');
    }

    public function setNoRawat($no_rawat)
    {
        $this->no_rawat = $no_rawat;
    }

    public function updatedNoRawat()
    {
        try{

            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan')
                    ->where('no_rawat', $this->no_rawat)
                    ->first();

            if($data){
                $this->tanggal_awal = $data->tanggal;
                $this->informasi = $data->anamnesis;
                $this->ket_informasi = $data->hubungan;
                $this->keluhan_utama = $data->keluhan_utama;
                $this->penyakit_sekarang = $data->rps;
                $this->penyakit_dahulu = $data->rpd;
                $this->penyakit_keluarga = $data->rpk;
                $this->riwayat_pengobatan = $data->rpo;
                $this->riwayat_alergi = $data->alergi;
                $this->keadaan = $data->keadaan;
                $this->kesadaran = $data->kesadaran;
                $this->gcs = $data->gcs;
                $this->tb = $data->tb;
                $this->bb = $data->bb;
                $this->td = $data->td;
                $this->nadi = $data->nadi;
                $this->rr = $data->rr;
                $this->suhu = $data->suhu;
                $this->spo2 = $data->spo;
                $this->kepala = $data->kepala;
                $this->abdomen = $data->abdomen;
                $this->gigi = $data->gigi;
                $this->gental = $data->genital;
                $this->tht = $data->tht;
                $this->thoraks = $data->thoraks;
                $this->ekstremitas = $data->ekstremitas;
                $this->kulit = $data->kulit;
                $this->ket_fisik = $data->ket_fisik;
                $this->ket_lokalis = $data->ket_lokalis;
                $this->penunjang = $data->penunjang;
                $this->diagnosis = $data->diagnosis;
                $this->tatalaksana = $data->tata;
                $this->konsul = $data->konsulrujuk;

                $this->editMode = true;
            }

        }catch(\Exception $e){

        }
    }

    public function resetInput()
    {
        $this->reset(['ket_informasi', 'keluhan_utama', 'penyakit_sekarang', 'penyakit_dahulu', 'penyakit_keluarga', 'riwayat_pengobatan', 'riwayat_alergi', 'gcs', 'tb', 'bb', 'td', 'nadi', 'rr', 'suhu', 'spo2', 'ket_fisik', 'ket_lokalis', 'penunjang', 'diagnosis', 'tatalaksana', 'konsul']);

        $this->tanggal_awal = Carbon::now()->format('Y-m-d H:i:s');
        $this->informasi = 'Autoanamnesis';
        $this->keadaan = 'Sehat';
        $this->kesadaran = 'Compos Mentis';
        $this->kepala = 'Normal';
        $this->abdomen = 'Normal';
        $this->gigi = 'Normal';
        $this->gental = 'Normal';
        $this->tht = 'Normal';
        $this->ekstremitas = 'Normal';
        $this->thoraks = 'Normal';
        $this->kulit = 'Normal';
    }

    public function simpan()
    {
        $data = [
            'no_rawat' => $this->no_rawat,
            'tanggal' => $this->tanggal_awal,
            'kd_dokter' => session()->get('username'),
            'anamnesis' => $this->informasi,
            'hubungan' => $this->ket_informasi ?? '',
            'keluhan_utama' => $this->keluhan_utama ?? '',
            'rps' => $this->penyakit_sekarang ?? '',
            'rpd' => $this->penyakit_dahulu ?? '',
            'rpk' => $this->penyakit_keluarga ?? '',
            'rpo' => $this->riwayat_pengobatan ?? '',
            'alergi' => $this->riwayat_alergi ?? '-',
            'keadaan' => $this->keadaan ?? '',
            'kesadaran' => $this->kesadaran ?? '',
            'gcs' => $this->gcs ?? '',
            'tb' => $this->tb ?? '',
            'bb' => $this->bb ?? '',
            'td' => $this->td ?? '',
            'nadi' => $this->nadi ?? '',
            'rr' => $this->rr ?? '',
            'suhu' => $this->suhu ?? '',
            'spo' => $this->spo2 ?? '',
            'kepala' => $this->kepala ?? '',
            'abdomen' => $this->abdomen ?? '',
            'gigi' => $this->gigi ?? '',
            'genital' => $this->gental ?? '',
            'tht' => $this->tht ?? '',
            'thoraks' => $this->thoraks ?? '',
            'ekstremitas' => $this->ekstremitas ?? '',
            'kulit' => $this->kulit ?? '',
            'ket_fisik' => $this->ket_fisik ?? '',
            'ket_lokalis' => $this->ket_lokalis ?? '',
            'penunjang' => $this->penunjang ?? '',
            'diagnosis' => $this->diagnosis ?? '',
            'tata' => $this->tatalaksana ?? '',
            'konsulrujuk' => $this->konsul ?? ''
        ];

        try{

            if($this->editMode){

                $data = Arr::except($data, ['no_rawat', 'kd_dokter']);
                DB::table('penilaian_medis_ralan')
                    ->where('no_rawat', $this->no_rawat)
                    ->update($data);

                $this->alert('success', 'Data berhasil diubah');
                $this->editMode = true;

            }else{

                DB::table('penilaian_medis_ralan')
                    ->insert($data);

                $this->alert('success', 'Data berhasil disimpan');

            }

        }catch(\Exception $e){
            $this->alert('error', 'Gagal', [
                'position' =>  'center',
                'timer' =>  '',
                'toast' =>  false,
                'text' =>  App::environment('local') ? $e->getMessage() : 'Terjadi Kesalahan',
                'confirmButtonText' =>  'Oke'
            ]);
        }
    }

    public function confirmHapus()
    {
        try{

            $data = DB::table('penilaian_medis_ralan')
                    ->where('no_rawat', $this->no_rawat)
                    ->first();
            if($data){
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed' => 'hapusMedisRalan',
                    'cancelButtonText' => 'Batal',
                    'confirmButtonText' => 'Hapus',
                ]);
            }else{
                $this->alert('warning', 'Gagal', [
                    'position' =>  'center',
                    'timer' =>  '',
                    'toast' =>  false,
                    'text' =>  'Data tidak ditemukan',
                    'showConfirmButton' =>  true,
                    'confirmButtonText' =>  'Oke'
                ]);
            }

        }catch(\Exception $e){

        }
    }

    public function hapus()
    {
        try{
            DB::table('penilaian_medis_ralan')
                ->where('no_rawat', $this->no_rawat)
                ->delete();

            $this->resetInput();
            $this->editMode = false;
            $this->alert('success', 'Berhasil hapus data');
        }catch(\Exception $e){
            $this->alert('error', 'Gagal', [
                'position' =>  'center',
                'timer' =>  '',
                'toast' =>  false,
                'text' =>  App::environment('local') ? $e->getMessage() : 'Terjadi Kesalahan',
                'confirmButtonText' =>  'Oke'
            ]);
        }
    }
}

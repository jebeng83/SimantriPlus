<?php

namespace App\Http\Livewire\Component\AwalKandungan;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Arr;

class FormKandungan extends Component
{
    use LivewireAlert;
    public $no_rawat, $editMode = false;
    public $tanggal_kandungan, $anamnesis, $hubungan, $keluhan_utama, $rps, $rpo, $rpd, $rpk, $alergi, $keadaan, $kesadaran, $gcs, $td, $tb, $bb, $suhu, $nadi, $rr, $spo, $kepala, $abdomen, $gigi, $genital, $tht, $ekstremitas, $thoraks, $kulit, $ket_fisik, $tfu, $tbj, $his, $kontraksi, $djj, $inspeksi, $vt, $inspekulo, $ultra, $kardio, $lab, $diagnosis, $tata, $konsul;
    protected $listeners = ['hapusMedisRalanKandungan' => 'hapus'];

    public function mount()
    {
        $this->resetInput();
    }

    public function render()
    {
        return view('livewire.component.awal-kandungan.form-kandungan');
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'tanggal_kandungan', 'anamnesis', 'editMode', 'keadaan', 'kesadaran', 'kepala', 'abdomen', 'gigi', 'genital', 'tht', 'ekstremitas', 'thoraks', 'kulit', 'kontraksi']);

        $this->tanggal_kandungan = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis = 'Autoanamnesis';
        $this->keadaan = 'Sehat';
        $this->kesadaran = 'Compos Mentis';
        $this->kepala = 'Normal';
        $this->abdomen = 'Normal';
        $this->gigi = 'Normal';
        $this->genital = 'Normal';
        $this->tht = 'Normal';
        $this->ekstremitas = 'Normal';
        $this->thoraks = 'Normal';
        $this->kulit = 'Normal';
        $this->kontraksi = 'Ada';
    }

    public function updatedNoRawat()
    {
        try{

            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_kandungan')
                    ->where('no_rawat', $this->no_rawat)
                    ->first();
            
            if($data){
                $this->tanggal_kandungan = $data->tanggal;
                $this->anamnesis = $data->anamnesis;
                $this->hubungan = $data->hubungan;
                $this->keluhan_utama = $data->keluhan_utama;
                $this->rps = $data->rps;
                $this->rpo = $data->rpo;
                $this->rpd = $data->rpd;
                $this->rpk = $data->rpk;
                $this->alergi = $data->alergi;
                $this->keadaan = $data->keadaan;
                $this->kesadaran = $data->kesadaran;
                $this->gcs = $data->gcs;
                $this->td = $data->td;
                $this->tb = $data->tb;
                $this->bb = $data->bb;
                $this->suhu = $data->suhu;
                $this->nadi = $data->nadi;
                $this->rr = $data->rr;
                $this->spo = $data->spo;
                $this->kepala = $data->kepala;
                $this->abdomen = $data->abdomen;
                $this->gigi = $data->gigi;
                $this->genital = $data->genital;
                $this->tht = $data->tht;
                $this->ekstremitas = $data->ekstremitas;
                $this->thoraks = $data->thoraks;
                $this->kulit = $data->kulit;
                $this->ket_fisik = $data->ket_fisik;
                $this->tfu = $data->tfu;
                $this->tbj = $data->tbj;
                $this->his = $data->his;
                $this->kontraksi = $data->kontraksi;
                $this->djj = $data->djj;
                $this->inspeksi = $data->inspeksi;
                $this->vt = $data->vt;
                $this->inspekulo = $data->inspekulo;
                $this->ultra = $data->ultra;
                $this->kardio = $data->kardio;
                $this->lab = $data->lab;
                $this->diagnosis = $data->diagnosis;
                $this->tata = $data->tata;
                $this->konsul = $data->konsul;

                $this->editMode = true;
            }

        }catch(\Exception $e){

        }
    }

    public function simpan()
    {
        $data = [
            'no_rawat' => $this->no_rawat,
            'tanggal' => $this->tanggal_kandungan,
            'kd_dokter' => session()->get('username'),
            'anamnesis' => $this->anamnesis,
            'hubungan' => $this->hubungan ?? '',
            'keluhan_utama' => $this->keluhan_utama ?? '',
            'rps' => $this->rps ?? '',
            'rpo' => $this->rpo ?? '',
            'rpd' => $this->rpd ?? '',
            'rpk' => $this->rpk ?? '',
            'alergi' => $this->alergi ?? '',
            'keadaan' => $this->keadaan,
            'kesadaran' => $this->kesadaran,
            'gcs' => $this->gcs ?? '',
            'td' => $this->td ?? '',
            'tb' => $this->tb ?? '',
            'bb' => $this->bb ?? '',
            'suhu' => $this->suhu ?? '',
            'nadi' => $this->nadi ?? '',
            'rr' => $this->rr ?? '',
            'spo' => $this->spo ?? '',
            'kepala' => $this->kepala,
            'abdomen' => $this->abdomen,
            'gigi' => $this->gigi,
            'genital' => $this->genital,
            'tht' => $this->tht,
            'ekstremitas' => $this->ekstremitas,
            'thoraks' => $this->thoraks,
            'kulit' => $this->kulit,
            'ket_fisik' => $this->ket_fisik ?? '',
            'tfu' => $this->tfu ?? '',
            'tbj' => $this->tbj ?? '',
            'his' => $this->his ?? '',
            'kontraksi' => $this->kontraksi,
            'djj' => $this->djj ?? '',
            'inspeksi' => $this->inspeksi ?? '',
            'vt' => $this->vt ?? '',
            'inspekulo' => $this->inspekulo ?? '',
            'ultra' => $this->ultra ?? '',
            'kardio' => $this->kardio ?? '',
            'lab' => $this->lab ?? '',
            'diagnosis' => $this->diagnosis ?? '',
            'tata' => $this->tata ?? '',
            'konsul' => $this->konsul ?? '',
        ];

        try{

            if($this->editMode){

                DB::table('penilaian_medis_ralan_kandungan')
                    ->where('no_rawat', $this->no_rawat)
                    ->update($data);

                $this->alert('success', 'Data berhasil diubah');
            }else{
                DB::table('penilaian_medis_ralan_kandungan')->insert($data);

                $this->editMode = true;
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

            $data = DB::table('penilaian_medis_ralan_kandungan')
                    ->where('no_rawat', $this->no_rawat)
                    ->first();
            if($data){
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed' => 'hapusMedisRalanKandungan',
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
            DB::table('penilaian_medis_ralan_kandungan')
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

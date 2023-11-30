<?php

namespace App\Http\Livewire\Component\AwalAnak;

use Illuminate\Support\Arr;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\App;

class FormAnak extends Component
{
    use LivewireAlert;
    public $no_rawat, $editMode = false;
    public $tanggal_anak, $anamnesis, $hubungan, $keluhan_utama, $rps, $rpo, $rpd, $rpk, $alergi, $keadaan, $kesadaran, $gcs, $td, $tb, $bb, $suhu, $nadi, $rr, $spo, $kepala, $abdomen, $gigi, $genital, $tht, $ekstremitas, $thoraks, $kulit, $ket_fisik, $ket_lokalis, $penunjang, $diagnosis, $tata, $konsul;
    protected $listeners = ['hapusMedisRalanAnak' => 'hapus'];

    public function render()
    {
        return view('livewire.component.awal-anak.form-anak');
    }

    public function setNoRawat($no_rawat)
    {
        $this->no_rawat = $no_rawat;
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'tanggal_anak', 'anamnesis', 'editMode', 'keadaan', 'kesadaran', 'kepala', 'abdomen', 'gigi', 'genital', 'tht', 'ekstremitas', 'thoraks', 'kulit']);

        $this->tanggal_anak = Carbon::now()->format('Y-m-d H:i:s');
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
    }

    public function updatedNoRawat()
    {
        try{
            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_anak')
                    ->where('no_rawat', $this->no_rawat)
                    ->first();

            if($data){
                $this->tanggal_anak = $data->tanggal;
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
                $this->ket_lokalis = $data->ket_lokalis;
                $this->penunjang = $data->penunjang;
                $this->diagnosis = $data->diagnosis;
                $this->tata = $data->tata;
                $this->konsul = $data->konsul;

                $this->editMode = true;
            }else{

                $this->editMode = false;
            }
        }catch(\Exception $e){

        }
    }

    public function simpan()
    {
        $data = [
            'no_rawat' => $this->no_rawat,
            'tanggal' => $this->tanggal_anak,
            'kd_dokter' => session()->get('username'),
            'anamnesis' => $this->anamnesis,
            'hubungan' => $this->hubungan ?? '',
            'keluhan_utama' => $this->keluhan_utama ?? '',
            'rps' => $this->rps ?? '',
            'rpo' => $this->rpo ?? '',
            'rpd' => $this->rpd ?? '',
            'rpk' => $this->rpk ?? '',
            'alergi' => $this->alergi ?? '',
            'kesadaran' => $this->kesadaran ?? '',
            'keadaan' => $this->keadaan ?? '',
            'gcs' => $this->gcs ?? '',
            'td' => $this->td ?? '',
            'tb' => $this->tb ?? '',
            'bb' => $this->bb ?? '',
            'suhu' => $this->suhu ?? '',
            'nadi' => $this->nadi ?? '',
            'rr' => $this->rr ?? '',
            'spo' => $this->spo ?? '',
            'kepala' => $this->kepala ?? '',
            'abdomen' => $this->abdomen ?? '',
            'gigi' => $this->gigi ?? '',
            'genital' => $this->genital ?? '',
            'tht' => $this->tht ?? '',
            'ekstremitas' => $this->ekstremitas ?? '',
            'thoraks' => $this->thoraks ?? '',
            'kulit' => $this->kulit ?? '',
            'ket_fisik' => $this->ket_fisik ?? '',
            'ket_lokalis' => $this->ket_lokalis ?? '',
            'penunjang' => $this->penunjang ?? '',
            'diagnosis' => $this->diagnosis ?? '',
            'tata' => $this->tata ?? '',
            'konsul' => $this->konsul ?? '',
        ];

        try{

            if($this->editMode){
                $data = Arr::except($data, ['no_rawat', 'kd_dokter']);
                DB::table('penilaian_medis_ralan_anak')
                    ->where('no_rawat', $this->no_rawat)
                    ->update($data);
                
                $this->alert('success', 'Data berhasil diubah');
                
            }else{
                DB::table('penilaian_medis_ralan_anak')
                    ->insert($data);

                $this->alert('success', 'Data berhasil disimpan');
                $this->editMode = true;
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

            $data = DB::table('penilaian_medis_ralan_anak')
                    ->where('no_rawat', $this->no_rawat)
                    ->first();
            if($data){
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed' => 'hapusMedisRalanAnak',
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
            DB::table('penilaian_medis_ralan_anak')
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

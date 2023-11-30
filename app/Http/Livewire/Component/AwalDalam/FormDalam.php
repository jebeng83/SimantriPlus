<?php

namespace App\Http\Livewire\Component\AwalDalam;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class FormDalam extends Component
{
    use LivewireAlert;
    public $no_rawat, $editMode = false;
    public $tanggal_dalam, $anamnesis, $hubungan, $keluhan_utama, $rps, $rpo, $rpd, $alergi, $nutrisi, $td, $nadi, $rr, $suhu, $bb, $gcs, $kondisi, $kepala, $keterangan_kepala, $abdomen, $keterangan_abdomen, $ekstremitas, $keterangan_ekstremitas, $thoraks, $keterangan_thoraks, $lainnya, $lab, $rad, $penunjanglain, $diagnosis, $diagnosis2, $permasalahan, $terapi, $tindakan, $edukasi;
    protected $listeners = ['hapusMedisDalam' => 'hapus'];

    public function mount()
    {
        $this->resetInput();
    }

    public function render()
    {
        return view('livewire.component.awal-dalam.form-dalam');
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'tanggal_dalam', 'anamnesis', 'kepala', 'abdomen', 'ekstremitas', 'thoraks']);

        $this->tanggal_dalam = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis = 'Autoanamnesis';
        $this->kepala = 'Normal';
        $this->abdomen = 'Normal';
        $this->ekstremitas = 'Normal';
        $this->thoraks = 'Normal';

    }

    public function updatedNoRawat()
    {
        try{

            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_penyakit_dalam')
                    ->where('no_rawat', $this->no_rawat)
                    ->first();

            if($data){
                $this->tanggal_dalam = $data->tanggal;
                $this->anamnesis = $data->anamnesis;
                $this->hubungan = $data->hubungan;
                $this->keluhan_utama = $data->keluhan_utama;
                $this->rps = $data->rps;
                $this->rpo = $data->rpo;
                $this->rpd = $data->rpd;
                $this->alergi = $data->alergi;
                $this->nutrisi = $data->status;
                $this->td = $data->td;
                $this->nadi = $data->nadi;
                $this->rr = $data->rr;
                $this->suhu = $data->suhu;
                $this->bb = $data->bb;
                $this->gcs = $data->gcs;
                $this->kondisi = $data->kondisi;
                $this->kepala = $data->kepala;
                $this->keterangan_kepala = $data->keterangan_kepala;
                $this->abdomen = $data->abdomen;
                $this->keterangan_abdomen = $data->keterangan_abdomen;
                $this->ekstremitas = $data->ekstremitas;
                $this->keterangan_ekstremitas = $data->keterangan_ekstremitas;
                $this->thoraks = $data->thoraks;
                $this->keterangan_thoraks = $data->keterangan_thorak;
                $this->lainnya = $data->lainnya;
                $this->lab = $data->lab;
                $this->rad = $data->rad;
                $this->penunjanglain = $data->penunjanglain;
                $this->diagnosis = $data->diagnosis;
                $this->diagnosis2 = $data->diagnosis2;
                $this->permasalahan = $data->permasalahan;
                $this->terapi = $data->terapi;
                $this->tindakan = $data->tindakan;
                $this->edukasi = $data->edukasi;

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

    public function simpan()
    {
        $data = [
            'no_rawat' => $this->no_rawat,
            'tanggal' => $this->tanggal_dalam,
            'kd_dokter' => session()->get('username'),
            'anamnesis' => $this->anamnesis,
            'hubungan' => $this->hubungan ?? '',
            'keluhan_utama' => $this->keluhan_utama ?? '',
            'rps' => $this->rps ?? '',
            'rpo' => $this->rpo ?? '',
            'rpd' => $this->rpd ?? '',
            'alergi' => $this->alergi ?? '',
            'status' => $this->nutrisi ?? '',
            'td' => $this->td ?? '',
            'nadi' => $this->nadi ?? '',
            'rr' => $this->rr ?? '',
            'suhu' => $this->suhu ?? '',
            'bb' => $this->bb ?? '',
            'gcs' => $this->gcs ?? '',
            'kondisi' => $this->kondisi ?? '',
            'kepala' => $this->kepala ?? '',
            'keterangan_kepala' => $this->keterangan_kepala ?? '',
            'abdomen' => $this->abdomen ?? '',
            'keterangan_abdomen' => $this->keterangan_abdomen ?? '',
            'ekstremitas' => $this->ekstremitas ?? '',
            'keterangan_ekstremitas' => $this->keterangan_ekstremitas ?? '',
            'thoraks' => $this->thoraks ?? '',
            'keterangan_thorak' => $this->keterangan_thoraks ?? '',
            'lainnya' => $this->lainnya ?? '',
            'lab' => $this->lab ?? '',
            'rad' => $this->rad ?? '',
            'penunjanglain' => $this->penunjanglain ?? '',
            'diagnosis' => $this->diagnosis ?? '',
            'diagnosis2' => $this->diagnosis2 ?? '',
            'permasalahan' => $this->permasalahan ?? '',
            'terapi' => $this->terapi ?? '',
            'tindakan' => $this->tindakan ?? '',
            'edukasi' => $this->edukasi ?? '',
        ];

        try{

            if($this->editMode){

                DB::table('penilaian_medis_ralan_penyakit_dalam')
                    ->where('no_rawat', $this->no_rawat)
                    ->update(Arr::except($data, ['no_rawat']));

                $this->alert('success', 'Data berhasil diubah');
            }else{
                DB::table('penilaian_medis_ralan_penyakit_dalam')->insert($data);
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

            $data = DB::table('penilaian_medis_ralan_penyakit_dalam')
                    ->where('no_rawat', $this->no_rawat)
                    ->first();
            if($data){
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed' => 'hapusMedisDalam',
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
            DB::table('penilaian_medis_ralan_penyakit_dalam')
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

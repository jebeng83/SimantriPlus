<?php

namespace App\Http\Livewire\Component\AwalPsikiatri;

use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class FormPsikiatri extends Component
{
    use LivewireAlert;
    public $no_rawat, $editMode = false;
    public $tanggal_psikiatri, $anamnesis, $hubungan, $keluhan_utama, $rps, $rpo, $rpk, $rpd, $alergi, $penampilan, $gangguan_persepsi, $pembicara, $proses_pikir, $psikomotor, $pengendalian_impuls, $sikap, $tilikan, $mood, $rta, $fungsi_kognitif, $gcs, $td, $tb, $bb, $suhu, $nadi, $rr, $spo, $kepala, $abdomen, $gigi, $gental, $tht, $ekstremitas, $thoraks, $kulit, $ket_fisik, $penunjang, $diagnosis, $tata;
    protected $listeners = ['hapusMedisRalanPsikiatri' => 'hapus'];

    public function mount()
    {
        $this->resetInput();
    }

    public function render()
    {
        return view('livewire.component.awal-psikiatri.form-psikiatri');
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'tanggal_psikiatri', 'anamnesis', 'editMode', 'kepala', 'abdomen', 'gigi', 'gental', 'tht', 'ekstremitas', 'thoraks', 'kulit']);

        $this->tanggal_psikiatri = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis = 'Autoanamnesis';
        $this->kepala = 'Normal';
        $this->abdomen = 'Normal';
        $this->gigi = 'Normal';
        $this->gental = 'Normal';
        $this->tht = 'Normal';
        $this->ekstremitas = 'Normal';
        $this->thoraks = 'Normal';
        $this->kulit = 'Normal';

    }

    public function updatedNoRawat()
    {
        try{
            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_psikiatrik')
                    ->where('no_rawat', $this->no_rawat)
                    ->first();
            // dd($data);
            if($data){
                $this->tanggal_psikiatri = $data->tanggal;
                $this->anamnesis = $data->anamnesis;
                $this->hubungan = $data->hubungan;
                $this->keluhan_utama = $data->keluhan_utama;
                $this->rps = $data->rps;
                $this->rpo = $data->rpo;
                $this->rpk = $data->rpk;
                $this->rpd = $data->rpd;
                $this->alergi = $data->alergi;
                $this->penampilan = $data->penampilan;
                $this->gangguan_persepsi = $data->gangguan_persepsi;
                $this->pembicara = $data->pembicara;
                $this->proses_pikir = $data->proses_pikir;
                $this->psikomotor = $data->psikomotor;
                $this->pengendalian_impuls = $data->pengendalian_impuls;
                $this->sikap = $data->sikap;
                $this->tilikan = $data->tilikan;
                $this->mood = $data->mood;
                $this->rta = $data->rta;
                $this->fungsi_kognitif = $data->fungsi_kognitif;
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
                $this->gental = $data->gental;
                $this->tht = $data->tht;
                $this->ekstremitas = $data->ekstremitas;
                $this->thoraks = $data->thoraks;
                $this->kulit = $data->kulit;
                $this->ket_fisik = $data->ket_fisik;
                $this->penunjang = $data->penunjang;
                $this->diagnosis = $data->diagnosis;
                $this->tata = $data->tata;
                $this->editMode = true;
            }else{
                $this->editMode = false;
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
            'tanggal' => $this->tanggal_psikiatri,
            'kd_dokter' => session()->get('username'),
            'anamnesis' => $this->anamnesis,
            'hubungan' => $this->hubungan ?? '',
            'keluhan_utama' => $this->keluhan_utama ?? '',
            'rps' => $this->rps ?? '',
            'rpo' => $this->rpo ?? '',
            'rpk' => $this->rpk ?? '',
            'rpd' => $this->rpd ?? '',
            'alergi' => $this->alergi ?? '',
            'penampilan' => $this->penampilan ?? '',
            'gangguan_persepsi' => $this->gangguan_persepsi ?? '',
            'pembicara' => $this->pembicara ?? '',
            'proses_pikir' => $this->proses_pikir ?? '',
            'psikomotor' => $this->psikomotor ?? '',
            'pengendalian_impuls' => $this->pengendalian_impuls ?? '',
            'sikap' => $this->sikap ?? '',
            'tilikan' => $this->tilikan ?? '',
            'mood' => $this->mood ?? '',
            'rta' => $this->rta ?? '',
            'fungsi_kognitif' => $this->fungsi_kognitif ?? '',
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
            'gental' => $this->gental ?? '',
            'tht' => $this->tht ?? '',
            'ekstremitas' => $this->ekstremitas ?? '',
            'thoraks' => $this->thoraks ?? '',
            'kulit' => $this->kulit ?? '',
            'ket_fisik' => $this->ket_fisik ?? '',
            'penunjang' => $this->penunjang ?? '',
            'diagnosis' => $this->diagnosis ?? '',
            'tata' => $this->tata ?? '',
        ];

        try{
            if($this->editMode){

                DB::table('penilaian_medis_ralan_psikiatrik')
                    ->where('no_rawat', $this->no_rawat)
                    ->update(Arr::except($data, ['no_rawat', 'kd_dokter']));

                $this->resetInput();
                $this->alert('success', 'Data berhasil diubah');

            }else{

                DB::table('penilaian_medis_ralan_psikiatrik')
                    ->insert($data);

                $this->resetInput();
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

            $data = DB::table('penilaian_medis_ralan_psikiatrik')
                    ->where('no_rawat', $this->no_rawat)
                    ->first();
            if($data){
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed' => 'hapusMedisRalanPsikiatri',
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

            $this->alert('error', 'Gagal', [
                'position' =>  'center',
                'timer' =>  '',
                'toast' =>  false,
                'text' =>  App::environment('local') ? $e->getMessage() : 'Terjadi Kesalahan',
                'showConfirmButton' =>  true,
                'confirmButtonText' =>  'Oke'
            ]);
        }
    }

    public function hapus()
    {
        try{
            DB::table('penilaian_medis_ralan_psikiatrik')
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

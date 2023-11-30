<?php

namespace App\Http\Livewire\Component\AwalTht;

use Illuminate\Support\Arr;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Form extends Component
{
    use LivewireAlert;
    public $no_rawat, $editMode = false;
    public $tanggal_tht, $anamnesis, $hubungan, $keluhan_utama, $rps, $rpo, $rpd, $alergi, $td, $tb, $bb, $suhu, $nadi, $rr, $nyeri, $nutrisi, $kondisi, $ket_lokalis, $lab, $rad, $tes_pendengaran, $penunjang, $diagnosis, $diagnosisbanding, $permasalahan, $terapi, $tindakan, $tatalaksana, $edukasi;
    protected $listeners = ['hapusMedisRalanTHT' => 'hapus'];

    public function mount()
    {
        $this->tanggal_tht = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis = 'Autoanamnesis';
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'tanggal_tht', 'anamnesis', 'editMode']);
        $this->tanggal_tht = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis = 'Autoanamnesis';
    }

    public function render()
    {
        return view('livewire.component.awal-tht.form');
    }

    public function setNoRawat($no_rawat)
    {
        $this->no_rawat = $no_rawat;
    }

    public function updatedNoRawat()
    {
        try{
            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_tht')
                    ->where('no_rawat', $this->no_rawat)
                    ->first();
            // dd($data);
            if($data){
                $this->tanggal_tht = $data->tanggal;
                $this->anamnesis = $data->anamnesis;
                $this->hubungan = $data->hubungan;
                $this->keluhan_utama = $data->keluhan_utama;
                $this->rps = $data->rps;
                $this->rpo = $data->rpo;
                $this->rpd = $data->rpd;
                $this->alergi = $data->alergi;
                $this->td = $data->td;
                $this->tb = $data->tb;
                $this->bb = $data->bb;
                $this->suhu = $data->suhu;
                $this->nadi = $data->nadi;
                $this->rr = $data->rr;
                $this->nyeri = $data->nyeri;
                $this->nutrisi = $data->status_nutrisi;
                $this->kondisi = $data->kondisi;
                $this->ket_lokalis = $data->ket_lokalis;
                $this->lab = $data->lab;
                $this->rad = $data->rad;
                $this->tes_pendengaran = $data->tes_pendengaran;
                $this->penunjang = $data->penunjang;
                $this->diagnosis = $data->diagnosis;
                $this->diagnosisbanding = $data->diagnosisbanding;
                $this->permasalahan = $data->permasalahan;
                $this->terapi = $data->terapi;
                $this->tindakan = $data->tindakan;
                $this->tatalaksana = $data->tatalaksana;
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
            'tanggal' => $this->tanggal_tht,
            'kd_dokter' => session()->get('username'),
            'anamnesis' => $this->anamnesis,
            'hubungan' => $this->hubungan ?? '',
            'keluhan_utama' => $this->keluhan_utama ?? '',
            'rps' => $this->rps ?? '',
            'rpo' => $this->rpo ?? '',
            'rpd' => $this->rpd ?? '',
            'alergi' => $this->alergi ?? '',
            'td' => $this->td ?? '',
            'nadi' => $this->nadi ?? '',
            'rr' => $this->rr ?? '',
            'suhu' => $this->suhu ?? '',
            'tb' => $this->tb ?? '',
            'bb' => $this->bb ?? '',
            'nyeri' => $this->nyeri ?? '',
            'status_nutrisi' => $this->nutrisi ?? '',
            'kondisi' => $this->kondisi ?? '',
            'ket_lokalis' => $this->ket_lokalis ?? '',
            'lab'   => $this->lab ?? '',
            'rad'   => $this->rad ?? '',
            'tes_pendengaran'   => $this->tes_pendengaran ?? '',  
            'penunjang' => $this->penunjang ?? '',
            'diagnosis' => $this->diagnosis ?? '',
            'diagnosisbanding' => $this->diagnosisbanding ?? '',
            'permasalahan' => $this->permasalahan ?? '',
            'terapi' => $this->terapi ?? '',
            'tindakan' => $this->tindakan ?? '',
            'tatalaksana' => $this->tatalaksana ?? '',
            'edukasi' => $this->edukasi ?? '',
        ];

        try{

            if($this->editMode){

                DB::table('penilaian_medis_ralan_tht')
                    ->where('no_rawat', $this->no_rawat)
                    ->update(Arr::except($data, ['no_rawat', 'kd_dokter']));

                $this->alert('success', 'Data berhasil diubah');

            }else{

                DB::table('penilaian_medis_ralan_tht')
                    ->insert($data);

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

            $data = DB::table('penilaian_medis_ralan_tht')
                    ->where('no_rawat', $this->no_rawat)
                    ->first();
            if($data){
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed' => 'hapusMedisRalanTHT',
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
            DB::table('penilaian_medis_ralan_tht')
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

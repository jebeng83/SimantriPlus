<?php

namespace App\Http\Livewire\Component;

use Illuminate\Support\Facades\App;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class FormMasterOperasi extends Component
{
    use LivewireAlert;
    public $nama_operasi, $diagnosa_preop, $diagnosa_postop, $laporan_operasi, $jaringan_dieksisi, $permintaan_pa = 'Tidak';
    public $no_template;
    public $modeEdit = false;
    protected $listeners = ['edit' => 'edit'];

    public function render()
    {
        return view('livewire.component.form-master-operasi');
    }

    public function resetInput()
    {
        $this->reset([
            'nama_operasi',
            'diagnosa_preop',
            'diagnosa_postop',
            'laporan_operasi',
            'jaringan_dieksisi',
        ]);
        $this->permintaan_pa = 'Tidak';
        $this->modeEdit = false;
    }

    public function edit($id)
    {
        $data = DB::table('template_laporan_operasi')->where('no_template', $id)->first();
        $this->no_template = $id;
        $this->nama_operasi = $data->nama_operasi;
        $this->diagnosa_preop = $data->diagnosa_preop;
        $this->diagnosa_postop = $data->diagnosa_postop;
        $this->laporan_operasi = $data->laporan_operasi;
        $this->jaringan_dieksisi = $data->jaringan_dieksisi;
        $this->permintaan_pa = $data->permintaan_pa;
        $this->modeEdit = true;
    }

    public function simpan()
    {
        $this->validate([
            'nama_operasi' => 'required',
            'diagnosa_preop' => 'required',
            'diagnosa_postop' => 'required',
            'laporan_operasi' => 'required',
            'jaringan_dieksisi' => 'required',
            'permintaan_pa' => 'required',
        ],[
            'nama_operasi.required' => 'Nama Operasi tidak boleh kosong',
            'diagnosa_preop.required' => 'Diagnosa Pra Operasi tidak boleh kosong',
            'diagnosa_postop.required' => 'Diagnosa Pasca Operasi tidak boleh kosong',
            'laporan_operasi.required' => 'Laporan Operasi tidak boleh kosong',
            'jaringan_dieksisi.required' => 'Jaringan Dieksi tidak boleh kosong',
            'permintaan_pa.required' => 'Permintaan PA tidak boleh kosong',
        ]);

        try{
            if($this->modeEdit){
                $data = [
                    'nama_operasi' => $this->nama_operasi,
                    'diagnosa_preop' => $this->diagnosa_preop,
                    'diagnosa_postop' => $this->diagnosa_postop,
                    'laporan_operasi' => $this->laporan_operasi,
                    'jaringan_dieksisi' => $this->jaringan_dieksisi,
                    'permintaan_pa' => $this->permintaan_pa,
                ];
                DB::table('template_laporan_operasi')->where('no_template', $this->no_template)->update($data);
                $this->resetInput();
                $this->emit('refreshTable');
                $this->alert('success', 'Berhasil ubah data');
            }else{
                $no = DB::table('template_laporan_operasi')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_template,4),signed)),0) as template")->first();
                $max_no = substr($no->template, 0, 4);
                $nextNo = sprintf('%04s', ($max_no + 1));
                $no_template = 'O'.$nextNo;
                // dd($no_template);
                $data = [
                    'no_template' => $no_template,
                    'nama_operasi' => $this->nama_operasi,
                    'diagnosa_preop' => $this->diagnosa_preop,
                    'diagnosa_postop' => $this->diagnosa_postop,
                    'laporan_operasi' => $this->laporan_operasi,
                    'jaringan_dieksisi' => $this->jaringan_dieksisi,
                    'permintaan_pa' => $this->permintaan_pa,
                ];
                DB::table('template_laporan_operasi')->insert($data);
                $this->resetInput();
                $this->emit('refreshTable');
                $this->alert('success', 'Berhasil tambah data');
            }

        }catch(\Exception $e){
            $this->alert('error', 'Gagal', [
                'position' =>  'center',
                'timer' =>  '',
                'toast' =>  false,
                'text' =>  App::environment('local') ? $e->getMessage() : 'Terjadi Kesalahan saat input data',
                'confirmButtonText' =>  'Tutup',
                'cancelButtonText' =>  'Batalkan',
                'showCancelButton' =>  false,
                'showConfirmButton' =>  true,
            ]);
        }
    }
}

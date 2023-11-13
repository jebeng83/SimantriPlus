<?php

namespace App\Http\Livewire\Component;

use Illuminate\Support\Facades\App;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithPagination;

class TableMasterOperasi extends Component
{
    use LivewireAlert, WithPagination;
    public $search = '';
    public $no_template;
    public $readyToLoad = false;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['refreshTable' => '$refresh', 'delete' => 'delete'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function loadDatas()
    {
        $this->readyToLoad = true;
    }

    public function render()
    {
        return view('livewire.component.table-master-operasi', [
            'templates' => $this->readyToLoad ? DB::table('template_laporan_operasi')
                        ->where('nama_operasi', 'like', '%'.$this->search.'%')
                        ->paginate(5) : []
        ]);
    }

    public function confirmDelete($id)
    {
        $this->no_template = $id;
        $this->confirm('Yakin ingin menghapus data ini?', [
            'toast' => false,
            'position' => 'center',
            'confirmButtonText' => 'Ya, Hapus',
            'cancelButtonText' => 'Tidak',
            'onConfirmed' => 'delete'
        ]);
    }

    public function delete()
    {
        try{

            DB::table('template_laporan_operasi')->where('no_template', $this->no_template)->delete();
            $this->alert('success', 'Data berhasil dihapus');
            $this->emit('refreshTable');

        }catch(\Exception $e){
            $this->alert('error', 'Gagal menghapus data', [
                'position' =>  'center',
                'timer' =>  '',
                'toast' =>  false,
                'text' => App::environment('local') ? $e->getMessage() : 'Terjadi kesalahan saat menhapus data',
                'confirmButtonText' =>  'Tutup',
                'showCancelButton' =>  false,
                'showConfirmButton' =>  true,
            ]);
        }
    }

    public function edit($id)
    {
        $this->emit('edit', $id);
    }
}

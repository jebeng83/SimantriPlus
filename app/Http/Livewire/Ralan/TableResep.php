<?php

namespace App\Http\Livewire\Ralan;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class TableResep extends Component
{
    use WithPagination;
    public $selectedItem = [], $selectAll = false, $resep, $noRawat, $dokter;

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->dokter = session()->get('username');
        $this->getResep();
    }

    public function render()
    {
        return view('livewire.ralan.table-resep');
    }

    public function hydrate()
    {
        $this->getResep();
    }

    public function getResep()
    {
        // Log untuk tracking
        Log::info('TableResep: Mengambil data resep untuk no_rawat: ' . $this->noRawat . ', dokter: ' . $this->dokter);
        
        // Ambil nomor resep terbaru untuk no_rawat ini
        $latestResep = DB::table('resep_obat')
            ->where('no_rawat', $this->noRawat)
            ->where('kd_dokter', $this->dokter)
            ->orderBy('tgl_peresepan', 'desc')
            ->orderBy('jam_peresepan', 'desc')
            ->select('no_resep')
            ->first();
        
        if (!$latestResep) {
            Log::info('TableResep: Tidak ada resep untuk no_rawat: ' . $this->noRawat);
            $this->resep = collect(); // Set resep ke collection kosong
            return;
        }
        
        Log::info('TableResep: Mengambil detail untuk resep no: ' . $latestResep->no_resep);
        
        // Ambil detail resep untuk nomor resep terbaru saja
        $this->resep = DB::table('resep_dokter')
            ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
            ->join('resep_obat', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
            ->where('resep_dokter.no_resep', $latestResep->no_resep)
            ->select('resep_dokter.no_resep', 'resep_dokter.kode_brng', 'resep_dokter.jml', 'databarang.nama_brng', 'resep_dokter.aturan_pakai', 'resep_obat.tgl_peresepan', 'resep_obat.jam_peresepan')
            ->get();
            
        Log::info('TableResep: Jumlah obat ditemukan: ' . $this->resep->count());
    }

    public function checkAll()
    {
        $this->selectAll = !$this->selectAll;
        
        if ($this->selectAll) {
            // Pilih semua item
            $this->selectedItem = $this->resep->pluck('kode_brng')->toArray();
        } else {
            // Kosongkan pilihan
            $this->selectedItem = [];
        }
    }

    public function selectResep($kode_obat)
    {
        // Cek apakah obat sudah ada di selectedItem
        $key = array_search($kode_obat, $this->selectedItem);
        
        if ($key !== false) {
            // Jika sudah ada, hapus dari array
            unset($this->selectedItem[$key]);
            $this->selectedItem = array_values($this->selectedItem); // Reindex array
        } else {
            // Jika belum ada, tambahkan ke array
            array_push($this->selectedItem, $kode_obat);
        }
        
        // Emit event untuk memberitahu komponen lain jika diperlukan
        $this->emit('selectedItemsUpdated', $this->selectedItem);
    }
    
    public function deleteSelected()
    {
        if (empty($this->selectedItem)) {
            return;
        }
        
        try {
            // Ambil data obat yang dipilih
            $resepsToDelete = $this->resep->whereIn('kode_brng', $this->selectedItem);
            
            foreach ($resepsToDelete as $r) {
                DB::table('resep_dokter')
                    ->where('no_resep', $r->no_resep)
                    ->where('kode_brng', $r->kode_brng)
                    ->delete();
                
                // Cek apakah masih ada obat lain di resep ini
                $remainingObats = DB::table('resep_dokter')
                    ->where('no_resep', $r->no_resep)
                    ->count();
                
                // Jika tidak ada obat lagi dan tidak ada racikan, hapus resep_obat
                if ($remainingObats == 0) {
                    $remainingRacikan = DB::table('resep_dokter_racikan')
                        ->where('no_resep', $r->no_resep)
                        ->count();
                    
                    if ($remainingRacikan == 0) {
                        DB::table('resep_obat')
                            ->where('no_resep', $r->no_resep)
                            ->delete();
                    }
                }
            }
            
            // Reset pilihan dan refresh data
            $this->selectedItem = [];
            $this->selectAll = false;
            $this->getResep();
            
            // Tampilkan pesan sukses
            $this->dispatchBrowserEvent('swal:success', [
                'title' => 'Berhasil',
                'text' => 'Obat berhasil dihapus',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting selected items: ' . $e->getMessage());
            $this->dispatchBrowserEvent('swal:error', [
                'title' => 'Gagal',
                'text' => 'Gagal menghapus obat: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }
}

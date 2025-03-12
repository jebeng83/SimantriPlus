<?php

namespace App\Http\Livewire\Ranap;

use App\Traits\SwalResponse;
use App\Traits\EnkripsiData;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CatatanPasien extends Component
{
    use SwalResponse;
    use EnkripsiData;
    public $noRawat, $catatan, $isCollapsed = true, $listCatatan = [], $noRm;
    protected $rules = [
        'catatan' => 'required',
    ];

    protected $messages = [
        'catatan.required' => 'Catatan tidak boleh kosong',
    ];

    public function mount($noRawat, $noRm)
    {
        $this->noRawat = $noRawat;
        
        // Pastikan $noRm sudah terdekripsi
        try {
            // Periksa apakah $noRm terenkripsi (biasanya diawali dengan eyJ)
            if (substr($noRm, 0, 3) === 'eyJ') {
                $decryptedValue = $this->decryptData($noRm);
                if ($decryptedValue) {
                    $this->noRm = $decryptedValue;
                } else {
                    $this->noRm = $noRm;
                }
            } else {
                $this->noRm = $noRm;
            }
        } catch (\Exception $e) {
            $this->noRm = $noRm;
        }
    }

    public function hydrate()
    {
        $this->getCatatan();
    }

    public function render()
    {
        return view('livewire.ranap.catatan-pasien');
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function getCatatan()
    {
        // Pastikan no_rkm_medis tidak terenkripsi
        $noRmToFind = $this->noRm;
        
        // Coba mendekripsi sekali lagi jika masih mengandung format terenkripsi
        if (substr($noRmToFind, 0, 3) === 'eyJ') {
            try {
                $decrypted = $this->decryptData($noRmToFind);
                if ($decrypted) {
                    $noRmToFind = $decrypted;
                }
            } catch (\Exception $e) {
                // Biarkan nilai asli jika dekripsi gagal
            }
            
            // Jika masih dalam format terenkripsi, coba cari di database
            if (substr($noRmToFind, 0, 3) === 'eyJ') {
                // Coba dapatkan data pasien berdasarkan noRawat
                $dataPasien = DB::table('reg_periksa')
                              ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                              ->where('reg_periksa.no_rawat', $this->noRawat)
                              ->select('pasien.no_rkm_medis')
                              ->first();
                
                if ($dataPasien && $dataPasien->no_rkm_medis) {
                    $noRmToFind = $dataPasien->no_rkm_medis;
                }
            }
        }
        
        // Cek semua catatan pasien
        try {
            $this->listCatatan = DB::table('catatan_pasien')
                                ->where('no_rkm_medis', $noRmToFind)
                                ->get();
            
            // Jika tidak ditemukan catatan, coba cari berdasarkan no_rawat
            if (count($this->listCatatan) === 0) {
                // Coba dapatkan no_rkm_medis dari reg_periksa sebagai fallback
                $dataPasien = DB::table('reg_periksa')
                              ->where('no_rawat', $this->noRawat)
                              ->first();
                
                if ($dataPasien && isset($dataPasien->no_rkm_medis)) {
                    $this->listCatatan = DB::table('catatan_pasien')
                                        ->where('no_rkm_medis', $dataPasien->no_rkm_medis)
                                        ->get();
                }
            }
        } catch (\Exception $e) {
            $this->listCatatan = [];
        }
    }

    public function simpanCatatan()
    {
        $this->validate();

        try{
            DB::beginTransaction();
            
            // Pastikan $noRm sudah terdekripsi terlebih dahulu
            $noRmToSave = $this->noRm;
            
            // Jika masih terlihat seperti data terenkripsi, coba ambil dari database
            if (substr($noRmToSave, 0, 3) === 'eyJ') {
                // Coba dapatkan data pasien berdasarkan noRawat
                $dataPasien = DB::table('reg_periksa')
                             ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                             ->where('reg_periksa.no_rawat', $this->noRawat)
                             ->select('pasien.no_rkm_medis')
                             ->first();
                
                if ($dataPasien && $dataPasien->no_rkm_medis) {
                    $noRmToSave = $dataPasien->no_rkm_medis;
                }
            }
            
            // Cek apakah nomor RM valid di database
            $exists = DB::table('pasien')->where('no_rkm_medis', $noRmToSave)->exists();
            
            if (!$exists) {
                // Jika tidak valid, cari catatan pasien berdasarkan no_rawat
                $dataPasien = DB::table('reg_periksa')
                             ->where('no_rawat', $this->noRawat)
                             ->first();
                
                if ($dataPasien && isset($dataPasien->no_rkm_medis)) {
                    $noRmToSave = $dataPasien->no_rkm_medis;
                } else {
                    throw new \Exception('Nomor rekam medis tidak dapat ditemukan');
                }
            }
            
            DB::table('catatan_pasien')
                ->insert([
                    'no_rkm_medis' => $noRmToSave,
                    'catatan' => $this->catatan,
                ]);
            
            DB::commit();
            $this->getCatatan();
            $this->catatan = '';
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Catatan Pasien berhasil ditambahkan'));
            
        } catch(\Exception $ex){
            DB::rollBack();
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Catatan Pasien gagal ditambahkan: ' . $ex->getMessage(), 'error'));
        }
    }

    public function hapusCatatan($noRM)
    {
        try{
            DB::beginTransaction();
            
            DB::table('catatan_pasien')
                ->where('no_rkm_medis', $noRM)
                ->delete();
            
            DB::commit();
            $this->getCatatan();
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Catatan Pasien berhasil dihapus'));
            
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollBack();
            $this->dispatchBrowserEvent('swal', $this->toastResponse($ex->getMessage() ?? 'Catatan Pasien gagal dihapus', 'error'));
        }
    }
}

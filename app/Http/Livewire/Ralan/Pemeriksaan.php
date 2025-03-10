<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Pemeriksaan extends Component
{
    use SwalResponse, LivewireAlert;
    public $listPemeriksaan, $isCollapsed = false, $noRawat, $noRm, $isMaximized = true, $keluhan, $pemeriksaan, $penilaian, $instruksi, $rtl, $alergi, $suhu, $berat, $tinggi, $tensi, $nadi, $respirasi, $evaluasi, $gcs, $kesadaran = 'Compos Mentis', $lingkar, $spo2;
    public $tgl, $jam;
    public $listeners = ['refreshData' => '$refresh', 'hapusPemeriksaan' => 'hapus', 'updateStatus' => 'updateStatusPasien'];

    public function mount($noRawat, $noRm)
    {
        $this->noRawat = $noRawat;
        $this->noRm = $noRm;
        if (!$this->isCollapsed) {
            $this->getPemeriksaan();
            $this->getListPemeriksaan();
            $this->pemeriksaan = $this->pemeriksaan ?? 'KU Baik, Composmentis
Thorax : Cor S1-2 intensitas normal, reguler, bising (-)
Pulmo : SDV +/+ ST -/-
Abdomen : Supel, NT(-), peristaltik (+) normal.
EXT : Oedem -/-';
            $this->keluhan = $this->keluhan ?? 'Pasien datang dengan keluhan';
            $this->penilaian = $this->penilaian ?? '-';
            $this->instruksi = $this->instruksi ?? 'Istirahat Cukup, PHBS';
            $this->rtl = $this->rtl ?? 'Edukasi Kesehatan';
            $this->alergi = $this->alergi ?? 'Tidak Ada';
            $this->suhu = $this->suhu ?? '36.5';
            $this->lingkar = $this->lingkar ?? '72';
            $this->nadi = $this->nadi ?? '82';
            $this->respirasi = $this->respirasi ?? '20';
            $this->spo2 = $this->spo2 ?? '96';
            $this->gcs = $this->gcs ?? '15';
            $this->evaluasi = $this->evaluasi ?? 'Kontrol Ulang Jika belum Ada Perubahan';
        }
    }

    public function openModal()
    {
        $this->emit('openModalRehabMedik');
    }

    public function render()
    {
        return view('livewire.ralan.pemeriksaan');
    }

    public function hydrate()
    {
        $this->getPemeriksaan();
        $this->getListPemeriksaan();
    }

    /**
     * Helper untuk mendekode no_rawat
     *
     * @param string $noRawat
     * @return string
     */
    private function decodeNoRawat($noRawat)
    {
        // Jika tidak ada parameter atau tidak ada karakter %, kembalikan nilai asli
        if (!$noRawat || strpos($noRawat, '%') === false) {
            return $noRawat;
        }
        
        $decodedNoRawat = $noRawat;
        $urlDecoded = urldecode($noRawat);
        
        // Coba base64 decode
        try {
            $base64Decoded = base64_decode($urlDecoded);
            if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                $decodedNoRawat = $base64Decoded;
                \Illuminate\Support\Facades\Log::info('No Rawat berhasil didekode: ' . $decodedNoRawat);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal mendekode no_rawat: ' . $e->getMessage());
        }
        
        return $decodedNoRawat;
    }

    public function getListPemeriksaan()
    {
        // Dekode no_rawat
        $decodedNoRawat = $this->decodeNoRawat($this->noRawat);
    
        $this->listPemeriksaan = DB::table('pemeriksaan_ralan')
            ->join('pegawai', 'pemeriksaan_ralan.nip', '=', 'pegawai.nik')
            ->where('no_rawat', $decodedNoRawat)
            ->select('pemeriksaan_ralan.*', 'pegawai.nama')
            ->get();
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function expanded()
    {
        $this->isMaximized = !$this->isMaximized;
    }

    public function getPemeriksaan()
    {
        // Dekode no_rawat
        $decodedNoRawat = $this->decodeNoRawat($this->noRawat);
    
        $data = DB::table('pasien')
            ->join('pemeriksaan_ralan', 'pasien.no_rkm_medis', '=', 'pemeriksaan_ralan.no_rawat')
            ->where('pasien.no_rkm_medis', $this->noRm)
            ->where('pemeriksaan_ralan.alergi', '<>', 'Tidak Ada')
            ->select('pemeriksaan_ralan.alergi')
            ->first();

        $pemeriksaan = DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $decodedNoRawat)
            ->orderBy('jam_rawat', 'desc')
            ->first();
        if ($pemeriksaan) {
            $this->keluhan = $pemeriksaan->keluhan;
            $this->pemeriksaan = $pemeriksaan->pemeriksaan;
            $this->penilaian = $pemeriksaan->penilaian;
            $this->instruksi = $pemeriksaan->instruksi;
            $this->rtl = $pemeriksaan->rtl;
            $this->alergi = $pemeriksaan->alergi ?? $data->alergi ?? 'Tidak Ada';
            $this->suhu = $pemeriksaan->suhu_tubuh;
            $this->berat = $pemeriksaan->berat;
            $this->tinggi = $pemeriksaan->tinggi;
            $this->tensi = $pemeriksaan->tensi;
            $this->nadi = $pemeriksaan->nadi;
            $this->respirasi = $pemeriksaan->respirasi;
            $this->evaluasi = $pemeriksaan->evaluasi;
            $this->gcs = $pemeriksaan->gcs;
            $this->kesadaran = $pemeriksaan->kesadaran;
            $this->lingkar = $pemeriksaan->lingkar_perut;
            $this->spo2 = $pemeriksaan->spo2;
        }
    }

    public function simpanPemeriksaan()
    {
        try {
            // Dekode no_rawat
            $decodedNoRawat = $this->decodeNoRawat($this->noRawat);
            
            // Verifikasi apakah no_rawat ada di tabel reg_periksa
            $cekReg = DB::table('reg_periksa')
                ->where('no_rawat', $decodedNoRawat)
                ->first();
                
            if (!$cekReg) {
                \Illuminate\Support\Facades\Log::error('No Rawat tidak ditemukan di reg_periksa: ' . $decodedNoRawat);
                throw new \Exception('Data registrasi pasien tidak ditemukan. Hubungi administrator.');
            }
            
            DB::beginTransaction();
            DB::table('pemeriksaan_ralan')
                ->insert([
                    'no_rawat' => $decodedNoRawat, // Gunakan no_rawat yang sudah didekode
                    'keluhan' => $this->keluhan ?? '-',
                    'pemeriksaan' => $this->pemeriksaan ?? '-',
                    'penilaian' => $this->penilaian ?? '-',
                    'instruksi' => $this->instruksi ?? '-',
                    'rtl' => $this->rtl ?? '-',
                    'alergi' => $this->alergi ?? '-',
                    'suhu_tubuh' => $this->suhu,
                    'berat' => $this->berat ?? '0',
                    'tinggi' => $this->tinggi ?? '0',
                    'tensi' => $this->tensi ?? '-',
                    'nadi' => $this->nadi ?? '-',
                    'respirasi' => $this->respirasi ?? '-',
                    'gcs' => $this->gcs ?? '-',
                    'kesadaran' => $this->kesadaran ?? 'Compos Mentis',
                    'lingkar_perut' => $this->lingkar ?? '0',
                    'spo2' => $this->spo2 ?? '-',
                    'evaluasi' => $this->evaluasi ?? '-',
                    'tgl_perawatan' => date('Y-m-d'),
                    'jam_rawat' => date('H:i:s'),
                    'nip' => session()->get('username'),
                ]);
            
            // Update status pasien juga menggunakan no_rawat yang sudah didekode
            DB::table('reg_periksa')
                ->where('no_rawat', $decodedNoRawat)
                ->update(['stts' => 'Sudah']);

            DB::commit();
            $this->getListPemeriksaan();
            
            $this->alert('success', 'Pemeriksaan berhasil disimpan dan status pasien telah diupdate', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
            ]);
            
            $this->emit('refreshData');
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Error QueryException saat simpan pemeriksaan: ' . $ex->getMessage(), [
                'no_rawat_original' => $this->noRawat,
                'decoded' => $decodedNoRawat ?? 'not_decoded',
                'code' => $ex->getCode(),
                'sql' => $ex->getSql() ?? 'undefined'
            ]);
            $this->dispatchBrowserEvent('swal:pemeriksaan', $this->toastResponse($ex->getMessage() ?? 'Pemeriksaan gagal ditambahkan', 'error'));
        } catch (\Exception $e) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Error Exception saat simpan pemeriksaan: ' . $e->getMessage());
            $this->dispatchBrowserEvent('swal:pemeriksaan', $this->toastResponse($e->getMessage() ?? 'Pemeriksaan gagal ditambahkan', 'error'));
        }
    }

    public function confirmHapus($noRawat, $tgl, $jam)
    {
        $this->noRawat = $noRawat;
        $this->tgl = $tgl;
        $this->jam = $jam;
        $this->confirm('Yakin ingin menghapus pemeriksaan ini?', [
            'toast' => false,
            'position' => 'center',
            'showConfirmButton' => true,
            'cancelButtonText' => 'Tidak',
            'onConfirmed' => 'hapusPemeriksaan',
        ]);
    }

    public function hapus()
    {
        try {
            // Dekode no_rawat jika perlu
            $decodedNoRawat = $this->decodeNoRawat($this->noRawat);
            
            DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $decodedNoRawat)
                ->where('tgl_perawatan', $this->tgl)
                ->where('jam_rawat', $this->jam)
                ->delete();
            $this->getListPemeriksaan();
            $this->alert('success', 'Pemeriksaan berhasil dihapus', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saat hapus pemeriksaan: ' . $e->getMessage(), [
                'no_rawat' => $this->noRawat,
                'decoded' => $decodedNoRawat ?? 'not_decoded',
                'tgl' => $this->tgl,
                'jam' => $this->jam
            ]);
            $this->alert('error', 'Gagal', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
                'text' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Update status pasien menjadi "Sudah"
     * Dapat dipanggil dari komponen lain
     * 
     * @return void
     */
    public function updateStatusPasien()
    {
        try {
            DB::table('reg_periksa')
                ->where('no_rawat', $this->noRawat)
                ->update(['stts' => 'Sudah']);
            
            $this->alert('success', 'Status pasien berhasil diupdate menjadi Sudah', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal mengupdate status pasien: ' . $e->getMessage(), [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
            ]);
        }
    }
}

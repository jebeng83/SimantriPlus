<!-- Debug Info untuk development -->
@if(app()->environment('local') || app()->environment('development'))
<div class="debug-info p-2 mb-2 border border-secondary rounded bg-light" style="font-size: 12px;">
   <strong>Debug Info:</strong>
   <ul class="mb-0 pl-3">
      <li>noRawat: {{ $noRawat ?? 'Tidak ada' }}</li>
      <li>noRm: {{ $noRm ?? 'Tidak ada' }}</li>
   </ul>
</div>
@endif

<!-- Error Messages -->
@if (session()->has('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
   <h5><i class="icon fas fa-ban"></i> Error!</h5>
   {{ session('error') }}
   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
   </button>
</div>
@endif

@if (session()->has('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
   <h5><i class="icon fas fa-check"></i> Success!</h5>
   {{ session('success') }}
   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
   </button>
</div>
@endif

<!-- Loading Indicator -->
<div wire:loading class="text-center p-3">
   <div class="spinner-border text-primary" role="status">
      <span class="sr-only">Loading...</span>
   </div>
</div>

<!-- Main Content -->
<div wire:loading.remove>
   <form wire:submit.prevent="save">
      <!-- Data Wajib Diisi -->
      <div class="form-group">
         <h5 class="mb-3 font-weight-bold">Data Wajib Diisi</h5>

         <div class="form-group row">
            <label for="tanggal_anc" class="col-sm-2 col-form-label">Tanggal ANC</label>
            <div class="col-sm-4">
               <div class="input-group">
                  <input type="datetime-local" class="form-control @error('tanggal_anc') is-invalid @enderror"
                     wire:model="tanggal_anc" id="tanggal_anc">
                  <div class="input-group-append">
                     <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                  </div>
                  @error('tanggal_anc')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>

            <label for="diperiksa_oleh" class="col-sm-2 col-form-label">Diperiksa Oleh</label>
            <div class="col-sm-4">
               <select class="form-control @error('diperiksa_oleh') is-invalid @enderror" wire:model="diperiksa_oleh"
                  id="diperiksa_oleh">
                  <option value="">- Pilih Petugas -</option>
                  @if(isset($petugas) && $petugas->count() > 0)
                  @foreach($petugas as $p)
                  <option value="{{ $p->nama }}">{{ $p->nama }}</option>
                  @endforeach
                  @endif
               </select>
               @error('diperiksa_oleh')
               <div class="invalid-feedback">{{ $message }}</div>
               @enderror
            </div>
         </div>

         <div class="form-group row">
            <label for="usia_kehamilan" class="col-sm-2 col-form-label">Usia Kehamilan</label>
            <div class="col-sm-4">
               <div class="input-group">
                  <input type="number" class="form-control @error('usia_kehamilan') is-invalid @enderror"
                     wire:model="usia_kehamilan" id="usia_kehamilan">
                  <div class="input-group-append">
                     <span class="input-group-text">Minggu</span>
                  </div>
                  @error('usia_kehamilan')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>

            <label for="trimester" class="col-sm-2 col-form-label">Trimester</label>
            <div class="col-sm-4">
               <div class="d-flex">
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="trimester" id="trimester1"
                        wire:model="trimester" value="1">
                     <label class="form-check-label" for="trimester1">1</label>
                  </div>
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="trimester" id="trimester2"
                        wire:model="trimester" value="2">
                     <label class="form-check-label" for="trimester2">2</label>
                  </div>
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="trimester" id="trimester3"
                        wire:model="trimester" value="3">
                     <label class="form-check-label" for="trimester3">3</label>
                  </div>
               </div>
               @error('trimester')
               <div class="text-danger">{{ $message }}</div>
               @enderror
            </div>
         </div>

         <div class="form-group row">
            <label for="kunjungan_ke" class="col-sm-2 col-form-label">Kunjungan K</label>
            <div class="col-sm-4">
               <select class="form-control @error('kunjungan_ke') is-invalid @enderror" wire:model="kunjungan_ke"
                  id="kunjungan_ke">
                  <option value="">- Pilih -</option>
                  <option value="1">K1</option>
                  <option value="2">K2</option>
                  <option value="3">K3</option>
                  <option value="4">K4</option>
                  <option value="5">K5</option>
                  <option value="6">K6</option>
               </select>
               @error('kunjungan_ke')
               <div class="invalid-feedback">{{ $message }}</div>
               @enderror
            </div>
         </div>
      </div>

      <!-- Timbang Berat Badan dan Ukur Tinggi Badan -->
      <div class="form-group">
         <h5 class="mb-3 font-weight-bold">Timbang Berat Badan dan Ukur Tinggi Badan</h5>

         <div class="form-group row">
            <label for="berat_badan" class="col-sm-2 col-form-label">Berat Badan (saat ini)</label>
            <div class="col-sm-4">
               <div class="input-group">
                  <input type="number" step="0.01" class="form-control @error('berat_badan') is-invalid @enderror"
                     wire:model="berat_badan" id="berat_badan" wire:change="hitungIMT">
                  <div class="input-group-append">
                     <span class="input-group-text">Kg</span>
                  </div>
                  @error('berat_badan')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>

            <label for="tinggi_badan" class="col-sm-2 col-form-label">Tinggi Badan</label>
            <div class="col-sm-4">
               <div class="input-group">
                  <input type="number" step="0.01" class="form-control @error('tinggi_badan') is-invalid @enderror"
                     wire:model="tinggi_badan" id="tinggi_badan" wire:change="hitungIMT">
                  <div class="input-group-append">
                     <span class="input-group-text">cm</span>
                  </div>
                  @error('tinggi_badan')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
         </div>

         <div class="form-group row">
            <label for="imt" class="col-sm-2 col-form-label">IMT Saat ini</label>
            <div class="col-sm-4">
               <input type="number" step="0.01" class="form-control" wire:model="imt" id="imt" readonly>
            </div>

            <label for="kategori_imt" class="col-sm-2 col-form-label">Kategori IMT</label>
            <div class="col-sm-4">
               <input type="text" class="form-control" wire:model="kategori_imt" id="kategori_imt" readonly>
            </div>
         </div>

         <div class="form-group row">
            <label for="jumlah_janin" class="col-sm-2 col-form-label">Jumlah Janin</label>
            <div class="col-sm-4">
               <div class="d-flex">
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="jumlah_janin" id="janin_tidak_diketahui"
                        wire:model="jumlah_janin" value="Tidak Diketahui">
                     <label class="form-check-label" for="janin_tidak_diketahui">Tidak Diketahui</label>
                  </div>
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="jumlah_janin" id="janin_tunggal"
                        wire:model="jumlah_janin" value="Tunggal">
                     <label class="form-check-label" for="janin_tunggal">Tunggal</label>
                  </div>
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="jumlah_janin" id="janin_ganda"
                        wire:model="jumlah_janin" value="Ganda">
                     <label class="form-check-label" for="janin_ganda">Ganda</label>
                  </div>
               </div>
               @error('jumlah_janin')
               <div class="text-danger">{{ $message }}</div>
               @enderror
            </div>
         </div>
      </div>

      <!-- Ukur Tekanan Darah -->
      <div class="form-group">
         <h5 class="mb-3 font-weight-bold">Ukur Tekanan Darah</h5>

         <div class="form-group row">
            <label for="td_sistole" class="col-sm-2 col-form-label">TD Sistole</label>
            <div class="col-sm-4">
               <div class="input-group">
                  <input type="number" class="form-control @error('td_sistole') is-invalid @enderror"
                     wire:model="td_sistole" id="td_sistole">
                  <div class="input-group-append">
                     <span class="input-group-text">mm</span>
                  </div>
                  @error('td_sistole')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>

            <label for="td_diastole" class="col-sm-2 col-form-label">TD Diastole</label>
            <div class="col-sm-4">
               <div class="input-group">
                  <input type="number" class="form-control @error('td_diastole') is-invalid @enderror"
                     wire:model="td_diastole" id="td_diastole">
                  <div class="input-group-append">
                     <span class="input-group-text">HG</span>
                  </div>
                  @error('td_diastole')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
         </div>
      </div>

      <!-- Pemberian Tablet Tambah Darah (TTD) -->
      <div class="form-group">
         <h5 class="mb-3 font-weight-bold">Pemberian Tablet Tambah Darah (TTD)</h5>

         <div class="form-group row">
            <label for="jumlah_fe" class="col-sm-2 col-form-label">Jumlah Tablet Fe</label>
            <div class="col-sm-4">
               <div class="input-group">
                  <input type="number" class="form-control @error('jumlah_fe') is-invalid @enderror"
                     wire:model="jumlah_fe" id="jumlah_fe">
                  <div class="input-group-append">
                     <span class="input-group-text">(Tab/Botol)</span>
                  </div>
                  @error('jumlah_fe')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>

            <label for="dosis" class="col-sm-2 col-form-label">Dosis (Tablet/hari)</label>
            <div class="col-sm-4">
               <input type="number" class="form-control @error('dosis') is-invalid @enderror" wire:model="dosis"
                  id="dosis">
               @error('dosis')
               <div class="invalid-feedback">{{ $message }}</div>
               @enderror
            </div>
         </div>
      </div>

      <!-- Pemeriksaan Laboratorium -->
      <div class="form-group">
         <h5 class="mb-3 font-weight-bold">Pemeriksaan Laboratorium</h5>

         <div class="form-group row">
            <label for="pemeriksaan_lab" class="col-sm-2 col-form-label">Pemeriksaan</label>
            <div class="col-sm-10">
               <select class="form-control @error('pemeriksaan_lab') is-invalid @enderror" wire:model="pemeriksaan_lab"
                  id="pemeriksaan_lab">
                  <option value="">- Pilih Pemeriksaan -</option>
                  <option value="COVID-19">COVID-19</option>
                  <option value="Hemoglobin (Hb)">Hemoglobin (Hb)</option>
                  <option value="Anemia">Anemia</option>
                  <option value="Protein Urin">Protein Urin</option>
                  <option value="Reduksi Gula Darah">Reduksi Gula Darah</option>
                  <option value="VDRL (Veneral Disease Research Lab)">VDRL (Veneral Disease Research Lab)</option>
                  <option value="HIV">HIV</option>
                  <option value="Sifilis">Sifilis</option>
                  <option value="HBsAg">HBsAg</option>
                  <option value="Thalasemia">Thalasemia</option>
                  <option value="Lainnya">Lainnya</option>
               </select>
               @error('pemeriksaan_lab')
               <div class="invalid-feedback">{{ $message }}</div>
               @enderror
            </div>
         </div>
      </div>

      <!-- Tatalaksana Kasus -->
      <div class="form-group">
         <h5 class="mb-3 font-weight-bold">Tatalaksana Kasus</h5>

         <div class="form-group row">
            <label for="jenis_tatalaksana" class="col-sm-2 col-form-label">Jenis Tatalaksana</label>
            <div class="col-sm-10">
               <select class="form-control @error('jenis_tatalaksana') is-invalid @enderror"
                  wire:model="jenis_tatalaksana" id="jenis_tatalaksana">
                  <option value="">- Pilih Jenis tatalaksana -</option>
                  <option value="Anemia">Anemia</option>
                  <option value="Makanan Tambahan Ibu Hamil">Makanan Tambahan Ibu Hamil</option>
                  <option value="Hipertensi">Hipertensi</option>
                  <option value="Eklampsia">Eklampsia</option>
                  <option value="KEK">KEK</option>
                  <option value="Obesitas">Obesitas</option>
                  <option value="Infeksi">Infeksi</option>
                  <option value="Penyakit Jantung">Penyakit Jantung</option>
                  <option value="HIV">HIV</option>
                  <option value="TB">TB</option>
                  <option value="Malaria">Malaria</option>
                  <option value="Kecacingan">Kecacingan</option>
                  <option value="Infeksi Menular Seksual (IMS)">Infeksi Menular Seksual (IMS)</option>
                  <option value="Hepatitis">Hepatitis</option>
                  <option value="Lainnya">Lainnya</option>
               </select>
               @error('jenis_tatalaksana')
               <div class="invalid-feedback">{{ $message }}</div>
               @enderror
            </div>
         </div>
      </div>

      <!-- Temu Wicara/Konseling -->
      <div class="form-group">
         <h5 class="mb-3 font-weight-bold">Temu Wicara/Konseling</h5>

         <div class="form-group row">
            <label for="materi" class="col-sm-2 col-form-label">Materi <span class="text-danger">*</span></label>
            <div class="col-sm-10">
               <textarea class="form-control @error('materi') is-invalid @enderror" rows="3" wire:model="materi"
                  id="materi"></textarea>
               @error('materi')
               <div class="invalid-feedback">{{ $message }}</div>
               @enderror
            </div>
         </div>

         <div class="form-group row">
            <label for="rekomendasi" class="col-sm-2 col-form-label">Rekomendasi Berdasarkan Hasil Pemeriksaan dan
               Laboratorium <span class="text-danger">*</span></label>
            <div class="col-sm-10">
               <textarea class="form-control @error('rekomendasi') is-invalid @enderror" rows="3"
                  wire:model="rekomendasi" id="rekomendasi"></textarea>
               @error('rekomendasi')
               <div class="invalid-feedback">{{ $message }}</div>
               @enderror
            </div>
         </div>

         <div class="form-group row">
            <label for="konseling_menyusui" class="col-sm-2 col-form-label">Konseling Menyusui <span
                  class="text-danger">*</span></label>
            <div class="col-sm-10">
               <div class="d-flex">
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="konseling_menyusui" id="menyusui_ya"
                        wire:model="konseling_menyusui" value="Ya">
                     <label class="form-check-label" for="menyusui_ya">Ya</label>
                  </div>
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="konseling_menyusui" id="menyusui_tidak"
                        wire:model="konseling_menyusui" value="Tidak">
                     <label class="form-check-label" for="menyusui_tidak">Tidak</label>
                  </div>
               </div>
               @error('konseling_menyusui')
               <div class="text-danger">{{ $message }}</div>
               @enderror
            </div>
         </div>

         <div class="form-group row">
            <label for="tanda_bahaya_kehamilan" class="col-sm-2 col-form-label">Tanda Bahaya Kehamilan <span
                  class="text-danger">*</span></label>
            <div class="col-sm-10">
               <div class="d-flex">
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="tanda_bahaya_kehamilan" id="bahaya_hamil_ya"
                        wire:model="tanda_bahaya_kehamilan" value="Ya">
                     <label class="form-check-label" for="bahaya_hamil_ya">Ya</label>
                  </div>
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="tanda_bahaya_kehamilan" id="bahaya_hamil_tidak"
                        wire:model="tanda_bahaya_kehamilan" value="Tidak">
                     <label class="form-check-label" for="bahaya_hamil_tidak">Tidak</label>
                  </div>
               </div>
               @error('tanda_bahaya_kehamilan')
               <div class="text-danger">{{ $message }}</div>
               @enderror
            </div>
         </div>

         <div class="form-group row">
            <label for="tanda_bahaya_persalinan" class="col-sm-2 col-form-label">Tanda Bahaya Persalinan <span
                  class="text-danger">*</span></label>
            <div class="col-sm-10">
               <div class="d-flex">
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="tanda_bahaya_persalinan" id="bahaya_salin_ya"
                        wire:model="tanda_bahaya_persalinan" value="Ya">
                     <label class="form-check-label" for="bahaya_salin_ya">Ya</label>
                  </div>
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="tanda_bahaya_persalinan" id="bahaya_salin_tidak"
                        wire:model="tanda_bahaya_persalinan" value="Tidak">
                     <label class="form-check-label" for="bahaya_salin_tidak">Tidak</label>
                  </div>
               </div>
               @error('tanda_bahaya_persalinan')
               <div class="text-danger">{{ $message }}</div>
               @enderror
            </div>
         </div>

         <div class="form-group row">
            <label for="konseling_phbs" class="col-sm-2 col-form-label">Konseling PHBS <span
                  class="text-danger">*</span></label>
            <div class="col-sm-10">
               <div class="d-flex">
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="konseling_phbs" id="phbs_ya"
                        wire:model="konseling_phbs" value="Ya">
                     <label class="form-check-label" for="phbs_ya">Ya</label>
                  </div>
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="konseling_phbs" id="phbs_tidak"
                        wire:model="konseling_phbs" value="Tidak">
                     <label class="form-check-label" for="phbs_tidak">Tidak</label>
                  </div>
               </div>
               @error('konseling_phbs')
               <div class="text-danger">{{ $message }}</div>
               @enderror
            </div>
         </div>

         <div class="form-group row">
            <label for="konseling_gizi" class="col-sm-2 col-form-label">Konseling Gizi Ibu Hamil <span
                  class="text-danger">*</span></label>
            <div class="col-sm-10">
               <div class="d-flex">
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="konseling_gizi" id="gizi_ya"
                        wire:model="konseling_gizi" value="Ya">
                     <label class="form-check-label" for="gizi_ya">Ya</label>
                  </div>
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="konseling_gizi" id="gizi_tidak"
                        wire:model="konseling_gizi" value="Tidak">
                     <label class="form-check-label" for="gizi_tidak">Tidak</label>
                  </div>
               </div>
               @error('konseling_gizi')
               <div class="text-danger">{{ $message }}</div>
               @enderror
            </div>
         </div>

         <div class="form-group row">
            <label for="konseling_ibu_hamil" class="col-sm-2 col-form-label">Konseling Ibu Hamil <span
                  class="text-danger">*</span></label>
            <div class="col-sm-10">
               <div class="d-flex">
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="konseling_ibu_hamil" id="ibu_hamil_ya"
                        wire:model="konseling_ibu_hamil" value="Ya">
                     <label class="form-check-label" for="ibu_hamil_ya">Ya</label>
                  </div>
                  <div class="form-check form-check-inline">
                     <input class="form-check-input" type="radio" name="konseling_ibu_hamil" id="ibu_hamil_tidak"
                        wire:model="konseling_ibu_hamil" value="Tidak">
                     <label class="form-check-label" for="ibu_hamil_tidak">Tidak</label>
                  </div>
               </div>
               @error('konseling_ibu_hamil')
               <div class="text-danger">{{ $message }}</div>
               @enderror
            </div>
         </div>

         <div class="form-group row">
            <label for="konseling_lainnya" class="col-sm-2 col-form-label">Konseling Lainnya</label>
            <div class="col-sm-10">
               <input type="text" class="form-control" wire:model="konseling_lainnya" id="konseling_lainnya"
                  placeholder="Sebutkan">
            </div>
         </div>
      </div>

      <!-- Keadaan Pulang -->
      <div class="form-group">
         <h5 class="mb-3 font-weight-bold">Keadaan Pulang</h5>

         <div class="form-group row">
            <label for="keadaan_pulang" class="col-sm-2 col-form-label">Keadaan Pulang <span
                  class="text-danger">*</span></label>
            <div class="col-sm-10">
               <select class="form-control @error('keadaan_pulang') is-invalid @enderror" wire:model="keadaan_pulang"
                  id="keadaan_pulang">
                  <option value="">- Pilih -</option>
                  <option value="Baik">Baik</option>
                  <option value="Dirujuk">Dirujuk</option>
                  <option value="Pulang Paksa">Pulang Paksa</option>
                  <option value="Meninggal">Meninggal</option>
               </select>
               @error('keadaan_pulang')
               <div class="invalid-feedback">{{ $message }}</div>
               @enderror
            </div>
         </div>
      </div>

      <div class="form-group row">
         <div class="col-sm-10 offset-sm-2">
            <button type="button" class="btn btn-secondary" wire:click="batal">Batal</button>
            <button type="submit" class="btn btn-success">Tambahkan</button>
         </div>
      </div>
   </form>
</div>

@push('scripts')
<script>
   document.addEventListener('livewire:load', function () {
      console.log('Pemeriksaan ANC component loaded');
      
      Livewire.on('showError', function(message) {
         console.error('PemeriksaanANC Error:', message);
      });
      
      Livewire.on('formSaved', function() {
         console.log('Pemeriksaan ANC saved');
      });
   });
</script>
@endpush
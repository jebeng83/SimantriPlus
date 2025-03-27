<div>
   @push('styles')
   <style>
      /* Style untuk printing */
      @media print {
         .no-print {
            display: none !important;
         }

         .print-only {
            display: block !important;
         }

         .form-control {
            border: none;
            padding: 0;
            margin-bottom: 0.2rem;
         }

         .card {
            border: none;
            box-shadow: none;
         }

         .position-fixed {
            display: none;
         }

         button {
            display: none;
         }

         select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: none;
         }

         .form-check-input {
            margin-left: 0;
         }
      }

      /* Mengurangi margin dan padding berlebih */
      .card-body {
         padding: 1rem;
      }

      .form-group {
         margin-bottom: 0.8rem;
      }

      h5.mb-3 {
         margin-bottom: 0.75rem !important;
      }

      /* Membuat tampilan lebih kompak */
      .form-group.row {
         margin-bottom: 0.5rem;
      }

      .nav-pills {
         margin-bottom: 0.5rem;
      }

      /* Menyesuaikan dengan #patient-content */
      #temu-wicara,
      #keadaan-pulang,
      #data-wajib,
      #ukur-bb-tb,
      #ukur-td,
      #tablet-tambah-darah,
      #pemeriksaan-lab,
      #tatalaksana-kasus {
         padding-top: 0.5rem;
      }

      /* Animasi loading */
      .spinner-wave {
         width: 100px;
         height: 100px;
         margin: auto;
         position: relative;
      }

      .spinner-wave div {
         position: absolute;
         bottom: 0;
         width: 15px;
         height: 15px;
         background-color: #3498db;
         border-radius: 50%;
         animation: wave 1.2s infinite ease-in-out;
      }

      .spinner-wave div:nth-child(1) {
         left: 0;
         animation-delay: 0s;
      }

      .spinner-wave div:nth-child(2) {
         left: 25px;
         animation-delay: 0.2s;
      }

      .spinner-wave div:nth-child(3) {
         left: 50px;
         animation-delay: 0.4s;
      }

      .spinner-wave div:nth-child(4) {
         left: 75px;
         animation-delay: 0.6s;
      }

      @keyframes wave {

         0%,
         100% {
            transform: translateY(0);
         }

         50% {
            transform: translateY(-20px);
         }
      }
   </style>
   @endpush

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

   <!-- Perhatian Khusus - Keterangan field wajib -->
   {{-- <div class="alert alert-info alert-dismissible fade show no-print" role="alert">
      <h5><i class="icon fas fa-info"></i> Perhatian!</h5>
      <p>Field dengan tanda <span class="text-danger">*</span> wajib diisi. Pastikan semua data terisi dengan benar
         sebelum menyimpan.</p>
      <p>Pemeriksaan ANC ini merupakan pemeriksaan untuk pasien <strong>{{ $noRm }}</strong> dengan No. Rawat <strong>{{
            $noRawat }}</strong>.</p>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
      </button>
   </div> --}}

   <!-- Loading Indicator -->
   <div wire:loading class="text-center p-3">
      <div class="spinner-wave">
         <div></div>
         <div></div>
         <div></div>
         <div></div>
      </div>
      <p class="mt-2 text-primary">Memproses data...</p>
   </div>

   <!-- Main Content -->
   <div wire:loading.remove>
      <form wire:submit.prevent="save">
         <!-- Navigation Pills -->
         {{-- <div class="card mb-4 no-print">
            <div class="card-header bg-primary text-white">
               <h5 class="mb-0">Navigasi Cepat</h5>
            </div> --}}
            <div class="card-body">
               <nav class="nav nav-pills flex-column flex-sm-row">
                  <a class="flex-sm-fill text-sm-center nav-link active" href="#data-wajib"
                     onclick="event.preventDefault(); document.getElementById('data-wajib').scrollIntoView({behavior: 'smooth'})">
                     <i class="fas fa-clipboard-list mr-1"></i> Data Wajib
                  </a>
                  <a class="flex-sm-fill text-sm-center nav-link" href="#ukur-bb-tb"
                     onclick="event.preventDefault(); document.getElementById('ukur-bb-tb').scrollIntoView({behavior: 'smooth'})">
                     <i class="fas fa-weight mr-1"></i> Ukur BB & TB
                  </a>
                  <a class="flex-sm-fill text-sm-center nav-link" href="#ukur-td"
                     onclick="event.preventDefault(); document.getElementById('ukur-td').scrollIntoView({behavior: 'smooth'})">
                     <i class="fas fa-heartbeat mr-1"></i> Ukur TD
                  </a>
                  <a class="flex-sm-fill text-sm-center nav-link" href="#tablet-tambah-darah"
                     onclick="event.preventDefault(); document.getElementById('tablet-tambah-darah').scrollIntoView({behavior: 'smooth'})">
                     <i class="fas fa-capsules mr-1"></i> Tablet Fe
                  </a>
               </nav>
               <nav class="nav nav-pills flex-column flex-sm-row mt-2">
                  <a class="flex-sm-fill text-sm-center nav-link" href="#pemeriksaan-lab"
                     onclick="event.preventDefault(); document.getElementById('pemeriksaan-lab').scrollIntoView({behavior: 'smooth'})">
                     <i class="fas fa-flask mr-1"></i> Lab
                  </a>
                  <a class="flex-sm-fill text-sm-center nav-link" href="#tatalaksana-kasus"
                     onclick="event.preventDefault(); document.getElementById('tatalaksana-kasus').scrollIntoView({behavior: 'smooth'})">
                     <i class="fas fa-procedures mr-1"></i> Tatalaksana
                  </a>
                  <a class="flex-sm-fill text-sm-center nav-link" href="#temu-wicara"
                     onclick="event.preventDefault(); document.getElementById('temu-wicara').scrollIntoView({behavior: 'smooth'})">
                     <i class="fas fa-comments mr-1"></i> Konseling
                  </a>
                  <a class="flex-sm-fill text-sm-center nav-link" href="#keadaan-pulang"
                     onclick="event.preventDefault(); document.getElementById('keadaan-pulang').scrollIntoView({behavior: 'smooth'})">
                     <i class="fas fa-home mr-1"></i> Pulang
                  </a>
               </nav>
            </div>
         </div>

         <!-- Data Wajib Diisi -->
         <div class="form-group" id="data-wajib">
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
         <div class="form-group" id="ukur-bb-tb">
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
         <div class="form-group" id="ukur-td">
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
         <div class="form-group" id="tablet-tambah-darah">
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
         <div class="form-group" id="pemeriksaan-lab">
            <h5 class="mb-3 font-weight-bold">Pemeriksaan Laboratorium</h5>

            <div class="form-group row">
               <label for="pemeriksaan_lab" class="col-sm-2 col-form-label">Pemeriksaan</label>
               <div class="col-sm-10">
                  <select class="form-control @error('pemeriksaan_lab') is-invalid @enderror"
                     wire:model="pemeriksaan_lab" id="pemeriksaan_lab">
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
         <div class="form-group" id="tatalaksana-kasus">
            <h5 class="mb-3 font-weight-bold">Tatalaksana Kasus</h5>

            <div class="form-group row">
               <label for="jenis_tatalaksana" class="col-sm-2 col-form-label">Jenis Tatalaksana</label>
               <div class="col-sm-10">
                  <select class="form-control @error('jenis_tatalaksana') is-invalid @enderror"
                     wire:model="jenis_tatalaksana" id="jenis_tatalaksana" wire:change="onChangeTatalaksana">
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

            <!-- Form khusus untuk Anemia -->
            @if($jenis_tatalaksana == 'Anemia')
            <div class="card mt-3 border-left-primary" x-data="{ show: false }"
               x-init="setTimeout(() => { show = true }, 50)" x-show="show"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform scale-90"
               x-transition:enter-end="opacity-100 transform scale-100">
               <div class="card-body">
                  <div class="form-group row">
                     <label for="diberikan_tablet_fe" class="col-sm-2 col-form-label">Diberikan Tablet FE <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="diberikan_tablet_fe" id="tablet_fe_ya"
                                 wire:model="diberikan_tablet_fe" value="Ya">
                              <label class="form-check-label" for="tablet_fe_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="diberikan_tablet_fe"
                                 id="tablet_fe_tidak" wire:model="diberikan_tablet_fe" value="Tidak">
                              <label class="form-check-label" for="tablet_fe_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('diberikan_tablet_fe')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="jumlah_tablet_dikonsumsi" class="col-sm-2 col-form-label">Jumlah Tablet Fe yang
                        dikonsumsi
                        <span class="text-danger">*</span></label>
                     <div class="col-sm-8">
                        <input type="number"
                           class="form-control @error('jumlah_tablet_dikonsumsi') is-invalid @enderror"
                           wire:model="jumlah_tablet_dikonsumsi" id="jumlah_tablet_dikonsumsi" min="0">
                        @error('jumlah_tablet_dikonsumsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                     </div>
                     <div class="col-sm-2">
                        <div class="form-control bg-light text-center">(Tab/Botol)</div>
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="jumlah_tablet_ditambahkan" class="col-sm-2 col-form-label">Jumlah Tablet Fe yang
                        ditambahkan saat ini <span class="text-danger">*</span></label>
                     <div class="col-sm-8">
                        <input type="number"
                           class="form-control @error('jumlah_tablet_ditambahkan') is-invalid @enderror"
                           wire:model="jumlah_tablet_ditambahkan" id="jumlah_tablet_ditambahkan" min="0">
                        @error('jumlah_tablet_ditambahkan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                     </div>
                     <div class="col-sm-2">
                        <div class="form-control bg-light text-center">(Tab/Botol)</div>
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="tatalaksana_lainnya" class="col-sm-2 col-form-label">Lain-lain</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" wire:model="tatalaksana_lainnya"
                           id="tatalaksana_lainnya" placeholder="Lainnya">
                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-danger" wire:click="hapusFormAnemia">
                           <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            <!-- Form khusus untuk Makanan Tambahan Ibu Hamil -->
            @if($jenis_tatalaksana == 'Makanan Tambahan Ibu Hamil')
            <div class="card mt-3 border-left-success" x-data="{ show: false }"
               x-init="setTimeout(() => { show = true }, 50)" x-show="show"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform scale-90"
               x-transition:enter-end="opacity-100 transform scale-100">
               <div class="card-body">
                  <div class="form-group row">
                     <label for="pemberian_mt" class="col-sm-2 col-form-label">Pemberian MT <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pemberian_mt" id="mt_lokal"
                                 wire:model="pemberian_mt" value="MT Lokal">
                              <label class="form-check-label" for="mt_lokal">MT Lokal</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pemberian_mt" id="mt_pabrikan"
                                 wire:model="pemberian_mt" value="MT Pabrikan">
                              <label class="form-check-label" for="mt_pabrikan">MT Pabrikan</label>
                           </div>
                        </div>
                        @error('pemberian_mt')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="jumlah_mt" class="col-sm-2 col-form-label">Jumlah MT yang diberikan
                        <span class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <input type="number" class="form-control @error('jumlah_mt') is-invalid @enderror"
                           wire:model="jumlah_mt" id="jumlah_mt" min="0">
                        @error('jumlah_mt')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-danger" wire:click="hapusFormMT">
                           <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            <!-- Form khusus untuk Hipertensi -->
            @if($jenis_tatalaksana == 'Hipertensi')
            <div class="card mt-3 border-left-danger" x-data="{ show: false }"
               x-init="setTimeout(() => { show = true }, 50)" x-show="show"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform scale-90"
               x-transition:enter-end="opacity-100 transform scale-100">
               <div class="card-body">
                  <div class="form-group row">
                     <label for="pantau_tekanan_darah" class="col-sm-2 col-form-label">Pantau Tekanan Darah <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pantau_tekanan_darah" id="td_ya"
                                 wire:model="pantau_tekanan_darah" value="Ya">
                              <label class="form-check-label" for="td_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pantau_tekanan_darah" id="td_tidak"
                                 wire:model="pantau_tekanan_darah" value="Tidak">
                              <label class="form-check-label" for="td_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('pantau_tekanan_darah')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="pantau_protein_urine" class="col-sm-2 col-form-label">Pantau Protein Urine <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pantau_protein_urine" id="protein_ya"
                                 wire:model="pantau_protein_urine" value="Ya">
                              <label class="form-check-label" for="protein_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pantau_protein_urine"
                                 id="protein_tidak" wire:model="pantau_protein_urine" value="Tidak">
                              <label class="form-check-label" for="protein_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('pantau_protein_urine')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="pantau_kondisi_janin" class="col-sm-2 col-form-label">Pantau Kondisi Janin <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pantau_kondisi_janin" id="janin_ya"
                                 wire:model="pantau_kondisi_janin" value="Ya">
                              <label class="form-check-label" for="janin_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pantau_kondisi_janin" id="janin_tidak"
                                 wire:model="pantau_kondisi_janin" value="Tidak">
                              <label class="form-check-label" for="janin_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('pantau_kondisi_janin')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="hipertensi_lainnya" class="col-sm-2 col-form-label">Lain-lain</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" wire:model="hipertensi_lainnya" id="hipertensi_lainnya"
                           placeholder="Lainnya">
                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-danger" wire:click="hapusFormHipertensi">
                           <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            <!-- Form khusus untuk Eklampsia -->
            @if($jenis_tatalaksana == 'Eklampsia')
            <div class="card mt-3 border-left-warning" x-data="{ show: false }"
               x-init="setTimeout(() => { show = true }, 50)" x-show="show"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform scale-90"
               x-transition:enter-end="opacity-100 transform scale-100">
               <div class="card-body">
                  <div class="form-group row">
                     <label for="pantau_tekanan_darah_eklampsia" class="col-sm-2 col-form-label">Pantau Tekanan Darah
                        <span class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pantau_tekanan_darah_eklampsia"
                                 id="td_eklampsia_ya" wire:model="pantau_tekanan_darah_eklampsia" value="Ya">
                              <label class="form-check-label" for="td_eklampsia_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pantau_tekanan_darah_eklampsia"
                                 id="td_eklampsia_tidak" wire:model="pantau_tekanan_darah_eklampsia" value="Tidak">
                              <label class="form-check-label" for="td_eklampsia_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('pantau_tekanan_darah_eklampsia')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="pantau_protein_urine_eklampsia" class="col-sm-2 col-form-label">Pantau Protein Urine
                        <span class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pantau_protein_urine_eklampsia"
                                 id="protein_eklampsia_ya" wire:model="pantau_protein_urine_eklampsia" value="Ya">
                              <label class="form-check-label" for="protein_eklampsia_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pantau_protein_urine_eklampsia"
                                 id="protein_eklampsia_tidak" wire:model="pantau_protein_urine_eklampsia" value="Tidak">
                              <label class="form-check-label" for="protein_eklampsia_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('pantau_protein_urine_eklampsia')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="pantau_kondisi_janin_eklampsia" class="col-sm-2 col-form-label">Pantau Kondisi Janin
                        <span class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pantau_kondisi_janin_eklampsia"
                                 id="janin_eklampsia_ya" wire:model="pantau_kondisi_janin_eklampsia" value="Ya">
                              <label class="form-check-label" for="janin_eklampsia_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pantau_kondisi_janin_eklampsia"
                                 id="janin_eklampsia_tidak" wire:model="pantau_kondisi_janin_eklampsia" value="Tidak">
                              <label class="form-check-label" for="janin_eklampsia_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('pantau_kondisi_janin_eklampsia')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="pemberian_antihipertensi" class="col-sm-2 col-form-label">Pemberian Antihipertensi
                        <span class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pemberian_antihipertensi"
                                 id="antihipertensi_ya" wire:model="pemberian_antihipertensi" value="Ya">
                              <label class="form-check-label" for="antihipertensi_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pemberian_antihipertensi"
                                 id="antihipertensi_tidak" wire:model="pemberian_antihipertensi" value="Tidak">
                              <label class="form-check-label" for="antihipertensi_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('pemberian_antihipertensi')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="pemberian_mgso4" class="col-sm-2 col-form-label">Pemberian MgSO4 <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pemberian_mgso4" id="mgso4_ya"
                                 wire:model="pemberian_mgso4" value="Ya">
                              <label class="form-check-label" for="mgso4_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pemberian_mgso4" id="mgso4_tidak"
                                 wire:model="pemberian_mgso4" value="Tidak">
                              <label class="form-check-label" for="mgso4_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('pemberian_mgso4')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="pemberian_diazepam" class="col-sm-2 col-form-label">Pemberian Diazepam Injeksi <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pemberian_diazepam" id="diazepam_ya"
                                 wire:model="pemberian_diazepam" value="Ya">
                              <label class="form-check-label" for="diazepam_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pemberian_diazepam" id="diazepam_tidak"
                                 wire:model="pemberian_diazepam" value="Tidak">
                              <label class="form-check-label" for="diazepam_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('pemberian_diazepam')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-danger" wire:click="hapusFormEklampsia">
                           <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            <!-- Form khusus untuk KEK -->
            @if($jenis_tatalaksana == 'KEK')
            <div class="card mt-3 border-left-info" x-data="{ show: false }"
               x-init="setTimeout(() => { show = true }, 50)" x-show="show"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform scale-90"
               x-transition:enter-end="opacity-100 transform scale-100">
               <div class="card-body">
                  <div class="form-group row">
                     <label for="edukasi_gizi" class="col-sm-2 col-form-label">Edukasi Gizi <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="edukasi_gizi" id="edukasi_gizi_ya"
                                 wire:model="edukasi_gizi" value="Ya">
                              <label class="form-check-label" for="edukasi_gizi_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="edukasi_gizi" id="edukasi_gizi_tidak"
                                 wire:model="edukasi_gizi" value="Tidak">
                              <label class="form-check-label" for="edukasi_gizi_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('edukasi_gizi')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="kek_lainnya" class="col-sm-2 col-form-label">Lain-lain</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" wire:model="kek_lainnya" id="kek_lainnya"
                           placeholder="Lainnya">
                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-danger" wire:click="hapusFormKEK">
                           <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            <!-- Form khusus untuk Obesitas -->
            @if($jenis_tatalaksana == 'Obesitas')
            <div class="card mt-3 border-left-dark" x-data="{ show: false }"
               x-init="setTimeout(() => { show = true }, 50)" x-show="show"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform scale-90"
               x-transition:enter-end="opacity-100 transform scale-100">
               <div class="card-body">
                  <div class="form-group row">
                     <label for="edukasi_gizi_obesitas" class="col-sm-2 col-form-label">Edukasi Gizi Ibu Hamil <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="edukasi_gizi_obesitas"
                                 id="edukasi_gizi_obesitas_ya" wire:model="edukasi_gizi_obesitas" value="Ya">
                              <label class="form-check-label" for="edukasi_gizi_obesitas_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="edukasi_gizi_obesitas"
                                 id="edukasi_gizi_obesitas_tidak" wire:model="edukasi_gizi_obesitas" value="Tidak">
                              <label class="form-check-label" for="edukasi_gizi_obesitas_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('edukasi_gizi_obesitas')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="obesitas_lainnya" class="col-sm-2 col-form-label">Lain-lain</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" wire:model="obesitas_lainnya" id="obesitas_lainnya"
                           placeholder="Lainnya">
                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-danger" wire:click="hapusFormObesitas">
                           <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            <!-- Form khusus untuk Infeksi -->
            @if($jenis_tatalaksana == 'Infeksi')
            <div class="card mt-3 border-left-secondary" x-data="{ show: false }"
               x-init="setTimeout(() => { show = true }, 50)" x-show="show"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform scale-90"
               x-transition:enter-end="opacity-100 transform scale-100">
               <div class="card-body">
                  <div class="form-group row">
                     <label for="pemberian_antipiretik" class="col-sm-2 col-form-label">Pemberian Antipiretik <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pemberian_antipiretik"
                                 id="antipiretik_ya" wire:model="pemberian_antipiretik" value="Ya">
                              <label class="form-check-label" for="antipiretik_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pemberian_antipiretik"
                                 id="antipiretik_tidak" wire:model="pemberian_antipiretik" value="Tidak">
                              <label class="form-check-label" for="antipiretik_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('pemberian_antipiretik')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="pemberian_antibiotik" class="col-sm-2 col-form-label">Pemberian Antibiotik <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pemberian_antibiotik"
                                 id="antibiotik_ya" wire:model="pemberian_antibiotik" value="Ya">
                              <label class="form-check-label" for="antibiotik_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="pemberian_antibiotik"
                                 id="antibiotik_tidak" wire:model="pemberian_antibiotik" value="Tidak">
                              <label class="form-check-label" for="antibiotik_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('pemberian_antibiotik')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="infeksi_lainnya" class="col-sm-2 col-form-label">Lain-lain</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" wire:model="infeksi_lainnya" id="infeksi_lainnya"
                           placeholder="Lainnya">
                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-danger" wire:click="hapusFormInfeksi">
                           <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            <!-- Form khusus untuk Penyakit Jantung -->
            @if($jenis_tatalaksana == 'Penyakit Jantung')
            <div class="card mt-3 border-left-danger" x-data="{ show: false }"
               x-init="setTimeout(() => { show = true }, 50)" x-show="show"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform scale-90"
               x-transition:enter-end="opacity-100 transform scale-100">
               <div class="card-body">
                  <div class="form-group row">
                     <label for="edukasi" class="col-sm-2 col-form-label">Edukasi <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="edukasi" id="edukasi_ya"
                                 wire:model="edukasi" value="Ya">
                              <label class="form-check-label" for="edukasi_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="edukasi" id="edukasi_tidak"
                                 wire:model="edukasi" value="Tidak">
                              <label class="form-check-label" for="edukasi_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('edukasi')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="jantung_lainnya" class="col-sm-2 col-form-label">Lain-lain</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" wire:model="jantung_lainnya" id="jantung_lainnya"
                           placeholder="Lainnya">
                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-danger" wire:click="hapusFormJantung">
                           <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            <!-- Form khusus untuk HIV -->
            @if($jenis_tatalaksana == 'HIV')
            <div class="card mt-3 border-left-warning" x-data="{ show: false }"
               x-init="setTimeout(() => { show = true }, 50)" x-show="show"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform scale-90"
               x-transition:enter-end="opacity-100 transform scale-100">
               <div class="card-body">
                  <div class="form-group row">
                     <label for="datang_dengan_hiv" class="col-sm-2 col-form-label">Datang Dengan HIV <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="datang_dengan_hiv"
                                 id="datang_hiv_negatif" wire:model="datang_dengan_hiv" value="Negatif (-)">
                              <label class="form-check-label" for="datang_hiv_negatif">Negatif (-)</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="datang_dengan_hiv"
                                 id="datang_hiv_positif" wire:model="datang_dengan_hiv" value="Positif (+)">
                              <label class="form-check-label" for="datang_hiv_positif">Positif (+)</label>
                           </div>
                        </div>
                        @error('datang_dengan_hiv')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="persalinan_pervaginam" class="col-sm-2 col-form-label">Persalinan Pervaginam <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="persalinan_pervaginam"
                                 id="persalinan_pervaginam_negatif" wire:model="persalinan_pervaginam"
                                 value="Negatif (-)">
                              <label class="form-check-label" for="persalinan_pervaginam_negatif">Negatif (-)</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="persalinan_pervaginam"
                                 id="persalinan_pervaginam_positif" wire:model="persalinan_pervaginam"
                                 value="Positif (+)">
                              <label class="form-check-label" for="persalinan_pervaginam_positif">Positif (+)</label>
                           </div>
                        </div>
                        @error('persalinan_pervaginam')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="persalinan_perapdoinam" class="col-sm-2 col-form-label">Persalinan Perapdoinam (SC)
                        <span class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="persalinan_perapdoinam"
                                 id="persalinan_perapdoinam_negatif" wire:model="persalinan_perapdoinam"
                                 value="Negatif (-)">
                              <label class="form-check-label" for="persalinan_perapdoinam_negatif">Negatif (-)</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="persalinan_perapdoinam"
                                 id="persalinan_perapdoinam_positif" wire:model="persalinan_perapdoinam"
                                 value="Positif (+)">
                              <label class="form-check-label" for="persalinan_perapdoinam_positif">Positif (+)</label>
                           </div>
                        </div>
                        @error('persalinan_perapdoinam')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="ditawarkan_tes" class="col-sm-2 col-form-label">Ditawarkan Tes <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="ditawarkan_tes" id="ditawarkan_tes_ya"
                                 wire:model="ditawarkan_tes" value="Ya">
                              <label class="form-check-label" for="ditawarkan_tes_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="ditawarkan_tes"
                                 id="ditawarkan_tes_tidak" wire:model="ditawarkan_tes" value="Tidak">
                              <label class="form-check-label" for="ditawarkan_tes_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('ditawarkan_tes')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="dilakukan_tes" class="col-sm-2 col-form-label">Dilakukan Tes <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="dilakukan_tes" id="dilakukan_tes_ya"
                                 wire:model="dilakukan_tes" value="Ya">
                              <label class="form-check-label" for="dilakukan_tes_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="dilakukan_tes" id="dilakukan_tes_tidak"
                                 wire:model="dilakukan_tes" value="Tidak">
                              <label class="form-check-label" for="dilakukan_tes_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('dilakukan_tes')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="hasil_tes_hiv" class="col-sm-2 col-form-label">Hasil Tes HIV <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="hasil_tes_hiv"
                                 id="hasil_tes_hiv_negatif" wire:model="hasil_tes_hiv" value="Negatif (-)">
                              <label class="form-check-label" for="hasil_tes_hiv_negatif">Negatif (-)</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="hasil_tes_hiv"
                                 id="hasil_tes_hiv_positif" wire:model="hasil_tes_hiv" value="Positif (+)">
                              <label class="form-check-label" for="hasil_tes_hiv_positif">Positif (+)</label>
                           </div>
                        </div>
                        @error('hasil_tes_hiv')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="mendapatkan_art" class="col-sm-2 col-form-label">Mendapatkan ART <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="mendapatkan_art"
                                 id="mendapatkan_art_ya" wire:model="mendapatkan_art" value="Ya">
                              <label class="form-check-label" for="mendapatkan_art_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="mendapatkan_art"
                                 id="mendapatkan_art_tidak" wire:model="mendapatkan_art" value="Tidak">
                              <label class="form-check-label" for="mendapatkan_art_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('mendapatkan_art')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="vct_pict" class="col-sm-2 col-form-label">VCT (PICT) <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="vct_pict" id="vct_pict_ya"
                                 wire:model="vct_pict" value="Ya">
                              <label class="form-check-label" for="vct_pict_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="vct_pict" id="vct_pict_tidak"
                                 wire:model="vct_pict" value="Tidak">
                              <label class="form-check-label" for="vct_pict_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('vct_pict')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="periksa_darah" class="col-sm-2 col-form-label">Periksa Darah <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="periksa_darah" id="periksa_darah_ya"
                                 wire:model="periksa_darah" value="Ya">
                              <label class="form-check-label" for="periksa_darah_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="periksa_darah" id="periksa_darah_tidak"
                                 wire:model="periksa_darah" value="Tidak">
                              <label class="form-check-label" for="periksa_darah_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('periksa_darah')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="serologi" class="col-sm-2 col-form-label">Serologi <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="serologi" id="serologi_negatif"
                                 wire:model="serologi" value="Negatif (-)">
                              <label class="form-check-label" for="serologi_negatif">Negatif (-)</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="serologi" id="serologi_positif"
                                 wire:model="serologi" value="Positif (+)">
                              <label class="form-check-label" for="serologi_positif">Positif (+)</label>
                           </div>
                        </div>
                        @error('serologi')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="arv_profilaksis" class="col-sm-2 col-form-label">ARV Profilaksis</label>
                     <div class="col-sm-10">
                        <select class="form-control @error('arv_profilaksis') is-invalid @enderror"
                           wire:model="arv_profilaksis" id="arv_profilaksis">
                           <option value="">- Pilih -</option>
                           <option value="Ya">Ya</option>
                           <option value="Tidak">Tidak</option>
                        </select>
                        @error('arv_profilaksis')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="hiv_lainnya" class="col-sm-2 col-form-label">Lain-lain</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" wire:model="hiv_lainnya" id="hiv_lainnya"
                           placeholder="TC Lainnya">
                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-danger" wire:click="hapusFormHIV">
                           <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            <!-- Form khusus untuk TB -->
            @if($jenis_tatalaksana == 'TB')
            <div class="card mt-3 border-left-info" x-data="{ show: false }"
               x-init="setTimeout(() => { show = true }, 50)" x-show="show"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform scale-90"
               x-transition:enter-end="opacity-100 transform scale-100">
               <div class="card-body">
                  <div class="form-group row">
                     <label for="diperiksa_dahak" class="col-sm-2 col-form-label">Diperiksa dahak <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="diperiksa_dahak"
                                 id="diperiksa_dahak_ya" wire:model="diperiksa_dahak" value="Ya">
                              <label class="form-check-label" for="diperiksa_dahak_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="diperiksa_dahak"
                                 id="diperiksa_dahak_tidak" wire:model="diperiksa_dahak" value="Tidak">
                              <label class="form-check-label" for="diperiksa_dahak_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('diperiksa_dahak')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="tbc" class="col-sm-2 col-form-label">TBC <span class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="tbc" id="tbc_negatif" wire:model="tbc"
                                 value="Negatif (-)">
                              <label class="form-check-label" for="tbc_negatif">Negatif (-)</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="tbc" id="tbc_positif" wire:model="tbc"
                                 value="Positif (+)">
                              <label class="form-check-label" for="tbc_positif">Positif (+)</label>
                           </div>
                        </div>
                        @error('tbc')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="obat_tb" class="col-sm-2 col-form-label">Obat TB</label>
                     <div class="col-sm-10">
                        <select class="form-control @error('obat_tb') is-invalid @enderror" wire:model="obat_tb"
                           id="obat_tb">
                           <option value="">- Pilih -</option>
                           <option value="Kategori 1">Kategori 1</option>
                           <option value="Kategori 2">Kategori 2</option>
                           <option value="Kategori Anak">Kategori Anak</option>
                        </select>
                        @error('obat_tb')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="sisa_obat" class="col-sm-2 col-form-label">Sisa Obat</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" wire:model="sisa_obat" id="sisa_obat"
                           placeholder="TC Lainnya">
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="tb_lainnya" class="col-sm-2 col-form-label">Lain-lain</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" wire:model="tb_lainnya" id="tb_lainnya"
                           placeholder="Lainnya">
                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-danger" wire:click="hapusFormTB">
                           <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            @endif

            <!-- Form khusus untuk Malaria -->
            @if($jenis_tatalaksana == 'Malaria')
            <div class="card mt-3 border-left-success" x-data="{ show: false }"
               x-init="setTimeout(() => { show = true }, 50)" x-show="show"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 transform scale-90"
               x-transition:enter-end="opacity-100 transform scale-100">
               <div class="card-body">
                  <div class="form-group row">
                     <label for="diberikan_kelambu" class="col-sm-2 col-form-label">Diberikan Kelambu Berinsektisida
                        <span class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="diberikan_kelambu" id="kelambu_ya"
                                 wire:model="diberikan_kelambu" value="Ya">
                              <label class="form-check-label" for="kelambu_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="diberikan_kelambu" id="kelambu_tidak"
                                 wire:model="diberikan_kelambu" value="Tidak">
                              <label class="form-check-label" for="kelambu_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('diberikan_kelambu')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="darah_malaria_rdt" class="col-sm-2 col-form-label">Darah malaria diperiksa - RDT <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="darah_malaria_rdt"
                                 id="darah_malaria_rdt_ya" wire:model="darah_malaria_rdt" value="Ya">
                              <label class="form-check-label" for="darah_malaria_rdt_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="darah_malaria_rdt"
                                 id="darah_malaria_rdt_tidak" wire:model="darah_malaria_rdt" value="Tidak">
                              <label class="form-check-label" for="darah_malaria_rdt_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('darah_malaria_rdt')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="darah_malaria_mikroskopis" class="col-sm-2 col-form-label">Darah malaria diperiksa -
                        Mikroskopis <span class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="darah_malaria_mikroskopis"
                                 id="darah_malaria_mikroskopis_ya" wire:model="darah_malaria_mikroskopis" value="Ya">
                              <label class="form-check-label" for="darah_malaria_mikroskopis_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="darah_malaria_mikroskopis"
                                 id="darah_malaria_mikroskopis_tidak" wire:model="darah_malaria_mikroskopis"
                                 value="Tidak">
                              <label class="form-check-label" for="darah_malaria_mikroskopis_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('darah_malaria_mikroskopis')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="ibu_hamil_malaria_rdt" class="col-sm-2 col-form-label">Ibu Hamil Malaria - RDT <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="ibu_hamil_malaria_rdt"
                                 id="ibu_hamil_malaria_rdt_ya" wire:model="ibu_hamil_malaria_rdt" value="Ya">
                              <label class="form-check-label" for="ibu_hamil_malaria_rdt_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="ibu_hamil_malaria_rdt"
                                 id="ibu_hamil_malaria_rdt_tidak" wire:model="ibu_hamil_malaria_rdt" value="Tidak">
                              <label class="form-check-label" for="ibu_hamil_malaria_rdt_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('ibu_hamil_malaria_rdt')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="ibu_hamil_malaria_mikroskopis" class="col-sm-2 col-form-label">Ibu Hamil Malaria -
                        Mikroskopis <span class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="ibu_hamil_malaria_mikroskopis"
                                 id="ibu_hamil_malaria_mikroskopis_ya" wire:model="ibu_hamil_malaria_mikroskopis"
                                 value="Ya">
                              <label class="form-check-label" for="ibu_hamil_malaria_mikroskopis_ya">Ya</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="ibu_hamil_malaria_mikroskopis"
                                 id="ibu_hamil_malaria_mikroskopis_tidak" wire:model="ibu_hamil_malaria_mikroskopis"
                                 value="Tidak">
                              <label class="form-check-label" for="ibu_hamil_malaria_mikroskopis_tidak">Tidak</label>
                           </div>
                        </div>
                        @error('ibu_hamil_malaria_mikroskopis')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="hasil_test_malaria" class="col-sm-2 col-form-label">Hasil Test Darah Malaria <span
                           class="text-danger">*</span></label>
                     <div class="col-sm-10">
                        <div class="d-flex">
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="hasil_test_malaria"
                                 id="hasil_test_malaria_negatif" wire:model="hasil_test_malaria" value="Negatif (-)">
                              <label class="form-check-label" for="hasil_test_malaria_negatif">Negatif (-)</label>
                           </div>
                           <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="hasil_test_malaria"
                                 id="hasil_test_malaria_positif" wire:model="hasil_test_malaria" value="Positif (+)">
                              <label class="form-check-label" for="hasil_test_malaria_positif">Positif (+)</label>
                           </div>
                        </div>
                        @error('hasil_test_malaria')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="obat_malaria" class="col-sm-2 col-form-label">Obat Malaria/Kina/ACT</label>
                     <div class="col-sm-10">
                        <select class="form-control @error('obat_malaria') is-invalid @enderror"
                           wire:model="obat_malaria" id="obat_malaria">
                           <option value="">- Pilih -</option>
                           <option value="Diberikan Obat">Diberikan Obat</option>
                           <option value="Tidak Diberikan Obat">Tidak Diberikan Obat</option>
                        </select>
                        @error('obat_malaria')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                     </div>
                  </div>

                  <div class="form-group row">
                     <label for="malaria_lainnya" class="col-sm-2 col-form-label">Lain-lain</label>
                     <div class="col-sm-10">
                        <input type="text" class="form-control" wire:model="malaria_lainnya" id="malaria_lainnya"
                           placeholder="Lainnya">
                     </div>
                  </div>

                  <div class="form-group row">
                     <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-danger" wire:click="hapusFormMalaria">
                           <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            @endif
         </div>

         <!-- Temu Wicara/Konseling -->
         <div class="form-group" id="temu-wicara">
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
                        <input class="form-check-input" type="radio" name="tanda_bahaya_kehamilan"
                           id="bahaya_hamil_tidak" wire:model="tanda_bahaya_kehamilan" value="Tidak">
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
                        <input class="form-check-input" type="radio" name="tanda_bahaya_persalinan"
                           id="bahaya_salin_tidak" wire:model="tanda_bahaya_persalinan" value="Tidak">
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
         <div class="form-group" id="keadaan-pulang">
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
            <div class="col-sm-12 text-center no-print">
               <button type="button" class="btn btn-secondary btn-lg mr-2" wire:click="batal">
                  <i class="fas fa-times mr-1"></i> Batal
               </button>

               <button type="button" class="btn btn-info btn-lg mr-2" wire:click="resetForm">
                  <i class="fas fa-sync-alt mr-1"></i> Reset
               </button>

               <button type="submit" class="btn btn-success btn-lg" wire:loading.attr="disabled">
                  <i class="fas fa-save mr-1"></i> Simpan
                  <span wire:loading wire:target="save" class="spinner-border spinner-border-sm ml-1" role="status"
                     aria-hidden="true"></span>
               </button>
            </div>
         </div>
      </form>
   </div>

   @push('scripts')
   <script>
      document.addEventListener('livewire:load', function () {
         console.log('Pemeriksaan ANC component loaded');
         
         // Observer untuk menampilkan tooltip pada semua elemen dengan data-toggle="tooltip"
         const tooltipEnabler = () => {
            $('[data-toggle="tooltip"]').tooltip();
         };
         
         tooltipEnabler();
         
         // Listener untuk error events
         Livewire.on('showError', function(message) {
            console.error('PemeriksaanANC Error:', message);
            Swal.fire({
               title: 'Error!',
               text: message,
               icon: 'error',
               confirmButtonText: 'Ok'
            });
         });
         
         // Listener untuk form saved events
         Livewire.on('formSaved', function(message) {
            console.log('Pemeriksaan ANC saved');
            Swal.fire({
               title: 'Berhasil!',
               text: message || 'Data pemeriksaan ANC berhasil disimpan',
               icon: 'success',
               confirmButtonText: 'Ok'
            });
         });
         
         // Ketika form berubah, refresh tooltips
         Livewire.hook('message.processed', (message, component) => {
            tooltipEnabler();
         });
         
         // Menampilkan dialog konfirmasi untuk penghapusan
         window.confirmDelete = function(action) {
            Swal.fire({
               title: 'Apakah Anda yakin?',
               text: "Data yang dihapus tidak dapat dikembalikan!",
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#d33',
               cancelButtonColor: '#3085d6',
               confirmButtonText: 'Ya, hapus!',
               cancelButtonText: 'Batal'
            }).then((result) => {
               if (result.isConfirmed) {
                  Livewire.emit(action);
               }
            });
         };
      });
      
      // Fungsi untuk mencetak form
      function printForm() {
         window.print();
      }
   </script>
   @endpush

   <!-- Floating Action Button untuk aksi cepat -->
   <div class="position-fixed" style="bottom: 2rem; right: 2rem; z-index: 1030;">
      <div class="dropdown">
         <button class="btn btn-primary btn-lg rounded-circle shadow" type="button" id="dropdownMenuButton"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-plus"></i>
         </button>
         <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#" onclick="printForm()">
               <i class="fas fa-print mr-2"></i> Cetak Formulir
            </a>
            <a class="dropdown-item" href="#" wire:click="resetForm">
               <i class="fas fa-sync-alt mr-2"></i> Reset Formulir
            </a>
            <a class="dropdown-item" href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
               <i class="fas fa-arrow-up mr-2"></i> Ke Atas
            </a>
         </div>
      </div>
   </div>
</div>
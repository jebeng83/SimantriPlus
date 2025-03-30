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

      /* Styles untuk informasi ibu hamil */
      .info-ibu-hamil {
         background-color: #f8f9fa;
         border-left: 4px solid #17a2b8;
         padding: 15px;
         margin-bottom: 20px;
         border-radius: 4px;
      }

      .info-ibu-hamil h5 {
         color: #17a2b8;
         margin-bottom: 10px;
      }

      .info-ibu-hamil .row {
         margin-bottom: 5px;
      }

      .alert-not-registered {
         background-color: #fff3cd;
         border-left: 4px solid #ffc107;
         color: #856404;
         padding: 15px;
         margin-bottom: 20px;
         border-radius: 4px;
      }

      /* Warna biru tua untuk judul section */
      .text-navy-blue {
         color: #0d47a1 !important;
      }

      /* Style untuk bagian form */
      .form-group {
         border-left: 3px solid #e3f2fd;
         padding-left: 15px;
         margin-bottom: 20px;
         padding-top: 10px;
         padding-bottom: 10px;
      }

      /* Style untuk riwayat penyakit */
      #riwayat_lainnya {
         display: none;
      }

      .riwayat_penyakit_lainnya.active #riwayat_lainnya {
         display: block;
         margin-top: 0.5rem;
         transition: all 0.3s ease;
      }

      .riwayat_penyakit_lainnya {
         transition: all 0.3s ease;
      }

      /* Styling untuk tombol hitung */
      .btn-sm.btn-primary {
         transition: all 0.2s ease;
      }

      .btn-sm.btn-primary:hover {
         background-color: #1565c0;
         transform: translateY(-1px);
      }

      .btn-sm.btn-primary:active {
         transform: translateY(1px);
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
      @if(!$validIbuHamil && $errorMessage)
      <div class="alert-not-registered">
         <h5><i class="fas fa-exclamation-triangle mr-2"></i>Pasien Bukan Ibu Hamil Aktif</h5>
         <p>{{ $errorMessage }}</p>
         <p>Pasien harus terdaftar sebagai ibu hamil aktif di sistem untuk mengakses form Pemeriksaan ANC. Silakan
            daftarkan pasien terlebih dahulu melalui menu Data Ibu Hamil.</p>
      </div>
      @else

      <!-- Informasi Dasar Ibu Hamil -->
      <div class="info-ibu-hamil">
         <h5><i class="fas fa-female mr-2"></i>Informasi Ibu Hamil</h5>
         <div class="row">
            <div class="col-md-6">
               <div class="row">
                  <div class="col-md-4 font-weight-bold">Nama</div>
                  <div class="col-md-8">: {{ $dataIbuHamil['nama'] ?? '-' }}</div>
               </div>
               <div class="row">
                  <div class="col-md-4 font-weight-bold">Usia</div>
                  <div class="col-md-8">: {{ $dataIbuHamil['usia'] ?? '-' }} tahun</div>
               </div>
               <div class="row">
                  <div class="col-md-4 font-weight-bold">HPHT</div>
                  <div class="col-md-8">: {{ $dataIbuHamil['hpht'] ?? '-' }}</div>
               </div>
            </div>
            <div class="col-md-6">
               <div class="row">
                  <div class="col-md-4 font-weight-bold">HPL</div>
                  <div class="col-md-8">: {{ $dataIbuHamil['hpl'] ?? '-' }}</div>
               </div>
               <div class="row">
                  <div class="col-md-4 font-weight-bold">Usia Kehamilan</div>
                  <div class="col-md-8">: {{ $dataIbuHamil['usia_kehamilan'] ?? '-' }} minggu</div>
               </div>
               @if($id_hamil)
               <div class="row mt-1">
                  <div class="col-md-12 text-right">
                     <button type="button" class="btn btn-sm btn-primary"
                        wire:click="showHistoriByIdHamil('{{ $id_hamil }}')">
                        <i class="fas fa-history mr-1"></i> Tampilkan Riwayat Kunjungan ANC
                     </button>
                  </div>
               </div>
               @endif
            </div>
         </div>
      </div>

      <form wire:submit.prevent="save">
         <!-- Navigation Pills -->
         <div class="card-body">
            <nav class="nav nav-pills flex-column flex-sm-row">
               <a class="flex-sm-fill text-sm-center nav-link active" href="#anamnesis"
                  onclick="event.preventDefault(); document.getElementById('anamnesis').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-clipboard-list mr-1"></i> Anamnesis
               </a>
               <a class="flex-sm-fill text-sm-center nav-link" href="#data-wajib"
                  onclick="event.preventDefault(); document.getElementById('data-wajib').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-clipboard-check mr-1"></i> Data Wajib
               </a>
               <a class="flex-sm-fill text-sm-center nav-link" href="#ukur-bb-tb"
                  onclick="event.preventDefault(); document.getElementById('ukur-bb-tb').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-weight mr-1"></i> T1: BB & TB
               </a>
               <a class="flex-sm-fill text-sm-center nav-link" href="#ukur-td"
                  onclick="event.preventDefault(); document.getElementById('ukur-td').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-heartbeat mr-1"></i> T2: TD
               </a>
            </nav>
            <nav class="nav nav-pills flex-column flex-sm-row mt-2">
               <a class="flex-sm-fill text-sm-center nav-link" href="#status-gizi"
                  onclick="event.preventDefault(); document.getElementById('status-gizi').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-ruler mr-1"></i> T3: Status Gizi
               </a>
               <a class="flex-sm-fill text-sm-center nav-link" href="#tinggi-fundus-uteri"
                  onclick="event.preventDefault(); document.getElementById('tinggi-fundus-uteri').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-child mr-1"></i> T4: Tinggi Fundus
               </a>
               <a class="flex-sm-fill text-sm-center nav-link" href="#djj-presentasi"
                  onclick="event.preventDefault(); document.getElementById('djj-presentasi').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-baby mr-1"></i> T5: Denyut Jantung Janin dan Presentasi Janin
               </a>
               <a class="flex-sm-fill text-sm-center nav-link" href="#status-imunisasi"
                  onclick="event.preventDefault(); document.getElementById('status-imunisasi').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-syringe mr-1"></i> T6: Imunisasi TT
               </a>
            </nav>
            <nav class="nav nav-pills flex-column flex-sm-row mt-2">
               <a class="flex-sm-fill text-sm-center nav-link" href="#tablet-tambah-darah"
                  onclick="event.preventDefault(); document.getElementById('tablet-tambah-darah').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-pills mr-1"></i> T7: Tablet Fe
               </a>
               <a class="flex-sm-fill text-sm-center nav-link" href="#pemeriksaan-lab"
                  onclick="event.preventDefault(); document.getElementById('pemeriksaan-lab').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-flask mr-1"></i> T8: Lab
               </a>
               <a class="flex-sm-fill text-sm-center nav-link" href="#tatalaksana-kasus"
                  onclick="event.preventDefault(); document.getElementById('tatalaksana-kasus').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-procedures mr-1"></i> T9: Tatalaksana
               </a>
               <a class="flex-sm-fill text-sm-center nav-link" href="#temu-wicara"
                  onclick="event.preventDefault(); document.getElementById('temu-wicara').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-comments mr-1"></i> T10: Konseling
               </a>
            </nav>
            <nav class="nav nav-pills flex-column flex-sm-row mt-2">
               <a class="flex-sm-fill text-sm-center nav-link" href="#tindak-lanjut"
                  onclick="event.preventDefault(); document.getElementById('tindak-lanjut').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-clipboard-check mr-1"></i> Tindak Lanjut
               </a>
               <a class="flex-sm-fill text-sm-center nav-link" href="#keadaan-pulang"
                  onclick="event.preventDefault(); document.getElementById('keadaan-pulang').scrollIntoView({behavior: 'smooth'})">
                  <i class="fas fa-home mr-1"></i> Keadaan Pulang
               </a>
            </nav>
         </div>

         <!-- Anamnesis -->
         <div class="form-group" id="anamnesis">
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">1</span> Anamnesis
            </h5>

            <div class="form-group row">
               <label for="keluhan_utama" class="col-sm-2 col-form-label">Keluhan Utama</label>
               <div class="col-sm-10">
                  <textarea class="form-control @error('keluhan_utama') is-invalid @enderror" rows="2"
                     wire:model.defer="keluhan_utama" id="keluhan_utama"></textarea>
                  @error('keluhan_utama')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>

            <div class="form-group row">
               <label for="riwayat_kehamilan" class="col-sm-2 col-form-label">Riwayat Obstetri</label>
               <div class="col-sm-10">
                  <div class="row">
                     <div class="col-md-2">
                        <div class="form-group">
                           <label for="gravida">G (Gravida)</label>
                           <input type="number" class="form-control @error('gravida') is-invalid @enderror"
                              wire:model.defer="gravida" id="gravida" min="0">
                           @error('gravida')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label for="partus">P (Partus)</label>
                           <input type="number" class="form-control @error('partus') is-invalid @enderror"
                              wire:model.defer="partus" id="partus" min="0">
                           @error('partus')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label for="abortus">A (Abortus)</label>
                           <input type="number" class="form-control @error('abortus') is-invalid @enderror"
                              wire:model.defer="abortus" id="abortus" min="0">
                           @error('abortus')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                     </div>
                     <div class="col-md-2">
                        <div class="form-group">
                           <label for="hidup">Hidup</label>
                           <input type="number" class="form-control @error('hidup') is-invalid @enderror"
                              wire:model.defer="hidup" id="hidup" min="0">
                           @error('hidup')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <div class="form-group row">
               <label for="riwayat_penyakit" class="col-sm-2 col-form-label">Riwayat Penyakit</label>
               <div class="col-sm-10">
                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" id="hipertensi"
                              wire:model.defer="riwayat_penyakit.hipertensi">
                           <label class="form-check-label" for="hipertensi">Hipertensi</label>
                        </div>
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" id="diabetes"
                              wire:model.defer="riwayat_penyakit.diabetes">
                           <label class="form-check-label" for="diabetes">Diabetes Mellitus</label>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" id="jantung"
                              wire:model.defer="riwayat_penyakit.jantung">
                           <label class="form-check-label" for="jantung">Penyakit Jantung</label>
                        </div>
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" id="asma"
                              wire:model.defer="riwayat_penyakit.asma">
                           <label class="form-check-label" for="asma">Asma</label>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" id="lainnya_check"
                              wire:model.defer="riwayat_penyakit.lainnya_check">
                           <label class="form-check-label" for="lainnya_check">Lainnya</label>
                        </div>
                        <div class="riwayat_penyakit_lainnya" id="riwayat_lainnya_container">
                           <input type="text" class="form-control mt-1" id="riwayat_lainnya"
                              wire:model.defer="riwayat_penyakit.lainnya" placeholder="Sebutkan" id="riwayat_lainnya">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Data Wajib Diisi -->
         <div class="form-group" id="data-wajib">
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">2</span> Data Wajib Diisi
            </h5>

            <div class="form-group row">
               <label for="tanggal_anc" class="col-sm-2 col-form-label">Tanggal ANC</label>
               <div class="col-sm-4">
                  <div class="input-group">
                     <input type="text" class="form-control @error('tanggal_anc') is-invalid @enderror" id="tanggal_anc"
                        wire:model.defer="tanggal_anc_input" placeholder="DD/MM/YYYY, HH:MM"
                        value="{{ isset($tanggal_anc) ? \Carbon\Carbon::parse($tanggal_anc)->format('d/m/Y, H:i') : now()->format('d/m/Y, H:i') }}">
                     <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                     </div>
                     @error('tanggal_anc')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
                  <small class="text-muted">Format: DD/MM/YYYY, HH:MM (contoh: 30/03/2025, 06:49)</small>
               </div>

               <label for="diperiksa_oleh" class="col-sm-2 col-form-label">Diperiksa Oleh</label>
               <div class="col-sm-4">
                  <select class="form-control @error('diperiksa_oleh') is-invalid @enderror"
                     wire:model.defer="diperiksa_oleh" id="diperiksa_oleh">
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
                        wire:model.defer="usia_kehamilan" id="usia_kehamilan">
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
                           wire:model.defer="trimester" value="1">
                        <label class="form-check-label" for="trimester1">1</label>
                     </div>
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="trimester" id="trimester2"
                           wire:model.defer="trimester" value="2">
                        <label class="form-check-label" for="trimester2">2</label>
                     </div>
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="trimester" id="trimester3"
                           wire:model.defer="trimester" value="3">
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
                  <select class="form-control @error('kunjungan_ke') is-invalid @enderror"
                     wire:model.defer="kunjungan_ke" id="kunjungan_ke">
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
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">3</span> Timbang Berat Badan dan Ukur Tinggi Badan (T1)
            </h5>

            <div class="form-group row">
               <label for="berat_badan" class="col-sm-2 col-form-label">Berat Badan (saat ini)</label>
               <div class="col-sm-4">
                  <div class="input-group">
                     <input type="number" step="0.01" class="form-control @error('berat_badan') is-invalid @enderror"
                        wire:model.defer="berat_badan" id="berat_badan">
                     <div class="input-group-append">
                        <span class="input-group-text">Kg</span>
                     </div>
                     @error('berat_badan')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
                  @if(isset($berat_badan) && $berat_badan < 20) <small class="text-danger">The berat badan must be at
                     least 20.</small>
                     @endif
               </div>

               <label for="tinggi_badan" class="col-sm-2 col-form-label">Tinggi Badan</label>
               <div class="col-sm-4">
                  <div class="input-group">
                     <input type="number" step="0.01" class="form-control @error('tinggi_badan') is-invalid @enderror"
                        wire:model.defer="tinggi_badan" id="tinggi_badan">
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
               <div class="col-sm-6 offset-sm-2">
                  <button type="button" class="btn btn-sm btn-primary" wire:click="hitungIMT">
                     <i class="fas fa-calculator mr-1"></i> Hitung IMT
                  </button>
               </div>
            </div>

            <div class="form-group row">
               <label for="imt" class="col-sm-2 col-form-label">IMT Saat ini</label>
               <div class="col-sm-4">
                  <input type="text" class="form-control bg-light" wire:model="imt" id="imt" readonly>
               </div>

               <label for="kategori_imt" class="col-sm-2 col-form-label">Kategori IMT</label>
               <div class="col-sm-4">
                  <input type="text" class="form-control bg-light" wire:model="kategori_imt" id="kategori_imt" readonly>
               </div>
            </div>

            <div class="form-group row">
               <label for="jumlah_janin" class="col-sm-2 col-form-label">Jumlah Janin</label>
               <div class="col-sm-4">
                  <div class="d-flex">
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jumlah_janin" id="janin_tidak_diketahui"
                           wire:model.defer="jumlah_janin" value="Tidak Diketahui">
                        <label class="form-check-label" for="janin_tidak_diketahui">Tidak Diketahui</label>
                     </div>
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jumlah_janin" id="janin_tunggal"
                           wire:model.defer="jumlah_janin" value="Tunggal">
                        <label class="form-check-label" for="janin_tunggal">Tunggal</label>
                     </div>
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jumlah_janin" id="janin_ganda"
                           wire:model.defer="jumlah_janin" value="Ganda">
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
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">4</span> Ukur Tekanan Darah (T2)
            </h5>

            <div class="form-group row">
               <label for="td_sistole" class="col-sm-2 col-form-label">TD Sistole</label>
               <div class="col-sm-4">
                  <div class="input-group">
                     <input type="number" class="form-control @error('td_sistole') is-invalid @enderror"
                        wire:model.defer="td_sistole" id="td_sistole">
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
                        wire:model.defer="td_diastole" id="td_diastole">
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

         <!-- Status Gizi -->
         <div class="form-group" id="status-gizi">
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">5</span> Status Gizi (T3)
            </h5>

            <div class="form-group row">
               <label for="lila" class="col-sm-2 col-form-label">Lingkar Lengan Atas (LILA)</label>
               <div class="col-sm-4">
                  <div class="input-group">
                     <input type="number" step="0.1" class="form-control @error('lila') is-invalid @enderror" id="lila"
                        wire:model.defer="lila">
                     <div class="input-group-append">
                        <span class="input-group-text">cm</span>
                     </div>
                     @error('lila')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
                  <button type="button" class="btn btn-sm btn-primary mt-2" wire:click="tentukanStatusGizi">
                     <i class="fas fa-calculator mr-1"></i> Hitung Status Gizi
                  </button>
               </div>

               <label for="status_gizi" class="col-sm-2 col-form-label">Status Gizi</label>
               <div class="col-sm-4">
                  <input type="text" class="form-control bg-light" wire:model="status_gizi" id="status_gizi" readonly>
               </div>
            </div>
         </div>

         <!-- Tinggi Fundus Uteri -->
         <div class="form-group" id="tinggi-fundus-uteri">
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">6</span> Tinggi Fundus Uteri (T4)
            </h5>

            <div class="form-group row">
               <label for="tinggi_fundus" class="col-sm-2 col-form-label">Tinggi Fundus</label>
               <div class="col-sm-4">
                  <div class="input-group">
                     <input type="number" step="0.1" class="form-control @error('tinggi_fundus') is-invalid @enderror"
                        id="tinggi_fundus" wire:model.defer="tinggi_fundus">
                     <div class="input-group-append">
                        <span class="input-group-text">cm</span>
                     </div>
                     @error('tinggi_fundus')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
                  <button type="button" class="btn btn-sm btn-primary mt-2" wire:click="hitungTaksiranBeratJanin">
                     <i class="fas fa-calculator mr-1"></i> Hitung TBJ
                  </button>
               </div>

               <label for="taksiran_berat_janin" class="col-sm-2 col-form-label">Taksiran Berat Janin</label>
               <div class="col-sm-4">
                  <div class="input-group">
                     <input type="number" class="form-control bg-light" wire:model="taksiran_berat_janin"
                        id="taksiran_berat_janin" readonly>
                     <div class="input-group-append">
                        <span class="input-group-text">gram</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Tentukan DJJ dan Presentasi Janin -->
         <div class="form-group" id="djj-presentasi">
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">7</span> Tentukan Denyut Jantung Janin dan Presentasi Janin (T5)
            </h5>

            <div class="form-group row">
               <label for="denyut_jantung_janin" class="col-sm-2 col-form-label">Detak Jantung Janin</label>
               <div class="col-sm-4">
                  <div class="input-group">
                     <input type="number" class="form-control @error('denyut_jantung_janin') is-invalid @enderror"
                        wire:model.defer="denyut_jantung_janin" id="denyut_jantung_janin">
                     <div class="input-group-append">
                        <span class="input-group-text">bpm</span>
                     </div>
                     @error('denyut_jantung_janin')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
               </div>

               <label for="presentasi" class="col-sm-2 col-form-label">Presentasi Janin</label>
               <div class="col-sm-4">
                  <select class="form-control @error('presentasi') is-invalid @enderror" wire:model.defer="presentasi"
                     id="presentasi">
                     <option value="">- Pilih -</option>
                     <option value="Kepala">Kepala</option>
                     <option value="Bokong">Bokong</option>
                     <option value="Lintang">Lintang</option>
                     <option value="Belum Diketahui">Belum Diketahui</option>
                  </select>
                  @error('presentasi')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
         </div>

         <!-- Status Imunisasi TT -->
         <div class="form-group" id="status-imunisasi">
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">8</span> Status Imunisasi TT (T6)
            </h5>

            <div class="form-group row">
               <label for="status_tt" class="col-sm-2 col-form-label">Status Imunisasi TT</label>
               <div class="col-sm-4">
                  <select class="form-control @error('status_tt') is-invalid @enderror" wire:model.defer="status_tt"
                     id="status_tt">
                     <option value="">- Pilih -</option>
                     <option value="TT1">TT1</option>
                     <option value="TT2">TT2</option>
                     <option value="TT3">TT3</option>
                     <option value="TT4">TT4</option>
                     <option value="TT5">TT5</option>
                     <option value="TT Lengkap">TT Lengkap</option>
                  </select>
                  @error('status_tt')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>

               <label for="tanggal_imunisasi" class="col-sm-2 col-form-label">Tanggal Imunisasi</label>
               <div class="col-sm-4">
                  <div class="input-group">
                     <input type="date" class="form-control @error('tanggal_imunisasi') is-invalid @enderror"
                        wire:model.defer="tanggal_imunisasi" id="tanggal_imunisasi">
                     <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                     </div>
                     @error('tanggal_imunisasi')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
               </div>
            </div>
         </div>

         <!-- Pemberian Tablet Tambah Darah (TTD) -->
         <div class="form-group" id="tablet-tambah-darah">
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">9</span> Pemberian Tablet Tambah Darah (T7)
            </h5>

            <div class="form-group row">
               <label for="jumlah_fe" class="col-sm-2 col-form-label">Jumlah Tablet Fe</label>
               <div class="col-sm-4">
                  <div class="input-group">
                     <input type="number" class="form-control @error('jumlah_fe') is-invalid @enderror"
                        wire:model.defer="jumlah_fe" id="jumlah_fe">
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
                  <input type="number" class="form-control @error('dosis') is-invalid @enderror"
                     wire:model.defer="dosis" id="dosis">
                  @error('dosis')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>
         </div>

         <!-- Pemeriksaan Laboratorium -->
         <div class="form-group" id="pemeriksaan-lab">
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">10</span> Pemeriksaan Laboratorium (T8)
            </h5>

            <div class="form-group row">
               <label class="col-sm-3 col-form-label">Tanggal Pengambilan Sampel</label>
               <div class="col-sm-9">
                  <div class="input-group">
                     <input type="date" class="form-control @error('tanggal_lab') is-invalid @enderror"
                        wire:model.defer="tanggal_lab" id="tanggal_lab">
                     <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                     </div>
                     @error('tanggal_lab')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
               </div>
            </div>

            <div class="form-group">
               <div class="card card-body bg-light">
                  <h6 class="font-weight-bold mb-3">Wajib diperiksa</h6>
                  <div class="row">
                     <!-- Hemoglobin -->
                     <div class="col-md-4">
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" wire:model.defer="lab.hb.checked"
                              id="lab_hb">
                           <label class="form-check-label font-weight-bold" for="lab_hb">Hemoglobin (Hb)</label>
                        </div>
                        <div class="input-group mb-3" id="input_hb" @if(!isset($lab['hb']['checked']) ||
                           !$lab['hb']['checked']) style="display:none" @endif>
                           <input type="number" step="0.01" class="form-control" wire:model.defer="lab.hb.nilai"
                              placeholder="Nilai">
                           <div class="input-group-append">
                              <span class="input-group-text">g/dL</span>
                           </div>
                        </div>
                     </div>

                     <!-- Golongan Darah -->
                     <div class="col-md-4">
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" wire:model.defer="lab.goldar.checked"
                              id="lab_goldar">
                           <label class="form-check-label font-weight-bold" for="lab_goldar">Golongan Darah</label>
                        </div>
                        <select class="form-control" wire:model.defer="lab.goldar.nilai" id="input_goldar"
                           @if(!isset($lab['goldar']['checked']) || !$lab['goldar']['checked']) style="display:none"
                           @endif>
                           <option value="">- Pilih -</option>
                           <option value="A">A</option>
                           <option value="B">B</option>
                           <option value="AB">AB</option>
                           <option value="O">O</option>
                        </select>
                     </div>

                     <!-- Protein Urin -->
                     <div class="col-md-4">
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" wire:model.defer="lab.protein_urin.checked"
                              id="lab_protein_urin">
                           <label class="form-check-label font-weight-bold" for="lab_protein_urin">Protein Urin</label>
                        </div>
                        <select class="form-control" wire:model.defer="lab.protein_urin.nilai" id="input_protein_urin"
                           @if(!isset($lab['protein_urin']['checked']) || !$lab['protein_urin']['checked'])
                           style="display:none" @endif>
                           <option value="">- Pilih -</option>
                           <option value="Negatif">Negatif</option>
                           <option value="+1">+1</option>
                           <option value="+2">+2</option>
                           <option value="+3">+3</option>
                           <option value="+4">+4</option>
                        </select>
                     </div>
                  </div>
               </div>
            </div>

            <div class="form-group mt-3">
               <div class="card card-body bg-light">
                  <h6 class="font-weight-bold mb-3">Skrining HIV, Sifilis, Hepatitis B</h6>
                  <div class="row">
                     <!-- HIV -->
                     <div class="col-md-4">
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" wire:model.defer="lab.hiv.checked"
                              id="lab_hiv">
                           <label class="form-check-label font-weight-bold" for="lab_hiv">HIV</label>
                        </div>
                        <select class="form-control" wire:model.defer="lab.hiv.nilai" id="input_hiv"
                           @if(!isset($lab['hiv']['checked']) || !$lab['hiv']['checked']) style="display:none" @endif>
                           <option value="">- Pilih -</option>
                           <option value="Non-Reaktif">Non-Reaktif</option>
                           <option value="Reaktif">Reaktif</option>
                        </select>
                     </div>

                     <!-- Sifilis -->
                     <div class="col-md-4">
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" wire:model.defer="lab.sifilis.checked"
                              id="lab_sifilis">
                           <label class="form-check-label font-weight-bold" for="lab_sifilis">Sifilis (VDRL)</label>
                        </div>
                        <select class="form-control" wire:model.defer="lab.sifilis.nilai" id="input_sifilis"
                           @if(!isset($lab['sifilis']['checked']) || !$lab['sifilis']['checked']) style="display:none"
                           @endif>
                           <option value="">- Pilih -</option>
                           <option value="Non-Reaktif">Non-Reaktif</option>
                           <option value="Reaktif">Reaktif</option>
                        </select>
                     </div>

                     <!-- Hepatitis B -->
                     <div class="col-md-4">
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" wire:model.defer="lab.hbsag.checked"
                              id="lab_hbsag">
                           <label class="form-check-label font-weight-bold" for="lab_hbsag">Hepatitis B (HBsAg)</label>
                        </div>
                        <select class="form-control" wire:model.defer="lab.hbsag.nilai" id="input_hbsag"
                           @if(!isset($lab['hbsag']['checked']) || !$lab['hbsag']['checked']) style="display:none"
                           @endif>
                           <option value="">- Pilih -</option>
                           <option value="Non-Reaktif">Non-Reaktif</option>
                           <option value="Reaktif">Reaktif</option>
                        </select>
                     </div>
                  </div>

                  <div class="form-group row mt-3" id="rujukan_div" @if((!isset($lab['hiv']['checked']) ||
                     !$lab['hiv']['checked'] || !isset($lab['hiv']['nilai']) || $lab['hiv']['nilai'] !='Reaktif' ) &&
                     (!isset($lab['sifilis']['checked']) || !$lab['sifilis']['checked'] ||
                     !isset($lab['sifilis']['nilai']) || $lab['sifilis']['nilai'] !='Reaktif' ) &&
                     (!isset($lab['hbsag']['checked']) || !$lab['hbsag']['checked'] || !isset($lab['hbsag']['nilai']) ||
                     $lab['hbsag']['nilai'] !='Reaktif' )) style="display:none" @endif>
                     <label class="col-sm-3 col-form-label">Tindak Lanjut Rujukan</label>
                     <div class="col-sm-9">
                        <textarea class="form-control" wire:model.defer="rujukan_ims" rows="2"
                           placeholder="Detail rujukan untuk pasien positif HIV/Sifilis/Hepatitis B"></textarea>
                     </div>
                  </div>
               </div>
            </div>

            <div class="form-group mt-3">
               <div class="card card-body bg-light">
                  <h6 class="font-weight-bold mb-3">Pemeriksaan Lainnya</h6>
                  <div class="row">
                     <!-- Gula Darah -->
                     <div class="col-md-4">
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" wire:model.defer="lab.gula_darah.checked"
                              id="lab_gula_darah">
                           <label class="form-check-label font-weight-bold" for="lab_gula_darah">Gula Darah</label>
                        </div>
                        <div class="input-group mb-3" id="input_gula_darah" @if(!isset($lab['gula_darah']['checked']) ||
                           !$lab['gula_darah']['checked']) style="display:none" @endif>
                           <input type="number" class="form-control" wire:model.defer="lab.gula_darah.nilai"
                              placeholder="Nilai">
                           <div class="input-group-append">
                              <span class="input-group-text">mg/dL</span>
                           </div>
                        </div>
                     </div>

                     <!-- Malaria -->
                     <div class="col-md-4">
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" wire:model.defer="lab.malaria.checked"
                              id="lab_malaria">
                           <label class="form-check-label font-weight-bold" for="lab_malaria">Malaria</label>
                        </div>
                        <select class="form-control" wire:model.defer="lab.malaria.nilai" id="input_malaria"
                           @if(!isset($lab['malaria']['checked']) || !$lab['malaria']['checked']) style="display:none"
                           @endif>
                           <option value="">- Pilih -</option>
                           <option value="Negatif">Negatif</option>
                           <option value="Positif">Positif</option>
                        </select>
                     </div>

                     <!-- Pemeriksaan Lain -->
                     <div class="col-md-4">
                        <div class="form-check mb-2">
                           <input class="form-check-input" type="checkbox" wire:model.defer="lab.lainnya.checked"
                              id="lab_lainnya">
                           <label class="form-check-label font-weight-bold" for="lab_lainnya">Lainnya</label>
                        </div>
                        <div id="input_lab_lainnya" @if(!isset($lab['lainnya']['checked']) ||
                           !$lab['lainnya']['checked']) style="display:none" @endif>
                           <input type="text" class="form-control mb-2" wire:model.defer="lab.lainnya.nama"
                              placeholder="Nama Pemeriksaan">
                           <input type="text" class="form-control" wire:model.defer="lab.lainnya.nilai"
                              placeholder="Hasil">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Tatalaksana Kasus -->
         <div class="form-group" id="tatalaksana-kasus">
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">11</span> Tatalaksana Kasus (T9)
            </h5>

            <div class="form-group row">
               <label for="jenis_tatalaksana" class="col-sm-2 col-form-label">Jenis Tatalaksana</label>
               <div class="col-sm-10">
                  <select class="form-control @error('jenis_tatalaksana') is-invalid @enderror"
                     wire:model.defer="jenis_tatalaksana" id="jenis_tatalaksana">
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

            <!-- Form khusus untuk berbagai jenis tatalaksana -->
            @if($jenis_tatalaksana == 'Anemia')
            <!-- Form anemia -->
            @endif

            @if($jenis_tatalaksana == 'Makanan Tambahan Ibu Hamil')
            <!-- Form Makanan Tambahan -->
            @endif

            @if($jenis_tatalaksana == 'Hipertensi')
            <!-- Form Hipertensi -->
            @endif

            <!-- Bentuk tatalaksana lainnya... -->

         </div>

         <!-- Temu Wicara/Konseling -->
         <div class="form-group" id="temu-wicara">
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">12</span> Temu Wicara/Konseling (T10)
            </h5>

            <div class="form-group row">
               <label for="materi" class="col-sm-2 col-form-label">Materi <span class="text-danger">*</span></label>
               <div class="col-sm-10">
                  <textarea class="form-control @error('materi') is-invalid @enderror" rows="3"
                     wire:model.defer="materi" id="materi"></textarea>
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
                     wire:model.defer="rekomendasi" id="rekomendasi"></textarea>
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
                           wire:model.defer="konseling_menyusui" value="Ya">
                        <label class="form-check-label" for="menyusui_ya">Ya</label>
                     </div>
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="konseling_menyusui" id="menyusui_tidak"
                           wire:model.defer="konseling_menyusui" value="Tidak">
                        <label class="form-check-label" for="menyusui_tidak">Tidak</label>
                     </div>
                  </div>
                  @error('konseling_menyusui')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
               </div>
            </div>

            <!-- Opsi konseling lainnya -->
            <div class="form-group row">
               <label for="tanda_bahaya_kehamilan" class="col-sm-2 col-form-label">Tanda Bahaya Kehamilan <span
                     class="text-danger">*</span></label>
               <div class="col-sm-10">
                  <div class="d-flex">
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tanda_bahaya_kehamilan" id="bahaya_hamil_ya"
                           wire:model.defer="tanda_bahaya_kehamilan" value="Ya">
                        <label class="form-check-label" for="bahaya_hamil_ya">Ya</label>
                     </div>
                     <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tanda_bahaya_kehamilan"
                           id="bahaya_hamil_tidak" wire:model.defer="tanda_bahaya_kehamilan" value="Tidak">
                        <label class="form-check-label" for="bahaya_hamil_tidak">Tidak</label>
                     </div>
                  </div>
                  @error('tanda_bahaya_kehamilan')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
               </div>
            </div>

            <!-- Opsi konseling lainnya -->
            <div class="form-group row">
               <label for="konseling_lainnya" class="col-sm-2 col-form-label">Konseling Lainnya</label>
               <div class="col-sm-10">
                  <input type="text" class="form-control" wire:model.defer="konseling_lainnya" id="konseling_lainnya"
                     placeholder="Sebutkan">
               </div>
            </div>
         </div>

         <!-- Keadaan Pulang -->
         <div class="form-group" id="keadaan-pulang">
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">13</span> Keadaan Pulang
            </h5>

            <div class="form-group row">
               <label for="keadaan_pulang" class="col-sm-2 col-form-label">Keadaan Pulang <span
                     class="text-danger">*</span></label>
               <div class="col-sm-10">
                  <select class="form-control @error('keadaan_pulang') is-invalid @enderror"
                     wire:model.defer="keadaan_pulang" id="keadaan_pulang">
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

         <!-- Tindak Lanjut -->
         <div class="form-group" id="tindak-lanjut">
            <h5 class="mb-3 font-weight-bold text-navy-blue d-flex align-items-center">
               <span class="badge badge-primary mr-2">14</span> Tindak Lanjut
            </h5>

            <div class="form-group row">
               <label for="tindak_lanjut" class="col-sm-2 col-form-label">Jenis Tindak Lanjut</label>
               <div class="col-sm-10">
                  <select class="form-control @error('tindak_lanjut') is-invalid @enderror"
                     wire:model.defer="tindak_lanjut" id="tindak_lanjut">
                     <option value="">- Pilih -</option>
                     <option value="Kunjungan ANC berikutnya">Kunjungan ANC berikutnya</option>
                     <option value="Dirujuk ke Faskes Tingkat Lanjut">Dirujuk ke Faskes Tingkat Lanjut</option>
                     <option value="Pemeriksaan USG">Pemeriksaan USG</option>
                     <option value="Pemeriksaan Laboratorium">Pemeriksaan Laboratorium</option>
                     <option value="Lainnya">Lainnya</option>
                  </select>
                  @error('tindak_lanjut')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>

            <div class="form-group row" id="detail_tindak_lanjut_div" @if(!isset($tindak_lanjut) ||
               empty($tindak_lanjut)) style="display:none" @endif>
               <label for="detail_tindak_lanjut" class="col-sm-2 col-form-label">Detail Tindak Lanjut</label>
               <div class="col-sm-10">
                  <textarea class="form-control @error('detail_tindak_lanjut') is-invalid @enderror" rows="3"
                     wire:model.defer="detail_tindak_lanjut" id="detail_tindak_lanjut"></textarea>
                  @error('detail_tindak_lanjut')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
            </div>

            <div class="form-group row">
               <label for="tanggal_kunjungan_berikutnya" class="col-sm-2 col-form-label">Tanggal Kunjungan
                  Berikutnya</label>
               <div class="col-sm-4">
                  <div class="input-group">
                     <input type="date" class="form-control @error('tanggal_kunjungan_berikutnya') is-invalid @enderror"
                        wire:model.defer="tanggal_kunjungan_berikutnya" id="tanggal_kunjungan_berikutnya">
                     <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                     </div>
                     @error('tanggal_kunjungan_berikutnya')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                  </div>
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

      <!-- Tabel Histori Pemeriksaan ANC -->
      @if($riwayat && $riwayat->count() > 0)
      <div class="card shadow mb-4">
         <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Pemeriksaan ANC</h6>
         </div>
         <div class="card-body">
            <div class="table-responsive">
               <table class="table table-bordered table-striped table-hover small" id="tabel-riwayat-anc" width="100%"
                  cellspacing="0">
                  <thead class="bg-primary text-white">
                     <tr>
                        <th>No</th>
                        <th>ID ANC</th>
                        <th>Tanggal</th>
                        <th>Diperiksa Oleh</th>
                        <th>UK</th>
                        <th>BB</th>
                        <th>TD</th>
                        <th>Tinggi Fundus</th>
                        <th>Keluhan</th>
                        <th>Tatalaksana</th>
                        <th>Tindak Lanjut</th>
                        <th>Aksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($riwayat as $index => $item)
                     <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->id_anc }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_anc)->format('d-m-Y H:i') }}</td>
                        <td>{{ $item->diperiksa_oleh }}</td>
                        <td>{{ $item->usia_kehamilan }} minggu</td>
                        <td>{{ $item->berat_badan }} kg</td>
                        <td>{{ $item->td_sistole }}/{{ $item->td_diastole }}</td>
                        <td>{{ $item->tinggi_fundus ?? '-' }}</td>
                        <td>{{ Str::limit($item->keluhan_utama, 30) }}</td>
                        <td>{{ $item->jenis_tatalaksana ?? '-' }}</td>
                        <td>{{ $item->tindak_lanjut ?? '-' }}</td>
                        <td class="text-center">
                           <div class="btn-group">
                              <button type="button" class="btn btn-xs btn-primary"
                                 wire:click="showHistoriANC('{{ $item->id_anc }}')">
                                 <i class="fas fa-eye"></i>
                              </button>
                              <button type="button" class="btn btn-xs btn-warning"
                                 wire:click="edit('{{ $item->id_anc }}')">
                                 <i class="fas fa-edit"></i>
                              </button>
                           </div>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      @endif

      <!-- Riwayat Kunjungan ANC Berdasarkan Id Hamil -->
      @if($id_hamil && $riwayatByIdHamil->count() > 0)
      <div class="card shadow mb-4">
         <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
               <i class="fas fa-history mr-1"></i> Riwayat Kunjungan ANC (K1-K4)
            </h6>
         </div>
         <div class="card-body">
            <!-- Tab navigation untuk bulan-bulan -->
            <ul class="nav nav-tabs" id="riwayat-tab" role="tablist">
               @php
               $riwayatByMonth = $riwayatByIdHamil->groupBy(function($item) {
               return \Carbon\Carbon::parse($item->tanggal_anc)->format('Y-m');
               });
               $counter = 0;
               @endphp

               @foreach($riwayatByMonth as $yearMonth => $items)
               @php
               $monthName = \Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->format('F Y');
               $tabId = 'month-' . str_replace(' ', '-', strtolower($monthName));
               $isActive = $counter === 0 ? 'active' : '';
               $counter++;
               @endphp
               <li class="nav-item" role="presentation">
                  <a class="nav-link {{ $isActive }}" id="{{ $tabId }}-tab" data-toggle="tab" href="#{{ $tabId }}"
                     role="tab" aria-controls="{{ $tabId }}" aria-selected="{{ $counter === 1 ? 'true' : 'false' }}">
                     {{ $monthName }}
                  </a>
               </li>
               @endforeach
            </ul>

            <!-- Tab content untuk data setiap bulan -->
            <div class="tab-content mt-3" id="riwayat-content">
               @php $counter = 0; @endphp
               @foreach($riwayatByMonth as $yearMonth => $items)
               @php
               $monthName = \Carbon\Carbon::createFromFormat('Y-m', $yearMonth)->format('F Y');
               $tabId = 'month-' . str_replace(' ', '-', strtolower($monthName));
               $isActive = $counter === 0 ? 'show active' : '';
               $counter++;
               @endphp
               <div class="tab-pane fade {{ $isActive }}" id="{{ $tabId }}" role="tabpanel"
                  aria-labelledby="{{ $tabId }}-tab">
                  <div class="table-responsive">
                     <table class="table table-bordered table-hover small" id="table-{{ $tabId }}">
                        <thead class="bg-primary text-white">
                           <tr>
                              <th>No</th>
                              <th>Tanggal</th>
                              <th>Kunjungan Ke</th>
                              <th>Usia Kehamilan</th>
                              <th>BB (kg)</th>
                              <th>TD</th>
                              <th>Tinggi Fundus (cm)</th>
                              <th>TBJ (gram)</th>
                              <th>Keluhan</th>
                              <th>Tindak Lanjut</th>
                              <th>Aksi</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach($items as $index => $item)
                           <tr>
                              <td>{{ $index + 1 }}</td>
                              <td>{{ \Carbon\Carbon::parse($item->tanggal_anc)->format('d-m-Y') }}</td>
                              <td>K{{ $item->kunjungan_ke ?? '-' }}</td>
                              <td>{{ $item->usia_kehamilan }} minggu</td>
                              <td>{{ $item->berat_badan }}</td>
                              <td>{{ $item->td_sistole }}/{{ $item->td_diastole }}</td>
                              <td>{{ $item->tinggi_fundus ?? '-' }}</td>
                              <td>{{ $item->taksiran_berat_janin ?? '-' }}</td>
                              <td>{{ Str::limit($item->keluhan_utama, 30) }}</td>
                              <td>{{ $item->tindak_lanjut ?? '-' }}</td>
                              <td class="text-center">
                                 <div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-primary"
                                       wire:click="showHistoriANC('{{ $item->id_anc }}')">
                                       <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-warning"
                                       wire:click="edit('{{ $item->id_anc }}')">
                                       <i class="fas fa-edit"></i>
                                    </button>
                                 </div>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
               </div>
               @endforeach
            </div>
         </div>
      </div>
      @endif
      @endif
   </div>

   @push('scripts')
   <!-- Scripts jika diperlukan -->
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         // Format tanggal dari string ISO ke format datetime-local yang diterima HTML
         function formatDateForInput(dateStr) {
            if (!dateStr) return '';
            try {
               const date = new Date(dateStr);
               // Pastikan tanggal valid
               if (isNaN(date.getTime())) return '';
               
               // Format ke YYYY-MM-DDThh:mm yang diterima oleh input datetime-local
               return date.toISOString().slice(0, 16);
            } catch (e) {
               console.error('Error formatting date:', e);
               return '';
            }
         }

         // Fungsi untuk menghandle input dan output tanggal dihapus
         // Jalankan tanpa setupDateTimeInput karena sudah ditangani oleh wire:model

         // Inisialisasi DataTable untuk tabel riwayat ANC utama
         if (document.getElementById('tabel-riwayat-anc')) {
            $('#tabel-riwayat-anc').DataTable({
               "language": {
                  "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
               },
               "pageLength": 5,
               "ordering": true,
               "responsive": true,
               "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]]
            });
         }
         
         // Fungsi untuk inisialisasi DataTables pada tab yang aktif
         function initDataTable(tabId) {
            const tableId = 'table-' + tabId;
            if (document.getElementById(tableId)) {
               // Hancurkan instance sebelumnya jika ada
               if ($.fn.DataTable.isDataTable('#' + tableId)) {
                  $('#' + tableId).DataTable().destroy();
               }
               
               // Inisialisasi DataTable baru
               $('#' + tableId).DataTable({
                  "language": {
                     "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                  },
                  "pageLength": 5,
                  "ordering": true,
                  "responsive": true,
                  "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]]
               });
            }
         }
         
         // Inisialisasi DataTable pada tab aktif pertama
         const firstActiveTab = document.querySelector('.tab-pane.active');
         if (firstActiveTab) {
            const firstTabId = firstActiveTab.id;
            initDataTable(firstTabId);
         }
         
         // Handler untuk event saat tab diklik
         $('#riwayat-tab a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const tabId = e.target.getAttribute('href').substring(1);
            initDataTable(tabId);
         });
         
         // Handler untuk update tabel setelah Livewire update
         document.addEventListener('livewire:load', function () {
            Livewire.hook('message.processed', (message, component) => {
               // Reinisialisasi semua DataTables setelah update Livewire
               if (document.getElementById('tabel-riwayat-anc')) {
                  if ($.fn.DataTable.isDataTable('#tabel-riwayat-anc')) {
                     $('#tabel-riwayat-anc').DataTable().destroy();
                  }
                  
                  $('#tabel-riwayat-anc').DataTable({
                     "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                     },
                     "pageLength": 5,
                     "ordering": true,
                     "responsive": true,
                     "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]]
                  });
               }
               
               // Reinisialisasi tab yang aktif
               const activeTab = document.querySelector('.tab-pane.active');
               if (activeTab) {
                  const activeTabId = activeTab.id;
                  initDataTable(activeTabId);
               }
            });
            
            // Menangani tampilan field lainnya pada Riwayat Penyakit
            const lainnyaCheck = document.getElementById('lainnya_check');
            const riwayatLainnya = document.getElementById('riwayat_lainnya');
            
            // Fungsi untuk update tampilan field lainnya
            function updateRiwayatLainnyaVisibility() {
               if (lainnyaCheck && riwayatLainnya) {
                  if (lainnyaCheck.checked) {
                     riwayatLainnya.style.display = 'block';
                  } else {
                     riwayatLainnya.style.display = 'none';
                  }
               }
            }
            
            // Inisialisasi saat halaman dimuat
            updateRiwayatLainnyaVisibility();
            
            // Tambahkan event listener untuk checkbox lainnya
            if (lainnyaCheck) {
               lainnyaCheck.addEventListener('change', updateRiwayatLainnyaVisibility);
            }
            
            // Update setiap kali Livewire merender ulang
            Livewire.hook('element.updated', (el, component) => {
               updateRiwayatLainnyaVisibility();
            });
         });
      });
   </script>
   @endpush

   @push('js')
   <script>
      document.addEventListener('DOMContentLoaded', function () {
         // Integrasi dengan DataTables dipertahankan
         
         // Tampilkan pesan setelah update form berhasil
         Livewire.on('formSaved', function() {
            setTimeout(function() {
               const alerts = document.querySelectorAll('.alert');
               alerts.forEach(function(alert) {
                  const closeBtn = alert.querySelector('.close');
                  if (closeBtn) {
                     closeBtn.click();
                  }
               });
            }, 3000); // Pesan akan hilang setelah 3 detik
         });
         
         // Tampilkan error jika ada
         Livewire.on('showError', function(message) {
            console.error('Error:', message);
         });
         
         // Handle checkbox Lainnya pada Riwayat Penyakit
         const lainnyaCheck = document.getElementById('lainnya_check');
         const riwayatLainnyaContainer = document.getElementById('riwayat_lainnya_container');
         
         // Fungsi untuk mengupdate tampilan
         function updateRiwayatLainnyaVisibility() {
            if (lainnyaCheck && riwayatLainnyaContainer) {
               if (lainnyaCheck.checked) {
                  riwayatLainnyaContainer.classList.add('active');
               } else {
                  riwayatLainnyaContainer.classList.remove('active');
               }
            }
         }
         
         // Jalankan saat awal load
         updateRiwayatLainnyaVisibility();
         
         // Tambahkan event listener
         if (lainnyaCheck) {
            lainnyaCheck.addEventListener('change', updateRiwayatLainnyaVisibility);
         }
         
         // Update setiap kali Livewire melakukan rendering ulang
         document.addEventListener('livewire:load', function() {
            Livewire.hook('message.processed', (message, component) => {
               updateRiwayatLainnyaVisibility();
               
               // Update nilai dari formulir setelah perhitungan server
               if (message.updateQueue.find(update => update.payload.event === 'hitungIMT' || 
                    update.payload.event === 'tentukanStatusGizi' || 
                    update.payload.event === 'hitungTaksiranBeratJanin')) {
                  console.log('Perhitungan dilakukan di server');
               }
            });
         });
         
         // =========================================================
         // PERHITUNGAN SISI KLIEN (JAVASCRIPT) UNTUK PREVIEW INSTANT
         // =========================================================
         // Perhitungan ini membantu menampilkan hasil sementara
         // tanpa harus mengirim request ke server
         // Nilai aktual tetap dihitung di server saat tombol Hitung diklik
         // =========================================================
         
         // Preview perhitungan IMT di sisi klien (tanpa refresh)
         const beratBadanInput = document.getElementById('berat_badan');
         const tinggiBadanInput = document.getElementById('tinggi_badan');
         const imtDisplay = document.getElementById('imt');
         const kategoriImtDisplay = document.getElementById('kategori_imt');
         
         // Fungsi untuk menghitung IMT di sisi klien
         function hitungImtClient() {
            if (beratBadanInput && tinggiBadanInput && beratBadanInput.value && tinggiBadanInput.value) {
               const beratBadan = parseFloat(beratBadanInput.value);
               const tinggiBadan = parseFloat(tinggiBadanInput.value) / 100; // konversi ke meter
               
               if (beratBadan > 0 && tinggiBadan > 0) {
                  const imt = beratBadan / (tinggiBadan * tinggiBadan);
                  const imtRounded = Math.round(imt * 100) / 100;
                  
                  // Update display IMT
                  if (imtDisplay) {
                     imtDisplay.value = imtRounded;
                  }
                  
                  // Update kategori IMT
                  if (kategoriImtDisplay) {
                     let kategori = '';
                     if (imtRounded < 18.5) {
                        kategori = 'KURUS';
                     } else if (imtRounded >= 18.5 && imtRounded <= 24.9) {
                        kategori = 'NORMAL';
                     } else if (imtRounded >= 25 && imtRounded <= 29.9) {
                        kategori = 'GEMUK';
                     } else {
                        kategori = 'OBESITAS';
                     }
                     kategoriImtDisplay.value = kategori;
                  }
               }
            }
         }
         
         // Event listeners untuk perubahan input
         if (beratBadanInput && tinggiBadanInput) {
            beratBadanInput.addEventListener('input', hitungImtClient);
            tinggiBadanInput.addEventListener('input', hitungImtClient);
         }
         
         // Preview perhitungan Taksiran Berat Janin di sisi klien
         const tinggiFundusInput = document.getElementById('tinggi_fundus');
         const tbjanDisplay = document.getElementById('taksiran_berat_janin');
         
         function hitungTbjanClient() {
            if (tinggiFundusInput && tinggiFundusInput.value && tbjanDisplay) {
               const tfu = parseFloat(tinggiFundusInput.value);
               if (tfu > 0) {
                  // Rumus Johnson-Toshach: BB (gram) = 155 x (tinggi fundus dalam cm - 13)
                  const n = 13; // Asumsi kepala belum masuk PAP
                  const beratJanin = 155 * (tfu - n);
                  
                  if (beratJanin > 0) {
                     tbjanDisplay.value = Math.round(beratJanin);
                  } else {
                     tbjanDisplay.value = 0;
                  }
               }
            }
         }
         
         if (tinggiFundusInput) {
            tinggiFundusInput.addEventListener('input', hitungTbjanClient);
         }
         
         // Preview perhitungan Status Gizi berdasarkan LILA
         const lilaInput = document.getElementById('lila');
         const statusGiziDisplay = document.getElementById('status_gizi');
         
         function hitungStatusGiziClient() {
            if (lilaInput && lilaInput.value && statusGiziDisplay) {
               const lila = parseFloat(lilaInput.value);
               if (lila > 0) {
                  if (lila < 23.5) {
                     statusGiziDisplay.value = 'KEK (Kurang Energi Kronis)';
                  } else {
                     statusGiziDisplay.value = 'Normal';
                  }
               }
            }
         }
         
         if (lilaInput) {
            lilaInput.addEventListener('input', hitungStatusGiziClient);
         }
      });
   </script>
   @endpush
</div>
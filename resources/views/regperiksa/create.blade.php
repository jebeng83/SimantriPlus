@extends('adminlte::page')

@section('title', 'Registrasi Periksa')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
   <div>
      <h1 class="text-gradient">
         <i class="fas fa-notes-medical"></i> Registrasi Periksa Pasien
      </h1>
      <p class="text-muted mb-0">Form pendaftaran pemeriksaan pasien</p>
   </div>
   <nav aria-label="breadcrumb">
      <ol class="breadcrumb shadow-sm border bg-white py-2 px-3">
         <li class="breadcrumb-item"><a href="{{ route('pasien.index') }}" class="text-decoration-none">Pasien</a></li>
         <li class="breadcrumb-item active">Registrasi Periksa</li>
      </ol>
   </nav>
</div>
@stop

@section('content')
<div class="row">
   <div class="col-12">
      <!-- Data Pasien Card -->
      <div class="card card-hover mb-4">
         <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0">
               <i class="fas fa-user-circle mr-2"></i>Data Pasien
            </h5>
         </div>
         <div class="card-body">
            <div class="row align-items-start">
               <div class="col-md-2 text-center mb-3 mb-md-0">
                  <div class="avatar-circle mx-auto mb-3">
                     <i class="fas fa-user-injured fa-3x text-primary"></i>
                  </div>
                  <div class="badge badge-primary-soft px-3 py-2 mb-2">
                     <i class="fas fa-id-card-alt mr-1"></i>
                     RM: {{ $pasien->no_rkm_medis }}
                  </div>
                  <div class="badge badge-info-soft px-3 py-2">
                     @php
                     $umur = intval($pasien->umur);
                     $sasaran = '';
                     if ($umur < 5) { $sasaran='Bayi dan Balita' ; } elseif ($umur>= 5 && $umur <= 9) {
                           $sasaran='Anak-Anak' ; } elseif ($umur>= 10 && $umur <= 18) { $sasaran='Remaja' ; } elseif
                              ($umur>= 19 && $umur <= 59) { $sasaran='Dewasa/Produktif' ; } else { $sasaran='Lansia' ; }
                                 @endphp <i class="fas fa-users mr-1"></i>
                                 {{ $sasaran }}
                  </div>
               </div>
               <div class="col-md-5">
                  <div class="info-group">
                     <div class="info-item">
                        <i class="fas fa-user icon-circle bg-primary-soft text-primary"></i>
                        <div class="info-content">
                           <label>Nama Pasien</label>
                           <strong>{{ $pasien->nm_pasien }}</strong>
                        </div>
                     </div>
                     <div class="info-item">
                        <i class="fas fa-calendar icon-circle bg-primary-soft text-primary"></i>
                        <div class="info-content">
                           <label>Umur</label>
                           <span>{{ $pasien->umur }}</span>
                        </div>
                     </div>
                     <div class="info-item">
                        <i class="fas fa-briefcase icon-circle bg-primary-soft text-primary"></i>
                        <div class="info-content">
                           <label>Pekerjaan</label>
                           <span>{{ $pasien->pekerjaan }}</span>
                        </div>
                     </div>
                     <div class="info-item">
                        <i class="fas fa-id-card icon-circle bg-primary-soft text-primary"></i>
                        <div class="info-content">
                           <label>No. KTP</label>
                           <span>{{ $pasien->no_ktp ?: '-' }}</span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-5">
                  <div class="info-group">
                     <div class="info-item">
                        <i class="fas fa-phone icon-circle bg-primary-soft text-primary"></i>
                        <div class="info-content">
                           <label>No. Telepon</label>
                           <span>{{ $pasien->no_tlp }}</span>
                        </div>
                     </div>
                     <div class="info-item">
                        <i class="fas fa-map-marker-alt icon-circle bg-primary-soft text-primary"></i>
                        <div class="info-content">
                           <label>Alamat</label>
                           <span>{{ $pasien->alamat }}</span>
                        </div>
                     </div>
                     <div class="info-item">
                        <i class="fas fa-credit-card icon-circle bg-primary-soft text-primary"></i>
                        <div class="info-content">
                           <label>Cara Bayar</label>
                           <span>{{ $pasien->penjab_pasien }}</span>
                        </div>
                     </div>
                     <div class="info-item">
                        <i class="fas fa-address-card icon-circle bg-primary-soft text-primary"></i>
                        <div class="info-content">
                           <label>No. Peserta</label>
                           <span>{{ $pasien->no_peserta ?: '-' }}</span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Form Registrasi -->
      <form id="formRegPeriksa" action="{{ route('regperiksa.store') }}" method="POST" class="card card-hover">
         @csrf
         <input type="hidden" name="no_rkm_medis" value="{{ $pasien->no_rkm_medis }}">
         <input type="hidden" name="no_reg" id="no_reg">

         <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0">
               <i class="fas fa-file-medical mr-2"></i>Form Registrasi
            </h5>
         </div>

         <div class="card-body">
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">
                        <i class="fas fa-hospital text-primary"></i>
                        Poliklinik <span class="text-danger">*</span>
                     </label>
                     <select name="kd_poli" id="kd_poli" class="form-control form-control-lg select2bs4" required>
                        <option value="">Pilih Poliklinik</option>
                        @foreach($poliklinik as $poli)
                        <option value="{{ $poli->kd_poli }}">{{ $poli->nm_poli }}</option>
                        @endforeach
                     </select>
                  </div>

                  <div class="form-group">
                     <label class="form-label">
                        <i class="fas fa-user-md text-primary"></i>
                        Dokter <span class="text-danger">*</span>
                     </label>
                     <select name="kd_dokter" id="kd_dokter" class="form-control form-control-lg select2bs4" required>
                        <option value="">Pilih Dokter</option>
                        @foreach($dokter as $dok)
                        <option value="{{ $dok->kd_dokter }}">{{ $dok->nm_dokter }}</option>
                        @endforeach
                     </select>
                  </div>

                  <div class="form-group">
                     <label class="form-label">
                        <i class="fas fa-money-check text-primary"></i>
                        Cara Bayar <span class="text-danger">*</span>
                     </label>
                     <select name="kd_pj" id="kd_pj" class="form-control form-control-lg select2bs4" required>
                        <option value="">Pilih Cara Bayar</option>
                        @foreach($penjab as $pj)
                        <option value="{{ $pj->kd_pj }}" {{ $pasien->kd_pj == $pj->kd_pj ? 'selected' : '' }}>
                           {{ $pj->png_jawab }}
                        </option>
                        @endforeach
                     </select>
                     @if($pasien->penjab_pasien)
                     <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i>
                        Default: {{ $pasien->penjab_pasien }}
                     </small>
                     @endif
                  </div>
               </div>

               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">
                        <i class="fas fa-user-shield text-primary"></i>
                        Penanggung Jawab
                     </label>
                     <input type="text" name="p_jawab" class="form-control form-control-lg"
                        value="{{ $pasien->namakeluarga }}">
                  </div>

                  <div class="form-group">
                     <label class="form-label">
                        <i class="fas fa-map-marked text-primary"></i>
                        Alamat P.J.
                     </label>
                     <textarea name="almt_pj" class="form-control form-control-lg"
                        rows="2">{{ $pasien->alamatpj }}</textarea>
                  </div>

                  <div class="form-group">
                     <label class="form-label">
                        <i class="fas fa-clinic-medical text-primary"></i>
                        Posyandu
                     </label>
                     <select name="hubunganpj" class="form-control form-control-lg select2bs4">
                        <option value="">Pilih Posyandu</option>
                        @foreach($posyandu as $pos)
                        <option value="{{ $pos->nama_posyandu }}" {{ $pasien->data_posyandu == $pos->nama_posyandu ?
                           'selected' : '' }}>
                           {{ $pos->nama_posyandu }} - {{ $pos->desa }}
                        </option>
                        @endforeach
                     </select>
                     @if($pasien->nama_posyandu)
                     <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i>
                        Default: {{ $pasien->nama_posyandu }}
                     </small>
                     @endif
                  </div>
               </div>
            </div>
         </div>

         <div class="card-footer bg-light border-top py-3">
            <button type="submit" class="btn btn-primary btn-lg px-4" id="btnSimpan">
               <i class="fas fa-save mr-2"></i>Simpan Registrasi
            </button>
            <a href="{{ route('pasien.index') }}" class="btn btn-secondary btn-lg px-4">
               <i class="fas fa-times mr-2"></i>Batal
            </a>
         </div>
      </form>
   </div>
</div>
@stop

@section('css')
<style>
   /* Gradient & Colors */
   .text-gradient {
      background: linear-gradient(45deg, #2b5876, #4e4376);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin: 0;
   }

   .bg-gradient-primary {
      background: linear-gradient(45deg, #1e88e5, #1976d2);
   }

   .bg-primary-soft {
      background-color: rgba(30, 136, 229, 0.1);
   }

   .text-primary {
      color: #1e88e5 !important;
   }

   /* Card Styling */
   .card {
      border: none;
      border-radius: 1rem;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      transition: all 0.3s ease;
   }

   .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
   }

   /* Avatar & Icons */
   .avatar-circle {
      width: 100px;
      height: 100px;
      background: rgba(30, 136, 229, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #1e88e5;
   }

   .icon-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 1rem;
   }

   /* Info Groups */
   .info-group {
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
   }

   .info-item {
      display: flex;
      align-items: center;
   }

   .info-content {
      flex: 1;
   }

   .info-content label {
      display: block;
      font-size: 0.875rem;
      color: #6c757d;
      margin-bottom: 0.25rem;
   }

   .info-content strong,
   .info-content span {
      display: block;
      color: #2d3748;
   }

   /* Form Controls */
   .form-control {
      border-radius: 0.5rem;
      border: 1px solid #e2e8f0;
      padding: 0.75rem 1rem;
      transition: all 0.2s ease;
   }

   .form-control:focus {
      border-color: #1e88e5;
      box-shadow: 0 0 0 0.2rem rgba(30, 136, 229, 0.25);
   }

   .form-control-lg {
      height: calc(1.5em + 1.5rem + 2px);
   }

   .form-label {
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: #2d3748;
   }

   /* Select2 Customization */
   .select2-container--bootstrap4 .select2-selection {
      border-radius: 0.5rem;
      border: 1px solid #e2e8f0;
      min-height: 50px;
      display: flex;
      align-items: center;
   }

   .select2-container--bootstrap4 .select2-selection--single {
      padding: 0.75rem 1rem;
   }

   .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
      padding: 0;
      line-height: 1.5;
      color: #2d3748;
      font-size: 1rem;
   }

   .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
      height: 100%;
      position: absolute;
      top: 0;
      right: 0.75rem;
      width: 2rem;
   }

   .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow b {
      border-color: #1e88e5 transparent transparent transparent;
      border-width: 6px 4px 0 4px;
   }

   .select2-container--bootstrap4.select2-container--open .select2-selection--single .select2-selection__arrow b {
      border-color: transparent transparent #1e88e5 transparent;
      border-width: 0 4px 6px 4px;
   }

   .select2-container--bootstrap4 .select2-dropdown {
      border-color: #1e88e5;
      border-radius: 0.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
   }

   .select2-container--bootstrap4 .select2-results__option {
      padding: 0.75rem 1rem;
      font-size: 1rem;
      line-height: 1.5;
      color: #2d3748;
   }

   .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
      background-color: #1e88e5;
      color: white;
   }

   .select2-container--bootstrap4 .select2-results__option[aria-selected=true] {
      background-color: rgba(30, 136, 229, 0.1);
      color: #1e88e5;
   }

   .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
      border: 1px solid #e2e8f0;
      border-radius: 0.25rem;
      padding: 0.5rem;
      font-size: 1rem;
   }

   .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field:focus {
      border-color: #1e88e5;
      outline: none;
   }

   /* Form Group Spacing */
   .form-group {
      margin-bottom: 1.5rem;
   }

   .form-group:last-child {
      margin-bottom: 0;
   }

   /* Form Label Alignment */
   .form-label {
      display: flex;
      align-items: center;
      margin-bottom: 0.75rem;
      color: #2d3748;
      font-weight: 500;
   }

   .form-label .fas {
      margin-right: 0.5rem;
      width: 1.25rem;
      text-align: center;
   }

   .form-label .text-danger {
      margin-left: 0.25rem;
   }

   /* Form Control Consistency */
   .form-control-lg,
   .select2-container--bootstrap4 .select2-selection {
      font-size: 1rem !important;
      line-height: 1.5;
      height: 50px;
   }

   /* Placeholder Styling */
   .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
      color: #a0aec0;
   }

   /* Default Text Info */
   .form-text.text-muted {
      margin-top: 0.5rem;
      font-size: 0.875rem;
      display: flex;
      align-items: center;
   }

   .form-text.text-muted .fas {
      margin-right: 0.375rem;
      font-size: 0.875rem;
   }

   /* Buttons */
   .btn {
      border-radius: 0.5rem;
      font-weight: 500;
      padding: 0.75rem 1.5rem;
      transition: all 0.3s ease;
   }

   .btn-lg {
      padding: 1rem 2rem;
   }

   .btn-primary {
      background: linear-gradient(45deg, #1e88e5, #1976d2);
      border: none;
   }

   .btn-primary:hover {
      background: linear-gradient(45deg, #1976d2, #1565c0);
      transform: translateY(-2px);
   }

   .btn-secondary {
      background: #f8f9fa;
      border: 1px solid #e2e8f0;
      color: #2d3748;
   }

   .btn-secondary:hover {
      background: #e2e8f0;
      color: #1a202c;
   }

   /* Badges */
   .badge-primary-soft {
      background-color: rgba(30, 136, 229, 0.1);
      color: #1e88e5;
      font-weight: 500;
   }

   .badge-info-soft {
      background-color: rgba(3, 169, 244, 0.1);
      color: #03a9f4;
      font-weight: 500;
   }

   .badge {
      font-size: 0.875rem;
      padding: 0.5em 1em;
      border-radius: 30px;
      display: inline-flex;
      align-items: center;
      line-height: 1;
   }

   .badge i {
      font-size: 0.875rem;
   }

   /* Responsive Adjustments */
   @media (max-width: 768px) {
      .avatar-circle {
         width: 80px;
         height: 80px;
      }

      .info-item {
         margin-bottom: 1rem;
      }

      .btn-lg {
         width: 100%;
         margin-bottom: 0.5rem;
      }
   }
</style>
@stop

@section('js')
<script>
   $(function() {
    // Inisialisasi Select2 dengan tema custom
    $('.select2bs4').select2({
        theme: 'bootstrap4',
        width: '100%',
        containerCssClass: 'select2-lg',
        dropdownCssClass: 'select2-lg'
    });

    // Event handler dokter dengan loading state
    $('#kd_dokter').on('change', function() {
        var dokter = $(this).val();
        if (!dokter) return;
        
        var btn = $('#btnSimpan').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...');
            
        $.get('/regperiksa/generate-noreg/' + dokter + '/' + '{{ date("Y-m-d") }}')
            .done(function(data) {
                $('#no_reg').val(data);
                btn.prop('disabled', false)
                   .html('<i class="fas fa-save mr-2"></i>Simpan Registrasi');
                   
                // Notifikasi sukses
                toastr.success('Nomor registrasi berhasil digenerate');
            })
            .fail(function() {
                toastr.error('Gagal mengambil nomor registrasi');
                btn.prop('disabled', false)
                   .html('<i class="fas fa-save mr-2"></i>Simpan Registrasi');
            });
    });

    // Form submission dengan animasi dan feedback
    $('#formRegPeriksa').on('submit', function(e) {
        e.preventDefault();
        
        if (!$('#kd_dokter').val() || !$('#no_reg').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih dokter terlebih dahulu',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#1e88e5'
            });
            return;
        }
        
        var btn = $('#btnSimpan').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');
        
        $.ajax({
            url: this.action,
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data registrasi berhasil disimpan',
                        timer: 1500,
                        showConfirmButton: false,
                        backdrop: `
                            rgba(30, 136, 229, 0.4)
                            url("/images/success-confetti.gif")
                            center top
                            no-repeat
                        `
                    }).then(() => location.href = '{{ route("pasien.index") }}');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Gagal menyimpan data',
                        confirmButtonText: 'Coba Lagi',
                        confirmButtonColor: '#1e88e5'
                    });
                    btn.prop('disabled', false)
                       .html('<i class="fas fa-save mr-2"></i>Simpan Registrasi');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#1e88e5'
                });
                btn.prop('disabled', false)
                   .html('<i class="fas fa-save mr-2"></i>Simpan Registrasi');
            }
        });
    });
});
</script>
@stop
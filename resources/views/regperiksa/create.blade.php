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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
   /* Sembunyikan tombol i-Care BPJS khusus di halaman ini */
   .btn-success.btn-block,
   [id^="btnIcareBpjs"],
   button.btn[onclick*="showIcareHistory"],
   .mb-3 .btn-success.btn-block,
   div.mb-3 button.btn-success.btn-block {
      display: none !important;
      visibility: hidden !important;
      width: 0 !important;
      height: 0 !important;
      position: absolute !important;
      overflow: hidden !important;
      opacity: 0 !important;
      z-index: -999 !important;
      clip: rect(0, 0, 0, 0) !important;
      margin: 0 !important;
      padding: 0 !important;
   }

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

   .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow b {
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   $(document).ready(function() {
        // Inisialisasi Select2
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Pilih opsi...',
            allowClear: true
        });
        
        // Inisialisasi toastr
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 5000
        };

        // Inisialisasi SweetAlert2
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        // Fungsi untuk menampilkan notifikasi dengan fallback
        function showNotification(type, message) {
            try {
                if (typeof toastr !== 'undefined' && toastr) {
                    switch(type) {
                        case 'success':
                            toastr.success(message);
                            break;
                        case 'error':
                            toastr.error(message);
                            break;
                        case 'warning':
                            toastr.warning(message);
                            break;
                        default:
                            toastr.info(message);
                    }
                } else if (typeof Swal !== 'undefined' && Swal) {
                    Toast.fire({
                        icon: type,
                        title: message
                    });
                } else {
                    alert(message);
                }
            } catch (e) {
                console.error("Notification error:", e);
                alert(message);
            }
        }

        // Debug untuk melihat event binding
        console.log("Script loaded, binding change event to #kd_dokter");

        // Event change untuk dokter
        $('#kd_dokter').on('change', function() {
            const dokter = $(this).val();
            console.log("Dokter changed:", dokter);
            
            if (!dokter) {
                $('#no_reg').val('');
                return;
            }
            
            const tglReg = '{{ date('Y-m-d') }}';
            const kdPoli = $('#kd_poli').val();
            
            if (!kdPoli) {
                showNotification('warning', 'Silakan pilih poliklinik terlebih dahulu');
                $(this).val('').trigger('change.select2');
                return;
            }
            
            $('#btnSimpan').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...');
            
            $.ajax({
                url: '{{ route("livewire.generate-noreg") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    dokter: dokter,
                    kd_poli: kdPoli,
                    tgl_registrasi: tglReg
                },
                dataType: 'json',
                timeout: 60000, // 60 detik timeout
                beforeSend: function() {
                    $('#no_reg').val('Loading...');
                    console.log("Sending request to generate noreg via Livewire");
                },
                success: function(response) {
                    console.log("Response received:", response);
                    
                    if (response.success) {
                        // Gunakan langsung nomor reg dari respons
                        let noReg = response.no_reg;
                        $('#no_reg').val(noReg);
                        showNotification('success', 'Nomor registrasi: ' + noReg);
                    } else {
                        $('#no_reg').val('001');
                        showNotification('warning', response.message || 'Gagal memperoleh nomor registrasi');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", status, error);
                    console.error("Response:", xhr.responseText);
                    
                    // Penanganan error yang lebih baik
                    let errorResponse = { message: 'Gagal mengambil nomor registrasi' };
                    
                    if (status === 'timeout') {
                        errorResponse.message = 'Koneksi timeout, nomor default digunakan';
                    }
                    
                    try {
                        if (xhr.responseJSON) {
                            errorResponse = xhr.responseJSON;
                        } else if (xhr.responseText) {
                            errorResponse = JSON.parse(xhr.responseText);
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                    
                    $('#no_reg').val('001');
                    showNotification('error', errorResponse.message || 'Gagal mengambil nomor registrasi: ' + error);
                },
                complete: function() {
                    $('#btnSimpan').prop('disabled', false)
                       .html('<i class="fas fa-save mr-2"></i>Simpan Registrasi');
                }
            });
        });

        // Penanganan submit form
        $('#formRegPeriksa').on('submit', function(e) {
            e.preventDefault();
            console.log("Form submitted");
            
            // Validasi form
            if (!$('#no_reg').val() || !$('#kd_dokter').val() || !$('#kd_poli').val() || !$('#kd_pj').val()) {
                showNotification('error', 'Semua field wajib diisi');
                return false;
            }
            
            // Ambil data form
            const formData = $(this).serialize();
            console.log("Form data:", formData);
            
            // Cek jika pasien menggunakan BPJS
            const kdPj = $('#kd_pj').val();
            const isBpjs = kdPj === 'A03' || kdPj === 'A14' || kdPj === 'A15' || kdPj === 'BPJ' || kdPj.toLowerCase().includes('bpjs');
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                timeout: 60000, // 60 detik timeout
                beforeSend: function() {
                    $('#btnSimpan').prop('disabled', true)
                        .html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');
                        
                    if (isBpjs) {
                        showNotification('info', 'Pendaftaran pasien BPJS sedang diproses, mohon tunggu...');
                    }
                },
                success: function(response) {
                    console.log("Response:", response);
                    
                    if (response.success) {
                        let successTitle = 'Berhasil!';
                        let successText = 'Registrasi berhasil disimpan dengan nomor: ' + response.no_rawat;
                        
                        // Tambahkan informasi khusus untuk pasien BPJS
                        if (isBpjs) {
                            successText += '<br><br><strong>Data pasien BPJS telah dikirim ke sistem Antrian BPJS.</strong><br>'+
                                           'Proses pengiriman data dilakukan di background.';
                            
                            // Pengiriman data ke BPJS sudah dilakukan di backend melalui metode kirimAntreanBPJS()
                            // Jadi tidak perlu melakukan pengiriman kedua di sini
                        }
                        
                        showNotification('success', response.message || 'Data berhasil disimpan');
                        
                        // Tampilkan alert sukses dengan SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: successTitle,
                            html: successText,
                            timer: 3000,
                            showConfirmButton: false
                        }).then(function() {
                            // Redirect ke halaman daftar pasien
                            window.location.href = '{{ route("pasien.index") }}';
                        });
                    } else {
                        showNotification('error', response.message || 'Gagal menyimpan data');
                        $('#btnSimpan').prop('disabled', false)
                            .html('<i class="fas fa-save mr-2"></i>Simpan Registrasi');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", status, error);
                    
                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                    
                    if (status === 'timeout') {
                        errorMessage = 'Koneksi timeout, silakan coba lagi';
                    }
                    
                    try {
                        if (xhr.responseJSON) {
                            errorMessage = xhr.responseJSON.message || errorMessage;
                        } else if (xhr.responseText) {
                            const errorResponse = JSON.parse(xhr.responseText);
                            errorMessage = errorResponse.message || errorMessage;
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                    
                    showNotification('error', errorMessage);
                    $('#btnSimpan').prop('disabled', false)
                        .html('<i class="fas fa-save mr-2"></i>Simpan Registrasi');
                }
            });
        });

        // Event change untuk poliklinik
        $('#kd_poli').on('change', function() {
            // Reset dokter ketika poli berubah
            $('#kd_dokter').val('').trigger('change.select2');
            $('#no_reg').val('');
        });
    });
</script>
@stop
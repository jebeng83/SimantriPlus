@extends('adminlte::page')

@section('title', 'KYC SATUSEHAT')

@section('css')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
   /* Improved typography */
   body {
      font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      color: #3a3a3a;
   }

   h5,
   h6 {
      letter-spacing: 0.3px;
   }

   /* Button styles */
   .btn-lg {
      padding: 12px 32px;
      font-size: 16px;
      border-radius: 6px;
      font-weight: 600;
      letter-spacing: 0.3px;
      transition: all 0.3s;
   }

   .btn-primary {
      background: linear-gradient(135deg, #2e5cb8 0%, #1a3c7e 100%);
      border: none;
   }

   .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 15px rgba(0, 85, 179, 0.3);
   }

   .btn-sm {
      font-weight: 600;
      border-radius: 4px;
      padding: 6px 12px;
      transition: all 0.2s;
   }

   .btn-sm:hover {
      transform: translateY(-1px);
   }

   /* Card styling */
   .card {
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.3s;
   }

   .card-header {
      border-bottom: 0;
      padding: 15px 20px;
   }

   /* Input fields */
   .form-control {
      border-radius: 6px;
      padding: 12px 15px;
      border: 1px solid #e0e0e0;
      transition: all 0.2s;
   }

   .form-control:focus {
      border-color: #4a89dc;
      box-shadow: 0 0 0 0.2rem rgba(74, 137, 220, 0.15);
   }

   .form-control-lg {
      height: auto;
      font-size: 16px;
   }

   label {
      margin-bottom: 0.5rem;
      font-size: 14px;
   }

   /* Gradient backgrounds */
   .bg-gradient-primary {
      background: linear-gradient(135deg, #2e5cb8 0%, #1a3c7e 100%);
   }

   .bg-gradient-info {
      background: linear-gradient(135deg, #17a2b8 0%, #0f7386 100%);
   }

   .bg-gradient-info-light {
      background: linear-gradient(135deg, #e6f7fa 0%, #d0f0f7 100%);
      border-left: 4px solid #17a2b8;
   }

   .bg-gradient-warning-light {
      background: linear-gradient(135deg, #fff8e6 0%, #fff2d0 100%);
      border-left: 4px solid #ffc107;
   }

   /* Alert styling */
   .alert {
      border-radius: 8px;
      padding: 15px 20px;
   }

   .border-left-success {
      border-left: 4px solid #28a745;
   }

   .border-left-danger {
      border-left: 4px solid #dc3545;
   }

   .border-left-primary {
      border-left: 4px solid #4e73df;
   }

   /* Help cards */
   .help-card {
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      transition: all 0.3s;
   }

   .help-card:hover {
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      transform: translateY(-2px);
   }

   /* Shadow effects */
   .shadow-lg {
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
   }

   /* Autocomplete styling */
   .ui-autocomplete {
      max-height: 300px;
      overflow-y: auto;
      overflow-x: hidden;
      padding: 10px 0;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      border: 1px solid #e0e0e0;
      background-color: #fff;
      z-index: 9999;
   }

   .ui-autocomplete .ui-menu-item {
      padding: 0;
      margin: 0;
   }

   .ui-autocomplete .ui-menu-item div {
      padding: 10px 15px;
      border-bottom: 1px solid #f0f0f0;
      cursor: pointer;
      transition: all 0.2s;
   }

   .ui-autocomplete .ui-menu-item:last-child div {
      border-bottom: none;
   }

   .ui-autocomplete .ui-menu-item div:hover,
   .ui-autocomplete .ui-menu-item div.ui-state-active {
      background-color: #f8f9fa;
   }

   .ui-helper-hidden-accessible {
      display: none;
   }

   /* Valid input styling */
   .form-control.is-valid {
      border-color: #28a745;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right calc(0.375em + 0.1875rem) center;
      background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
   }
</style>
@stop

@section('content')
<div class="container py-4">
   <div class="row justify-content-center">
      <div class="col-md-8">
         <div class="card shadow-lg border-0 mb-4">
            <div
               class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
               <h5 class="mb-0 font-weight-bold"><i class="fas fa-id-card mr-2"></i> KYC SATUSEHAT</h5>
               <div>
                  <a href="{{ route('kyc.status') }}" class="btn btn-sm btn-light text-primary mr-2 shadow-sm">
                     <i class="fas fa-user-check mr-1"></i> Status Verifikasi
                  </a>
                  <a href="{{ route('kyc.config') }}" class="btn btn-sm btn-outline-light shadow-sm">
                     <i class="fas fa-cog mr-1"></i> Konfigurasi
                  </a>
               </div>
            </div>
            <div class="card-body p-4">
               @if (session('success'))
               <div class="alert alert-success alert-dismissible fade show shadow-sm border-left-success" role="alert">
                  <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               @endif

               @if (session('error'))
               <div class="alert alert-danger alert-dismissible fade show shadow-sm border-left-danger" role="alert">
                  <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>

                  @if(session('error_details'))
                  <hr>
                  <div class="mt-2">
                     <button class="btn btn-sm btn-outline-danger" type="button" data-toggle="collapse"
                        data-target="#errorDetails" aria-expanded="false" aria-controls="errorDetails">
                        <i class="fas fa-info-circle"></i> Lihat Detail Error
                     </button>
                     <div class="collapse mt-2" id="errorDetails">
                        <div class="card card-body bg-light">
                           {!! nl2br(e(session('error_details'))) !!}
                        </div>
                     </div>
                  </div>
                  @endif
               </div>
               @endif

               <div class="alert alert-info mb-4 shadow-sm border-0 bg-gradient-info-light">
                  <h5 class="font-weight-bold text-info"><i class="fas fa-info-circle mr-2"></i> Tentang KYC SATUSEHAT
                  </h5>
                  <p class="mb-0 text-dark">KYC (Know Your Customer) SATUSEHAT adalah proses verifikasi identitas pasien
                     menggunakan aplikasi
                     SATUSEHAT Mobile. Proses ini memastikan bahwa data pasien yang digunakan adalah valid dan sesuai
                     dengan data Dukcapil.</p>
               </div>

               <form method="POST" action="{{ route('kyc.process') }}" class="mt-4" id="kycForm">
                  @csrf

                  <!-- Pencarian Pasien -->
                  <div class="card shadow-sm mb-4 border-left-primary">
                     <div class="card-body">
                        <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-search mr-2"></i> Cari Data
                           Pasien</h6>
                        <div class="form-group">
                           <div class="input-group">
                              <div class="input-group-prepend">
                                 <span class="input-group-text bg-primary text-white"><i
                                       class="fas fa-search"></i></span>
                              </div>
                              <input type="text" class="form-control form-control-lg" id="searchPatient"
                                 placeholder="Cari berdasarkan NIK atau nama pasien...">
                              <div class="input-group-append">
                                 <button type="button" class="btn btn-outline-secondary" id="clearSearch">
                                    <i class="fas fa-times"></i> Bersihkan
                                 </button>
                              </div>
                           </div>
                           <small class="form-text text-muted">Masukkan NIK atau nama pasien untuk mencari data</small>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="nik" class="font-weight-bold text-dark"><i
                                 class="fas fa-id-card mr-1 text-primary"></i> NIK <span
                                 class="text-danger">*</span></label>
                           <input type="text" class="form-control form-control-lg @error('nik') is-invalid @enderror"
                              id="nik" name="nik" value="{{ old('nik') }}" maxlength="16" required
                              placeholder="Masukkan 16 digit NIK">
                           @error('nik')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                           <small class="form-text text-muted">Masukkan 16 digit NIK pasien sesuai KTP</small>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="name" class="font-weight-bold text-dark"><i
                                 class="fas fa-user mr-1 text-primary"></i> Nama Lengkap
                              <span class="text-danger">*</span></label>
                           <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                              id="name" name="name" value="{{ old('name') }}" required
                              placeholder="Masukkan nama lengkap">
                           @error('name')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                           <small class="form-text text-muted">Masukkan nama lengkap sesuai KTP</small>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="dob" class="font-weight-bold text-dark"><i
                                 class="fas fa-calendar-alt mr-1 text-primary"></i> Tanggal
                              Lahir <span class="text-danger">*</span></label>
                           <input type="date" class="form-control form-control-lg @error('dob') is-invalid @enderror"
                              id="dob" name="dob" value="{{ old('dob') }}" required>
                           @error('dob')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <label for="gender" class="font-weight-bold text-dark"><i
                                 class="fas fa-venus-mars mr-1 text-primary"></i> Jenis
                              Kelamin <span class="text-danger">*</span></label>
                           <select class="form-control form-control-lg @error('gender') is-invalid @enderror"
                              id="gender" name="gender" required>
                              <option value="">-- Pilih Jenis Kelamin --</option>
                              <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Laki-laki</option>
                              <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Perempuan</option>
                           </select>
                           @error('gender')
                           <div class="invalid-feedback">{{ $message }}</div>
                           @enderror
                        </div>
                     </div>
                  </div>

                  <div class="form-group">
                     <label for="phone" class="font-weight-bold text-dark"><i
                           class="fas fa-phone mr-1 text-primary"></i> Nomor Telepon <span
                           class="text-danger">*</span></label>
                     <input type="text" class="form-control form-control-lg @error('phone') is-invalid @enderror"
                        id="phone" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 08123456789">
                     @error('phone')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                     <small class="form-text text-muted">Masukkan nomor telepon aktif pasien</small>
                  </div>

                  <div class="alert alert-warning shadow-sm mt-4 border-0 bg-gradient-warning-light">
                     <i class="fas fa-exclamation-triangle mr-2 text-warning"></i> <strong>Penting:</strong> Pastikan
                     data yang dimasukkan sesuai dengan data di KTP pasien untuk menghindari kegagalan verifikasi.
                  </div>

                  <div class="alert alert-info shadow-sm mt-3 border-0 bg-gradient-info-light">
                     <h6 class="font-weight-bold text-info"><i class="fas fa-info-circle mr-2"></i> Petunjuk Format
                        Nama:</h6>
                     <ul class="mb-0 pl-3">
                        <li>Gunakan nama lengkap persis seperti di KTP/KK</li>
                        <li>Jangan mengubah format nama (misal: ",AN" menjadi "ANAK")</li>
                        <li>Jika nama mengandung akhiran seperti ",AN" (anak) atau "NY" (nyonya), pastikan ditulis sama
                           persis</li>
                        <li>Sistem akan mencoba beberapa variasi nama, tetapi format yang tepat akan meningkatkan
                           keberhasilan</li>
                     </ul>
                  </div>

                  <div class="form-group text-center mt-5">
                     <button type="submit" class="btn btn-primary btn-lg px-5 shadow-lg">
                        <i class="fas fa-check-circle mr-2"></i> Verifikasi Pasien
                     </button>
                  </div>
               </form>
            </div>
         </div>

         <div class="card shadow-lg border-0 mt-4">
            <div class="card-header bg-gradient-info text-white py-3">
               <h5 class="mb-0 font-weight-bold"><i class="fas fa-question-circle mr-2"></i> Bantuan</h5>
            </div>
            <div class="card-body p-4">
               <div class="row">
                  <div class="col-md-6">
                     <div class="help-card p-3 bg-light rounded mb-3 mb-md-0 h-100">
                        <h6 class="font-weight-bold text-info"><i class="fas fa-check mr-2"></i> Cara Verifikasi:</h6>
                        <ol class="pl-3 mb-0">
                           <li class="mb-2">Isi semua data pasien dengan lengkap dan benar</li>
                           <li class="mb-2">Klik tombol "Verifikasi Pasien"</li>
                           <li class="mb-2">Pasien akan menerima kode verifikasi di aplikasi SATUSEHAT Mobile</li>
                           <li>Masukkan kode verifikasi untuk menyelesaikan proses</li>
                        </ol>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="help-card p-3 bg-light rounded h-100">
                        <h6 class="font-weight-bold text-danger"><i class="fas fa-times-circle mr-2"></i> Penyebab Gagal
                           Verifikasi:</h6>
                        <ul class="pl-3 mb-0">
                           <li class="mb-2">Data tidak sesuai dengan data di Dukcapil</li>
                           <li class="mb-2">NIK tidak valid atau tidak terdaftar</li>
                           <li class="mb-2">Pasien belum menginstall aplikasi SATUSEHAT Mobile</li>
                           <li>Koneksi internet tidak stabil</li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
   $(document).ready(function() {
      console.log('Document ready - Initializing KYC form');
      
      // Format NIK input to only accept numbers
      $('#nik').on('input', function() {
         $(this).val($(this).val().replace(/[^0-9]/g, ''));
      });
      
      // Format phone input to only accept numbers
      $('#phone').on('input', function() {
         $(this).val($(this).val().replace(/[^0-9]/g, ''));
      });
      
      // Add floating label effect
      $('.form-control').on('focus blur', function (e) {
         $(this).parents('.form-group').toggleClass('focused', (e.type === 'focus'));
      }).trigger('blur');
      
      // Smooth scrolling for anchor links
      $('a[href*="#"]').on('click', function(e) {
         e.preventDefault();
         $('html, body').animate({
            scrollTop: $($(this).attr('href')).offset().top - 100
         }, 500, 'linear');
      });
      
      // Debug: Log jQuery and jQuery UI versions
      console.log('jQuery version:', $.fn.jquery);
      console.log('jQuery UI version:', $.ui ? $.ui.version : 'not loaded');
      
      // Autosearching pasien
      $('#searchPatient').autocomplete({
         source: function(request, response) {
            console.log('Searching for:', request.term);
            $.ajax({
               url: "{{ route('kyc.search-patient') }}",
               method: "GET",
               dataType: "json",
               data: {
                  search: request.term
               },
               success: function(data) {
                  console.log('Search response:', data);
                  if (data.success) {
                     response($.map(data.data, function(item) {
                        return {
                           label: item.label,
                           value: item.label,
                           data: item
                        };
                     }));
                  } else {
                     // Jika tidak ada hasil, tampilkan pesan
                     var result = [
                        {
                           label: 'Tidak ada data yang ditemukan',
                           value: '',
                           data: null
                        }
                     ];
                     response(result);
                  }
               },
               error: function(xhr, status, error) {
                  console.error('Error searching patient:', error);
                  console.error('Response:', xhr.responseText);
                  response([{
                     label: 'Terjadi kesalahan saat mencari data',
                     value: '',
                     data: null
                  }]);
               }
            });
         },
         minLength: 3,
         select: function(event, ui) {
            console.log('Selected item:', ui.item);
            if (ui.item.data) {
               // Isi form dengan data pasien
               $('#nik').val(ui.item.data.nik);
               $('#name').val(ui.item.data.name);
               $('#dob').val(ui.item.data.dob);
               $('#gender').val(ui.item.data.gender);
               $('#phone').val(ui.item.data.phone);
               
               // Highlight form yang terisi
               $('.form-control').each(function() {
                  if ($(this).val()) {
                     $(this).addClass('is-valid');
                  }
               });
               
               // Tampilkan notifikasi
               showNotification('success', 'Data pasien berhasil ditemukan dan diisi otomatis.');
            }
            return false;
         }
      }).autocomplete("instance")._renderItem = function(ul, item) {
         // Custom rendering untuk hasil pencarian
         if (!item.data) {
            return $("<li>")
               .append("<div class='text-danger'>" + item.label + "</div>")
               .appendTo(ul);
         }
         
         return $("<li>")
            .append("<div><strong>" + item.data.nik + "</strong> - " + item.data.name + 
                    "<br><small class='text-muted'>Tgl Lahir: " + item.data.dob + " | " + 
                    (item.data.gender === 'male' ? 'Laki-laki' : 'Perempuan') + "</small></div>")
            .appendTo(ul);
      };
      
      // Clear search dan reset form
      $('#clearSearch').click(function() {
         $('#searchPatient').val('');
         resetForm();
      });
      
      // Fungsi untuk reset form
      function resetForm() {
         $('#kycForm')[0].reset();
         $('.form-control').removeClass('is-valid');
      }
      
      // Fungsi untuk menampilkan notifikasi
      function showNotification(type, message) {
         var alertClass = type === 'success' ? 'alert-success border-left-success' : 'alert-danger border-left-danger';
         var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
         
         var alert = '<div class="alert ' + alertClass + ' alert-dismissible fade show shadow-sm" role="alert">' +
                     '<i class="fas ' + icon + ' mr-1"></i> ' + message +
                     '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                     '<span aria-hidden="true">&times;</span>' +
                     '</button>' +
                     '</div>';
         
         // Tambahkan notifikasi di atas form
         $('#kycForm').before(alert);
         
         // Otomatis hilangkan notifikasi setelah 5 detik
         setTimeout(function() {
            $('.alert').fadeOut(500, function() {
               $(this).remove();
            });
         }, 5000);
      }
   });
</script>
@stop
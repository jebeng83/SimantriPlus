@extends('adminlte::page')

@section('title', 'Pendaftaran Mobile JKN')

@section('content_header')
<div class="container-fluid">
   <div class="row mb-2">
      <div class="col-sm-6">
         <h1><i class="fas fa-mobile-alt text-primary"></i> Pendaftaran Mobile JKN</h1>
      </div>
      <div class="col-sm-6">
         <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Pendaftaran Mobile JKN</li>
         </ol>
      </div>
   </div>
</div>
@stop

@section('content')
<div class="container-fluid">
   <div class="row">
      <div class="col-lg-8 col-md-12 mx-auto">
         <!-- Phone Frame Container -->
         <div class="phone-container">
            <div class="phone-frame">
               <div class="phone-header">
                  <div class="phone-notch"></div>
               </div>
               <div class="phone-screen">
                  <!-- App Header -->
                  <div class="app-header">
                     <div class="row align-items-center">
                        <div class="col-12 text-center">
                           <img src="{{ asset('img/bpjs-logo.png') }}" alt="BPJS Logo" class="app-logo">
                           <h4 class="app-title mb-0">Mobile JKN</h4>
                        </div>
                     </div>
                  </div>

                  <!-- App Content -->
                  <div class="app-content mt-3">
                     <!-- Card Pendaftaran -->
                     <div class="mobile-card">
                        <div class="mobile-card-header">
                           <h5><i class="fas fa-user-plus"></i> Pendaftaran Antrean</h5>
                        </div>
                        <div class="mobile-card-body">
                           <!-- Form Pendaftaran -->
                           <form id="form-pendaftaran">
                              <meta name="csrf-token" content="{{ csrf_token() }}">
                              <div class="mobile-form-group">
                                 <label for="nomorkartu">Nomor Kartu BPJS</label>
                                 <div class="input-group">
                                    <input type="text" class="form-control mobile-input" id="nomorkartu"
                                       name="nomorkartu" placeholder="Masukkan nomor kartu BPJS">
                                    <div class="input-group-append">
                                       <button type="button" class="btn btn-primary mobile-btn" id="btn-cek-peserta">
                                          <i class="fas fa-search"></i>
                                       </button>
                                    </div>
                                 </div>
                              </div>

                              <div id="detail-peserta" style="display: none;">
                                 <div class="mobile-section-divider">
                                    <span>Data Peserta</span>
                                 </div>

                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="mobile-form-group">
                                          <label for="nik">NIK</label>
                                          <input type="text" class="form-control mobile-input" id="nik" name="nik"
                                             readonly>
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="mobile-form-group">
                                          <label for="nama">Nama</label>
                                          <input type="text" class="form-control mobile-input" id="nama" name="nama"
                                             readonly>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="mobile-form-group">
                                          <label for="nohp">Nomor HP</label>
                                          <input type="text" class="form-control mobile-input" id="nohp" name="nohp"
                                             placeholder="Masukkan nomor HP">
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="mobile-form-group">
                                          <label for="faskes">Faskes Tingkat Pertama</label>
                                          <input type="text" class="form-control mobile-input" id="faskes" name="faskes"
                                             readonly>
                                       </div>
                                    </div>
                                 </div>

                                 <div class="mobile-section-divider">
                                    <span>Detail Kunjungan</span>
                                 </div>

                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="mobile-form-group">
                                          <label for="kodepoli"><i class="fas fa-hospital"></i> Pilih Poli</label>
                                          <select class="form-control mobile-select" id="kodepoli" name="kodepoli">
                                             <option value="">-- Pilih Poli --</option>
                                          </select>
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="mobile-form-group">
                                          <label for="tanggalperiksa"><i class="fas fa-calendar-alt"></i> Tanggal
                                             Kunjungan</label>
                                          <div class="input-group date" id="tanggal-picker" data-target-input="nearest">
                                             <input type="text" class="form-control mobile-input datetimepicker-input"
                                                id="tanggalperiksa" name="tanggalperiksa" data-target="#tanggal-picker"
                                                placeholder="Pilih tanggal">
                                             <div class="input-group-append" data-target="#tanggal-picker"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="mobile-form-group">
                                    <label for="kodedokter"><i class="fas fa-user-md"></i> Pilih Jadwal Dokter</label>
                                    <select class="form-control mobile-select" id="kodedokter" name="kodedokter">
                                       <option value="">-- Pilih Jadwal Dokter --</option>
                                    </select>
                                 </div>
                                 <div class="mobile-form-group">
                                    <label for="keluhan"><i class="fas fa-notes-medical"></i> Keluhan</label>
                                    <textarea class="form-control mobile-textarea" id="keluhan" name="keluhan" rows="3"
                                       placeholder="Silakan isi keluhan Anda..."></textarea>
                                 </div>
                                 <div class="mobile-form-group text-center">
                                    <button type="button" class="btn btn-primary btn-lg mobile-btn-lg" id="btn-daftar">
                                       <i class="fas fa-paper-plane"></i> Daftar Sekarang
                                    </button>
                                 </div>
                              </div>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="phone-home-button"></div>
            </div>
         </div>
      </div>
   </div>

   <!-- Modal Detail Antrean -->
   <div class="modal fade" id="modal-detail-antrean" tabindex="-1" role="dialog"
      aria-labelledby="modal-detail-antrean-title" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
         <div class="modal-content mobile-modal">
            <div class="modal-header bg-primary">
               <h5 class="modal-title" id="modal-detail-antrean-title"><i class="fas fa-ticket-alt"></i> Detail Antrean
               </h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="text-center mb-4">
                  <button type="button" class="btn btn-info mobile-btn" id="btn-muat-ulang">
                     <i class="fas fa-sync-alt"></i> Perbarui Status
                  </button>
               </div>

               <div class="detail-ticket">
                  <div class="detail-ticket-header">
                     <div class="row">
                        <div class="col-6">
                           <h6 class="mb-0"><i class="fas fa-hospital"></i> Poli</h6>
                           <p id="detail-poli" class="detail-value"></p>
                        </div>
                        <div class="col-6">
                           <h6 class="mb-0"><i class="fas fa-calendar-day"></i> Tanggal</h6>
                           <p id="detail-tanggal" class="detail-value"></p>
                        </div>
                     </div>
                     <div class="row mt-2">
                        <div class="col-6">
                           <h6 class="mb-0"><i class="fas fa-comment-medical"></i> Keluhan</h6>
                           <p id="detail-keluhan" class="detail-value"></p>
                        </div>
                        <div class="col-6">
                           <h6 class="mb-0"><i class="fas fa-user-md"></i> Dokter</h6>
                           <p id="detail-dokter" class="detail-value"></p>
                        </div>
                     </div>
                  </div>

                  <div class="ticket-number-container">
                     <div class="ticket-number-label">Nomor Antrean Anda</div>
                     <div class="ticket-number" id="nomor-antrean">007</div>
                     <div class="ticket-barcode">
                        <i class="fas fa-barcode"></i>
                     </div>
                  </div>

                  <div class="ticket-status">
                     <div class="row">
                        <div class="col-4 text-center">
                           <div class="status-value" id="sisa-antrean">7</div>
                           <div class="status-label">Sisa Antrean</div>
                        </div>
                        <div class="col-4 text-center">
                           <div class="status-value" id="peserta-dilayani">0</div>
                           <div class="status-label">Dilayani</div>
                        </div>
                        <div class="col-4 text-center">
                           <div class="status-label">Estimasi</div>
                           <div class="status-value" id="estimasi-waktu">-</div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="row mt-4">
                  <div class="col-md-12 text-center">
                     <button type="button" class="btn btn-danger btn-lg mobile-btn-lg" id="btn-batalkan">
                        <i class="fas fa-times"></i> Batalkan Antrean
                     </button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
<style>
   /* Mobile Phone Frame Styling */
   .phone-container {
      max-width: 100%;
      margin: 20px auto;
      perspective: 1000px;
   }

   .phone-frame {
      position: relative;
      width: 100%;
      max-width: 380px;
      margin: 0 auto;
      height: 800px;
      background: #151515;
      border-radius: 40px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
      padding: 15px;
      overflow: visible;
      transform-style: preserve-3d;
      transform: rotateY(0deg) rotateX(3deg);
      transition: all 0.3s ease;
   }

   .phone-frame:hover {
      transform: rotateY(0deg) rotateX(0deg);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
   }

   .phone-header {
      position: relative;
      height: 30px;
      background: #151515;
      border-top-left-radius: 30px;
      border-top-right-radius: 30px;
   }

   .phone-notch {
      position: absolute;
      top: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 150px;
      height: 25px;
      background: #151515;
      border-bottom-left-radius: 15px;
      border-bottom-right-radius: 15px;
      z-index: 2;
   }

   .phone-screen {
      background: #f8f9fa;
      height: calc(100% - 60px);
      border-radius: 30px;
      overflow-y: auto;
      position: relative;
      padding: 15px;
      z-index: 5;
   }

   .phone-home-button {
      position: absolute;
      bottom: 7px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 5px;
      background: #444;
      border-radius: 5px;
   }

   /* App Styling */
   .app-header {
      background: linear-gradient(135deg, #0d47a1, #2196f3);
      color: white;
      padding: 15px 10px;
      border-radius: 15px 15px 0 0;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
   }

   .app-logo {
      height: 40px;
      margin-bottom: 5px;
   }

   .app-title {
      font-weight: 600;
      color: white;
      margin-bottom: 0;
   }

   .app-content {
      padding-bottom: 20px;
   }

   /* Mobile Card Styling */
   .mobile-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      margin-bottom: 20px;
      overflow: hidden;
      position: relative;
      z-index: 10;
   }

   .mobile-card-header {
      background: #f5f7fa;
      padding: 15px;
      border-bottom: 1px solid #eaedf3;
   }

   .mobile-card-header h5 {
      margin: 0;
      font-weight: 600;
      color: #444;
   }

   .mobile-card-body {
      padding: 20px 15px;
   }

   /* Form Styling */
   .mobile-form-group {
      margin-bottom: 20px;
      position: relative;
   }

   .mobile-form-group label {
      display: block;
      font-weight: 500;
      margin-bottom: 8px;
      color: #444;
      font-size: 14px;
   }

   .mobile-input,
   .mobile-select,
   .mobile-textarea {
      border-radius: 10px;
      border: 1px solid #dde2e8;
      padding: 12px 15px;
      width: 100%;
      transition: all 0.3s;
      background-color: #f8fafc;
   }

   .mobile-input:focus,
   .mobile-select:focus,
   .mobile-textarea:focus {
      border-color: #2196f3;
      box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
      background-color: #fff;
   }

   .mobile-input[readonly] {
      background-color: #f5f7fa;
      color: #5a6169;
      cursor: not-allowed;
   }

   .mobile-select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23444' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 15px center;
      background-size: 16px;
      position: relative;
      z-index: 20;
   }

   .mobile-btn {
      border-radius: 10px;
      padding: 10px 20px;
      font-weight: 500;
      transition: all 0.2s;
   }

   .mobile-btn-lg {
      border-radius: 12px;
      padding: 12px 30px;
      font-weight: 600;
      font-size: 16px;
      letter-spacing: 0.5px;
   }

   .mobile-btn:hover,
   .mobile-btn-lg:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
   }

   .mobile-section-divider {
      position: relative;
      text-align: center;
      margin: 25px 0;
   }

   .mobile-section-divider::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 1px;
      background: #eaedf3;
   }

   .mobile-section-divider span {
      position: relative;
      background: white;
      padding: 0 15px;
      font-size: 14px;
      font-weight: 600;
      color: #5a6169;
   }

   /* Modal Styling */
   .mobile-modal {
      border-radius: 20px;
      overflow: hidden;
   }

   .mobile-modal .modal-header {
      background: linear-gradient(135deg, #0d47a1, #2196f3);
      color: white;
      border-bottom: none;
   }

   .mobile-modal .modal-title {
      font-weight: 600;
   }

   .mobile-modal .close {
      color: white;
      text-shadow: none;
      opacity: 0.8;
   }

   /* Ticket Styling */
   .detail-ticket {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
   }

   .detail-ticket-header {
      padding: 15px;
      background: #f5f7fa;
      border-bottom: 1px dashed #dde2e8;
   }

   .detail-ticket-header h6 {
      font-weight: 600;
      color: #444;
      font-size: 14px;
   }

   .detail-value {
      margin-bottom: 0;
      color: #5a6169;
      font-weight: 500;
   }

   .ticket-number-container {
      padding: 25px 15px;
      text-align: center;
      background: linear-gradient(135deg, #0d47a1, #2196f3);
      color: white;
   }

   .ticket-number-label {
      font-size: 14px;
      margin-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
      opacity: 0.8;
   }

   .ticket-number {
      font-size: 64px;
      font-weight: 700;
      line-height: 1;
      margin-bottom: 10px;
      text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
   }

   .ticket-barcode {
      font-size: 28px;
      opacity: 0.5;
   }

   .ticket-status {
      padding: 15px;
      background: white;
      border-top: 1px dashed #dde2e8;
   }

   .status-label {
      font-size: 12px;
      color: #5a6169;
      margin-top: 5px;
   }

   .status-value {
      font-size: 24px;
      font-weight: 700;
      color: #0d47a1;
   }

   /* Responsive fixes */
   @media (max-width: 576px) {
      .phone-frame {
         transform: none;
         box-shadow: none;
         background: transparent;
         border-radius: 0;
         height: auto;
         padding: 0;
      }

      .phone-header,
      .phone-home-button {
         display: none;
      }

      .phone-screen {
         height: auto;
         border-radius: 0;
      }
   }

   /* Memperbaiki tampilan dropdown */
   .mobile-select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23444' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 15px center;
      background-size: 16px;
      position: relative;
      z-index: 20;
   }

   /* Fix untuk dropdown yang terpotong */
   .phone-screen {
      background: #f8f9fa;
      height: calc(100% - 60px);
      border-radius: 30px;
      overflow-y: auto;
      position: relative;
      padding: 15px;
      z-index: 5;
   }

   /* Pastikan dropdown menu muncul di atas */
   .dropdown-menu {
      z-index: 9999;
   }
</style>
@stop

@section('js')
<script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
<script src="{{ asset('vendor/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<script>
   $(document).ready(function () {
      // Setup untuk AJAX
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });
      
      // Setup DateTimePicker
      $('#tanggal-picker').datetimepicker({
         format: 'YYYY-MM-DD',
         minDate: moment().startOf('day'),
         maxDate: moment().add(7, 'days').endOf('day'),
         defaultDate: moment()
      });

      // Format nomor kartu BPJS
      $('#nomorkartu').on('input', function() {
         var value = $(this).val().replace(/\D/g, ''); // Hapus semua non-digit
         if (value.length > 13) {
            value = value.substring(0, 13); // Batasi hingga 13 digit
         }
         $(this).val(value);
      });

      // Load Poli saat tanggal berubah
      $('#tanggalperiksa').on('change', function() {
         loadPoli();
      });

      // Load Dokter saat poli berubah
      $('#kodepoli').on('change', function() {
         loadDokter();
      });

      // Default load poli dengan tanggal hari ini
      setTimeout(function() {
         loadPoli();
      }, 500);

      // Cek peserta saat tombol cek diklik
      $('#btn-cek-peserta').on('click', function() {
         checkPeserta();
      });

      // Tombol daftar
      $('#btn-daftar').on('click', function() {
         daftarAntrean();
      });

      // Tombol muat ulang di modal
      $('#btn-muat-ulang').on('click', function() {
         refreshAntrean();
      });

      // Memuat data poli pada saat halaman dimuat dan tanggal berubah
      function loadPoli() {
         var tanggal = $('#tanggalperiksa').val();
         
         if (!tanggal) {
            tanggal = moment().format('YYYY-MM-DD');
         }

         $.ajax({
            url: '/pendaftaran-mobile-jkn/get-poli?tanggal=' + tanggal,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
               $('#kodepoli').empty().append('<option value="">Sedang memuat...</option>');
            },
            success: function(data) {
               $('#kodepoli').empty().append('<option value="">-- Pilih Poli --</option>');

               if (data.metadata && data.metadata.code === 200 && data.response) {
                  $.each(data.response, function(i, item) {
                     $('#kodepoli').append('<option value="' + item.kodepoli + '">' + item.namapoli + '</option>');
                  });
               } else {
                  // Handle error response format
                  var message = (data.metadata && data.metadata.message) ? data.metadata.message : 'Gagal memuat data poli';
                  toastr.error(message);
                  console.error('Error loading poli:', data);
               }
            },
            error: function(xhr, status, error) {
               $('#kodepoli').empty().append('<option value="">-- Pilih Poli --</option>');
               toastr.error('Error loading poli: ' + error);
               console.error('Error loading poli:', error, xhr.responseText);
            }
         });
      }

      // Fungsi untuk memuat daftar dokter berdasarkan poli dan tanggal
      function loadDokter() {
         var kodePoli = $('#kodepoli').val();
         var tanggal = $('#tanggalperiksa').val();

         if (!kodePoli) {
            $('#kodedokter').empty().append('<option value="">-- Pilih Jadwal Dokter --</option>');
            return;
         }

         if (!tanggal) {
            tanggal = moment().format('YYYY-MM-DD');
         }

         $.ajax({
            url: '/pendaftaran-mobile-jkn/get-dokter?kodePoli=' + kodePoli + '&tanggal=' + tanggal,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
               $('#kodedokter').empty().append('<option value="">Sedang memuat...</option>');
            },
            success: function(data) {
               $('#kodedokter').empty().append('<option value="">-- Pilih Jadwal Dokter --</option>');

               if (data.metadata && data.metadata.code === 200 && data.response) {
                  $.each(data.response, function(i, item) {
                     $('#kodedokter').append('<option value="' + item.kodedokter + '" data-jampraktek="' + item.jampraktek + '">' + 
                        item.namadokter + ' (' + item.jampraktek + ') - Sisa Kuota: ' + item.kapasitas + '</option>');
                  });
               } else {
                  // Handle error response format
                  var message = (data.metadata && data.metadata.message) ? data.metadata.message : 'Gagal memuat data dokter';
                  toastr.error(message);
                  console.error('Error loading dokter:', data);
               }
            },
            error: function(xhr, status, error) {
               $('#kodedokter').empty().append('<option value="">-- Pilih Jadwal Dokter --</option>');
               toastr.error('Error loading dokter: ' + error);
               console.error('Error loading dokter:', error, xhr.responseText);
            }
         });
      }

      // Fungsi untuk memeriksa peserta BPJS
      function checkPeserta() {
         var nomorKartu = $('#nomorkartu').val();
         if (!nomorKartu) {
            Swal.fire({
               title: 'Peringatan',
               text: 'Nomor kartu BPJS harus diisi!',
               icon: 'warning'
            });
            return;
         }

         // Format nomor kartu - hapus karakter non-numerik
         nomorKartu = nomorKartu.replace(/[^0-9]/g, '');

         // Pastikan panjang 13 digit dengan leading zero jika kurang
         if (nomorKartu.length < 13) {
            nomorKartu = nomorKartu.padStart(13, '0');
         } else if (nomorKartu.length > 13) {
            nomorKartu = nomorKartu.substring(nomorKartu.length - 13);
         }
         
         // Update nilai input dengan format yang benar
         $('#nomorkartu').val(nomorKartu);

         $.ajax({
            url: '{{ route("mobile-jkn.get-peserta") }}',
            type: 'GET',
            data: {
               nomorKartu: nomorKartu
            },
            beforeSend: function() {
               Swal.fire({
                  title: 'Mohon Tunggu',
                  text: 'Sedang memeriksa data peserta...',
                  allowOutsideClick: false,
                  didOpen: () => {
                     Swal.showLoading();
                  }
               });
            },
            success: function(response) {
               Swal.close();
               if (response.metadata && response.metadata.code === 200) {
                  const data = response.response;
                  $('#nik').val(data.nik);
                  $('#nama').val(data.nama);
                  $('#nohp').val(data.nohp || '');
                  $('#faskes').val(data.faskes);
                  $('#detail-peserta').show();
               } else {
                  const message = response.metadata ? response.metadata.message : 'Terjadi kesalahan saat memeriksa data peserta';
                  Swal.fire({
                     title: 'Gagal',
                     text: message,
                     icon: 'error'
                  });
               }
            },
            error: function(xhr) {
               Swal.close();
               Swal.fire({
                  title: 'Gagal',
                  text: 'Terjadi kesalahan saat memeriksa data peserta',
                  icon: 'error'
               });
            }
         });
      }

      // Fungsi untuk mendaftar antrean baru
      function daftarAntrean() {
         // Validasi form
         var nomorKartu = $('#nomorkartu').val();
         const nik = $('#nik').val();
         var noHp = $('#nohp').val();
         const kodePoli = $('#kodepoli').val();
         const tanggalPeriksa = $('#tanggalperiksa').val();
         const kodeDokter = $('#kodedokter').val();
         const keluhan = $('#keluhan').val();
         
         if (!nomorKartu || !nik || !noHp || !kodePoli || !tanggalPeriksa || !kodeDokter) {
            Swal.fire({
               title: 'Peringatan',
               text: 'Semua field harus diisi!',
               icon: 'warning'
            });
            return;
         }
         
         // Format nomor kartu BPJS
         nomorKartu = nomorKartu.replace(/[^0-9]/g, '');
         if (nomorKartu.length < 13) {
            nomorKartu = nomorKartu.padStart(13, '0');
         } else if (nomorKartu.length > 13) {
            nomorKartu = nomorKartu.substring(nomorKartu.length - 13);
         }
         
         // Format nomor HP (hapus spasi)
         noHp = noHp.replace(/\s+/g, '');
         
         // Ambil data dokter dari option yang terpilih
         const selectedOption = $('#kodedokter option:selected');
         const namaPoli = $('#kodepoli option:selected').text();
         const jamPraktek = selectedOption.data('jampraktek') || '';
         const namaDokter = selectedOption.text().split(' (')[0];
         
         // Kirim data ke API
         $.ajax({
            url: '{{ route("mobile-jkn.daftar-antrean") }}',
            type: 'POST',
            data: {
               nomorkartu: nomorKartu,
               nik: nik,
               nohp: noHp,
               kodepoli: kodePoli,
               namapoli: namaPoli,
               tanggalperiksa: tanggalPeriksa,
               kodedokter: kodeDokter,
               namadokter: namaDokter,
               jampraktek: jamPraktek,
               keterangan: keluhan
            },
            beforeSend: function() {
               Swal.fire({
                  title: 'Mohon Tunggu',
                  text: 'Sedang mendaftarkan antrean...',
                  allowOutsideClick: false,
                  didOpen: () => {
                     Swal.showLoading();
                  }
               });
            },
            success: function(response) {
               Swal.close();
               if (response.metadata && response.metadata.code === 200) {
                  const data = response.response;
                  
                  // Tampilkan detail antrean di modal
                  $('#detail-poli').text(namaPoli);
                  $('#detail-tanggal').text(tanggalPeriksa);
                  $('#detail-keluhan').text(keluhan || '-');
                  $('#detail-dokter').text(namaDokter + ' (' + jamPraktek + ')');
                  $('#nomor-antrean').text(data.nomorantrean || '-');
                  $('#sisa-antrean').text(data.sisaantrean || '0');
                  $('#peserta-dilayani').text(data.dilayani || '0');
                  $('#estimasi-waktu').text(data.estimasidilayani ? data.estimasidilayani + ' menit' : '-');
                  
                  // Simpan data antrean untuk keperluan pembatalan
                  $('#btn-batalkan').data('antrean', {
                     nomorkartu: nomorKartu,
                     tanggalperiksa: tanggalPeriksa,
                     kodepoli: kodePoli
                  });
                  
                  // Tampilkan modal detail antrean
                  $('#modal-detail-antrean').modal('show');
               } else {
                  const message = response.metadata ? response.metadata.message : 'Terjadi kesalahan saat mendaftarkan antrean';
                  Swal.fire({
                     title: 'Gagal',
                     text: message,
                     icon: 'error'
                  });
               }
            },
            error: function(xhr) {
               Swal.close();
               let errorMsg = 'Terjadi kesalahan saat mendaftarkan antrean';
               
               if (xhr.responseJSON && xhr.responseJSON.metadata) {
                  errorMsg = xhr.responseJSON.metadata.message;
               }
               
               Swal.fire({
                  title: 'Gagal',
                  text: errorMsg,
                  icon: 'error'
               });
            }
         });
      }

      // Fungsi untuk refresh antrean
      function refreshAntrean() {
         const antreanData = $('#btn-batalkan').data('antrean');
         if (!antreanData) {
            toastr.error('Data antrean tidak ditemukan');
            return;
         }
         
         const kodePoli = antreanData.kodepoli;
         const tanggalPeriksa = antreanData.tanggalperiksa;
         
         $.ajax({
            url: '{{ route("mobile-jkn.get-status-antrean") }}',
            type: 'GET',
            data: {
               kodePoli: kodePoli,
               tanggalPeriksa: tanggalPeriksa
            },
            beforeSend: function() {
               $('#btn-muat-ulang').prop('disabled', true);
               $('#btn-muat-ulang').html('<i class="fas fa-spinner fa-spin"></i> Memuat...');
            },
            success: function(response) {
               $('#btn-muat-ulang').prop('disabled', false);
               $('#btn-muat-ulang').html('<i class="fas fa-sync-alt"></i> Muat ulang');

               if (response.metadata && response.metadata.code === 200) {
                  const data = response.response;
                  
                  // Update informasi antrean
                  $('#sisa-antrean').text(data.sisaantrean || '0');
                  $('#peserta-dilayani').text(data.antreanpanggil || '0');
                  $('#estimasi-waktu').text(data.waktutunggu ? data.waktutunggu + ' menit' : '-');
               } else {
                  const message = response.metadata ? response.metadata.message : 'Gagal memuat status antrean';
                  toastr.error(message);
               }
            },
            error: function(xhr) {
               $('#btn-muat-ulang').prop('disabled', false);
               $('#btn-muat-ulang').html('<i class="fas fa-sync-alt"></i> Muat ulang');
               
               toastr.error('Gagal memuat status antrean');
            }
         });
      }

      // Event klik tombol batal
      $('#btn-batalkan').on('click', function() {
         let antreanData = $(this).data('antrean');
         
         if (!antreanData) {
            Swal.fire({
               title: 'Peringatan',
               text: 'Data antrean tidak ditemukan',
               icon: 'warning'
            });
            return;
         }
         
         // Format nomor kartu BPJS
         let nomorKartu = antreanData.nomorkartu.replace(/[^0-9]/g, '');
         if (nomorKartu.length < 13) {
            nomorKartu = nomorKartu.padStart(13, '0');
         } else if (nomorKartu.length > 13) {
            nomorKartu = nomorKartu.substring(nomorKartu.length - 13);
         }
         
         // Update data antrean dengan nomor kartu yang sudah diformat
         antreanData.nomorkartu = nomorKartu;
         
         Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin membatalkan antrean ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak'
         }).then((result) => {
            if (result.isConfirmed) {
               $.ajax({
                  url: '{{ route("mobile-jkn.batal-antrean") }}',
                  type: 'POST',
                  data: {
                     nomorkartu: antreanData.nomorkartu,
                     tanggalperiksa: antreanData.tanggalperiksa,
                     kodepoli: antreanData.kodepoli
                  },
                  beforeSend: function() {
                     Swal.fire({
                        title: 'Mohon Tunggu',
                        text: 'Sedang membatalkan antrean...',
                        allowOutsideClick: false,
                        didOpen: () => {
                           Swal.showLoading();
                        }
                     });
                  },
                  success: function(response) {
                     Swal.close();
                     if (response.metadata && response.metadata.code === 200) {
                        Swal.fire({
                           title: 'Berhasil',
                           text: 'Antrean berhasil dibatalkan',
                           icon: 'success'
                        }).then(() => {
                           $('#modal-detail-antrean').modal('hide');
                           // Reset form
                           $('#form-pendaftaran')[0].reset();
                           $('#detail-peserta').hide();
                        });
                     } else {
                        const message = response.metadata ? response.metadata.message : 'Terjadi kesalahan saat membatalkan antrean';
                        Swal.fire({
                           title: 'Gagal',
                           text: message,
                           icon: 'error'
                        });
                     }
                  },
                  error: function(xhr) {
                     Swal.close();
                     let errorMsg = 'Terjadi kesalahan saat membatalkan antrean';
                     
                     if (xhr.responseJSON && xhr.responseJSON.metadata) {
                        errorMsg = xhr.responseJSON.metadata.message;
                     }
                     
                     Swal.fire({
                        title: 'Gagal',
                        text: errorMsg,
                        icon: 'error'
                     });
                  }
               });
            }
         });
      });
   });
</script>
@stop
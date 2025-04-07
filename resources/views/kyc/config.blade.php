@extends('adminlte::page')

@section('title', 'Konfigurasi SATUSEHAT')

@section('content')
<div class="container py-4">
   <div class="row justify-content-center">
      <div class="col-md-10">
         <div class="card shadow-lg border-0 mb-4">
            <div
               class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
               <h5 class="mb-0 font-weight-bold"><i class="fas fa-cogs mr-2"></i> Status Konfigurasi SATUSEHAT</h5>
               <a href="{{ route('kyc.index') }}" class="btn btn-sm btn-light">
                  <i class="fas fa-arrow-left mr-1"></i> Kembali
               </a>
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
               </div>
               @endif

               <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                     <tbody>
                        <tr>
                           <th style="width: 30%" class="bg-light">Environment</th>
                           <td>
                              <span
                                 class="badge badge-{{ strpos($config['api_url'], 'stg') !== false ? 'warning' : 'danger' }}">
                                 {{ strpos($config['api_url'], 'stg') !== false ? 'Staging' : 'Production' }}
                              </span>
                           </td>
                        </tr>
                        <tr>
                           <th class="bg-light">API URL</th>
                           <td>{{ $config['api_url'] }}</td>
                        </tr>
                        <tr>
                           <th class="bg-light">Auth URL</th>
                           <td>{{ $config['auth_url'] }}</td>
                        </tr>
                        <tr>
                           <th class="bg-light">Client ID</th>
                           <td>
                              @if($config['client_id'])
                              <span class="text-success"><i class="fas fa-check-circle mr-1"></i> Tersedia</span>
                              @else
                              <span class="text-danger"><i class="fas fa-times-circle mr-1"></i> Tidak Tersedia</span>
                              @endif
                           </td>
                        </tr>
                        <tr>
                           <th class="bg-light">Client Secret</th>
                           <td>
                              <span
                                 class="{{ $config['client_secret'] == 'Tersedia' ? 'text-success' : 'text-danger' }}">
                                 <i
                                    class="fas {{ $config['client_secret'] == 'Tersedia' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                 {{ $config['client_secret'] }}
                              </span>
                           </td>
                        </tr>
                        <tr>
                           <th class="bg-light">Organization ID</th>
                           <td>
                              @if($config['organization_id'])
                              <span class="text-success"><i class="fas fa-check-circle mr-1"></i> {{
                                 $config['organization_id'] }}</span>
                              @else
                              <span class="text-danger"><i class="fas fa-times-circle mr-1"></i> Tidak Tersedia</span>
                              @endif
                           </td>
                        </tr>
                        <tr>
                           <th class="bg-light">Token Status</th>
                           <td>
                              <span
                                 class="{{ strpos($config['token_status'], 'Tersedia') !== false ? 'text-success' : 'text-danger' }}">
                                 <i
                                    class="fas {{ strpos($config['token_status'], 'Tersedia') !== false ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                 {{ $config['token_status'] }}
                              </span>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </div>

               <div class="mt-4 text-center">
                  <button id="testTokenBtn" class="btn btn-primary btn-lg px-5 shadow-lg">
                     <i class="fas fa-sync-alt mr-2"></i> Tes Koneksi & Token
                  </button>
               </div>

               <!-- Container untuk hasil tes token (simple) -->
               <div id="simpleResponseContainer" class="mt-4" style="display: none;">
                  <div class="card shadow-sm border-0">
                     <div id="simpleResponseHeader" class="card-header py-3">
                        <div class="d-flex align-items-center">
                           <i id="simpleResponseIcon" class="fas mr-2"></i>
                           <h6 class="mb-0 font-weight-bold" id="simpleResponseText"></h6>
                        </div>
                     </div>
                     <div class="card-body p-4" id="simpleResponseBody">
                     </div>
                  </div>
               </div>

               <!-- Container untuk hasil tes token (detail) -->
               <div id="responseContainer" class="mt-4" style="display: none;">
                  <div class="card shadow-lg border-0">
                     <div id="responseHeader" class="card-header response-header">
                        <h5 class="mb-0 font-weight-bold"></h5>
                     </div>
                     <div id="responseBody" class="card-body response-body p-4">
                     </div>
                  </div>
               </div>

               <div id="tokenTestResult" class="mt-4" style="display: none;">
                  <div class="card shadow-lg border-0">
                     <div id="resultHeader" class="card-header bg-gradient-info text-white">
                        <h5 class="mb-0"><i class="fas fa-clipboard-check mr-2"></i> Hasil Pengujian Token</h5>
                     </div>
                     <div class="card-body p-4">
                        <div id="tokenTestContent"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="card shadow-lg border-0 mt-4">
            <div class="card-header bg-gradient-info text-white py-3">
               <h5 class="mb-0 font-weight-bold"><i class="fas fa-book mr-2"></i> Panduan Konfigurasi</h5>
            </div>
            <div class="card-body p-4">
               <h6 class="font-weight-bold"><i class="fas fa-list-ol mr-2"></i> Langkah-langkah Konfigurasi SATUSEHAT:
               </h6>
               <ol class="mt-3">
                  <li class="mb-2">Dapatkan kredensial SATUSEHAT (Client ID, Client Secret, Organization ID) dari tim
                     SATUSEHAT
                     Kemenkes.</li>
                  <li class="mb-2">Tambahkan kredensial tersebut ke file <code>.env</code> aplikasi dengan format
                     berikut:
                     <pre class="bg-light p-3 mt-2 rounded">
# Untuk Production
SATUSEHAT_API_URL=https://api-satusehat.kemkes.go.id
SATUSEHAT_AUTH_URL=https://api-satusehat.kemkes.go.id/oauth2/v1

# Untuk Staging
# SATUSEHAT_API_URL=https://api-satusehat-stg.dto.kemkes.go.id
# SATUSEHAT_AUTH_URL=https://api-satusehat-stg.dto.kemkes.go.id/oauth2/v1

SATUSEHAT_CLIENT_ID=your_client_id
SATUSEHAT_CLIENT_SECRET=your_client_secret
SATUSEHAT_ORGANIZATION_ID=your_organization_id
SATUSEHAT_TOKEN_EXPIRY=3600</pre>
                  </li>
                  <li class="mb-2">Restart server Laravel setelah melakukan perubahan pada file <code>.env</code>.</li>
                  <li class="mb-2">Gunakan tombol "Tes Koneksi & Token" untuk memverifikasi konfigurasi.</li>
               </ol>

               <div class="alert alert-warning mt-4 shadow-sm border-0 bg-gradient-warning-light">
                  <i class="fas fa-exclamation-triangle mr-2 text-warning"></i> <strong>Penting:</strong> Pastikan
                  kredensial
                  SATUSEHAT
                  disimpan dengan aman dan tidak dibagikan kepada pihak yang tidak berwenang.
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@section('css')
<style>
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

   /* Button styles */
   .btn-primary {
      background: linear-gradient(135deg, #2e5cb8 0%, #1a3c7e 100%);
      border: none;
   }

   .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 15px rgba(0, 85, 179, 0.3);
   }

   /* Gradient backgrounds */
   .bg-gradient-primary {
      background: linear-gradient(135deg, #2e5cb8 0%, #1a3c7e 100%);
   }

   .bg-gradient-success {
      background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
   }

   .bg-gradient-danger {
      background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%);
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

   /* Test connection button */
   .btn-test-connection {
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: 600;
      transition: all 0.3s;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
   }

   .btn-test-connection:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
   }

   /* Status badges */
   .badge {
      padding: 6px 12px;
      font-size: 12px;
      font-weight: 600;
      border-radius: 30px;
   }

   .badge-success {
      background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
   }

   .badge-danger {
      background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%);
   }

   .badge-warning {
      background: linear-gradient(135deg, #ffc107 0%, #d39e00 100%);
      color: #212529;
   }

   .badge-info {
      background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
   }

   /* Table styling */
   .table th {
      font-weight: 600;
      background-color: #f8f9fa;
   }

   .table td,
   .table th {
      padding: 12px 15px;
      vertical-align: middle;
   }

   /* Token info */
   .token-info {
      background-color: #f8f9fc;
      border-radius: 8px;
      padding: 15px;
      border-left: 4px solid #4e73df;
   }

   /* Shadow effects */
   .shadow-lg {
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
   }

   /* Response container */
   #responseContainer {
      border-radius: 8px;
      overflow: hidden;
      margin-top: 20px;
      display: none;
   }

   .response-header {
      padding: 12px 15px;
      color: white;
      font-weight: 600;
   }

   .response-body {
      padding: 15px;
      background-color: #f8f9fc;
      border: 1px solid #e3e6f0;
      border-top: none;
      max-height: 300px;
      overflow-y: auto;
   }

   .response-success {
      background-color: #28a745;
   }

   .response-error {
      background-color: #dc3545;
   }

   /* Simple response styling */
   .header-success {
      background-color: #d4edda;
      color: #155724;
      border-color: #c3e6cb;
   }

   .header-error {
      background-color: #f8d7da;
      color: #721c24;
      border-color: #f5c6cb;
   }

   /* Custom response styling to match image */
   .header-success-custom {
      background-color: #ffffff;
      color: #28a745;
      border-bottom: 1px solid #e3e6f0;
   }

   .header-error-custom {
      background-color: #dc3545;
      color: #ffffff;
      border-bottom: 1px solid #dc3545;
   }

   /* Token display */
   .token-display {
      font-family: monospace;
      word-break: break-all;
      background-color: #f8f9fc;
      padding: 10px;
      border-radius: 4px;
      border: 1px solid #e3e6f0;
   }

   /* Loading spinner */
   .spinner-border {
      width: 1.5rem;
      height: 1.5rem;
      border-width: 0.2em;
   }
</style>
@endsection

@section('js')
<script>
   $(document).ready(function() {
      console.log('Document ready - Initializing token test');
      
      // Test token connection
      $('#testTokenBtn').on('click', function() {
         console.log('Test token button clicked');
         
         // Show loading state
         const $btn = $(this);
         const originalText = $btn.html();
         $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menguji koneksi...');
         $btn.prop('disabled', true);
         
         // Hide previous response
         $('#responseContainer').hide();
         $('#tokenTestResult').hide();
         $('#simpleResponseContainer').hide();
         
         // Make AJAX request
         $.ajax({
            url: '{{ route("kyc.new.test-token") }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
               console.log('Response:', response);
               
               // Tambahkan logging untuk debugging
               if (!response.success) {
                  console.log('Error details:', response.error_details);
                  console.log('Petugas:', response.petugas);
               }
               
               // Reset button
               $btn.html(originalText);
               $btn.prop('disabled', false);
               
               // Show simple response first
               $('#simpleResponseContainer').show();
               
               if (response.success) {
                  // Success response - simple
                  $('#simpleResponseHeader').removeClass('header-error-custom').addClass('header-success-custom');
                  $('#simpleResponseIcon').removeClass('fa-exclamation-circle').addClass('fa-check-circle');
                  $('#simpleResponseText').html('Token Tersedia');
                  $('#simpleResponseBody').html('<p class="mb-0">Token berhasil diperoleh dan siap digunakan.</p>' +
                     '<button class="btn btn-sm btn-outline-success mt-2" id="showDetailBtn"><i class="fas fa-info-circle mr-1"></i> Lihat Detail</button>');
                  
                  // Prepare detailed response
                  $('#responseHeader').removeClass('response-error').addClass('response-success');
                  $('#responseHeader').html('<i class="fas fa-check-circle mr-2"></i> Koneksi Berhasil');
                  
                  // Pastikan data ada sebelum mengaksesnya
                  const data = response.data || {};
                  
                  // Cek apakah ada data petugas
                  let petugas = {};
                  if (response.petugas) {
                     petugas = response.petugas;
                  } else if (data.petugas) {
                     petugas = data.petugas;
                  }
                  
                  // Normalisasi properti petugas untuk menangani berbagai format
                  const namaPetugas = petugas.nama || petugas.name || petugas.nama_petugas || 'N/A';
                  const noKTP = petugas.no_ktp || petugas.ktp || petugas.nik_ktp || petugas.nomor_ktp || 'N/A';
                  const usernamePetugas = petugas.username || petugas.nik || petugas.id || 'N/A';
                  
                  // Validasi format NIK (harus 16 digit angka)
                  const isValidNIK = (nik) => {
                     return /^\d{16}$/.test(nik);
                  };
                  
                  // Periksa apakah NIK valid
                  const nikStatus = isValidNIK(noKTP) ? 
                     '<span class="badge badge-success">Valid (16 digit)</span>' : 
                     '<span class="badge badge-danger">Tidak Valid (harus 16 digit angka)</span>';
                  
                  // Tentukan apakah informasi petugas harus ditampilkan
                  const showPetugasInfo = namaPetugas !== 'N/A' || noKTP !== 'N/A';
                  
                  let responseHtml = `
                     <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle mr-2"></i> Token berhasil diperoleh!
                     </div>
                     <div class="mb-3">
                        <h6 class="font-weight-bold">Detail Token:</h6>
                        <ul class="list-group">
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span>Panjang Token</span>
                              <span class="badge badge-primary badge-pill">${data.token_length || 'N/A'} karakter</span>
                           </li>
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span>Waktu Kadaluarsa</span>
                              <span class="badge badge-info badge-pill">${data.expires_in || 'N/A'} detik</span>
                           </li>
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span>Organisasi</span>
                              <span class="badge badge-success badge-pill">${data.organization_name || 'N/A'}</span>
                           </li>
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span>Client ID</span>
                              <span class="badge badge-secondary badge-pill">${data.client_id || 'N/A'}</span>
                           </li>
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span>Environment</span>
                              <span class="badge badge-${data.environment === 'Production' ? 'danger' : 'warning'} badge-pill">${data.environment || 'N/A'}</span>
                           </li>
                        </ul>
                     </div>
                     <div class="token-info">
                        <h6 class="font-weight-bold">Token (Tersamarkan):</h6>
                        <div class="token-display">${data.masked_token || 'Token tidak tersedia'}</div>
                     </div>
                     ${showPetugasInfo ? `
                     <div class="mb-3 mt-4">
                        <h6 class="font-weight-bold">Informasi Petugas:</h6>
                        <div class="alert alert-info mb-2">
                           <i class="fas fa-info-circle mr-2"></i> Data berikut digunakan untuk validasi dengan SATUSEHAT
                        </div>
                        <ul class="list-group">
                           <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                              <span class="font-weight-bold">Nama Petugas</span>
                              <span class="text-primary font-weight-bold">${namaPetugas}</span>
                           </li>
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span class="font-weight-bold">Nomor KTP</span>
                              <div class="text-right">
                                 <span class="badge badge-primary badge-pill">${noKTP}</span>
                                 <div class="mt-1">${nikStatus}</div>
                              </div>
                           </li>
                           ${isValidNIK(noKTP) ? `
                           <li class="list-group-item bg-light">
                              <small class="text-success"><i class="fas fa-check-circle mr-1"></i> Format Nomor KTP valid untuk SATUSEHAT</small>
                           </li>
                           ` : ''}
                        </ul>
                        ${!isValidNIK(noKTP) ? `
                        <div class="alert alert-warning mt-2">
                           <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Perhatian:</strong> Nomor KTP harus berupa 16 digit angka untuk verifikasi SATUSEHAT. Silakan perbarui data petugas di sistem.
                           <div class="mt-2">
                              <strong>Saran:</strong>
                              <ol class="mb-0 pl-3 mt-1">
                                 <li>Pastikan Nomor KTP terdiri dari 16 digit angka</li>
                                 <li>Perbarui data petugas di database dengan Nomor KTP yang valid</li>
                                 <li>Pastikan Nomor KTP sesuai dengan KTP fisik petugas</li>
                              </ol>
                           </div>
                        </div>
                        ` : ''}
                     </div>
                     ` : ''}
                  `;
                  
                  $('#responseBody').html(responseHtml);
                  
                  // Add event listener for detail button
                  $('#showDetailBtn').on('click', function() {
                     $('#responseContainer').show();
                  });
               } else {
                  // Error response - simple
                  $('#simpleResponseHeader').removeClass('header-success-custom').addClass('header-error-custom');
                  $('#simpleResponseIcon').removeClass('fa-check-circle').addClass('fa-exclamation-circle');
                  $('#simpleResponseText').html('Koneksi Gagal');
                  $('#simpleResponseBody').html(`<p class="mb-0 mt-2">${response.message || 'Gagal mendapatkan token. Periksa konfigurasi Anda.'}</p>` +
                     '<button class="btn btn-sm btn-outline-light mt-2" id="showDetailBtn"><i class="fas fa-info-circle mr-1"></i> Lihat Detail</button>');
                  
                  // Prepare detailed response
                  $('#responseHeader').removeClass('response-success').addClass('response-error');
                  $('#responseHeader').html('<i class="fas fa-exclamation-circle mr-2"></i> Koneksi Gagal');
                  
                  // Pastikan error_details ada sebelum mengaksesnya
                  const errorDetails = response.error_details || {};
                  
                  // Pastikan data petugas ada dan diproses dengan benar
                  let petugas = {};
                  if (response.petugas) {
                     petugas = response.petugas;
                  } else if (response.data && response.data.petugas) {
                     petugas = response.data.petugas;
                  }
                  
                  // Normalisasi properti petugas untuk menangani berbagai format
                  const namaPetugas = petugas.nama || petugas.name || petugas.nama_petugas || 'N/A';
                  const noKTP = petugas.no_ktp || petugas.ktp || petugas.nik_ktp || petugas.nomor_ktp || 'N/A';
                  const usernamePetugas = petugas.username || petugas.nik || petugas.id || 'N/A';
                  
                  // Validasi format NIK (harus 16 digit angka)
                  const isValidNIK = (nik) => {
                     return /^\d{16}$/.test(nik);
                  };
                  
                  // Periksa apakah NIK valid
                  const nikStatus = isValidNIK(noKTP) ? 
                     '<span class="badge badge-success">Valid (16 digit)</span>' : 
                     '<span class="badge badge-danger">Tidak Valid (harus 16 digit angka)</span>';
                  
                  // Tentukan apakah informasi petugas harus ditampilkan
                  const showPetugasInfo = namaPetugas !== 'N/A' || noKTP !== 'N/A';
                  
                  let responseHtml = `
                     <div class="alert alert-danger mb-3">
                        <i class="fas fa-exclamation-circle mr-2"></i> Gagal mendapatkan token!
                        <p class="mt-2 mb-0 font-weight-bold">${response.message}</p>
                     </div>
                     <div class="mb-3">
                        <h6 class="font-weight-bold">Detail Kredensial:</h6>
                        <ul class="list-group">
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span>Client ID</span>
                              <span class="badge badge-info badge-pill">${errorDetails.client_id || 'N/A'}</span>
                           </li>
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span>Client Secret</span>
                              <span class="badge badge-info badge-pill">${errorDetails.client_secret || 'N/A'}</span>
                           </li>
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span>Organization ID</span>
                              <span class="badge badge-info badge-pill">${errorDetails.organization_id || 'N/A'}</span>
                           </li>
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span>Auth URL</span>
                              <span class="text-info">${errorDetails.auth_url || 'N/A'}</span>
                           </li>
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span>API URL</span>
                              <span class="text-info">${errorDetails.api_url || 'N/A'}</span>
                           </li>
                        </ul>
                     </div>
                     ${showPetugasInfo ? `
                     <div class="mb-3">
                        <h6 class="font-weight-bold">Informasi Petugas:</h6>
                        <div class="alert alert-info mb-2">
                           <i class="fas fa-info-circle mr-2"></i> Data berikut digunakan untuk validasi dengan SATUSEHAT
                        </div>
                        <ul class="list-group">
                           <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                              <span class="font-weight-bold">Nama Petugas</span>
                              <span class="text-primary font-weight-bold">${namaPetugas}</span>
                           </li>
                           <li class="list-group-item d-flex justify-content-between align-items-center">
                              <span class="font-weight-bold">Nomor KTP</span>
                              <div class="text-right">
                                 <span class="badge badge-primary badge-pill">${noKTP}</span>
                                 <div class="mt-1">${nikStatus}</div>
                              </div>
                           </li>
                           ${isValidNIK(noKTP) ? `
                           <li class="list-group-item bg-light">
                              <small class="text-success"><i class="fas fa-check-circle mr-1"></i> Format Nomor KTP valid untuk SATUSEHAT</small>
                           </li>
                           ` : ''}
                        </ul>
                        ${!isValidNIK(noKTP) ? `
                        <div class="alert alert-warning mt-2">
                           <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Perhatian:</strong> Nomor KTP harus berupa 16 digit angka untuk verifikasi SATUSEHAT. Silakan perbarui data petugas di sistem.
                           <div class="mt-2">
                              <strong>Saran:</strong>
                              <ol class="mb-0 pl-3 mt-1">
                                 <li>Pastikan Nomor KTP terdiri dari 16 digit angka</li>
                                 <li>Perbarui data petugas di database dengan Nomor KTP yang valid</li>
                                 <li>Pastikan Nomor KTP sesuai dengan KTP fisik petugas</li>
                              </ol>
                           </div>
                        </div>
                        ` : ''}
                     </div>
                     ` : ''}
                     <div class="alert alert-warning">
                        <h6 class="font-weight-bold"><i class="fas fa-lightbulb mr-2"></i> Kemungkinan Penyebab Error:</h6>
                        <ol class="mb-0 pl-3">
                           <li>Client ID atau Client Secret tidak valid atau sudah kedaluwarsa</li>
                           <li>Organization ID tidak terdaftar di sistem SATUSEHAT</li>
                           <li>Server SATUSEHAT sedang mengalami gangguan</li>
                           <li>Koneksi internet terputus atau tidak stabil</li>
                           <li>Firewall atau proxy memblokir koneksi ke server SATUSEHAT</li>
                           <li>Menggunakan endpoint yang salah (staging vs production)</li>
                        </ol>
                     </div>
                     <div class="alert alert-info">
                        <h6 class="font-weight-bold"><i class="fas fa-tools mr-2"></i> Saran Perbaikan:</h6>
                        <ol class="mb-0 pl-3">
                           <li>Periksa kembali Client ID dan Client Secret di file .env</li>
                           <li>Pastikan Organization ID sudah benar dan terdaftar</li>
                           <li>Periksa koneksi internet Anda</li>
                           <li>Pastikan Anda menggunakan endpoint yang benar:
                              <ul class="mt-1">
                                 <li>Staging: <code>https://api-satusehat-stg.dto.kemkes.go.id</code></li>
                                 <li>Production: <code>https://api-satusehat.kemkes.go.id</code></li>
                              </ul>
                           </li>
                           <li>Hubungi tim support SATUSEHAT jika masalah berlanjut</li>
                        </ol>
                     </div>
                     <div class="alert alert-primary mt-3">
                        <h6 class="font-weight-bold"><i class="fas fa-network-wired mr-2"></i> Memeriksa Masalah Koneksi:</h6>
                        <ol class="mb-0 pl-3">
                           <li>Coba akses URL SATUSEHAT dari browser untuk memeriksa apakah domain dapat diakses:
                              <ul class="mt-1">
                                 <li>Production: <a href="https://api-satusehat.kemkes.go.id" target="_blank" class="text-primary font-weight-bold">https://api-satusehat.kemkes.go.id</a></li>
                                 <li>Staging: <a href="https://api-satusehat-stg.dto.kemkes.go.id" target="_blank" class="text-primary font-weight-bold">https://api-satusehat-stg.dto.kemkes.go.id</a></li>
                              </ul>
                           </li>
                           <li>Jika tidak dapat diakses, kemungkinan koneksi diblokir oleh firewall atau memerlukan VPN</li>
                           <li>Periksa pengaturan proxy di jaringan Anda</li>
                           <li>Jika menggunakan VPN, pastikan VPN aktif dan terhubung dengan benar</li>
                           <li>Hubungi administrator jaringan untuk membuka akses ke domain SATUSEHAT</li>
                           <li>Pastikan format request sesuai dengan dokumentasi API SATUSEHAT</li>
                        </ol>
                     </div>
                  `;
                  
                  $('#responseBody').html(responseHtml);
                  
                  // Add event listener for detail button
                  $('#showDetailBtn').on('click', function() {
                     $('#responseContainer').show();
                  });
               }
            },
            error: function(xhr, status, error) {
               console.error('Error:', error);
               console.error('Response:', xhr.responseText);
               
               // Reset button
               $btn.html(originalText);
               $btn.prop('disabled', false);
               
               // Fungsi validasi NIK
               const isValidNIK = (nik) => {
                  return /^\d{16}$/.test(nik);
               };
               
               // Show simple response first
               $('#simpleResponseContainer').show();
               $('#simpleResponseHeader').removeClass('header-success-custom').addClass('header-error-custom');
               $('#simpleResponseIcon').removeClass('fa-check-circle').addClass('fa-exclamation-circle');
               $('#simpleResponseText').html('Koneksi Gagal');
               $('#simpleResponseBody').html('<p class="mb-0 mt-2">Terjadi kesalahan saat menghubungi server.</p>' +
                  '<button class="btn btn-sm btn-outline-light mt-2" id="showDetailBtn"><i class="fas fa-info-circle mr-1"></i> Lihat Detail</button>');
               
               // Prepare detailed response
               $('#responseHeader').removeClass('response-success').addClass('response-error');
               $('#responseHeader').html('<i class="fas fa-exclamation-circle mr-2"></i> Terjadi Kesalahan');
               
               let responseHtml = `
                  <div class="alert alert-danger mb-3">
                     <i class="fas fa-exclamation-circle mr-2"></i> Terjadi kesalahan saat menghubungi server!
                  </div>
                  <div class="mb-3">
                     <h6 class="font-weight-bold">Detail Error:</h6>
                     <div class="token-display">${xhr.responseText || 'No response from server'}</div>
                  </div>
                  <div class="alert alert-warning">
                     <h6 class="font-weight-bold"><i class="fas fa-lightbulb mr-2"></i> Saran Perbaikan:</h6>
                     <ol class="mb-0 pl-3">
                        <li>Periksa kembali Client ID dan Client Secret di file .env</li>
                        <li>Pastikan Organization ID sudah benar dan terdaftar</li>
                        <li>Periksa koneksi internet Anda</li>
                        <li>Pastikan Anda menggunakan endpoint yang benar:
                           <ul class="mt-1">
                              <li>Staging: <code>https://api-satusehat-stg.dto.kemkes.go.id</code></li>
                              <li>Production: <code>https://api-satusehat.kemkes.go.id</code></li>
                           </ul>
                        </li>
                        <li>Hubungi tim support SATUSEHAT jika masalah berlanjut</li>
                     </ol>
                  </div>
                  <div class="alert alert-primary mt-3">
                     <h6 class="font-weight-bold"><i class="fas fa-network-wired mr-2"></i> Memeriksa Masalah Koneksi:</h6>
                     <ol class="mb-0 pl-3">
                        <li>Coba akses URL SATUSEHAT dari browser untuk memeriksa apakah domain dapat diakses:
                           <ul class="mt-1">
                              <li>Production: <a href="https://api-satusehat.kemkes.go.id" target="_blank" class="text-primary font-weight-bold">https://api-satusehat.kemkes.go.id</a></li>
                              <li>Staging: <a href="https://api-satusehat-stg.dto.kemkes.go.id" target="_blank" class="text-primary font-weight-bold">https://api-satusehat-stg.dto.kemkes.go.id</a></li>
                           </ul>
                        </li>
                        <li>Jika tidak dapat diakses, kemungkinan koneksi diblokir oleh firewall atau memerlukan VPN</li>
                        <li>Periksa pengaturan proxy di jaringan Anda</li>
                        <li>Jika menggunakan VPN, pastikan VPN aktif dan terhubung dengan benar</li>
                        <li>Hubungi administrator jaringan untuk membuka akses ke domain SATUSEHAT</li>
                        <li>Pastikan format request sesuai dengan dokumentasi API SATUSEHAT</li>
                     </ol>
                  </div>
               `;
               
               $('#responseBody').html(responseHtml);
               
               // Add event listener for detail button
               $('#showDetailBtn').on('click', function() {
                  $('#responseContainer').show();
               });
            }
         });
      });
   });
</script>
@endsection
@extends('adminlte::page')

@section('title', 'Status Verifikasi KYC SATUSEHAT')

@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-8">
         <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
               <h5 class="mb-0"><i class="fas fa-user-shield mr-2"></i> Status Verifikasi KYC SATUSEHAT</h5>
               <div>
                  <a href="{{ route('kyc.index') }}" class="btn btn-sm btn-info mr-2">
                     <i class="fas fa-arrow-left mr-1"></i> Kembali
                  </a>
                  <a href="{{ route('kyc.config') }}" class="btn btn-sm btn-light">
                     <i class="fas fa-cog mr-1"></i> Konfigurasi
                  </a>
               </div>
            </div>
            <div class="card-body">
               @if (session('success'))
               <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               @endif

               @if (session('error'))
               <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               @endif

               <div class="text-center mb-4">
                  @if(session('patient_nik') && session('patient_name'))
                  <div class="mb-3">
                     <span class="fa-stack fa-3x">
                        <i class="fas fa-circle fa-stack-2x text-success"></i>
                        <i class="fas fa-user-check fa-stack-1x fa-inverse"></i>
                     </span>
                  </div>
                  <h4 class="font-weight-bold text-success">Verifikasi Berhasil</h4>
                  <p class="lead">Pasien telah berhasil diverifikasi melalui SATUSEHAT</p>
                  @else
                  <div class="mb-3">
                     <span class="fa-stack fa-3x">
                        <i class="fas fa-circle fa-stack-2x text-warning"></i>
                        <i class="fas fa-user-clock fa-stack-1x fa-inverse"></i>
                     </span>
                  </div>
                  <h4 class="font-weight-bold text-warning">Belum Diverifikasi</h4>
                  <p class="lead">Pasien belum melakukan verifikasi melalui SATUSEHAT</p>
                  @endif
               </div>

               <div class="card shadow-sm border-0 mb-4">
                  <div class="card-header bg-gradient-info text-white">
                     <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i> Informasi Pasien</h5>
                  </div>
                  <div class="card-body">
                     <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                           <tbody>
                              <tr>
                                 <th style="width: 30%" class="bg-light"><i class="fas fa-id-card mr-1"></i> NIK</th>
                                 <td>{{ session('patient_nik') ?? 'Belum diverifikasi' }}</td>
                              </tr>
                              <tr>
                                 <th class="bg-light"><i class="fas fa-user mr-1"></i> Nama</th>
                                 <td>{{ session('patient_name') ?? 'Belum diverifikasi' }}</td>
                              </tr>
                              @if(session('patient_dob'))
                              <tr>
                                 <th class="bg-light"><i class="fas fa-calendar-alt mr-1"></i> Tanggal Lahir</th>
                                 <td>{{ date('d-m-Y', strtotime(session('patient_dob'))) }}</td>
                              </tr>
                              @endif
                              @if(session('patient_gender'))
                              <tr>
                                 <th class="bg-light"><i class="fas fa-venus-mars mr-1"></i> Jenis Kelamin</th>
                                 <td>{{ session('patient_gender') == 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
                              </tr>
                              @endif
                              @if(session('kyc_ihs_number'))
                              <tr>
                                 <th class="bg-light"><i class="fas fa-fingerprint mr-1"></i> IHS Number</th>
                                 <td>{{ session('kyc_ihs_number') }}</td>
                              </tr>
                              @endif
                              <tr>
                                 <th class="bg-light"><i class="fas fa-check-circle mr-1"></i> Status Verifikasi</th>
                                 <td>
                                    @if(session('patient_nik') && session('patient_name'))
                                    <span class="badge badge-success px-3 py-2"><i class="fas fa-check mr-1"></i>
                                       Terverifikasi</span>
                                    @else
                                    <span class="badge badge-warning px-3 py-2"><i class="fas fa-clock mr-1"></i> Belum
                                       Diverifikasi</span>
                                    @endif
                                 </td>
                              </tr>
                              @if(session('kyc_expired_timestamp'))
                              <tr>
                                 <th class="bg-light"><i class="fas fa-clock mr-1"></i> Berlaku Hingga</th>
                                 <td>{{ date('d-m-Y H:i:s', strtotime(session('kyc_expired_timestamp'))) }}</td>
                              </tr>
                              @endif
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>

               <div class="text-center mt-4">
                  @if(session('patient_nik') && session('patient_name'))
                  <a href="{{ route('kyc.index') }}" class="btn btn-primary btn-lg px-5">
                     <i class="fas fa-redo mr-2"></i> Verifikasi Pasien Lain
                  </a>
                  <a href="{{ route('kyc.status') }}" class="btn btn-info btn-lg px-5 ml-2">
                     <i class="fas fa-sync-alt mr-2"></i> Segarkan Status
                  </a>
                  @else
                  <a href="{{ route('kyc.index') }}" class="btn btn-success btn-lg px-5">
                     <i class="fas fa-user-check mr-2"></i> Verifikasi Pasien Sekarang
                  </a>
                  @endif
               </div>
            </div>
         </div>

         @if(session('patient_nik') && session('patient_name'))
         <div class="card shadow-sm mt-4">
            <div class="card-header bg-gradient-success text-white">
               <h5 class="mb-0"><i class="fas fa-check-double mr-2"></i> Verifikasi Berhasil</h5>
            </div>
            <div class="card-body">
               <div class="alert alert-success mb-0">
                  <p><i class="fas fa-info-circle mr-2"></i> <strong>Informasi:</strong> Data pasien telah berhasil
                     diverifikasi melalui SATUSEHAT. Data ini dapat digunakan untuk keperluan pelayanan kesehatan.</p>
                  <p class="mb-0"><strong>Catatan:</strong> Verifikasi ini berlaku untuk satu sesi pelayanan. Untuk
                     pelayanan berikutnya, pasien perlu melakukan verifikasi ulang.</p>
                  @if(!session('kyc_ihs_number'))
                  <hr>
                  <p class="mb-0 text-warning">
                     <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Perhatian:</strong>
                     IHS Number tidak tersedia. Ini mungkin karena API SATUSEHAT sedang tidak dapat diakses.
                     Verifikasi tetap valid berdasarkan data yang tersimpan di sistem.
                  </p>
                  @endif
               </div>
            </div>
         </div>
         @else
         <div class="card shadow-sm mt-4">
            <div class="card-header bg-gradient-warning text-white">
               <h5 class="mb-0"><i class="fas fa-exclamation-triangle mr-2"></i> Belum Diverifikasi</h5>
            </div>
            <div class="card-body">
               <div class="alert alert-warning mb-0">
                  <p><i class="fas fa-info-circle mr-2"></i> <strong>Informasi:</strong> Pasien belum melakukan
                     verifikasi melalui SATUSEHAT. Silakan lakukan verifikasi untuk menggunakan layanan yang memerlukan
                     verifikasi identitas.</p>
                  <p class="mb-0"><strong>Catatan:</strong> Verifikasi diperlukan untuk memastikan keamanan data dan
                     kesesuaian identitas pasien.</p>
               </div>
            </div>
         </div>
         @endif
      </div>
   </div>
</div>
@endsection

@section('css')
<style>
   .btn-lg {
      padding: 12px 30px;
      font-size: 16px;
      border-radius: 5px;
   }

   .card {
      border-radius: 8px;
      overflow: hidden;
   }

   .card-header {
      border-bottom: 0;
   }

   .badge {
      font-size: 85%;
      font-weight: 500;
      border-radius: 4px;
   }

   .bg-gradient-primary {
      background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
   }

   .bg-gradient-info {
      background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
   }

   .bg-gradient-success {
      background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
   }

   .bg-gradient-warning {
      background: linear-gradient(135deg, #ffc107 0%, #d39e00 100%);
   }

   .table th,
   .table td {
      padding: 12px 15px;
      vertical-align: middle;
   }
</style>
@stop
@extends('adminlte::page')

@section('title', 'Challenge Code KYC SATUSEHAT')

@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-8">
         <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Challenge Code KYC SATUSEHAT</h5>
               <div>
                  <a href="{{ route('kyc.status') }}" class="btn btn-sm btn-info mr-2">
                     <i class="fas fa-user-check"></i> Status Verifikasi
                  </a>
                  <a href="{{ route('kyc.config') }}" class="btn btn-sm btn-light">
                     <i class="fas fa-cog"></i> Konfigurasi
                  </a>
               </div>
            </div>
            <div class="card-body text-center">
               <div class="mb-4">
                  <i class="fas fa-qrcode fa-5x text-primary mb-3"></i>
                  <h4>Kode Verifikasi SATUSEHAT</h4>
               </div>

               <div class="challenge-code-display p-4 mb-4 bg-light rounded">
                  <h1 class="display-4 font-weight-bold text-primary">{{ $challengeCode }}</h1>
               </div>

               <div class="alert alert-info">
                  <h5><i class="fas fa-info-circle"></i> Instruksi untuk Pasien:</h5>
                  <ol class="text-left mb-0">
                     <li>Buka aplikasi <strong>SATUSEHAT Mobile</strong> di smartphone Anda</li>
                     <li>Pilih menu <strong>Verifikasi KYC</strong> atau <strong>Scan QR</strong></li>
                     <li>Masukkan kode verifikasi <strong>{{ $challengeCode }}</strong> pada aplikasi</li>
                     <li>Ikuti instruksi selanjutnya pada aplikasi SATUSEHAT Mobile</li>
                     <li>Setelah verifikasi berhasil, beritahu petugas fasilitas kesehatan</li>
                  </ol>
               </div>

               <div class="alert alert-warning" id="countdown-alert">
                  <i class="fas fa-exclamation-triangle"></i> <strong>Penting:</strong> Kode verifikasi ini hanya
                  berlaku untuk satu kali penggunaan dan akan kedaluwarsa dalam 1 menit.
                  @if(session('kyc_expired_timestamp'))
                  <br>Kode akan kedaluwarsa pada: <strong id="expiry-time">
                     {{ \Carbon\Carbon::parse(session('kyc_expired_timestamp'))->setTimezone('Asia/Jakarta')->format('d
                     M Y H:i:s') }}
                  </strong>
                  @endif
               </div>

               <div class="mt-4">
                  <a href="{{ route('kyc.verification') }}" class="btn btn-success btn-lg mr-2">
                     <i class="fas fa-check-circle"></i> Verifikasi Selesai
                  </a>
                  <a href="{{ route('kyc.index') }}" class="btn btn-secondary">
                     <i class="fas fa-redo"></i> Mulai Ulang
                  </a>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<style>
   .challenge-code-display {
      letter-spacing: 5px;
      border: 2px dashed #007bff;
   }
</style>

@if(session('kyc_expired_timestamp'))
<script>
   document.addEventListener('DOMContentLoaded', function() {
   // Fungsi untuk menghitung mundur waktu kedaluwarsa
   function updateCountdown() {
      var expiryTime = new Date("{{ \Carbon\Carbon::parse(session('kyc_expired_timestamp'))->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') }}");
      var now = new Date();
      
      // Hitung selisih waktu dalam detik
      var diff = Math.floor((expiryTime - now) / 1000);
      
      if (diff <= 0) {
         document.getElementById('expiry-time').innerHTML = 'KEDALUWARSA';
         // Tampilkan pesan kedaluwarsa
         var warningDiv = document.getElementById('countdown-alert');
         warningDiv.className = 'alert alert-danger';
         warningDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> <strong>Perhatian:</strong> Kode verifikasi telah kedaluwarsa. Silakan <a href="{{ route("kyc.index") }}">mulai ulang</a> untuk mendapatkan kode baru.';
      } else {
         // Format waktu tersisa
         var minutes = Math.floor(diff / 60);
         var seconds = diff % 60;
         var minutesStr = minutes < 10 ? '0' + minutes : minutes;
         var secondsStr = seconds < 10 ? '0' + seconds : seconds;
         document.getElementById('expiry-time').innerHTML = minutesStr + ':' + secondsStr;
      }
   }
   
   // Update setiap detik
   setInterval(updateCountdown, 1000);
   
   // Panggil sekali saat halaman dimuat
   updateCountdown();
});
</script>
@endif
@endsection
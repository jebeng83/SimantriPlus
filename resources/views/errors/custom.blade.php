@extends('layouts.app')

@section('title', $title ?? 'Error')

@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-8">
         <div class="card shadow mt-5">
            <div class="card-header bg-danger text-white">
               <h4 class="mb-0">{{ $title ?? 'Terjadi Kesalahan' }}</h4>
            </div>
            <div class="card-body text-center">
               <img src="{{ asset('assets/img/warning.png') }}" alt="Warning" class="img-fluid mb-4"
                  style="max-width: 100px;">

               <h3 class="text-danger mb-3">{{ $title ?? 'Terjadi Kesalahan' }}</h3>

               <p class="mb-4">{{ $message ?? 'Maaf, sistem sedang mengalami gangguan teknis.' }}</p>

               <p class="mb-4">Tim kami sedang bekerja untuk memperbaiki masalah ini. Silakan coba lagi nanti.</p>

               <a href="{{ url('/') }}" class="btn btn-primary">
                  <i class="fas fa-home"></i> Kembali ke Beranda
               </a>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@extends('adminlte::page')

@section('title', 'Terjadi Kesalahan - Simantri PLUS')

@section('content_header')
<h1>Terjadi Kesalahan</h1>
@stop

@section('content')
<div class="error-page">
   <div class="row">
      <div class="col-12 col-md-8 offset-md-2">
         <div class="card">
            <div class="card-body text-center p-5">
               <img src="{{ asset('img/warning.png') }}" alt="Warning" class="mb-4" style="width: 80px; height: auto;">
               <h2 class="text-danger mb-4">Terjadi Kesalahan</h2>
               <p class="lead mb-5">Maaf, sistem sedang mengalami gangguan teknis.</p>
               <p class="mb-5">Tim kami sedang bekerja untuk memperbaiki masalah ini. Silakan coba lagi nanti.</p>

               <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                  <i class="fas fa-home mr-2"></i> Kembali ke Beranda
               </a>
            </div>
         </div>
      </div>
   </div>
</div>
@stop

@section('css')
<style>
   .error-page {
      margin-top: 2rem;
   }

   .card {
      border-radius: 10px;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
      overflow: hidden;
   }
</style>
@stop
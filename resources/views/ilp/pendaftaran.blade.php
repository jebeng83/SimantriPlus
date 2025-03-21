@extends('adminlte::page')

@section('title', 'Pendaftaran ILP')

@section('content_header')
<h1></h1>
<h5>Selamat Datang, {{$nm_dokter}}</h5>
@stop

@section('content')
<div class="row">
   <div class="col-md-12">
      {{-- Menggunakan ILP\Pendaftaran yang telah terintegrasi dengan FormPendaftaran dari Registrasi --}}
      <livewire:i-l-p.pendaftaran />
   </div>
</div>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)
@section('plugins.Sweetalert2', true)
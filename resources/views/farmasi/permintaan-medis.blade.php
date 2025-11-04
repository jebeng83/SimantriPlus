@extends('adminlte::page')

@section('title', 'Permintaan Obat/Alkes/BHP Medis')

@section('css')
    @vite('resources/css/app.css')
    <style>
        /* Sembunyikan root React global agar tidak mengganggu tampilan AdminLTE */
        #react-root { display: none !important; }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <!-- Root React untuk halaman Permintaan Medis -->
        <div id="farmasi-permintaan-root" class="mt-2"></div>
    </div>
@endsection

@section('js')
    {{-- App.jsx is loaded globally by adminlte::master. No per-page injection to avoid duplicate evaluation. --}}
@endsection
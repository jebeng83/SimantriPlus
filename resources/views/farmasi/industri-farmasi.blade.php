@extends('adminlte::page')

@section('title', 'Industri Farmasi')

@section('css')
    @vite('resources/css/app.css')
    <style>
        /* Sembunyikan HelloMotion global agar tidak mengganggu tampilan Farmasi */
        #react-root { display: none !important; }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <!-- React Root untuk halaman Industri Farmasi -->
        <div id="farmasi-industri-farmasi-root" class="mt-2"></div>
    </div>
@endsection

@section('js')
    {{-- App.jsx is loaded globally by adminlte::master. No per-page injection to avoid duplicate evaluation. --}}
@endsection
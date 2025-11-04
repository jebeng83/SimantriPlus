@extends('adminlte::page')

@section('title', 'Menu UKM')

@section('css')
    @vite('resources/css/app.css')
    <style>
        /* Sembunyikan HelloMotion global agar tidak mengganggu tampilan */
        #react-root { display: none !important; }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="w-full">
            <!-- React Root -->
            <div id="matrik-kegiatan-ukm-react-root" class="mt-2"></div>
        </div>
    </div>
@endsection

@section('js')
    {{-- App.jsx is loaded globally by adminlte::master. No per-page injection to avoid duplicate evaluation. --}}
@endsection
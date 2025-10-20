@extends('adminlte::page')

@section('title', 'Menu ePPBGM')

@section('css')
    @vite('resources/css/app.css')
    <style>
        /* Sembunyikan HelloMotion global agar tidak mengganggu tampilan ePPBGM */
        #react-root { display: none !important; }
        body { background-color: #f7fafc; }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="w-full">
            <!-- React Root -->
            <div id="eppbgm-menu-root" class="mt-2"></div>
        </div>
    </div>
@endsection

@section('js')
    @vite('resources/js/app.jsx')
@endsection
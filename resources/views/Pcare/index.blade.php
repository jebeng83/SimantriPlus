@extends('adminlte::page')

@section('title', 'Menu PCare')

@section('css')
    @vite('resources/css/app.css')
    <style>
        /* Sembunyikan HelloMotion global agar tidak mengganggu tampilan PCare */
        #react-root { display: none !important; }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="w-full">
            <!-- React Root -->
            <div id="pcare-menu-root" class="mt-2"></div>
        </div>
    </div>
@endsection

@section('js')
    @vite('resources/js/app.jsx')
@endsection
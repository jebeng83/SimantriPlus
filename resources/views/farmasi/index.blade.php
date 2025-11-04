@extends('adminlte::page')

@section('title', 'Modul Farmasi')

@section('css')
    <style>
        /* Sembunyikan HelloMotion global agar tidak mengganggu tampilan Farmasi */
        #react-root { display: none !important; }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <!-- React Root -->
        <div id="farmasi-index-root" class="mt-2"></div>
    </div>
@endsection
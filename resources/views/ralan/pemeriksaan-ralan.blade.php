@extends('adminlte::page')

@section('title', 'Pemeriksaan Pasien Ralan')

@section('content_header')
    <h1>Pemeriksaan Ralan</h1>
@stop

@section('content')
    <x-ralan.riwayat :no-rawat="request()->get('no_rawat')" />
    <div class="row">
        <div class="col-md-4">
            <x-ralan.pasien :no-rawat="request()->get('no_rawat')" />
        </div>
        <div class="col-md-8">
            <x-ralan.pemeriksaan :no-rawat="request()->get('no_rawat')" />
            <x-ralan.resep />
            <x-ralan.resume />
            <x-ralan.catatan :no-rawat="request()->get('no_rawat')" />
        </div>
    </div>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
    <script> console.log('Hi!'); </script>
@stop
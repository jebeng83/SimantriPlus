@extends('adminlte::page')

@section('title', 'Pemeriksaan Pasien Ranap')

@section('content_header')
    <h1>Pemeriksaan Ranap</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <x-ranap.pasien :no-rawat="request()->get('no_rawat')" />
        </div>
        <div class="col-md-8">
            <x-ranap.pemeriksaan-ranap :no-rawat="request()->get('no_rawat')" />
        </div>
    </div>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
    <script> console.log('Hi!'); </script>
@stop
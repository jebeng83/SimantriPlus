@extends('adminlte::page')

@section('title', 'Pemeriksaan Pasien Ralan')

@section('content_header')
    <h1>Pemeriksaan Ralan</h1>
@stop

@section('content')
    @isset($error)
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ $error }}
        </x-adminlte-alert>    
    @endisset
    @isset($success)
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ $success }}
        </x-adminlte-alert>
    @endisset
    <x-ralan.riwayat :no-rawat="request()->get('no_rawat')" />
    <div class="row">
        <div class="col-md-4">
            <x-ralan.pasien :no-rawat="request()->get('no_rawat')" />
        </div>
        <div class="col-md-8">
            <x-ralan.pemeriksaan :no-rawat="request()->get('no_rawat')" />
            <x-ralan.resep />
            <x-ralan.resume />
        </div>
    </div>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
    <script> console.log('Hi!'); </script>
@stop
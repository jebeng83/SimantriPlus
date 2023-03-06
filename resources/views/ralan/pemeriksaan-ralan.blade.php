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
            {{-- <x-ralan.pemeriksaan :no-rawat="request()->get('no_rawat')" /> --}}
            <livewire:ralan.pemeriksaan :noRawat="request()->get('no_rawat')" />
            @if(session()->get('username') == 'U002')
                <livewire:ralan.odontogram :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')">
            @endif
            <x-ralan.resep />
            {{-- <livewire:ralan.resep :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')"> --}}
            {{-- <x-ralan.resume /> --}}
            <livewire:ralan.resume :no-rawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')"/>
            {{-- <x-ralan.catatan :no-rawat="request()->get('no_rawat')" /> --}}
            <livewire:ralan.catatan :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
            <x-ralan.rujuk-internal :no-rawat="request()->get('no_rawat')" />
            {{-- <x-ralan.permintaan-lab :no-rawat="request()->get('no_rawat')" /> --}}
            <livewire:ralan.permintaan-lab :no-rawat="request()->get('no_rawat')" />
            {{-- <x-ralan.permintaan-radiologi :no-rawat="request()->get('no_rawat')" /> --}}
            <livewire:ralan.permintaan-radiologi :no-rawat="request()->get('no_rawat')" />
        </div>
    </div>
    
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
@stop
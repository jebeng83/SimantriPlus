@extends('adminlte::page')

@section('title', 'Pemeriksaan Pasien Ralan')

@section('content_header')
<div class="d-flex flex-row justify-content-between">
    <h1>Pemeriksaan Ralan</h1>
    <a name="" id="" class="btn btn-primary" href="{{ url('ralan/pasien') }}" role="button">Daftar Pasien</a>
</div>

@stop

@section('content')
<x-ralan.riwayat :no-rawat="request()->get('no_rawat')" />
<div class="row">
    <div class="col-md-4">
        <x-ralan.pasien :no-rawat="request()->get('no_rawat')" />
    </div>
    <div class="col-md-8">
        {{--
        <x-ralan.pemeriksaan :no-rawat="request()->get('no_rawat')" /> --}}
        {{-- <x-adminlte-card title="Penilaian Awal Rehab Medik" theme="info" collapsible maximizable>
            <livewire:ralan.penilaian-rehab-medik :noRawat="request()->get('no_rawat')" />
        </x-adminlte-card> --}}
        <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-bell" collapsible maximizable>
            <livewire:ralan.pemeriksaan :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
        </x-adminlte-card>
        @if(session()->get('kd_poli') == 'U002' || session()->get('kd_poli') == 'U003')
        <livewire:ralan.odontogram :noRawat=" request()->get('no_rawat')" :noRm="request()->get('no_rm')">
            @endif
            <x-ralan.resep />
            {{-- <livewire:ralan.resep :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')"> --}}
                {{--
                <x-ralan.resume /> --}}
                <livewire:ralan.resume :no-rawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
                {{--
                <x-ralan.catatan :no-rawat="request()->get('no_rawat')" /> --}}
                <livewire:ralan.catatan :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
                <x-ralan.rujuk-internal :no-rawat="request()->get('no_rawat')" />
                {{--
                <x-ralan.permintaan-lab :no-rawat="request()->get('no_rawat')" /> --}}
                <livewire:ralan.permintaan-lab :no-rawat="request()->get('no_rawat')" />
                {{--
                <x-ralan.permintaan-radiologi :no-rawat="request()->get('no_rawat')" /> --}}
                <livewire:ralan.permintaan-radiologi :no-rawat="request()->get('no_rawat')" />
    </div>
</div>

@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
@stop
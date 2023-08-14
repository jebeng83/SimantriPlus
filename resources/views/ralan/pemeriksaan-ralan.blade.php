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
        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pemeriksaan-tab" data-toggle="pill"
                            href="#pemeriksaan-tab-costume" role="tab" aria-controls="pemeriksaan-tab-costume"
                            aria-selected="true">Pemeriksaan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="resep-tab" data-toggle="pill" href="#resep-tab-costume" role="tab"
                            aria-controls="resep-tab-costume" aria-selected="false">Resep</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="resume-tab" data-toggle="pill" href="#resume-tab-costume" role="tab"
                            aria-controls="resume-tab-costume" aria-selected="false">Resume</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="catatan-tab" data-toggle="pill" href="#catatan-tab-costume" role="tab"
                            aria-controls="catatan-tab-costume" aria-selected="false">Catatan</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                    <div class="tab-pane fade active show" id="pemeriksaan-tab-costume" role="tabpanel"
                        aria-labelledby="pemeriksaan-tab">
                        <livewire:ralan.pemeriksaan :noRawat="request()->get('no_rawat')"
                            :noRm="request()->get('no_rm')" />
                    </div>
                    <div class="tab-pane fade" id="resep-tab-costume" role="tabpanel" aria-labelledby="resep-tab">
                        {{--
                        <x-ralan.resep /> --}}
                        <livewire:ralan.resep :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')">
                    </div>
                    <div class="tab-pane fade" id="resume-tab-costume" role="tabpanel" aria-labelledby="resume-tab">
                        Resume
                    </div>
                    <div class="tab-pane fade" id="catatan-tab-costume" role="tabpanel" aria-labelledby="catatan-tab">
                        Catatan
                    </div>
                </div>
            </div>

        </div>
        {{--
        <x-ralan.pemeriksaan :no-rawat="request()->get('no_rawat')" /> --}}
        {{-- <x-adminlte-card title="Penilaian Awal Rehab Medik" theme="info" collapsible maximizable>
            <livewire:ralan.penilaian-rehab-medik :noRawat="request()->get('no_rawat')" />
        </x-adminlte-card> --}}
        {{-- <x-adminlte-card title="Pemeriksaan" theme="info" icon="fas fa-lg fa-bell" collapsible maximizable>
            <livewire:ralan.pemeriksaan :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
        </x-adminlte-card> --}}
        @if(session()->get('kd_poli') == 'U002' || session()->get('kd_poli') == 'U003')
        <livewire:ralan.odontogram :noRawat=" request()->get('no_rawat')" :noRm="request()->get('no_rm')">
            @endif
            {{-- <x-adminlte-card title="Resep" id="resepCard" theme="info" icon="fas fa-lg fa-pills"
                collapsible="collapsed" maximizable>
                <x-ralan.resep />
            </x-adminlte-card> --}}
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
@push('js')
<script>
    $(function () {
        $('#pemeriksaan-tab').on('click', function () {
            alert('pemeriksaan');
        })
    })
</script>
@endpush
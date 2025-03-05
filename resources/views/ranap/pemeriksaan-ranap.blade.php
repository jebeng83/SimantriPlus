@extends('adminlte::page')

@section('title', 'Pemeriksaan Pasien Ranap')

@section('content_header')
  <div class="d-flex flex-row justify-content-between">
    <h1 style="font-family: Tahoma; color: #4B0082;">( CPPT Pasien Inap )</h1>
    <a name="" id="" class="btn btn-danger" href="{{ url('ranap/pasien') }}" role="button">Pasien Inap</a>
</div>
@stop

@section('content')
    <x-ranap.riwayat-ranap :no-rawat="request()->get('no_rawat')" />
    <div class="row">
        <div class="col-md-4">
            <x-ranap.pasien :no-rawat="request()->get('no_rawat')" />
        </div>
        <div class="col-md-8">
            <x-ranap.pemeriksaan-ranap :no-rawat="request()->get('no_rawat')" />
            <x-ranap.resep-ranap />
            <livewire:ranap.permintaan-lab :no-rawat="request()->get('no_rawat')" />
            <livewire:ranap.resume-pasien :no-rawat="request()->get('no_rawat')" />
            <livewire:ranap.catatan-pasien :noRawat="request()->get('no_rawat')" :noRm="request()->get('no_rm')" />
            <!--<livewire:ranap.permintaan-radiologi :no-rawat="request()->get('no_rawat')" -->
            <!--<x-adminlte-card title="Laporan Operasi" icon='fas fa-stethoscope' theme="info" maximizable collapsible="collapsed">-->
            <!--    <livewire:ranap.lap-operasi :no-rawat="request()->get('no_rawat')" -->
            <!--    <livewire:ranap.template-lap-operasi -->
            <!--</x-adminlte-card>-->
            <x-adminlte-card title="SBAR" icon='fas fa-stethoscope' theme="info" maximizable collapsible="collapsed">
                <livewire:ranap.sbar.detail-sbar />
                <livewire:ranap.sbar.table-sbar :noRawat="request()->get('no_rawat')" />
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
    <script> console.log('Hi!'); </script>
@stop
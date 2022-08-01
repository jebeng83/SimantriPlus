@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard Dokter</h1>
@stop

@section('content')
    <x-adminlte-callout theme="info" title="Information">
        <div class="row">
            <div class="col-md-3">
                <x-adminlte-small-box title="{{$totalPasien}}" text="Total Pasien" icon="fas fa-sm fa-user-plus text-primary" theme="gradient-primary" icon-theme="white"/>
            </div>
            <div class="col-md-3">
                <x-adminlte-small-box title="{{$pasienBulanIni}}" text="PASIEN BULAN INI" icon="fas fa-sm fa-user-plus text-primary" theme="gradient-primary" icon-theme="white"/>
            </div>
            <div class="col-md-3">
                <x-adminlte-small-box title="{{$pasienPoliBulanIni}}" text="PASIEN POLI BULAN INI" icon="fas fa-sm fa-user-plus text-primary" theme="gradient-primary" icon-theme="white"/>
            </div>
            <div class="col-md-3">
                <x-adminlte-small-box title="{{$pasienPoliHariIni}}" text="PASIEN POLI HARI INI" icon="fas fa-sm fa-user-plus text-primary" theme="gradient-primary" icon-theme="white"/>
            </div>
        </div>
    </x-adminlte-callout>
    @php
        $config = [
            'order' => [[2, 'asc']],
            'columns' => [null, null, null, ['orderable' => true]],
        ];
    @endphp
    <div class="row">
        <div class="col-md-6">
            <x-adminlte-callout theme="info" title="Pasien {{$poliklinik}} Paling Aktif">
                <x-adminlte-datatable id="table5" :heads="$headPasienAktif" theme="light" striped hoverable>
                    @foreach($pasienAktif as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td>{!! $cell !!}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-callout>
        </div>
        <div class="col-md-6">
            <x-adminlte-callout theme="info" title="Antrian 10 Pasien Terakhir {{$poliklinik}}">
                <x-adminlte-datatable id="table6" :heads="$headPasienTerakhir" theme="light" striped hoverable>
                    @foreach($pasienTerakhir as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td>{!! $cell !!}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </x-adminlte-datatable>
            </x-adminlte-callout>
        </div>
    </div>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop

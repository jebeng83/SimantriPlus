@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Selamat Datang, </br>{{$nm_dokter}}</h1>
@stop

@section('content')
        <div class="row">
            <div class="col-md-3">
                <x-adminlte-info-box title="TOTAL PASIEN" text="{{$totalPasien}}" icon="fas fa-lg fa-users" theme="primary"/>
            </div>
            <div class="col-md-3">
                <x-adminlte-info-box title="PASIEN BULAN INI" text="{{$pasienBulanIni}}" icon="fas fa-lg fa-clipboard" theme="success"/>
            </div>
            <div class="col-md-3">
                <x-adminlte-info-box title="PASIEN POLI BULAN INI" text="{{$pasienPoliBulanIni}}" icon="fas fa-lg fa-hospital" theme="danger"/>
            </div>
            <div class="col-md-3">
                <x-adminlte-info-box title="PASIEN POLI HARI INI" text="{{$pasienPoliHariIni}}" icon="fas fa-lg fa-stethoscope" theme="info"/>
            </div>
        </div>

        <x-adminlte-card title="{{ ucwords(strtolower($poliklinik))}}" theme="info" theme-mode="outline">
            @php 
                $bulan = [];
                $jumlah = [];
                foreach ($statistikKunjungan as $key => $value) {
                    $bulan[] = $value->bulan;
                    $jumlah[] = intval($value->jumlah);
                }
            @endphp
            <canvas id="chartKunjungan" height="100px"></canvas>
        </x-adminlte-card>
        
    @php
        $config = [
            'order' => [[2, 'asc']],
            'columns' => [null, null, null, ['orderable' => true]],
        ];
    @endphp
    <div class="row">
        <div class="col-md-6">
            <x-adminlte-card theme="info" title="Pasien {{ ucwords(strtolower($poliklinik))}} Paling Aktif" theme-mode="outline">
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
            <x-adminlte-card theme="info" title="Antrian 10 Pasien Terakhir {{ ucwords(strtolower($poliklinik))}}" theme-mode="outline">
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
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script>
        var colors = [];
        var i = 0;
        var dynamicColors = function() {
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            return "rgb(" + r + "," + g + "," + b + ")";
        };

        for(i in  {!! json_encode($jumlah) !!}){
            colors.push(dynamicColors());
        }
        
        const ctx = document.getElementById('chartKunjungan').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($bulan) !!},
                datasets: [{
                    label: 'Jumlah Kunjungan ' + "{{ ucwords(strtolower($poliklinik))}}",
                    data: {!! json_encode($jumlah) !!},
                    backgroundColor: colors,
                    borderColor:'rgba(200, 200, 200, 0.75)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@stop

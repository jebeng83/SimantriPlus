@extends('adminlte::page')
@extends('layouts.global-styles')

@section('title', 'Dashboard')

@section('content_header')
<h1>Selamat Datang, </br>{{ $nm_dokter != 'Dokter tidak ditemukan' ? $nm_dokter : 'Dokter' }}</h1>

@stop

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="info-box premium-info-box bg-primary">
            <span class="info-box-icon"><i class="fas fa-lg fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">TOTAL PASIEN</span>
                <span class="info-box-number">{{ number_format($totalPasien) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box premium-info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-lg fa-clipboard"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">PASIEN BULAN INI</span>
                <span class="info-box-number">{{ number_format($pasienBulanIni) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box premium-info-box bg-danger">
            <span class="info-box-icon"><i class="fas fa-lg fa-hospital"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">PASIEN POLI BULAN INI</span>
                <span class="info-box-number">{{ number_format($pasienPoliBulanIni) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box premium-info-box bg-info">
            <span class="info-box-icon"><i class="fas fa-lg fa-stethoscope"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">PASIEN POLI HARI INI</span>
                <span class="info-box-number">{{ number_format($pasienPoliHariIni) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card premium-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-bar mr-2"></i>
            {{ $poliklinik != 'Poliklinik tidak ditemukan' ? ucwords(strtolower($poliklinik)) : 'Poliklinik' }}
        </h3>
    </div>
    <div class="card-body">
        @php
        $bulan = [];
        $jumlah = [];
        foreach ($statistikKunjungan as $key => $value) {
        $bulan[] = $value->bulan;
        $jumlah[] = intval($value->jumlah);
        }
        @endphp
        <div class="chart-container">
            <canvas id="chartKunjungan" height="100px"></canvas>
        </div>
    </div>
</div>

@php
$config = [
'order' => [[2, 'asc']],
'columns' => [null, null, null, ['orderable' => true]],
];
@endphp
<div class="row">
    <div class="col-md-6">
        <div class="card premium-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-check mr-2"></i>
                    Pasien {{ $poliklinik != 'Poliklinik tidak ditemukan' ? ucwords(strtolower($poliklinik)) :
                    'Poliklinik' }} Paling Aktif
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table5" class="table premium-table">
                        <thead>
                            <tr>
                                @foreach($headPasienAktif as $head)
                                <th>{{ $head }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pasienAktif as $row)
                            <tr>
                                @foreach($row as $cell)
                                <td>{!! $cell !!}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card premium-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-clock mr-2"></i>
                    Antrian 10 Pasien Terakhir {{ $poliklinik != 'Poliklinik tidak ditemukan' ?
                    ucwords(strtolower($poliklinik)) : 'Poliklinik' }}
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table6" class="table premium-table">
                        <thead>
                            <tr>
                                @foreach($headPasienTerakhir as $head)
                                <th>{{ $head }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pasienTerakhir as $row)
                            <tr>
                                @foreach($row as $cell)
                                <td>{!! $cell !!}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugin', true)

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/dashboard-premium.css') }}">
<style>
    .content-wrapper {
        background-color: #f7f9fc;
        padding: 20px;
    }

    .card {
        margin-bottom: 25px;
    }
</style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // DataTables initialization
        $('#table5').DataTable({
            responsive: true,
            pageLength: 5,
            lengthChange: false,
            searching: false,
            language: {
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: {
                    previous: "<i class='fas fa-angle-left'></i>",
                    next: "<i class='fas fa-angle-right'></i>"
                }
            }
        });
        
        $('#table6').DataTable({
            responsive: true,
            pageLength: 5,
            lengthChange: false,
            searching: false,
            language: {
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: {
                    previous: "<i class='fas fa-angle-left'></i>",
                    next: "<i class='fas fa-angle-right'></i>"
                }
            }
        });
        
        // Chart initialization
        var ctx = document.getElementById('chartKunjungan');
        if (ctx) {
            // Create nice color palette
            var colors = [
                'rgba(79, 91, 218, 0.7)',
                'rgba(92, 104, 231, 0.7)',
                'rgba(105, 117, 244, 0.7)',
                'rgba(118, 130, 257, 0.7)',
                'rgba(131, 143, 270, 0.7)',
                'rgba(144, 156, 283, 0.7)',
                'rgba(157, 169, 296, 0.7)',
                'rgba(170, 182, 309, 0.7)',
                'rgba(183, 195, 322, 0.7)',
                'rgba(196, 208, 335, 0.7)',
                'rgba(209, 221, 348, 0.7)',
                'rgba(222, 234, 361, 0.7)'
            ];
            
            var borderColors = [
                'rgba(79, 91, 218, 1)',
                'rgba(92, 104, 231, 1)',
                'rgba(105, 117, 244, 1)',
                'rgba(118, 130, 257, 1)',
                'rgba(131, 143, 270, 1)',
                'rgba(144, 156, 283, 1)',
                'rgba(157, 169, 296, 1)',
                'rgba(170, 182, 309, 1)',
                'rgba(183, 195, 322, 1)',
                'rgba(196, 208, 335, 1)',
                'rgba(209, 221, 348, 1)',
                'rgba(222, 234, 361, 1)'
            ];
            
            const myChart = new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($bulan) !!},
                    datasets: [{
                        label: 'Jumlah Kunjungan ' + "{{ $poliklinik != 'Poliklinik tidak ditemukan' ? ucwords(strtolower($poliklinik)) : 'Poliklinik' }}",
                        data: {!! json_encode($jumlah) !!},
                        backgroundColor: colors,
                        borderColor: borderColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    family: 'Poppins',
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleFont: {
                                family: 'Poppins',
                                size: 13
                            },
                            bodyFont: {
                                family: 'Poppins',
                                size: 12
                            },
                            padding: 10,
                            cornerRadius: 5
                        }
                    }
                }
            });
        }
    });
</script>
@stop
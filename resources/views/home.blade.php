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
<style>
    .content-wrapper {
        background-color: #f7f9fc;
        padding: 20px;
    }

    .card {
        margin-bottom: 25px;
    }

    /* Style info boxes */
    .premium-info-box {
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 1.5rem;
        border: none !important;
    }

    .premium-info-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15);
    }

    .premium-info-box .info-box-icon {
        border-radius: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .premium-info-box .info-box-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 15px;
    }

    .premium-info-box .info-box-text {
        font-size: 0.9rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .premium-info-box .info-box-number {
        font-size: 1.7rem;
        font-weight: 700;
    }

    /* Force navbar header to be black */
    .main-header {
        background: #000000 !important;
        color: white !important;
        background-color: #000000 !important;
    }

    /* Force sidebar to be black */
    .main-sidebar {
        background: #000000 !important;
        background-color: #000000 !important;
        height: 100% !important;
        /* Memastikan sidebar memiliki tinggi penuh */
        min-height: 100vh !important;
        /* Memastikan sidebar minimal setinggi viewport */
        position: fixed !important;
        /* Pastikan sidebar tetap di posisinya */
    }

    /* Override sidebar menu items */
    .nav-sidebar .nav-item .nav-link {
        color: white !important;
    }

    .nav-sidebar .nav-item .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }

    .nav-sidebar .nav-item .nav-link.active {
        background-color: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
    }

    /* Fix sidebar height issue */
    .wrapper {
        min-height: 100vh !important;
    }

    /* Force sidebar to have full height */
    .sidebar {
        height: auto !important;
        min-height: 100% !important;
        padding-bottom: 100px !important;
        /* Tambahkan padding di bawah untuk menghindari terpotong */
    }

    /* Extra styling untuk halaman home */
    body .main-sidebar,
    html .main-sidebar,
    #app .main-sidebar,
    .wrapper .main-sidebar,
    body[data-route^="/home"] .main-sidebar,
    body[data-route="home"] .main-sidebar,
    [data-route^="/home"] .main-sidebar,
    [data-route="home"] .main-sidebar {
        background: #000000 !important;
        background-color: #000000 !important;
        height: 100% !important;
        min-height: 100vh !important;
    }

    body .main-header,
    html .main-header,
    #app .main-header,
    .wrapper .main-header,
    body[data-route^="/home"] .main-header,
    body[data-route="home"] .main-header,
    [data-route^="/home"] .main-header,
    [data-route="home"] .main-header {
        background: #000000 !important;
        background-color: #000000 !important;
    }

    /* Override any gradient or background images */
    .main-header[style*="background"],
    .main-sidebar[style*="background"],
    .sidebar[style*="background"] {
        background: #000000 !important;
        background-image: none !important;
        background-color: #000000 !important;
    }

    /* Mengatasi potongan sidebar */
    .layout-fixed .wrapper .sidebar {
        height: auto !important;
        min-height: calc(100vh - 3.5rem) !important;
    }

    /* Fix untuk AdminLTE versi tertentu */
    @supports not (-webkit-touch-callout: none) {
        .layout-fixed .wrapper .sidebar {
            height: auto !important;
            min-height: 100vh !important;
        }
    }

    /* Fix sidebar navigation */
    .nav-sidebar {
        padding-bottom: 50px !important;
    }
</style>
@stop

@section('js')
<script>
    // Set data-route attribute untuk membantu CSS selectors
    document.body.setAttribute('data-route', '/home');
    
    document.addEventListener('DOMContentLoaded', function() {
        // Set background hitam pada navbar dan sidebar secara langsung dengan JavaScript
        document.querySelectorAll('.main-sidebar, .sidebar, .main-header').forEach(function(element) {
            element.style.backgroundColor = '#000000';
            element.style.background = '#000000';
            // Hapus semua background image atau gradient
            element.style.backgroundImage = 'none';
        });
        
        // Perbaikan untuk tinggi sidebar
        const sidebar = document.querySelector('.main-sidebar');
        if (sidebar) {
            sidebar.style.height = '100%';
            sidebar.style.minHeight = '100vh';
        }
        
        const sidebarInner = document.querySelector('.sidebar');
        if (sidebarInner) {
            sidebarInner.style.height = 'auto';
            sidebarInner.style.minHeight = '100%';
            sidebarInner.style.paddingBottom = '100px';
        }
        
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
                                    family: "'Poppins', sans-serif",
                                    size: 12
                                },
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                family: "'Poppins', sans-serif",
                                size: 13
                            },
                            bodyFont: {
                                family: "'Poppins', sans-serif",
                                size: 12
                            },
                            padding: 12
                        }
                    }
                }
            });
        }
    });
</script>
@stop
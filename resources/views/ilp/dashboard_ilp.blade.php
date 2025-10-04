@extends('adminlte::page')

@section('title', 'Dashboard PWS - Pemantauan Wilayah Setempat')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Dashboard PWS - Pemantauan Wilayah Setempat</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('ilp.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">PWS</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('ilp.dashboard.pws') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="desa" class="form-label">Desa</label>
                            <select class="form-select" id="desa" name="desa">
                                <option value="">Semua Desa</option>
                                @foreach($daftar_desa as $desa)
                                    <option value="{{ $desa }}" {{ request('desa') == $desa ? 'selected' : '' }}>
                                        {{ $desa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="posyandu" class="form-label">Posyandu</label>
                            <select class="form-select" id="posyandu" name="posyandu">
                                <option value="">Semua Posyandu</option>
                                @foreach($daftar_posyandu as $posyandu)
                                    <option value="{{ $posyandu }}" {{ request('posyandu') == $posyandu ? 'selected' : '' }}>
                                        {{ $posyandu }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="periode" class="form-label">Periode</label>
                            <select class="form-select" id="periode" name="periode">
                                <option value="bulan_ini" {{ request('periode', 'bulan_ini') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="3_bulan" {{ request('periode') == '3_bulan' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                                <option value="6_bulan" {{ request('periode') == '6_bulan' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                                <option value="tahun_ini" {{ request('periode') == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Total Skrining">Total Skrining</h5>
                            <h3 class="my-2 py-1">{{ number_format($summary['total_skrining']) }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i></span>
                                Periode {{ ucfirst(str_replace('_', ' ', request('periode', 'bulan_ini'))) }}
                            </p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="total-skrining-chart" data-colors="#727cf5"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Risiko Tinggi">Risiko Tinggi</h5>
                            <h3 class="my-2 py-1">{{ number_format($summary['risiko_tinggi']) }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-danger me-2"><i class="mdi mdi-arrow-up-bold"></i></span>
                                {{ $summary['total_skrining'] > 0 ? number_format(($summary['risiko_tinggi'] / $summary['total_skrining']) * 100, 1) : 0 }}%
                            </p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="risiko-tinggi-chart" data-colors="#f77e53"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Risiko Sedang">Risiko Sedang</h5>
                            <h3 class="my-2 py-1">{{ number_format($summary['risiko_sedang']) }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-warning me-2"><i class="mdi mdi-arrow-up-bold"></i></span>
                                {{ $summary['total_skrining'] > 0 ? number_format(($summary['risiko_sedang'] / $summary['total_skrining']) * 100, 1) : 0 }}%
                            </p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="risiko-sedang-chart" data-colors="#ffbc00"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="text-muted fw-normal mt-0 text-truncate" title="Risiko Rendah">Risiko Rendah</h5>
                            <h3 class="my-2 py-1">{{ number_format($summary['risiko_rendah']) }}</h3>
                            <p class="mb-0 text-muted">
                                <span class="text-success me-2"><i class="mdi mdi-arrow-down-bold"></i></span>
                                {{ $summary['total_skrining'] > 0 ? number_format(($summary['risiko_rendah'] / $summary['total_skrining']) * 100, 1) : 0 }}%
                            </p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div id="risiko-rendah-chart" data-colors="#0acf97"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <!-- Distribusi Risiko PKG -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Distribusi Risiko PKG</h4>
                    <div id="distribusi-risiko-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Trend Skrining Bulanan -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Trend Skrining Bulanan</h4>
                    <div id="trend-skrining-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analisis per Posyandu -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Analisis per Posyandu</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Posyandu</th>
                                    <th>Desa</th>
                                    <th>Total Skrining</th>
                                    <th>Risiko Tinggi</th>
                                    <th>Risiko Sedang</th>
                                    <th>Risiko Rendah</th>
                                    <th>% Risiko Tinggi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($analisis_pkg as $data)
                                <tr>
                                    <td>{{ $data->nama_posyandu }}</td>
                                    <td>{{ $data->desa }}</td>
                                    <td>{{ number_format($data->total_skrining) }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ number_format($data->risiko_tinggi) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ number_format($data->risiko_sedang) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ number_format($data->risiko_rendah) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $persen_tinggi = $data->total_skrining > 0 ? ($data->risiko_tinggi / $data->total_skrining) * 100 : 0;
                                        @endphp
                                        {{ number_format($persen_tinggi, 1) }}%
                                    </td>
                                    <td>
                                        @if($persen_tinggi >= 30)
                                            <span class="badge bg-danger">Perlu Perhatian</span>
                                        @elseif($persen_tinggi >= 15)
                                            <span class="badge bg-warning">Waspada</span>
                                        @else
                                            <span class="badge bg-success">Baik</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data skrining PKG</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Faktor Risiko -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Faktor Risiko Utama</h4>
                    <div id="faktor-risiko-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Distribusi Umur -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Distribusi Umur Pasien</h4>
                    <div id="distribusi-umur-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/vendor/apexcharts.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Data untuk charts
    var distribusiRisikoData = @json($chart_data['distribusi_risiko']);
    var trendSkriningData = @json($chart_data['trend_skrining']);
    var faktorRisikoData = @json($chart_data['faktor_risiko']);
    var distribusiUmurData = @json($chart_data['distribusi_umur']);

    // Chart Distribusi Risiko (Pie Chart)
    var distribusiRisikoOptions = {
        series: [distribusiRisikoData.risiko_tinggi, distribusiRisikoData.risiko_sedang, distribusiRisikoData.risiko_rendah],
        chart: {
            type: 'pie',
            height: 350
        },
        labels: ['Risiko Tinggi', 'Risiko Sedang', 'Risiko Rendah'],
        colors: ['#f77e53', '#ffbc00', '#0acf97'],
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };
    var distribusiRisikoChart = new ApexCharts(document.querySelector("#distribusi-risiko-chart"), distribusiRisikoOptions);
    distribusiRisikoChart.render();

    // Chart Trend Skrining (Line Chart)
    var trendSkriningOptions = {
        series: [{
            name: 'Total Skrining',
            data: trendSkriningData.map(item => item.total)
        }],
        chart: {
            type: 'line',
            height: 350,
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        colors: ['#727cf5'],
        xaxis: {
            categories: trendSkriningData.map(item => item.bulan)
        },
        yaxis: {
            title: {
                text: 'Jumlah Skrining'
            }
        }
    };
    var trendSkriningChart = new ApexCharts(document.querySelector("#trend-skrining-chart"), trendSkriningOptions);
    trendSkriningChart.render();

    // Chart Faktor Risiko (Bar Chart)
    var faktorRisikoOptions = {
        series: [{
            name: 'Jumlah Kasus',
            data: faktorRisikoData.map(item => item.jumlah)
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: true,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: faktorRisikoData.map(item => item.faktor)
        },
        yaxis: {
            title: {
                text: 'Faktor Risiko'
            }
        },
        fill: {
            opacity: 1
        },
        colors: ['#f77e53']
    };
    var faktorRisikoChart = new ApexCharts(document.querySelector("#faktor-risiko-chart"), faktorRisikoOptions);
    faktorRisikoChart.render();

    // Chart Distribusi Umur (Column Chart)
    var distribusiUmurOptions = {
        series: [{
            name: 'Jumlah Pasien',
            data: distribusiUmurData.map(item => item.jumlah)
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                columnWidth: '50%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: distribusiUmurData.map(item => item.kelompok_umur)
        },
        yaxis: {
            title: {
                text: 'Jumlah Pasien'
            }
        },
        fill: {
            opacity: 1
        },
        colors: ['#0acf97']
    };
    var distribusiUmurChart = new ApexCharts(document.querySelector("#distribusi-umur-chart"), distribusiUmurOptions);
    distribusiUmurChart.render();

    // Filter dependencies
    $('#desa').change(function() {
        var desa = $(this).val();
        if (desa) {
            // Update posyandu options based on selected desa
            $.get('{{ route("ilp.dashboard") }}', {desa: desa, get_posyandu_by_desa: true}, function(response) {
                var posyanduSelect = $('#posyandu');
                posyanduSelect.empty();
                posyanduSelect.append('<option value="">Semua Posyandu</option>');
                $.each(response.daftar_posyandu, function(index, posyandu) {
                    posyanduSelect.append('<option value="' + posyandu + '">' + posyandu + '</option>');
                });
            });
        } else {
            // Reset posyandu to show all
            location.reload();
        }
    });
});
</script>
@endpush
@endsection
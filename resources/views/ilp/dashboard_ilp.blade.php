@extends('adminlte::page')

@section('title', 'Dashboard PWS - Pemantauan Wilayah Setempat')

@section('css')
<style>
    :root {
        --brand-primary: #727cf5;
        --brand-success: #0acf97;
        --brand-warning: #ffbc00;
        --brand-danger: #f77e53;
        --surface: #ffffff;
    }

    .elegant-card { border: none; border-radius: 14px; box-shadow: 0 8px 24px rgba(31,45,61,.08); transition: transform .25s ease, box-shadow .25s ease; background: var(--surface); }
    .elegant-card:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(31,45,61,.12); }

    .header-title { font-weight: 600; letter-spacing: .2px; }

    .table thead.table-dark { background: linear-gradient(90deg, #343a40 0%, #2c3136 100%); border-top-left-radius: 12px; border-top-right-radius: 12px; }
    .table thead.table-dark th { border: none !important; font-weight: 600; }
    .table tbody tr { transition: background-color .2s ease; }
    .table-hover tbody tr:hover { background-color: rgba(114,124,245,.08); }

    .reveal-on-scroll { opacity: 0; transform: translateY(16px); transition: opacity .6s ease, transform .6s ease; will-change: opacity, transform; }
    .reveal-on-scroll.in-view { opacity: 1; transform: translateY(0); }

    .chart-container { min-height: 350px; }

    .badge { box-shadow: 0 2px 6px rgba(0,0,0,.08); }
    .metric-title { color: #6c757d; font-weight: 500; }
    .metric-value { font-weight: 700; letter-spacing: .4px; }

    /* Skeleton loading */
    .loading-row td { padding: 24px; }
    .skeleton { position: relative; overflow: hidden; background-color: #e9ecef; border-radius: 8px; }
    .skeleton::after { content: ''; position: absolute; left: -150px; top: 0; height: 100%; width: 150px; background: linear-gradient(90deg, transparent, rgba(255,255,255,.4), transparent); animation: shimmer 1.2s infinite; }
    @keyframes shimmer { 0% { transform: translateX(0); } 100% { transform: translateX(300%); } }
</style>
@endsection

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
            <div class="card elegant-card reveal-on-scroll">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="metric-title mt-0 text-truncate" title="Total Skrining">Total Skrining</h5>
                            <h3 class="my-2 py-1 metric-value count-up" data-target="{{ $summary['total_skrining'] }}">0</h3>
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
            <div class="card elegant-card reveal-on-scroll">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="metric-title mt-0 text-truncate" title="Risiko Tinggi">Risiko Tinggi</h5>
                            <h3 class="my-2 py-1 metric-value count-up" data-target="{{ $summary['risiko_tinggi'] }}">0</h3>
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
            <div class="card elegant-card reveal-on-scroll">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="metric-title mt-0 text-truncate" title="Risiko Sedang">Risiko Sedang</h5>
                            <h3 class="my-2 py-1 metric-value count-up" data-target="{{ $summary['risiko_sedang'] }}">0</h3>
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
            <div class="card elegant-card reveal-on-scroll">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="metric-title mt-0 text-truncate" title="Risiko Rendah">Risiko Rendah</h5>
                            <h3 class="my-2 py-1 metric-value count-up" data-target="{{ $summary['risiko_rendah'] }}">0</h3>
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
            <div class="card elegant-card reveal-on-scroll">
                <div class="card-body">
                    <h4 class="header-title mb-3">Distribusi Risiko PKG</h4>
                    <div id="distribusi-risiko-chart" class="chart-container reveal-on-scroll" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Trend Skrining Bulanan -->
        <div class="col-xl-6">
            <div class="card elegant-card reveal-on-scroll">
                <div class="card-body">
                    <h4 class="header-title mb-3">Trend Skrining Bulanan</h4>
                    <div id="trend-skrining-chart" class="chart-container reveal-on-scroll" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analisis per Posyandu -->
    <div class="row">
        <div class="col-12">
            <div class="card elegant-card reveal-on-scroll" id="analisis-posyandu-section">
                <div class="card-body">
                    <h4 class="header-title mb-3">Analisis per Posyandu</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="analisis-pkg-table">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-white">Posyandu</th>
                                    <th class="text-white">Desa</th>
                                    <th class="text-white">Total Skrining</th>
                                    <th class="text-white">Laki-laki</th>
                                    <th class="text-white">Perempuan</th>
                                    <th class="text-white">Risiko Tinggi</th>
                                    <th class="text-white">Risiko Sedang</th>
                                    <th class="text-white">Risiko Rendah</th>
                                    <th class="text-white">% Risiko Tinggi</th>
                                    <th class="text-white">TD ≥ 140</th>
                                    <th class="text-white">GDS ≥ 200</th>
                                    <th class="text-white">GDP ≥ 126</th>
                                    <th class="text-white">BMI ≥ 30</th>
                                    <th class="text-white">Status</th>
                                </tr>
                            </thead>
                            <tbody id="analisis-pkg-body">
                                <tr class="loading-row">
                                    <td colspan="14" class="text-center">
                                        Memuat data analisis…
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="analisis-pkg-pagination" class="d-flex justify-content-between align-items-center mt-2">
                        <div class="text-muted small" id="analisis-pkg-summary"></div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item"><a class="page-link" href="#" data-page="prev">Prev</a></li>
                                <li class="page-item"><a class="page-link" href="#" data-page="next">Next</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Faktor Risiko -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card elegant-card reveal-on-scroll">
                <div class="card-body">
                    <h4 class="header-title mb-3">Faktor Risiko Utama</h4>
                    <div id="faktor-risiko-chart" class="chart-container reveal-on-scroll" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Distribusi Umur -->
        <div class="col-xl-6">
            <div class="card elegant-card reveal-on-scroll">
                <div class="card-body">
                    <h4 class="header-title mb-3">Distribusi CKG Menurut Sasaran Usia</h4>
                    <div id="distribusi-umur-chart" class="chart-container reveal-on-scroll" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- Use CDN for ApexCharts since local asset does not exist -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(document).ready(function() {
    // Motion: reveal on scroll
    const revealEls = document.querySelectorAll('.reveal-on-scroll');
    const io = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                io.unobserve(entry.target);
            }
        });
    }, {threshold: 0.15});
    revealEls.forEach(el => io.observe(el));

    // Count-up animation for summary metrics
    const formatNumber = (n) => n.toLocaleString('id-ID');
    document.querySelectorAll('.count-up').forEach(el => {
        const target = parseInt(el.getAttribute('data-target')) || 0;
        const duration = 1200; // ms
        const start = 0;
        const startTime = performance.now();
        const step = (now) => {
            const progress = Math.min((now - startTime) / duration, 1);
            const value = Math.floor(progress * (target - start) + start);
            el.textContent = formatNumber(value);
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    });
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
            height: 350,
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: { enabled: true, delay: 200 },
                dynamicAnimation: { enabled: true, speed: 350 }
            },
            toolbar: { show: false }
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
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: { enabled: true, delay: 150 },
                dynamicAnimation: { enabled: true, speed: 350 }
            },
            toolbar: { show: false },
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
            height: 350,
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: { enabled: true, delay: 150 },
                dynamicAnimation: { enabled: true, speed: 350 }
            },
            toolbar: { show: false }
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
            height: 350,
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: { enabled: true, delay: 150 },
                dynamicAnimation: { enabled: true, speed: 350 }
            },
            toolbar: { show: false }
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
                posyanduSelect.val('');
            });
        } else {
            // Reset posyandu to show all (without reloading, so filter can submit all desa)
            var posyanduSelect = $('#posyandu');
            posyanduSelect.empty();
            posyanduSelect.append('<option value="">Semua Posyandu</option>');
            @foreach($daftar_posyandu as $posyandu)
                posyanduSelect.append('<option value="{{ $posyandu }}">{{ $posyandu }}</option>');
            @endforeach
            posyanduSelect.val('');
        }
    });

    // =============================
    // Analisis per Posyandu (Async + Pagination + Lazy Loading)
    // =============================
    var analisisLoaded = false;
    var analisisMeta = { total: 0, per_page: 10, current_page: 1, last_page: 1 };
    var $tbody = $('#analisis-pkg-body');
    var $summary = $('#analisis-pkg-summary');
    var $pagination = $('#analisis-pkg-pagination');

    function numberFormat(n){ return (n||0).toLocaleString('id-ID'); }
    function statusBadge(persen){
        if (persen >= 30) return '<span class="badge bg-danger">Perlu Perhatian</span>';
        if (persen >= 15) return '<span class="badge bg-warning text-dark">Waspada</span>';
        return '<span class="badge bg-success">Baik</span>';
    }

    function renderRows(items){
        if (!items || !items.length){
            $tbody.html('<tr><td colspan="14" class="text-center">Tidak ada data skrining PKG</td></tr>');
            return;
        }
        var rows = items.map(function(item){
            var persen = item.persen_tinggi || 0;
            return '<tr>'+
                '<td>'+(item.nama_posyandu || '-')+'</td>'+
                '<td>'+(item.desa || '-')+'</td>'+
                '<td>'+numberFormat(item.total_skrining)+'</td>'+
                '<td>'+numberFormat(item.laki_laki || 0)+'</td>'+
                '<td>'+numberFormat(item.perempuan || 0)+'</td>'+
                '<td><span class="badge bg-danger">'+numberFormat(item.risiko_tinggi)+'</span></td>'+
                '<td><span class="badge bg-warning text-dark">'+numberFormat(item.risiko_sedang)+'</span></td>'+
                '<td><span class="badge bg-success">'+numberFormat(item.risiko_rendah)+'</span></td>'+
                '<td>'+persen.toFixed(1)+'%</td>'+
                '<td><span class="badge bg-danger">'+numberFormat(item.td_ge_140)+'</span></td>'+
                '<td><span class="badge bg-danger">'+numberFormat(item.gds_ge_200)+'</span></td>'+
                '<td><span class="badge bg-danger">'+numberFormat(item.gdp_ge_126)+'</span></td>'+
                '<td><span class="badge bg-danger">'+numberFormat(item.bmi_ge_30)+'</span></td>'+
                '<td>'+statusBadge(persen)+'</td>'+
            '</tr>';
        }).join('');
        $tbody.html(rows);
    }

    function renderPagination(meta){
        analisisMeta = meta || analisisMeta;
        var cur = analisisMeta.current_page, last = analisisMeta.last_page;
        var start = Math.max(1, cur - 2), end = Math.min(last, cur + 2);
        var pages = '';
        for (var i=start;i<=end;i++){
            pages += '<li class="page-item '+(i===cur?'active':'')+'"><a class="page-link" href="#" data-page="'+i+'">'+i+'</a></li>';
        }
        $pagination.find('ul.pagination').html(
            '<li class="page-item '+(cur<=1?'disabled':'')+'"><a class="page-link" href="#" data-page="prev">Prev</a></li>'+
            pages+
            '<li class="page-item '+(cur>=last?'disabled':'')+'"><a class="page-link" href="#" data-page="next">Next</a></li>'
        );
        $summary.text('Menampilkan halaman '+cur+' dari '+last+' • Total '+numberFormat(analisisMeta.total)+' posyandu');
    }

    function fetchAnalisisPkg(page){
        page = page || 1;
        var params = {
            desa: $('#desa').val() || '',
            posyandu: $('#posyandu').val() || '',
            periode: $('#periode').val() || 'bulan_ini',
            page: page,
            per_page: 10
        };
        $tbody.html('<tr class="loading-row"><td colspan="14" class="text-center"><div class="skeleton" style="height:16px"></div></td></tr>');
        $.get('{{ route("ilp.dashboard.pws.analisis") }}', params, function(resp){
            renderRows(resp.data);
            renderPagination(resp.meta);
            analisisLoaded = true;
        }).fail(function(){
            $tbody.html('<tr><td colspan="14" class="text-center text-danger">Gagal memuat data</td></tr>');
        });
    }

    // Lazy load ketika section terlihat
    var analisisSection = document.getElementById('analisis-posyandu-section');
    if (analisisSection){
        const ioAnalisis = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !analisisLoaded){
                    fetchAnalisisPkg(1);
                    ioAnalisis.unobserve(analisisSection);
                }
            });
        }, {threshold: 0.2});
        ioAnalisis.observe(analisisSection);
    }

    // Pagination click handler
    $pagination.on('click', 'a.page-link', function(e){
        e.preventDefault();
        var p = $(this).data('page');
        var cur = analisisMeta.current_page;
        var nextPage = cur;
        if (p === 'prev') nextPage = Math.max(1, cur - 1);
        else if (p === 'next') nextPage = Math.min(analisisMeta.last_page, cur + 1);
        else nextPage = parseInt(p, 10) || 1;
        fetchAnalisisPkg(nextPage);
    });
});
</script>
@endsection
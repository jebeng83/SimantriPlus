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

    .elegant-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(31, 45, 61, .08);
        transition: transform .25s ease, box-shadow .25s ease;
        background: var(--surface);
    }

    .elegant-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(31, 45, 61, .12);
    }

    .header-title {
        font-weight: 600;
        letter-spacing: .2px;
    }

    .table thead.table-dark {
        background: linear-gradient(90deg, #343a40 0%, #2c3136 100%);
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .table thead.table-dark th {
        border: none !important;
        font-weight: 600;
    }

    .table tbody tr {
        transition: background-color .2s ease;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(114, 124, 245, .08);
    }

    .reveal-on-scroll {
        opacity: 0;
        transform: translateY(16px);
        transition: opacity .6s ease, transform .6s ease;
        will-change: opacity, transform;
    }

    .reveal-on-scroll.in-view {
        opacity: 1;
        transform: translateY(0);
    }

    .chart-container {
        min-height: 350px;
    }

    .badge {
        box-shadow: 0 2px 6px rgba(0, 0, 0, .08);
    }

    .metric-title {
        color: #6c757d;
        font-weight: 500;
    }

    .metric-value {
        font-weight: 700;
        letter-spacing: .4px;
    }

    /* Skeleton loading */
    .loading-row td {
        padding: 24px;
    }

    .skeleton {
        position: relative;
        overflow: hidden;
        background-color: #e9ecef;
        border-radius: 8px;
    }

    .skeleton::after {
        content: '';
        position: absolute;
        left: -150px;
        top: 0;
        height: 100%;
        width: 150px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .4), transparent);
        animation: shimmer 1.2s infinite;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(300%);
        }
    }

    /* Enhanced theme */
    :root {
        --bg-gradient: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        --bg-soft: linear-gradient(180deg, rgba(114, 124, 245, .08), rgba(114, 124, 245, .02));
    }

    .hero-banner {
        background: var(--bg-gradient);
        color: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 12px 28px rgba(31, 45, 61, .18);
        position: relative;
        overflow: hidden;
    }

    .hero-banner::after {
        content: '';
        position: absolute;
        right: -80px;
        bottom: -80px;
        width: 220px;
        height: 220px;
        background: rgba(255, 255, 255, .15);
        filter: blur(6px);
        border-radius: 50%;
    }

    .hero-kicker {
        opacity: .9;
        font-weight: 500;
    }

    .hero-stat {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, .15);
        padding: 8px 12px;
        border-radius: 12px;
        margin-right: 10px;
    }

    .hero-stat .val {
        font-weight: 700;
    }

    .filter-card {
        border-radius: 16px;
        background: var(--bg-soft);
        border: 1px solid rgba(114, 124, 245, .12);
    }

    .filter-label {
        font-weight: 600;
        color: #5b6c8c;
    }

    .select-decor {
        border-radius: 12px;
    }

    .filter-card .form-label {
        margin-bottom: 0;
    }

    .filter-card .form-select,
    .filter-card .btn {
        height: 40px;
    }

    .filter-symmetric {
        align-items: center;
    }

    /* Transparent card variant for charts */
    .transparent-card {
        background: transparent !important;
        border: 1px dashed rgba(114, 124, 245, .24) !important;
        box-shadow: none;
    }

    /* Blue translucent variant for metric card */
    .metric-card.transparent-blue {
        background: rgba(114, 124, 245, .12);
        border: 1px solid rgba(114, 124, 245, .22);
        box-shadow: 0 8px 20px rgba(114, 124, 245, .12);
    }

    .metric-card.transparent-blue .accent {
        background: rgba(114, 124, 245, .6) !important;
    }

    .metric-card.transparent-blue .metric-icon {
        background: rgba(114, 124, 245, .9) !important;
    }

    /* Orange (danger) translucent */
    .metric-card.transparent-danger {
        background: rgba(247, 126, 83, .12);
        border: 1px solid rgba(247, 126, 83, .22);
        box-shadow: 0 8px 20px rgba(247, 126, 83, .12);
    }

    .metric-card.transparent-danger .accent {
        background: rgba(247, 126, 83, .6) !important;
    }

    .metric-card.transparent-danger .metric-icon {
        background: rgba(247, 126, 83, .95) !important;
    }

    /* Yellow (warning) translucent */
    .metric-card.transparent-warning {
        background: rgba(255, 188, 0, .12);
        border: 1px solid rgba(255, 188, 0, .22);
        box-shadow: 0 8px 20px rgba(255, 188, 0, .12);
    }

    .metric-card.transparent-warning .accent {
        background: rgba(255, 188, 0, .6) !important;
    }

    .metric-card.transparent-warning .metric-icon {
        background: rgba(255, 188, 0, .95) !important;
    }

    /* Green (success) translucent */
    .metric-card.transparent-success {
        background: rgba(10, 207, 151, .12);
        border: 1px solid rgba(10, 207, 151, .22);
        box-shadow: 0 8px 20px rgba(10, 207, 151, .12);
    }

    .metric-card.transparent-success .accent {
        background: rgba(10, 207, 151, .6) !important;
    }

    .metric-card.transparent-success .metric-icon {
        background: rgba(10, 207, 151, .95) !important;
    }
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

    <!-- Hero Banner -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="hero-banner reveal-on-scroll">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="mb-2">
                        <div class="hero-kicker">Pantau kinerja skrining PKG di wilayah Anda</div>
                        <h2 class="mt-1 mb-2">Insight Cepat PWS</h2>
                        <div class="d-flex flex-wrap">
                            <div class="hero-stat"><span class="val">{{ number_format($summary['total_skrining'])
                                    }}</span> Total Skrining</div>
                            <div class="hero-stat"><span class="val">{{ number_format($summary['risiko_tinggi'])
                                    }}</span> Risiko Tinggi</div>
                            <div class="hero-stat"><span class="val">{{ number_format($summary['risiko_sedang'])
                                    }}</span> Risiko Sedang</div>
                            <div class="hero-stat"><span class="val">{{ number_format($summary['risiko_rendah'])
                                    }}</span> Risiko Rendah</div>
                        </div>
                    </div>
                    <div class="d-none d-md-block">
                        <i class="fas fa-chart-area" style="font-size: 64px; opacity:.9"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-12">
            <div class="card filter-card elegant-card reveal-on-scroll">
                <div class="card-body">
                    <form method="GET" action="{{ route('ilp.dashboard.pws') }}" class="row g-3 filter-symmetric">
                        <div class="col-md-3">
                            <label for="desa" class="form-label filter-label mb-0"><i class="fas fa-city me-1"></i>
                                Desa</label>
                            <select class="form-select select-decor" id="desa" name="desa">
                                <option value="">Semua Desa</option>
                                @foreach($daftar_desa as $desa)
                                <option value="{{ $desa }}" {{ request('desa')==$desa ? 'selected' : '' }}>
                                    {{ $desa }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="posyandu" class="form-label filter-label mb-0"><i
                                    class="fas fa-map-marker-alt me-1"></i> Posyandu</label>
                            <select class="form-select select-decor" id="posyandu" name="posyandu">
                                <option value="">Semua Posyandu</option>
                                @foreach($daftar_posyandu as $posyandu)
                                <option value="{{ $posyandu }}" {{ request('posyandu')==$posyandu ? 'selected' : '' }}>
                                    {{ $posyandu }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="periode" class="form-label filter-label mb-0"><i
                                    class="fas fa-calendar-alt me-1"></i> Periode</label>
                            <select class="form-select select-decor" id="periode" name="periode">
                                <option value="bulan_ini" {{ request('periode', 'bulan_ini' )=='bulan_ini' ? 'selected'
                                    : '' }}>Bulan Ini</option>
                                <option value="3_bulan" {{ request('periode')=='3_bulan' ? 'selected' : '' }}>3 Bulan
                                    Terakhir</option>
                                <option value="6_bulan" {{ request('periode')=='6_bulan' ? 'selected' : '' }}>6 Bulan
                                    Terakhir</option>
                                <option value="tahun_ini" {{ request('periode')=='tahun_ini' ? 'selected' : '' }}>Tahun
                                    Ini</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-0">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary shadow-sm">
                                    <i class="fas fa-filter"></i> Terapkan Filter
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
            <div class="card metric-card transparent-blue reveal-on-scroll">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="metric-title mt-0 text-truncate" title="Total Skrining">Total Skrining</h5>
                            <h3 class="my-2 py-1 metric-value count-up" data-target="{{ $summary['total_skrining'] }}">0
                            </h3>
                            <p class="mb-0 metric-sub">Periode {{ ucfirst(str_replace('_', ' ', request('periode',
                                'bulan_ini'))) }}</p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="metric-icon"><i class="fas fa-clipboard-check"></i></div>
                                <div id="spark-total" style="height: 60px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="accent"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card metric-card transparent-danger reveal-on-scroll">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="metric-title mt-0 text-truncate" title="Risiko Tinggi">Risiko Tinggi</h5>
                            <h3 class="my-2 py-1 metric-value count-up" data-target="{{ $summary['risiko_tinggi'] }}">0
                            </h3>
                            <p class="mb-0 metric-sub">{{ $summary['total_skrining'] > 0 ?
                                number_format(($summary['risiko_tinggi'] / $summary['total_skrining']) * 100, 1) : 0 }}%
                                dari total</p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="metric-icon"><i class="fas fa-exclamation-triangle"></i></div>
                                <div id="spark-tinggi" style="height: 60px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="accent"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card metric-card transparent-warning reveal-on-scroll">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="metric-title mt-0 text-truncate" title="Risiko Sedang">Risiko Sedang</h5>
                            <h3 class="my-2 py-1 metric-value count-up" data-target="{{ $summary['risiko_sedang'] }}">0
                            </h3>
                            <p class="mb-0 metric-sub">{{ $summary['total_skrining'] > 0 ?
                                number_format(($summary['risiko_sedang'] / $summary['total_skrining']) * 100, 1) : 0 }}%
                                dari total</p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="metric-icon"><i class="fas fa-exclamation-circle"></i></div>
                                <div id="spark-sedang" style="height: 60px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="accent"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card metric-card transparent-success reveal-on-scroll">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h5 class="metric-title mt-0 text-truncate" title="Risiko Rendah">Risiko Rendah</h5>
                            <h3 class="my-2 py-1 metric-value count-up" data-target="{{ $summary['risiko_rendah'] }}">0
                            </h3>
                            <p class="mb-0 metric-sub">{{ $summary['total_skrining'] > 0 ?
                                number_format(($summary['risiko_rendah'] / $summary['total_skrining']) * 100, 1) : 0 }}%
                                dari total</p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <div class="metric-icon"><i class="fas fa-check-circle"></i></div>
                                <div id="spark-rendah" style="height: 60px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="accent"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->

    <!-- Kunjungan PKG per Desa -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card elegant-card reveal-on-scroll">
                <div class="card-body">
                    <h4 class="header-title mb-3">Kunjungan PKG per Desa</h4>
                    <div id="kunjungan-per-desa-chart" class="chart-container reveal-on-scroll" style="height: 350px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Kunjungan per Posyandu dari Desa -->
        <div class="col-xl-12">
            <div class="card elegant-card reveal-on-scroll">
                <div class="card-body">
                    <h4 class="header-title mb-3">Kunjungan per Posyandu dari Desa</h4>
                    <div id="kunjungan-per-posyandu-desa-chart" class="chart-container reveal-on-scroll"
                        style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <!-- Distribusi Risiko PKG -->
    <div class="col-xl-6">
        <div class="card elegant-card reveal-on-scroll">
            <div class="card-body">
                <h4 class="header-title mb-3">Distribusi Risiko PKG</h4>
                <div id="distribusi-risiko-chart" class="chart-container reveal-on-scroll" style="height: 350px;">
                </div>
                <div id="distribusi-risiko-empty" class="empty-chart d-none">Tidak ada data untuk periode ini</div>
            </div>
        </div>
    </div>

    <!-- Trend Skrining Bulanan -->
    <div class="col-xl-6">
        <div class="card elegant-card reveal-on-scroll">
            <div class="card-body">
                <h4 class="header-title mb-3">Trend Skrining Bulanan</h4>
                <div id="trend-skrining-chart" class="chart-container reveal-on-scroll" style="height: 350px;">
                </div>
                <div id="trend-skrining-empty" class="empty-chart d-none">Belum ada trend untuk periode ini</div>
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
                                <th class="text-white">Tidak Terklasifikasi</th>
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
                                <td colspan="15" class="text-center">
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
        <div class="card elegant-card transparent-card reveal-on-scroll">
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
                <div id="distribusi-umur-chart" class="chart-container reveal-on-scroll" style="height: 350px;">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Distribusi Jenis Kelamin -->
<div class="row">
    <div class="col-xl-6">
        <div class="card elegant-card reveal-on-scroll">
            <div class="card-body">
                <h4 class="header-title mb-3">Distribusi CKG Menurut Sasaran Jenis Kelamin</h4>
                <div id="distribusi-jenis-kelamin-chart" class="chart-container reveal-on-scroll"
                    style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Distribusi BMI/IMT -->
    <div class="col-xl-6">
        <div class="card elegant-card reveal-on-scroll">
            <div class="card-body">
                <h4 class="header-title mb-3">Distribusi Berdasarkan BMI/IMT</h4>
                <div id="distribusi-bmi-chart" class="chart-container reveal-on-scroll" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Analisa Rokok Section -->
<div class="row">
    <!-- Status Merokok -->
    <div class="col-xl-6">
        <div class="card elegant-card reveal-on-scroll">
            <div class="card-body">
                <h4 class="header-title mb-3">Status Merokok</h4>
                <div id="status-merokok-chart" class="chart-container reveal-on-scroll" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Paparan Asap Rokok -->
    <div class="col-xl-6">
        <div class="card elegant-card reveal-on-scroll">
            <div class="card-body">
                <h4 class="header-title mb-3">Paparan Asap Rokok</h4>
                <div id="paparan-asap-chart" class="chart-container reveal-on-scroll" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Durasi Merokok -->
    <div class="col-xl-6">
        <div class="card elegant-card reveal-on-scroll">
            <div class="card-body">
                <h4 class="header-title mb-3">Durasi Merokok (Perokok Aktif)</h4>
                <div id="durasi-merokok-chart" class="chart-container reveal-on-scroll" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Konsumsi Rokok Harian -->
    <div class="col-xl-6">
        <div class="card elegant-card reveal-on-scroll">
            <div class="card-body">
                <h4 class="header-title mb-3">Konsumsi Rokok Harian (Perokok Aktif)</h4>
                <div id="konsumsi-harian-chart" class="chart-container reveal-on-scroll" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Analisa Pendengaran dan Penglihatan -->
<div class="row">
    <!-- Gangguan Pendengaran -->
    <div class="col-xl-6">
        <div class="card elegant-card reveal-on-scroll">
            <div class="card-body">
                <h4 class="header-title mb-3">Analisa Gangguan Pendengaran</h4>
                <div id="pendengaran-chart" class="chart-container reveal-on-scroll" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Gangguan Penglihatan -->
    <div class="col-xl-6">
        <div class="card elegant-card reveal-on-scroll">
            <div class="card-body">
                <h4 class="header-title mb-3">Analisa Gangguan Penglihatan</h4>
                <div id="penglihatan-chart" class="chart-container reveal-on-scroll" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Analisa Kesehatan Gigi dan Mulut -->
<div class="row">
    <!-- Masalah Gigi Utama -->
    <div class="col-xl-6">
        <div class="card elegant-card reveal-on-scroll">
            <div class="card-body">
                <h4 class="header-title mb-3">Masalah Kesehatan Gigi Utama</h4>
                <div id="masalah-gigi-chart" class="chart-container reveal-on-scroll" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <!-- Kombinasi Masalah Gigi -->
    <div class="col-xl-6">
        <div class="card elegant-card reveal-on-scroll">
            <div class="card-body">
                <h4 class="header-title mb-3">Kombinasi Masalah Gigi dan Mulut</h4>
                <div id="kombinasi-masalah-gigi-chart" class="chart-container reveal-on-scroll" style="height: 350px;">
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
    var distribusiJenisKelaminData = @json($chart_data['distribusi_jenis_kelamin']);
    var kunjunganPerDesaData = @json($chart_data['kunjungan_per_desa']);
    var kunjunganPerPosyanduDesaData = @json($chart_data['kunjungan_per_posyandu_desa']);
    var analisaRokokData = @json($chart_data['analisa_rokok']);
    var distribusiBMIData = @json($chart_data['distribusi_bmi']);
    var analisaPendengaranPenglihatanData = @json($chart_data['analisa_pendengaran_penglihatan']);
    var analisaKesehatanGigiData = @json($chart_data['analisa_kesehatan_gigi']);

    // Validate chart data to prevent ApexCharts errors
    if (!distribusiRisikoData || typeof distribusiRisikoData !== 'object') {
        distribusiRisikoData = { risiko_tinggi: 0, risiko_sedang: 0, risiko_rendah: 0 };
    }
    if (!Array.isArray(trendSkriningData)) {
        trendSkriningData = [];
    }
    if (!Array.isArray(faktorRisikoData)) {
        faktorRisikoData = [];
    }
    if (!Array.isArray(distribusiUmurData)) {
        distribusiUmurData = [];
    }
    if (!Array.isArray(distribusiJenisKelaminData)) {
        distribusiJenisKelaminData = [];
    }
    if (!Array.isArray(kunjunganPerDesaData)) {
        kunjunganPerDesaData = [];
    }
    if (!Array.isArray(kunjunganPerPosyanduDesaData)) {
        kunjunganPerPosyanduDesaData = [];
    }
    if (!analisaRokokData || typeof analisaRokokData !== 'object') {
        analisaRokokData = {
            status_merokok: [],
            paparan_asap: [],
            durasi_merokok: [],
            konsumsi_harian: []
        };
    }
    if (!distribusiBMIData || typeof distribusiBMIData !== 'object') {
        distribusiBMIData = {
            distribusi_kategori: [],
            summary: { total_valid: 0 }
        };
    }
    if (!analisaPendengaranPenglihatanData || typeof analisaPendengaranPenglihatanData !== 'object') {
        analisaPendengaranPenglihatanData = {
            pendengaran: [],
            penglihatan: [],
            gangguan_gabungan: []
        };
    }
    if (!analisaKesehatanGigiData || typeof analisaKesehatanGigiData !== 'object') {
        analisaKesehatanGigiData = {
            masalah_gigi: [],
            kombinasi_masalah: []
        };
    }

    // Chart Distribusi Risiko (Pie Chart)
    var totalDistribusi = (parseInt(distribusiRisikoData.risiko_tinggi)||0) + (parseInt(distribusiRisikoData.risiko_sedang)||0) + (parseInt(distribusiRisikoData.risiko_rendah)||0);
    if (totalDistribusi > 0) {
        var distribusiRisikoOptions = {
            series: [
                parseInt(distribusiRisikoData.risiko_tinggi) || 0, 
                parseInt(distribusiRisikoData.risiko_sedang) || 0, 
                parseInt(distribusiRisikoData.risiko_rendah) || 0
            ],
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
        document.getElementById('distribusi-risiko-empty')?.classList.add('d-none');
    } else {
        document.getElementById('distribusi-risiko-chart')?.classList.add('d-none');
        document.getElementById('distribusi-risiko-empty')?.classList.remove('d-none');
    }

    // Chart Trend Skrining (Line Chart)
    var hasTrend = (Array.isArray(trendSkriningData) && trendSkriningData.some(function(item){ return (item.total||0) > 0; }));
    if (hasTrend) {
        var trendSkriningOptions = {
            series: [{
                name: 'Total Skrining',
                data: trendSkriningData.map(item => parseInt(item.total) || 0)
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
                categories: trendSkriningData.map(item => item.bulan || '')
            },
            yaxis: {
                title: {
                    text: 'Jumlah Skrining'
                }
            }
        };
        var trendSkriningChart = new ApexCharts(document.querySelector("#trend-skrining-chart"), trendSkriningOptions);
        trendSkriningChart.render();
        document.getElementById('trend-skrining-empty')?.classList.add('d-none');
    } else {
        document.getElementById('trend-skrining-chart')?.classList.add('d-none');
        document.getElementById('trend-skrining-empty')?.classList.remove('d-none');
    }

    // Sparkline mini radial charts for summary cards
    var summary = @json($summary);
    function renderSparkline(el, val, color){
        if (!el) return;
        var opt = {
            series: [Math.max(0, Math.min(100, Math.round(val)))],
            chart: { type: 'radialBar', height: 60, sparkline: { enabled: true } },
            plotOptions: { radialBar: { hollow: { size: '48%' }, track: { background: '#eef2ff' }, dataLabels: { show: false } } },
            colors: [color]
        };
        var chart = new ApexCharts(el, opt); chart.render();
    }
    var total = Number(summary.total_skrining||0);
    renderSparkline(document.querySelector('#spark-total'), 100, '#727cf5');
    renderSparkline(document.querySelector('#spark-tinggi'), total>0 ? (summary.risiko_tinggi/total*100) : 0, '#f77e53');
    renderSparkline(document.querySelector('#spark-sedang'), total>0 ? (summary.risiko_sedang/total*100) : 0, '#ffbc00');
    renderSparkline(document.querySelector('#spark-rendah'), total>0 ? (summary.risiko_rendah/total*100) : 0, '#0acf97');

    // Chart Faktor Risiko (Bar Chart)
    // Warna berbeda per faktor risiko + latar chart transparan
    if (faktorRisikoData.length > 0) {
        var faktorColors = faktorRisikoData.map(function(item){
            var name = String(item.faktor || '').toLowerCase();
            if (name.includes('td')) return '#dc3545';        // merah
            if (name.includes('gds')) return '#e25555';       // merah terang
            if (name.includes('gdp')) return '#ff6b6b';       // coral
            if (name.includes('bmi')) return '#ffbc00';       // oranye
            return '#727cf5';                                 // default brand
        });
        var faktorRisikoOptions = {
            series: [{
                name: 'Jumlah Kasus',
                data: faktorRisikoData.map(item => parseInt(item.jumlah) || 0)
            }],
            chart: {
                type: 'bar',
                height: 350,
                background: 'transparent',
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
                    endingShape: 'rounded',
                    distributed: true
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
                categories: faktorRisikoData.map(item => item.faktor || 'Unknown')
            },
            yaxis: {
                title: {
                    text: 'Faktor Risiko'
                }
            },
            fill: {
                opacity: 0.85
            },
            colors: faktorColors
        };
        var faktorRisikoChart = new ApexCharts(document.querySelector("#faktor-risiko-chart"), faktorRisikoOptions);
        faktorRisikoChart.render();
    } else {
        document.querySelector("#faktor-risiko-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-bar fa-3x mb-3"></i><br>Tidak ada data faktor risiko</div>';
    }

    // Chart Distribusi Umur (Column Chart)
    if (distribusiUmurData.length > 0) {
        var distribusiUmurOptions = {
            series: [{
                name: 'Jumlah Pasien',
                data: distribusiUmurData.map(item => parseInt(item.jumlah) || 0)
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
                categories: distribusiUmurData.map(item => item.kelompok_umur || 'Unknown')
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
    } else {
        document.querySelector("#distribusi-umur-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-bar fa-3x mb-3"></i><br>Tidak ada data distribusi umur</div>';
    }

    // Chart Distribusi Jenis Kelamin (Pie Chart)
    var distribusiJenisKelaminOptions = {
        series: distribusiJenisKelaminData.map(item => parseInt(item.jumlah) || 0),
        chart: {
            type: 'pie',
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
        labels: distribusiJenisKelaminData.map(item => item.jenis_kelamin),
        colors: ['#727cf5', '#f77e53'], // Blue for male, coral for female
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
    // Check if gender distribution data exists and has values
    var totalGender = distribusiJenisKelaminData.reduce((sum, item) => sum + (parseInt(item.jumlah) || 0), 0);
    if (totalGender > 0) {
        var distribusiJenisKelaminChart = new ApexCharts(document.querySelector("#distribusi-jenis-kelamin-chart"), distribusiJenisKelaminOptions);
        distribusiJenisKelaminChart.render();
    } else {
        // Show empty state if no data
        document.querySelector("#distribusi-jenis-kelamin-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-pie fa-3x mb-3"></i><br>Tidak ada data jenis kelamin</div>';
    }

    // Chart Distribusi BMI/IMT (Donut Chart)
    if (distribusiBMIData.distribusi_kategori && distribusiBMIData.distribusi_kategori.length > 0) {
        var bmiData = distribusiBMIData.distribusi_kategori.filter(item => (parseInt(item.jumlah) || 0) > 0);
        if (bmiData.length > 0) {
            var distribusiBMIOptions = {
                series: bmiData.map(item => parseInt(item.jumlah) || 0),
                chart: {
                    type: 'donut',
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
                labels: bmiData.map(item => item.kategori),
                colors: bmiData.map(item => item.color), // Use custom colors from backend
                legend: {
                    position: 'bottom',
                    fontSize: '12px'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    color: '#333',
                                    offsetY: -10
                                },
                                value: {
                                    show: true,
                                    fontSize: '16px',
                                    fontWeight: 700,
                                    color: '#333',
                                    offsetY: 10,
                                    formatter: function (val) {
                                        return parseInt(val).toLocaleString('id-ID');
                                    }
                                },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'Total Valid',
                                    fontSize: '12px',
                                    fontWeight: 600,
                                    color: '#666',
                                    formatter: function (w) {
                                        var total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return total.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return val.toFixed(1) + '%';
                    },
                    style: {
                        fontSize: '11px',
                        fontWeight: 'bold',
                        colors: ['#fff']
                    },
                    dropShadow: {
                        enabled: true
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 280
                        },
                        legend: {
                            position: 'bottom',
                            fontSize: '10px'
                        }
                    }
                }],
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val.toLocaleString('id-ID') + ' orang';
                        }
                    }
                }
            };
            var distribusiBMIChart = new ApexCharts(document.querySelector("#distribusi-bmi-chart"), distribusiBMIOptions);
            distribusiBMIChart.render();
        } else {
            document.querySelector("#distribusi-bmi-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-pie fa-3x mb-3"></i><br>Tidak ada data BMI/IMT valid</div>';
        }
    } else {
        document.querySelector("#distribusi-bmi-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-pie fa-3x mb-3"></i><br>Tidak ada data BMI/IMT</div>';
    }

    // Chart Kunjungan PKG per Desa (Bar Chart)
    if (kunjunganPerDesaData.length > 0) {
        var kunjunganPerDesaOptions = {
            series: [{
                name: 'Jumlah Kunjungan',
                data: kunjunganPerDesaData.map(item => parseInt(item.jumlah) || 0)
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
                    columnWidth: '60%',
                    endingShape: 'rounded',
                    distributed: true
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
                categories: kunjunganPerDesaData.map(item => item.desa || 'Unknown'),
                labels: {
                    rotate: -45,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Kunjungan'
                }
            },
            fill: {
                opacity: 0.9
            },
            colors: ['#727cf5', '#0acf97', '#ffbc00', '#f77e53', '#6c5ce7', '#a29bfe', '#fd79a8', '#fdcb6e', '#e17055', '#00b894']
        };
        var kunjunganPerDesaChart = new ApexCharts(document.querySelector("#kunjungan-per-desa-chart"), kunjunganPerDesaOptions);
        kunjunganPerDesaChart.render();
    } else {
        document.querySelector("#kunjungan-per-desa-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-bar fa-3x mb-3"></i><br>Tidak ada data kunjungan per desa</div>';
    }

    // Chart Kunjungan per Posyandu dari Desa (Horizontal Bar Chart)
    if (kunjunganPerPosyanduDesaData.length > 0) {
        var kunjunganPerPosyanduDesaOptions = {
            series: [{
                name: 'Jumlah Kunjungan',
                data: kunjunganPerPosyanduDesaData.map(item => parseInt(item.jumlah) || 0)
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
                    columnWidth: '70%',
                    endingShape: 'rounded',
                    distributed: true
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
                categories: kunjunganPerPosyanduDesaData.map(item => item.label || item.posyandu || 'Unknown'),
                title: {
                    text: 'Jumlah Kunjungan'
                }
            },
            yaxis: {
                title: {
                    text: 'Posyandu (Desa)'
                },
                labels: {
                    style: {
                        fontSize: '11px'
                    }
                }
            },
            fill: {
                opacity: 0.9
            },
            colors: ['#e17055', '#00b894', '#6c5ce7', '#a29bfe', '#fd79a8', '#fdcb6e', '#727cf5', '#0acf97', '#ffbc00', '#f77e53', '#74b9ff', '#55a3ff', '#ff7675', '#fab1a0', '#00cec9']
        };
        var kunjunganPerPosyanduDesaChart = new ApexCharts(document.querySelector("#kunjungan-per-posyandu-desa-chart"), kunjunganPerPosyanduDesaOptions);
        kunjunganPerPosyanduDesaChart.render();
    } else {
        document.querySelector("#kunjungan-per-posyandu-desa-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-bar fa-3x mb-3"></i><br>Tidak ada data kunjungan per posyandu</div>';
    }

    // =============================
    // Smoking Analysis Charts
    // =============================

    // Chart Status Merokok (Pie Chart)
    if (analisaRokokData.status_merokok && analisaRokokData.status_merokok.length > 0) {
        var statusMerokokData = analisaRokokData.status_merokok.filter(item => (parseInt(item.jumlah) || 0) > 0);
        if (statusMerokokData.length > 0) {
            var statusMerokokOptions = {
                series: statusMerokokData.map(item => parseInt(item.jumlah) || 0),
                chart: {
                    type: 'pie',
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
                labels: statusMerokokData.map(item => item.kategori),
                colors: ['#f77e53', '#0acf97', '#ffbc00'], // Red for smokers, green for non-smokers, yellow for ex-smokers
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
            var statusMerokokChart = new ApexCharts(document.querySelector("#status-merokok-chart"), statusMerokokOptions);
            statusMerokokChart.render();
        } else {
            document.querySelector("#status-merokok-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-pie fa-3x mb-3"></i><br>Tidak ada data status merokok</div>';
        }
    } else {
        document.querySelector("#status-merokok-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-pie fa-3x mb-3"></i><br>Tidak ada data status merokok</div>';
    }

    // Chart Paparan Asap (Donut Chart)
    if (analisaRokokData.paparan_asap && analisaRokokData.paparan_asap.length > 0) {
        var paparanAsapData = analisaRokokData.paparan_asap.filter(item => (parseInt(item.jumlah) || 0) > 0);
        if (paparanAsapData.length > 0) {
            var paparanAsapOptions = {
                series: paparanAsapData.map(item => parseInt(item.jumlah) || 0),
                chart: {
                    type: 'donut',
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
                labels: paparanAsapData.map(item => item.kategori),
                colors: ['#dc3545', '#28a745'], // Red for exposed, green for not exposed
                legend: {
                    position: 'bottom'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '60%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
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
            var paparanAsapChart = new ApexCharts(document.querySelector("#paparan-asap-chart"), paparanAsapOptions);
            paparanAsapChart.render();
        } else {
            document.querySelector("#paparan-asap-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-pie fa-3x mb-3"></i><br>Tidak ada data paparan asap</div>';
        }
    } else {
        document.querySelector("#paparan-asap-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-pie fa-3x mb-3"></i><br>Tidak ada data paparan asap</div>';
    }

    // Chart Durasi Merokok (Bar Chart)
    if (analisaRokokData.durasi_merokok && analisaRokokData.durasi_merokok.length > 0) {
        var durasiMerokokData = analisaRokokData.durasi_merokok.filter(item => (parseInt(item.jumlah) || 0) > 0);
        if (durasiMerokokData.length > 0) {
            var durasiMerokokOptions = {
                series: [{
                    name: 'Jumlah Perokok',
                    data: durasiMerokokData.map(item => parseInt(item.jumlah) || 0)
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
                        endingShape: 'rounded',
                        distributed: true
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
                    categories: durasiMerokokData.map(item => item.kategori)
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Perokok'
                    }
                },
                fill: {
                    opacity: 0.9
                },
                colors: ['#ffbc00', '#f77e53', '#dc3545', '#6f42c1'] // Yellow to purple gradient for duration
            };
            var durasiMerokokChart = new ApexCharts(document.querySelector("#durasi-merokok-chart"), durasiMerokokOptions);
            durasiMerokokChart.render();
        } else {
            document.querySelector("#durasi-merokok-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-bar fa-3x mb-3"></i><br>Tidak ada data durasi merokok</div>';
        }
    } else {
        document.querySelector("#durasi-merokok-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-bar fa-3x mb-3"></i><br>Tidak ada data durasi merokok</div>';
    }

    // Chart Konsumsi Harian (Horizontal Bar Chart)
    if (analisaRokokData.konsumsi_harian && analisaRokokData.konsumsi_harian.length > 0) {
        var konsumsiHarianData = analisaRokokData.konsumsi_harian.filter(item => (parseInt(item.jumlah) || 0) > 0);
        if (konsumsiHarianData.length > 0) {
            var konsumsiHarianOptions = {
                series: [{
                    name: 'Jumlah Perokok',
                    data: konsumsiHarianData.map(item => parseInt(item.jumlah) || 0)
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
                        columnWidth: '70%',
                        endingShape: 'rounded',
                        distributed: true
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
                    categories: konsumsiHarianData.map(item => item.kategori),
                    title: {
                        text: 'Jumlah Perokok'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Konsumsi per Hari'
                    }
                },
                fill: {
                    opacity: 0.9
                },
                colors: ['#20c997', '#ffc107', '#dc3545'] // Light to heavy smoking intensity colors
            };
            var konsumsiHarianChart = new ApexCharts(document.querySelector("#konsumsi-harian-chart"), konsumsiHarianOptions);
            konsumsiHarianChart.render();
        } else {
            document.querySelector("#konsumsi-harian-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-bar fa-3x mb-3"></i><br>Tidak ada data konsumsi harian</div>';
        }
    } else {
        document.querySelector("#konsumsi-harian-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-chart-bar fa-3x mb-3"></i><br>Tidak ada data konsumsi harian</div>';
    }

    // =============================
    // Hearing and Vision Analysis Charts
    // =============================

    // Chart Pendengaran (Pie Chart)
    if (analisaPendengaranPenglihatanData.pendengaran && analisaPendengaranPenglihatanData.pendengaran.length > 0) {
        var pendengaranData = analisaPendengaranPenglihatanData.pendengaran.filter(item => (parseInt(item.jumlah) || 0) > 0);
        if (pendengaranData.length > 0) {
            var pendengaranOptions = {
                series: pendengaranData.map(item => parseInt(item.jumlah) || 0),
                chart: {
                    type: 'pie',
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
                labels: pendengaranData.map(item => item.kategori),
                colors: ['#28a745', '#dc3545', '#6c757d'], // Green for normal, red for impaired, gray for not examined
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return val.toFixed(1) + '%';
                    }
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
            var pendengaranChart = new ApexCharts(document.querySelector("#pendengaran-chart"), pendengaranOptions);
            pendengaranChart.render();
        } else {
            document.querySelector("#pendengaran-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-ear-deaf fa-3x mb-3"></i><br>Tidak ada data pendengaran</div>';
        }
    } else {
        document.querySelector("#pendengaran-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-ear-deaf fa-3x mb-3"></i><br>Tidak ada data pendengaran</div>';
    }

    // Chart Penglihatan (Pie Chart)
    if (analisaPendengaranPenglihatanData.penglihatan && analisaPendengaranPenglihatanData.penglihatan.length > 0) {
        var penglihatanData = analisaPendengaranPenglihatanData.penglihatan.filter(item => (parseInt(item.jumlah) || 0) > 0);
        if (penglihatanData.length > 0) {
            var penglihatanOptions = {
                series: penglihatanData.map(item => parseInt(item.jumlah) || 0),
                chart: {
                    type: 'pie',
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
                labels: penglihatanData.map(item => item.kategori),
                colors: ['#28a745', '#dc3545', '#6c757d'], // Green for normal, red for impaired, gray for not examined
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return val.toFixed(1) + '%';
                    }
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
            var penglihatanChart = new ApexCharts(document.querySelector("#penglihatan-chart"), penglihatanOptions);
            penglihatanChart.render();
        } else {
            document.querySelector("#penglihatan-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-eye-slash fa-3x mb-3"></i><br>Tidak ada data penglihatan</div>';
        }
    } else {
        document.querySelector("#penglihatan-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-eye-slash fa-3x mb-3"></i><br>Tidak ada data penglihatan</div>';
    }

    // =============================
    // Dental Health Analysis Charts
    // =============================

    // Chart Masalah Gigi Utama (Donut Chart)
    if (analisaKesehatanGigiData.masalah_gigi && analisaKesehatanGigiData.masalah_gigi.length > 0) {
        var masalahGigiData = analisaKesehatanGigiData.masalah_gigi.filter(item => (parseInt(item.jumlah) || 0) > 0);
        if (masalahGigiData.length > 0) {
            var masalahGigiOptions = {
                series: masalahGigiData.map(item => parseInt(item.jumlah) || 0),
                chart: {
                    type: 'donut',
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
                labels: masalahGigiData.map(item => item.kategori),
                colors: ['#dc3545', '#fd7e14', '#ffc107', '#28a745'], // Red for caries, orange for missing, yellow for loose, green for healthy
                legend: {
                    position: 'bottom'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '60%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return val.toFixed(1) + '%';
                    }
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
            var masalahGigiChart = new ApexCharts(document.querySelector("#masalah-gigi-chart"), masalahGigiOptions);
            masalahGigiChart.render();
        } else {
            document.querySelector("#masalah-gigi-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-tooth fa-3x mb-3"></i><br>Tidak ada data masalah gigi</div>';
        }
    } else {
        document.querySelector("#masalah-gigi-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-tooth fa-3x mb-3"></i><br>Tidak ada data masalah gigi</div>';
    }

    // Chart Kombinasi Masalah Gigi (Horizontal Bar Chart)
    if (analisaKesehatanGigiData.kombinasi_masalah && analisaKesehatanGigiData.kombinasi_masalah.length > 0) {
        var kombinasiMasalahData = analisaKesehatanGigiData.kombinasi_masalah.filter(item => (parseInt(item.jumlah) || 0) > 0);
        if (kombinasiMasalahData.length > 0) {
            var kombinasiMasalahOptions = {
                series: [{
                    name: 'Jumlah Kasus',
                    data: kombinasiMasalahData.map(item => parseInt(item.jumlah) || 0)
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
                        columnWidth: '60%',
                        endingShape: 'rounded',
                        distributed: true
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
                    categories: kombinasiMasalahData.map(item => item.kategori),
                    title: {
                        text: 'Jumlah Kasus'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Kombinasi Masalah'
                    }
                },
                fill: {
                    opacity: 0.9
                },
                colors: ['#dc3545', '#fd7e14', '#ffc107', '#e83e8c', '#6f42c1', '#20c997', '#6610f2', '#28a745'], // Various colors for different combinations
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val.toLocaleString('id-ID') + ' orang';
                        }
                    }
                }
            };
            var kombinasiMasalahChart = new ApexCharts(document.querySelector("#kombinasi-masalah-gigi-chart"), kombinasiMasalahOptions);
            kombinasiMasalahChart.render();
        } else {
            document.querySelector("#kombinasi-masalah-gigi-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-tooth fa-3x mb-3"></i><br>Tidak ada data kombinasi masalah</div>';
        }
    } else {
        document.querySelector("#kombinasi-masalah-gigi-chart").innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-tooth fa-3x mb-3"></i><br>Tidak ada data kombinasi masalah</div>';
    }

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
            $tbody.html('<tr><td colspan="15" class="text-center">Tidak ada data skrining PKG</td></tr>');
            return;
        }
        var rows = items.map(function(item){
            var total = Number(item.total_skrining || 0);
            var tinggi = Number(item.risiko_tinggi || 0);
            var sedang = Number(item.risiko_sedang || 0);
            // Opsi (1): risiko rendah sebagai komplemen dari total
            var rendah = Math.max(0, total - tinggi - sedang);
            // Opsi (3): tampilkan data tidak terklasifikasi (berdasarkan rendah backend jika definisi ketat)
            var rendahBackend = Number(item.risiko_rendah || 0);
            var tidakTerk = Math.max(0, total - (tinggi + sedang + rendahBackend));
            var persen = total > 0 ? (tinggi / total * 100) : 0;
            return '<tr>'+
                '<td>'+(item.nama_posyandu || '-')+'</td>'+
                '<td>'+(item.desa || '-')+'</td>'+
                '<td>'+numberFormat(total)+'</td>'+
                '<td>'+numberFormat(item.laki_laki || 0)+'</td>'+
                '<td>'+numberFormat(item.perempuan || 0)+'</td>'+
                '<td><span class="badge bg-danger">'+numberFormat(tinggi)+'</span></td>'+
                '<td><span class="badge bg-warning text-dark">'+numberFormat(sedang)+'</span></td>'+
                '<td><span class="badge bg-success">'+numberFormat(rendah)+'</span></td>'+
                '<td>'+numberFormat(tidakTerk)+'</td>'+
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
        $tbody.html('<tr class="loading-row"><td colspan="15" class="text-center"><div class="skeleton" style="height:16px"></div></td></tr>');
        // Perbaiki nama route agar sesuai dengan definisi di routes/web.php
        $.get('{{ route("ilp.dashboard.pws.analisis") }}', params, function(resp){
            renderRows(resp.data);
            renderPagination(resp.meta);
            analisisLoaded = true;
        }).fail(function(){
            $tbody.html('<tr><td colspan="15" class="text-center text-danger">Gagal memuat data</td></tr>');
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

    // Muat data analisis segera saat halaman siap (tanpa menunggu scroll)
    fetchAnalisisPkg(1);
});
</script>
@endsection
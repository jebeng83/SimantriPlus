@extends('adminlte::page')

@section('title', 'Pasien Ralan')

@section('content_header')
<div class="ralan-header">
    <div class="header-content">
        <h1 class="ralan-title" style="color: white;">Pasien Ralan</h1>
        <p class="ralan-subtitle" style="color: white;">{{$nm_poli}}
            @if(session('username') === 'admin' && session('kd_poli') === 'U0011')
            <span class="badge badge-info">Menampilkan semua poli</span>
            @endif
        </p>
        <p class="ralan-sort-info" id="sort-label"><small><i class="fas fa-sort-numeric-down" style="color: white;"></i>
                Diurutkan berdasarkan
                No. Registrasi
                ASC</small></p>
    </div>
    <div class="header-actions">
        <div class="d-flex align-items-center mb-2">
            <div class="dropdown mr-2">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-sort mr-1"></i> Urutan
                </button>
                <div class="dropdown-menu" aria-labelledby="sortDropdown">
                    <a class="dropdown-item sort-option" href="#" data-sort="no_reg_asc"><i
                            class="fas fa-sort-numeric-down mr-2"></i>No. Reg (Naik)</a>
                    <a class="dropdown-item sort-option" href="#" data-sort="no_reg_desc"><i
                            class="fas fa-sort-numeric-down-alt mr-2"></i>No. Reg (Turun)</a>
                    <a class="dropdown-item sort-option" href="#" data-sort="nm_pasien_asc"><i
                            class="fas fa-sort-alpha-down mr-2"></i>Nama Pasien (A-Z)</a>
                    <a class="dropdown-item sort-option" href="#" data-sort="nm_pasien_desc"><i
                            class="fas fa-sort-alpha-down-alt mr-2"></i>Nama Pasien (Z-A)</a>
                    <a class="dropdown-item sort-option" href="#" data-sort="stts_asc"><i
                            class="fas fa-sort mr-2"></i>Status (Menunggu-Selesai)</a>
                    <a class="dropdown-item sort-option" href="#" data-sort="stts_desc"><i
                            class="fas fa-sort mr-2"></i>Status (Selesai-Menunggu)</a>
                </div>
            </div>
        </div>
        <div class="quick-stats">
            <div class="stat-item" id="totalPasien">
                <span class="stat-value">{{ $totalPasien }}</span>
                <span class="stat-label">Total Pasien</span>
            </div>
            <div class="stat-item" id="selesaiPasien">
                <span class="stat-value">{{ $selesai }}</span>
                <span class="stat-label">Selesai</span>
            </div>
            <div class="stat-item" id="menungguPasien">
                <span class="stat-value">{{ $menunggu }}</span>
                <span class="stat-label">Menunggu</span>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="ralan-container">
    <div class="ralan-card">
        <ul class="nav nav-tabs nav-tabs-custom" id="ralanTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pasien-tab" data-toggle="tab" href="#pasien" role="tab"
                    aria-controls="pasien" aria-selected="true">
                    <i class="fas fa-user-injured mr-2"></i>Pasien Ralan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="rujuk-tab" data-toggle="tab" href="#rujuk" role="tab" aria-controls="rujuk"
                    aria-selected="false">
                    <i class="fas fa-share-alt mr-2"></i>Rujuk Internal
                </a>
            </li>
        </ul>

        <div class="tab-content" id="ralanTabsContent">
            <div class="tab-pane fade show active" id="pasien" role="tabpanel" aria-labelledby="pasien-tab">
                <div class="filter-box">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <label class="filter-label">Filter Status Pasien</label>
                            <div class="d-flex">
                                <select id="filterStatus" class="filter-status-select">
                                    <option value="">Semua Status</option>
                                    <option value="Belum" selected>Menunggu (Belum)</option>
                                    <option value="Sudah">Selesai (Sudah)</option>
                                </select>
                                <button class="btn btn-sm btn-primary ml-2" id="applyFilterBtn">
                                    <i class="fas fa-filter"></i> Terapkan Filter
                                </button>
                                <button id="manualRefreshBtn" class="btn btn-sm btn-success ml-2">
                                    <i class="fas fa-sync-alt"></i> Refresh Data
                                </button>
                                <a href="/register" class="btn btn-sm btn-info ml-2">
                                    <i class="fas fa-user-plus"></i> Registrasi
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="filter-label">Pencarian Pasien</label>
                            <div class="d-flex">
                                <input type="text" id="searchBox" class="form-control"
                                    placeholder="Cari nama pasien, no.reg, atau no.rawat...">
                                <button class="btn btn-sm btn-secondary ml-2" id="clearSearchBtn">
                                    <i class="fas fa-times"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table-pasien" id="tablePasienRalan">
                        <thead>
                            <tr>
                                @foreach($heads as $head)
                                <th>{{ $head }}</th>
                                @endforeach
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $row)
                            <tr @if(!empty($row->diagnosa_utama)) class="completed" @endif>
                                <td>{{$row->no_reg}}</td>
                                <td>
                                    @php
                                    $noRawat = isset($row->no_rawat)
                                    ? App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rawat)
                                    : '';
                                    $noRM = isset($row->no_rkm_medis)
                                    ? App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rkm_medis)
                                    : '';

                                    // Menentukan ikon berdasarkan pola nama
                                    $nameWords = explode(' ', strtolower($row->nm_pasien));
                                    $nameLength = strlen($row->nm_pasien);
                                    $nameClass = '';
                                    $nameIcon = '';

                                    // Mengecek apakah nama mengandung title/profesi
                                    $hasTitle = false;
                                    $titles = ['dr.', 'drg.', 'prof.', 'ir.', 'drs.', 'ny.', 'tn.', 'sdr.', 'sdri.',
                                    'h.', 'hj.'];
                                    foreach ($titles as $title) {
                                    if (stripos($row->nm_pasien, $title) !== false) {
                                    $hasTitle = true;
                                    break;
                                    }
                                    }

                                    // Mengecek apakah nama mengandung gelar
                                    $hasGelar = false;
                                    $gelar = ['s.kom', 's.kep', 's.farm', 's.ked', 's.pd', 'm.pd', 'm.kom', 'm.si',
                                    'm.sc', 'ph.d', 's.e.', 's.sos', 's.h.', 's.t.', 's.k.m', 's.keb', 'amd'];
                                    foreach ($gelar as $g) {
                                    if (stripos($row->nm_pasien, $g) !== false) {
                                    $hasGelar = true;
                                    break;
                                    }
                                    }

                                    // Set variasi ikon dan class berdasarkan pola nama
                                    if ($hasTitle) {
                                    $nameIcon = 'fa-user-tie';
                                    $nameClass = 'name-professional';
                                    } else if ($hasGelar) {
                                    $nameIcon = 'fa-user-graduate';
                                    $nameClass = 'name-graduate';
                                    } else if (strtoupper($row->nm_pasien) === $row->nm_pasien) {
                                    // Nama dengan semua huruf kapital
                                    $nameIcon = 'fa-user-shield';
                                    $nameClass = 'name-official';
                                    } else if ($nameLength > 20) {
                                    // Nama panjang
                                    $nameIcon = 'fa-user-tag';
                                    $nameClass = 'name-long';
                                    } else {
                                    // Default
                                    $nameIcon = 'fa-user-circle';
                                    $nameClass = 'name-default';
                                    }
                                    @endphp

                                    <a href="{{route('ralan.pemeriksaan', ['no_rawat' => $noRawat, 'no_rm' => $noRM])}}"
                                        class="patient-name">
                                        <i class="fas {{ $nameIcon }} mr-1" id="icon-{{ $row->no_reg }}"></i>
                                        <span class="patient-fullname {{ $nameClass }}">{{ $row->nm_pasien }}</span>
                                    </a>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                            id="dropdownMenu-{{$row->no_rawat ? str_replace('/', '-', $row->no_rawat) : 'unknown'}}"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{$row->no_rawat ?? 'N/A'}}
                                        </button>
                                        <div class="dropdown-menu"
                                            aria-labelledby="dropdownMenu-{{$row->no_rawat ? str_replace('/', '-', $row->no_rawat) : 'unknown'}}">
                                            <a href="/ilp/dewasa/{{$row->no_rawat ?? ''}}" class="dropdown-item">
                                                <i class="fas fa-file-medical mr-2 text-primary"></i> Form ILP Dewasa
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($row->no_tlp)
                                    <span class="text-muted"><i class="fas fa-phone-alt mr-1"></i>
                                        {{$row->no_tlp}}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="d-flex align-items-center"><i
                                            class="fas fa-user-md mr-2 text-primary"></i> {{$row->nm_dokter ??
                                        ''}}</span>
                                </td>
                                <td>
                                    <span class="status-badge {{$row->stts == 'Sudah' ? 'completed' : 'pending'}}">
                                        {{$row->stts ?? 'Belum'}}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-bullhorn"></i> Aksi
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="javascript:void(0)" class="dropdown-item btn-panggil"
                                                data-no-reg="{{$row->no_reg ?? ''}}"
                                                data-nama="{{$row->nm_pasien ?? ''}}"
                                                data-poli="{{$row->nm_poli ?? ''}}"
                                                data-no-rawat="{{$row->no_rawat ?? ''}}">
                                                <i class="fas fa-volume-up mr-2"></i> Panggil Pasien
                                            </a>
                                            <a href="javascript:void(0)" class="dropdown-item btn-ulang-panggil"
                                                data-no-reg="{{$row->no_reg ?? ''}}"
                                                data-nama="{{$row->nm_pasien ?? ''}}"
                                                data-poli="{{$row->nm_poli ?? ''}}"
                                                data-no-rawat="{{$row->no_rawat ?? ''}}">
                                                <i class="fas fa-redo mr-2"></i> Ulang Panggil
                                            </a>
                                            <a href="/pcare/form-pendaftaran?no_rkm_medis={{$row->no_rkm_medis ?? ''}}&ts={{time()}}&clear_cache=true"
                                                class="dropdown-item btn-daftar-pcare"
                                                data-no-rkm-medis="{{$row->no_rkm_medis ?? ''}}">
                                                <i class="fas fa-clipboard-list mr-2"></i> Daftar Pcare
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-container">
                    <div class="pagination-info">
                        Menampilkan <span id="showing-from">1</span> sampai <span id="showing-to">{{min(10,
                            count($data))}}</span> dari <span id="total-entries">{{count($data)}}</span> entri
                    </div>
                    <ul class="pagination" id="pasien-pagination">
                        <!-- Pagination will be generated by JavaScript -->
                    </ul>
                </div>
            </div>

            <div class="tab-pane fade" id="rujuk" role="tabpanel" aria-labelledby="rujuk-tab">
                <div class="table-container">
                    <table class="table-pasien" id="tableRujuk">
                        <thead>
                            <tr>
                                @foreach($headsInternal as $head)
                                <th>{{ $head }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataInternal as $row)
                            <tr>
                                <td>{{$row->no_reg ?? ''}}</td>
                                <td>{{$row->no_rkm_medis ?? ''}}</td>
                                <td>
                                    @php
                                    $noRawat = isset($row->no_rawat)
                                    ? App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rawat)
                                    : '';
                                    $noRM = isset($row->no_rkm_medis)
                                    ? App\Http\Controllers\Ralan\PasienRalanController::encryptData($row->no_rkm_medis)
                                    : '';
                                    @endphp

                                    <a href="{{route('ralan.rujuk-internal', ['no_rawat' => $noRawat, 'no_rm' => $noRM])}}"
                                        class="patient-name">
                                        <i class="fas fa-user-circle mr-2"></i>
                                        <span class="patient-fullname">{{$row->nm_pasien ?? ''}}</span>
                                    </a>
                                </td>
                                <td>
                                    <span class="d-flex align-items-center"><i
                                            class="fas fa-user-md mr-2 text-primary"></i> {{$row->nm_dokter ??
                                        ''}}</span>
                                </td>
                                <td>
                                    <span
                                        class="status-badge {{isset($row->stts) && $row->stts == 'Sudah' ? 'completed' : 'pending'}}">
                                        {{$row->stts ?? 'Belum'}}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-container">
                    <div class="pagination-info">
                        Menampilkan <span id="rujuk-showing-from">1</span> sampai <span id="rujuk-showing-to">{{min(10,
                            count($dataInternal))}}</span> dari <span
                            id="rujuk-total-entries">{{count($dataInternal)}}</span> entri
                    </div>
                    <ul class="pagination" id="rujuk-pagination">
                        <!-- Pagination will be generated by JavaScript -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap"
    rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/adminlte-premium.css') }}">
<link rel="stylesheet" href="{{ asset('css/ralan-premium.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    :root {
        --premium-gradient: linear-gradient(135deg, #233292 0%, #4F5BDA 100%);
        --premium-shadow: 0 10px 30px rgba(35, 50, 146, 0.15);
        --premium-border-radius: 12px;
        --premium-font-heading: 'Playfair Display', serif;
    }

    body {
        background-color: #f7f9fc;
    }

    /* Mempercantik header "Pasien Ralan" */
    .ralan-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        border-radius: 8px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    /* Refresh indicator yang tidak mengganggu */
    .refresh-indicator {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0, 123, 255, 0.8);
        color: white;
        text-align: center;
        padding: 4px 0;
        font-size: 12px;
        font-weight: 500;
        transform: translateY(100%);
        animation: slideUp 0.3s forwards;
        z-index: 10;
    }

    .refresh-indicator a {
        color: #fff;
        text-decoration: underline;
        font-weight: bold;
    }

    .refresh-indicator a:hover {
        color: #fff;
        text-decoration: none;
    }

    @keyframes slideUp {
        to {
            transform: translateY(0);
        }
    }

    /* Animasi untuk icon refresh */
    .refresh-spin {
        animation: spin 1s infinite linear;
        display: inline-block;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Animasi pulsa untuk indikator update data */
    .stat-updated {
        animation: pulse-highlight 2s ease-in-out;
    }

    @keyframes pulse-highlight {
        0% {
            background-color: transparent;
        }

        50% {
            background-color: rgba(40, 167, 69, 0.2);
        }

        100% {
            background-color: transparent;
        }
    }

    .ralan-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
        transform: rotate(30deg);
        z-index: 1;
    }

    /* Memperbaiki area data pasien */
    .ralan-container {
        padding: 15px;
        background-color: #f8f9fa;
    }

    .ralan-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    /* Tab styling */
    .nav-tabs-custom {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 0;
    }

    .nav-tabs-custom .nav-item .nav-link {
        border: none;
        position: relative;
        color: #495057;
        padding: 15px 20px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .nav-tabs-custom .nav-item .nav-link.active {
        color: #007bff;
        background-color: #fff;
        border-top: 3px solid #007bff;
        border-radius: 0;
    }

    .nav-tabs-custom .nav-item .nav-link:hover {
        color: #007bff;
    }

    /* Filter styling */
    .filter-box {
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 15px;
    }

    .filter-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
        display: block;
    }

    .filter-status-select {
        width: 200px;
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        font-size: 14px;
        color: #495057;
        background-color: #fff;
        transition: all 0.2s ease;
    }

    .filter-status-select:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
    }

    #applyFilterBtn {
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        background-color: #007bff;
        border-color: #007bff;
        transition: all 0.3s;
    }

    #applyFilterBtn:hover {
        background-color: #0069d9;
        border-color: #0062cc;
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .d-flex {
        display: flex;
        align-items: center;
    }

    /* Table Container */
    .table-container {
        position: relative;
        margin: 0;
        padding: 0;
        background: #fff;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Table Styling */
    .table-pasien {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        background: #fff;
    }

    /* Header Styling */
    .table-pasien thead {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #fff;
    }

    .table-pasien th {
        background: #f8f9fa;
        padding: 12px 8px;
        font-weight: 600;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }

    /* Column Widths */
    .table-pasien th:nth-child(1),
    .table-pasien td:nth-child(1) {
        width: 8%;
    }

    /* No Reg */

    .table-pasien th:nth-child(2),
    .table-pasien td:nth-child(2) {
        width: 25%;
    }

    /* Nama */

    .table-pasien th:nth-child(3),
    .table-pasien td:nth-child(3) {
        width: 20%;
    }

    /* No Rawat */

    .table-pasien th:nth-child(4),
    .table-pasien td:nth-child(4) {
        width: 15%;
    }

    /* Telp */

    .table-pasien th:nth-child(5),
    .table-pasien td:nth-child(5) {
        width: 20%;
    }

    /* Dokter */

    .table-pasien th:nth-child(6),
    .table-pasien td:nth-child(6) {
        width: 12%;
    }

    /* Status */

    /* Body Styling */
    .table-pasien td {
        padding: 12px 8px;
        vertical-align: middle;
        border-bottom: 1px solid #f2f2f2;
    }

    /* Responsive Design */
    @media screen and (max-width: 768px) {
        .ralan-container {
            padding: 10px;
        }

        .table-container {
            margin: 0 -10px;
        }

        .table-pasien {
            min-width: 800px;
        }

        .table-pasien th,
        .table-pasien td {
            padding: 10px 6px;
            font-size: 13px;
        }

        /* Adjust column widths for mobile */
        .table-pasien th:nth-child(1),
        .table-pasien td:nth-child(1) {
            min-width: 60px;
        }

        /* No Reg */

        .table-pasien th:nth-child(2),
        .table-pasien td:nth-child(2) {
            min-width: 180px;
        }

        /* Nama */

        .table-pasien th:nth-child(3),
        .table-pasien td:nth-child(3) {
            min-width: 150px;
        }

        /* No Rawat */

        .table-pasien th:nth-child(4),
        .table-pasien td:nth-child(4) {
            min-width: 100px;
        }

        /* Telp */

        .table-pasien th:nth-child(5),
        .table-pasien td:nth-child(5) {
            min-width: 150px;
        }

        /* Dokter */

        .table-pasien th:nth-child(6),
        .table-pasien td:nth-child(6) {
            min-width: 80px;
        }

        /* Status */
    }

    @media screen and (max-width: 480px) {
        .ralan-container {
            padding: 5px;
        }

        .table-pasien {
            min-width: 650px;
        }

        .table-pasien th,
        .table-pasien td {
            padding: 8px 4px;
            font-size: 12px;
        }

        /* Further reduce widths for very small screens */
        .table-pasien th:nth-child(1),
        .table-pasien td:nth-child(1) {
            min-width: 50px;
        }

        .table-pasien th:nth-child(2),
        .table-pasien td:nth-child(2) {
            min-width: 150px;
        }

        .table-pasien th:nth-child(3),
        .table-pasien td:nth-child(3) {
            min-width: 130px;
        }

        .table-pasien th:nth-child(4),
        .table-pasien td:nth-child(4) {
            min-width: 90px;
        }

        .table-pasien th:nth-child(5),
        .table-pasien td:nth-child(5) {
            min-width: 130px;
        }

        .table-pasien th:nth-child(6),
        .table-pasien td:nth-child(6) {
            min-width: 70px;
        }
    }

    /* Hover Effects */
    .table-pasien tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    /* Scrollbar Styling */
    .table-container::-webkit-scrollbar {
        height: 6px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #666;
    }

    /* Pagination styling */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    .pagination {
        margin: 0;
    }

    .pagination .page-item .page-link {
        border: none;
        color: #495057;
        padding: 8px 12px;
        margin: 0 2px;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        color: #fff;
    }

    .pagination .page-item .page-link:hover {
        background-color: #e9ecef;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 13px;
    }

    /* Quick stats styling */
    .quick-stats {
        display: flex;
        gap: 15px;
    }

    .stat-item {
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 8px;
        text-align: center;
        min-width: 100px;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    #manualRefreshBtn {
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        background-color: #28a745;
        border-color: #28a745;
        transition: all 0.3s;
    }

    #manualRefreshBtn:hover {
        background-color: #218838;
        border-color: #1e7e34;
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    #manualRefreshBtn i {
        margin-right: 5px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        display: block;
    }

    .stat-label {
        font-size: 13px;
        opacity: 0.9;
    }

    /* Animasi rotasi untuk tombol refresh */
    .rotating i {
        animation: rotate 1s linear infinite;
    }

    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Status Badge Styling */
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        transition: all 0.2s ease;
    }

    .status-badge.completed {
        background-color: #e8f5e9;
        color: #2e7d32;
    }

    .status-badge.pending {
        background-color: #ffebee;
        color: #c62828;
    }

    /* Filter Box Styling */
    .filter-box {
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 15px;
    }

    .filter-status-select {
        width: 200px;
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        font-size: 14px;
        color: #495057;
        background-color: #fff;
        transition: all 0.2s ease;
    }

    .filter-status-select:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
    }

    /* Button Group Styling */
    .btn-group-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-group-actions .btn {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    /* Patient Name Styling */
    .patient-name {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #2c3e50;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .patient-name:hover {
        color: #007bff;
    }

    .patient-fullname {
        font-weight: 500;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .filter-box {
            padding: 10px;
        }

        .filter-status-select {
            width: 100%;
            margin-bottom: 10px;
        }

        .btn-group-actions {
            flex-direction: column;
            width: 100%;
        }

        .btn-group-actions .btn {
            width: 100%;
            justify-content: center;
        }

        .status-badge {
            padding: 4px 8px;
            font-size: 11px;
        }

        .patient-name {
            gap: 5px;
        }

        .patient-fullname {
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .filter-box {
            margin-bottom: 10px;
        }

        .btn-group-actions .btn {
            padding: 6px 12px;
            font-size: 13px;
        }

        .status-badge {
            padding: 3px 6px;
            font-size: 10px;
        }
    }

    /* Toastr Notification Styling */
    #toast-container {
        position: fixed;
        z-index: 999999;
        pointer-events: none;
    }

    #toast-container.toast-bottom-right {
        right: 12px;
        bottom: 12px;
    }

    #toast-container>div {
        position: relative;
        pointer-events: auto;
        overflow: hidden;
        margin: 0 0 6px;
        padding: 15px 15px 15px 50px;
        width: 300px;
        border-radius: 8px;
        background-position: 15px center;
        background-repeat: no-repeat;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        color: #ffffff;
        opacity: 0.95;
    }

    #toast-container>.toast-success {
        background-color: #28a745;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='%23fff' d='M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z'%3E%3C/path%3E%3C/svg%3E");
    }

    /* Loading Overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-overlay .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    /* Animasi loading baru */
    .loading-overlay {
        backdrop-filter: blur(5px);
        transition: opacity 0.5s ease;
    }

    .loading-overlay .loading-content {
        text-align: center;
    }

    .loading-overlay .loading-text {
        margin-top: 15px;
        color: #007bff;
        font-weight: 600;
        font-size: 16px;
    }

    .loading-overlay .progress {
        width: 250px;
        height: 10px;
        margin: 10px auto;
        border-radius: 10px;
        background-color: #e9ecef;
    }

    .loading-overlay .progress-bar {
        background-color: #007bff;
        border-radius: 10px;
        transition: width 0.8s ease;
    }

    .loading-overlay .spinner-grow {
        color: #007bff;
    }

    .loading-overlay .loading-icon {
        position: relative;
        display: inline-block;
    }

    .pulse-animation {
        animation: pulse 1.5s infinite ease-in-out;
    }

    @keyframes pulse {
        0% {
            transform: scale(0.95);
            opacity: 0.7;
        }

        50% {
            transform: scale(1.05);
            opacity: 1;
        }

        100% {
            transform: scale(0.95);
            opacity: 0.7;
        }
    }

    /* Modal Styling */
    #confirmPanggilModal .modal-content {
        border-radius: 10px;
        border: none;
    }

    #confirmPanggilModal .modal-header {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    #confirmPanggilModal .modal-title {
        font-weight: 600;
    }

    #confirmPanggilModal .close {
        color: white;
    }

    #confirmPanggilModal .modal-footer {
        border-top: 1px solid #eee;
    }

    #confirmPanggilModal .btn-primary {
        background: #007bff;
        border: none;
        padding: 8px 20px;
    }

    #confirmPanggilModal .btn-secondary {
        background: #6c757d;
        border: none;
        padding: 8px 20px;
    }
</style>
@stop

@section('plugins.TempusDominusBs4', true)
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    // Konfigurasi toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "2000", // Lebih cepat (2 detik)
        "extendedTimeOut": "1000",
        "preventDuplicates": true, // Mencegah duplikasi notifikasi
        "newestOnTop": true, // Terbaru di atas
        "showDuration": "300",
        "hideDuration": "300"
    };

    // Setup CSRF token untuk semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        // Set default sort option
        let currentSortOption = 'no_reg_asc';
        $('#sortDropdown').data('sort', currentSortOption);
        
        // Tambahkan variabel baru untuk status pencarian aktif
        let isSearchActive = false;
        let searchTimeout = null;
        
        // Handle sort option selection
        $('.sort-option').on('click', function(e) {
            e.preventDefault();
            currentSortOption = $(this).data('sort');
            $('#sortDropdown').data('sort', currentSortOption);
            
            // Update the sort info text based on selection
            let sortIconClass = 'fas fa-sort-numeric-down';
            let sortText = 'No. Registrasi ASC';
            
            switch(currentSortOption) {
                case 'no_reg_desc':
                    sortIconClass = 'fas fa-sort-numeric-down-alt';
                    sortText = 'No. Registrasi DESC';
                    break;
                case 'nm_pasien_asc':
                    sortIconClass = 'fas fa-sort-alpha-down';
                    sortText = 'Nama Pasien (A-Z)';
                    break;
                case 'nm_pasien_desc':
                    sortIconClass = 'fas fa-sort-alpha-down-alt';
                    sortText = 'Nama Pasien (Z-A)';
                    break;
                case 'stts_asc':
                    sortIconClass = 'fas fa-sort';
                    sortText = 'Status (Menunggu-Selesai)';
                    break;
                case 'stts_desc':
                    sortIconClass = 'fas fa-sort';
                    sortText = 'Status (Selesai-Menunggu)';
                    break;
            }
            
            $('#sort-label').html(`<small><i class="${sortIconClass}"></i> Diurutkan berdasarkan ${sortText}</small>`);
            
            // Refresh data with new sort option
            refreshData();
        });
        
        // Variabel untuk menyimpan konfigurasi paginasi
        const pageSize = 10;
        let currentPage = 1;
        let pasienData = [];
        let filteredData = [];
        
        // Inisialisasi data dari tabel SEBELUM DataTable diinisialisasi
        initializePaginationData();
        
        // Modifikasi setup DataTable untuk tabel pasien ralan
        let tablePasienRalan = $('#tablePasienRalan').DataTable({
            "paging": false,
            "searching": false, // Nonaktifkan pencarian bawaan DataTables
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "order": [[0, 'asc']],
            "retrieve": true, // Mencegah reinisialisasi
            "destroy": false, // Jangan hancurkan data yang ada
            "language": {
                "search": "Cari:",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "infoEmpty": "Tidak ada data yang tersedia"
            }
        });
        
        // Tambahkan fungsi pencarian manual dengan delay
        $('#searchBox').on('keyup', function() {
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            // Tandai pencarian sedang aktif
            isSearchActive = true;
            
            // Set flag untuk mencegah refresh otomatis
            window.isViewingDetails = true;
            
            // Dapatkan nilai pencarian
            const searchTerm = $(this).val().toLowerCase();
            
            // Terapkan delay 500ms untuk menghindari pencarian terlalu sering
            searchTimeout = setTimeout(function() {
                // Filter data berdasarkan kata kunci pencarian
                if (searchTerm.length > 0) {
                    // Filter data dari sumber asli
                    filteredData = pasienData.filter(item => {
                        return item.nama.toLowerCase().includes(searchTerm) || 
                               item.no_reg.toLowerCase().includes(searchTerm) ||
                               item.no_rawat.toLowerCase().includes(searchTerm) ||
                               item.telp.toLowerCase().includes(searchTerm);
                    });
                } else {
                    // Reset filter ke filter status saat ini
                    const currentStatus = $('#filterStatus').val();
                    if (currentStatus) {
                        filteredData = pasienData.filter(item => item.status.includes(currentStatus));
                    } else {
                        filteredData = [...pasienData];
                    }
                    
                    // Set flag bahwa pencarian tidak aktif lagi
                    isSearchActive = false;
                    window.isViewingDetails = false;
                }
                
                // Update paginasi
                createPagination(filteredData.length, pageSize, 'pasien-pagination');
                
                // Update info yang ditampilkan
                updatePaginationInfo(1, filteredData.length, 'showing-from', 'showing-to', 'total-entries');
                
                // Tampilkan halaman pertama
                displayPasienPage(1);
            }, 500);
        });
        
        // Tambahkan handler untuk tombol reset pencarian
        $('#clearSearchBtn').on('click', function() {
            // Reset nilai kotak pencarian
            $('#searchBox').val('');
            
            // Reset flag pencarian
            isSearchActive = false;
            window.isViewingDetails = false;
            
            // Reset filter ke filter status saat ini
            const currentStatus = $('#filterStatus').val();
            if (currentStatus) {
                filteredData = pasienData.filter(item => item.status.includes(currentStatus));
            } else {
                filteredData = [...pasienData];
            }
            
            // Update paginasi
            createPagination(filteredData.length, pageSize, 'pasien-pagination');
            
            // Update info yang ditampilkan
            updatePaginationInfo(1, filteredData.length, 'showing-from', 'showing-to', 'total-entries');
            
            // Tampilkan halaman pertama
            displayPasienPage(1);
            
            // Tampilkan notifikasi
            toastr.info('Pencarian telah direset');
        });
        
        // Set filter status default dan terapkan filter saat halaman dimuat
        // Pindahkan ke setTimeout untuk memastikan DOM sudah siap
        setTimeout(function() {
            // Tampilkan semua data saat halaman dimuat (tanpa filter default)
            console.log('Page loaded, showing all data');
            console.log('Total data available:', pasienData.length);
            
            // Reset filter dropdown ke "Semua"
            $('#filterStatus').val('');
            
            // Tampilkan semua data
            filterAndDisplayPasien('');
        }, 100);
        
        // Tambahkan event listener untuk tombol filter status
        $('#filterStatus').on('change', function() {
            const status = $(this).val();
            filterAndDisplayPasien(status);
        });
        
        // Tambahkan event listener untuk tombol terapkan filter
        $('#applyFilterBtn').on('click', function() {
            const status = $('#filterStatus').val();
            filterAndDisplayPasien(status);
        });
        
        // Fungsi untuk menginisialisasi data paginasi
        function initializePaginationData() {
            // Kumpulkan data pasien dari tabel
            pasienData = [];
            $('#tablePasienRalan tbody tr').each(function() {
                const $row = $(this);
                
                // Skip jika ini adalah row kosong atau pesan
                if ($row.find('td').length < 6) {
                    return;
                }
                
                const row = {
                    no_reg: $row.find('td:eq(0)').text().trim(),
                    nama: $row.find('td:eq(1)').text().trim(),
                    no_rawat: $row.find('td:eq(2)').text().trim(),
                    telp: $row.find('td:eq(3)').text().trim(),
                    dokter: $row.find('td:eq(4)').text().trim(),
                    status: $row.find('td:eq(5)').text().trim(),
                    html: $row[0].outerHTML
                };
                
                // Hanya tambahkan jika ada data yang valid
                if (row.no_reg || row.nama || row.no_rawat) {
                    pasienData.push(row);
                }
            });
            
            console.log('Data pasien yang diinisialisasi:', pasienData.length, 'rows');
            console.log('Sample data:', pasienData.slice(0, 2));
            
            // Set data yang difilter sama dengan data asli di awal
            filteredData = [...pasienData];
            
            // Tampilkan semua data jika ada
            if (pasienData.length > 0) {
                displayPasienPage(1);
                createPagination(filteredData.length, pageSize, 'pasien-pagination');
                updatePaginationInfo(1, Math.min(pageSize, filteredData.length), 'showing-from', 'showing-to', 'total-entries');
            } else {
                console.log('No patient data found in table');
                // Tampilkan pesan tidak ada data
                $('#tablePasienRalan tbody').html(`
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle mr-2"></i>
                            Tidak ada data pasien
                        </td>
                    </tr>
                `);
            }
            
            // Inisialisasi data rujukan internal
            const rujukData = [];
            $('#tableRujuk tbody tr').each(function() {
                rujukData.push($(this)[0].outerHTML);
            });
            
            // Buat paginasi untuk rujukan
            if (rujukData.length > 0) {
                createPagination(rujukData.length, pageSize, 'rujuk-pagination');
                displayRujukPage(1, rujukData);
            }
        }
        
        // Fungsi untuk memfilter dan menampilkan data pasien
        function filterAndDisplayPasien(status) {
            console.log('Filtering with status:', status);
            console.log('Available data:', pasienData.length);
            
            // Filter data berdasarkan status
            if (status) {
                filteredData = pasienData.filter(item => {
                    const itemStatus = item.status.toLowerCase();
                    const filterStatus = status.toLowerCase();
                    return itemStatus.includes(filterStatus);
                });
            } else {
                filteredData = [...pasienData];
            }
            
            console.log('Filtered data:', filteredData.length);
            
            // Update paginasi
            createPagination(filteredData.length, pageSize, 'pasien-pagination');
            
            // Update info yang ditampilkan
            updatePaginationInfo(1, filteredData.length, 'showing-from', 'showing-to', 'total-entries');
            
            // Tampilkan halaman pertama
            displayPasienPage(1);
            
            // Tampilkan notifikasi jika tidak ada data
            if (filteredData.length === 0 && status) {
                toastr.warning(`Tidak ada pasien dengan status "${status}"`);
            }
        }
        
        // Fungsi untuk menampilkan halaman tertentu dari data pasien
        function displayPasienPage(pageNum) {
            console.log('Displaying page:', pageNum, 'of filtered data:', filteredData.length);
            
            const startIndex = (pageNum - 1) * pageSize;
            const endIndex = Math.min(startIndex + pageSize, filteredData.length);
            
            console.log('Showing rows from index', startIndex, 'to', endIndex);
            
            // Kosongkan tabel
            $('#tablePasienRalan tbody').empty();
            
            // Tampilkan data untuk halaman ini
            if (filteredData.length > 0) {
                for (let i = startIndex; i < endIndex; i++) {
                    if (filteredData[i] && filteredData[i].html) {
                        $('#tablePasienRalan tbody').append(filteredData[i].html);
                    }
                }
            } else {
                // Tampilkan pesan jika tidak ada data
                $('#tablePasienRalan tbody').append(`
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle mr-2"></i>
                            Tidak ada data pasien yang sesuai dengan filter
                        </td>
                    </tr>
                `);
            }
            
            // Update info paginasi
            updatePaginationInfo(pageNum, filteredData.length, 'showing-from', 'showing-to', 'total-entries');
            
            // Simpan halaman saat ini
            currentPage = pageNum;
            
            // Highlight tombol pagination yang aktif
            $(`#pasien-pagination .page-item`).removeClass('active');
            $(`#pasien-pagination .page-item[data-page="${pageNum}"]`).addClass('active');
        }
        
        // Fungsi untuk menampilkan halaman tertentu dari data rujukan
        function displayRujukPage(pageNum, rujukData) {
            const startIndex = (pageNum - 1) * pageSize;
            const endIndex = Math.min(startIndex + pageSize, rujukData.length);
            
            // Kosongkan tabel
            $('#tableRujuk tbody').empty();
            
            // Tampilkan data untuk halaman ini
            for (let i = startIndex; i < endIndex; i++) {
                $('#tableRujuk tbody').append(rujukData[i]);
            }
            
            // Update info paginasi
            updatePaginationInfo(pageNum, rujukData.length, 'rujuk-showing-from', 'rujuk-showing-to', 'rujuk-total-entries');
            
            // Highlight tombol pagination yang aktif
            $(`#rujuk-pagination .page-item`).removeClass('active');
            $(`#rujuk-pagination .page-item[data-page="${pageNum}"]`).addClass('active');
        }
        
        // Fungsi untuk membuat pagination
        function createPagination(totalItems, itemsPerPage, containerId) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            
            // Reset pagination container
            $(`#${containerId}`).empty();
            
            // Jika hanya 1 halaman, jangan tampilkan pagination
            if (totalPages <= 1) {
                return;
            }
            
            // Tambahkan tombol Previous
            $(`#${containerId}`).append(`
                <li class="page-item" data-page="prev">
                    <a class="page-link" href="javascript:void(0)"><i class="fas fa-angle-left"></i></a>
                </li>
            `);
            
            // Tambahkan tombol halaman
            for (let i = 1; i <= totalPages; i++) {
                $(`#${containerId}`).append(`
                    <li class="page-item ${i === 1 ? 'active' : ''}" data-page="${i}">
                        <a class="page-link" href="javascript:void(0)">${i}</a>
                    </li>
                `);
            }
            
            // Tambahkan tombol Next
            $(`#${containerId}`).append(`
                <li class="page-item" data-page="next">
                    <a class="page-link" href="javascript:void(0)"><i class="fas fa-angle-right"></i></a>
                </li>
            `);
            
            // Event listener untuk pagination pasien
            if (containerId === 'pasien-pagination') {
                $(`#${containerId} .page-item`).on('click', function() {
                    const page = $(this).data('page');
                    
                    if (page === 'prev') {
                        if (currentPage > 1) displayPasienPage(currentPage - 1);
                    } else if (page === 'next') {
                        if (currentPage < totalPages) displayPasienPage(currentPage + 1);
                    } else {
                        displayPasienPage(page);
                    }
                });
            } 
            // Event listener untuk pagination rujukan
            else if (containerId === 'rujuk-pagination') {
                let rujukCurrentPage = 1;
                
                $(`#${containerId} .page-item`).on('click', function() {
                    const page = $(this).data('page');
                    
                    // Simpan data rujukan
                    const rujukData = [];
                    $('#tableRujuk tbody tr').each(function() {
                        rujukData.push($(this)[0].outerHTML);
                    });
                    
                    if (page === 'prev') {
                        if (rujukCurrentPage > 1) {
                            rujukCurrentPage--;
                            displayRujukPage(rujukCurrentPage, rujukData);
                        }
                    } else if (page === 'next') {
                        if (rujukCurrentPage < totalPages) {
                            rujukCurrentPage++;
                            displayRujukPage(rujukCurrentPage, rujukData);
                        }
                    } else {
                        rujukCurrentPage = page;
                        displayRujukPage(rujukCurrentPage, rujukData);
                    }
                });
            }
        }
        
        // Fungsi untuk memperbarui info pagination
        function updatePaginationInfo(pageNum, totalItems, fromId, toId, totalId) {
            const startIndex = (pageNum - 1) * pageSize + 1;
            const endIndex = Math.min(startIndex + pageSize - 1, totalItems);
            
            $(`#${fromId}`).text(totalItems > 0 ? startIndex : 0);
            $(`#${toId}`).text(endIndex);
            $(`#${totalId}`).text(totalItems);
        }
        
        // Tambahkan variabel untuk debounce dan timestamp refresh terakhir
        let lastRefreshTime = 0;
        let isRefreshPending = false;
        
        // Fungsi untuk memperbarui tabel dengan data baru - tambahkan proteksi format
        function updateTableWithNewData(response, activeTab) {
            console.log('Memperbarui tabel dengan data baru:', response);
            console.log('Data statistik yang diterima:', {
                total: response.statistik.total,
                selesai: response.statistik.selesai,
                menunggu: response.statistik.menunggu,
                dataCount: response.dataCount || response.pasienRalan.length
            });
            
            // Cek konsistensi data
            if (response.statistik.total !== (response.dataCount || response.pasienRalan.length)) {
                console.warn('Inkonsistensi terdeteksi: Statistik total (' + response.statistik.total + 
                             ') tidak sama dengan jumlah data (' + (response.dataCount || response.pasienRalan.length) + ')');
            }
            
            // Update statistik untuk semua tab
            $('#totalPasien .stat-value').text(response.statistik.total);
            $('#selesaiPasien .stat-value').text(response.statistik.selesai);
            $('#menungguPasien .stat-value').text(response.statistik.menunggu);
            
            // Tambahkan efek highlight pada statistik yang diperbarui
            $('#totalPasien, #selesaiPasien, #menungguPasien').addClass('stat-updated');
            setTimeout(function() {
                $('#totalPasien, #selesaiPasien, #menungguPasien').removeClass('stat-updated');
            }, 2000);
            
            if (activeTab === 'pasien') {
                // Jika pencarian sedang aktif, jangan perbarui data tabel
                if (isSearchActive) {
                    console.log('Pencarian aktif, mempertahankan hasil pencarian.');
                    // Hanya update statistik
                    return;
                }
                
                // Tampilkan notifikasi kecil di pojok kanan bawah
                toastr.info('<i class="fas fa-sync-alt refresh-spin mr-2"></i> Data pasien diperbarui', '', {
                    positionClass: 'toast-bottom-right',
                    timeOut: 3000,
                    closeButton: false,
                    progressBar: false
                });
                
                // Jika ada perubahan data yang signifikan, tampilkan indikator kecil di header
                if (response.returnFullData) {
                    // Tambahkan indikator refresh tanpa mengganggu
                    const refreshIndicator = $('<div class="refresh-indicator"><i class="fas fa-sync-alt refresh-spin mr-1"></i> Data berubah - <a href="#" id="refreshNowLink">Muat ulang sekarang</a> atau tunggu auto-refresh</div>');
                    if ($('.refresh-indicator').length === 0) {
                        $('.ralan-header').append(refreshIndicator);
                        refreshIndicator.fadeIn(300);
                        
                        // Tambahkan event handler untuk link refresh sekarang
                        $('#refreshNowLink').on('click', function(e) {
                            e.preventDefault();
                            refreshIndicator.fadeOut(300, function() {
                                $(this).remove();
                            });
                            // Muat ulang halaman dengan cara yang halus
                            $('#manualRefreshBtn').html('<i class="fas fa-sync-alt fa-spin"></i> Memperbarui...').prop('disabled', true);
                            setTimeout(function() {
                                window.location.reload(true);
                            }, 500);
                        });
                        
                        // Hilangkan indikator setelah 15 detik
                        setTimeout(function() {
                            refreshIndicator.fadeOut(300, function() {
                                $(this).remove();
                            });
                        }, 15000);
                    }
                }
                
                return;
            } else if (activeTab === 'rujuk') {
                // Tampilkan notifikasi kecil di pojok kanan bawah
                toastr.info('<i class="fas fa-sync-alt refresh-spin mr-2"></i> Data rujukan diperbarui', '', {
                    positionClass: 'toast-bottom-right',
                    timeOut: 3000,
                    closeButton: false,
                    progressBar: false
                });
                
                // Jika ada perubahan data yang signifikan, tampilkan indikator kecil di header
                if (response.returnFullData) {
                    // Tambahkan indikator refresh tanpa mengganggu
                    const refreshIndicator = $('<div class="refresh-indicator"><i class="fas fa-sync-alt refresh-spin mr-1"></i> Data rujukan berubah - <a href="#" id="refreshNowLink">Muat ulang sekarang</a> atau tunggu auto-refresh</div>');
                    if ($('.refresh-indicator').length === 0) {
                        $('.ralan-header').append(refreshIndicator);
                        refreshIndicator.fadeIn(300);
                        
                        // Tambahkan event handler untuk link refresh sekarang
                        $('#refreshNowLink').on('click', function(e) {
                            e.preventDefault();
                            refreshIndicator.fadeOut(300, function() {
                                $(this).remove();
                            });
                            // Muat ulang halaman dengan cara yang halus
                            $('#manualRefreshBtn').html('<i class="fas fa-sync-alt fa-spin"></i> Memperbarui...').prop('disabled', true);
                            setTimeout(function() {
                                window.location.reload(true);
                            }, 500);
                        });
                        
                        // Hilangkan indikator setelah 15 detik
                        setTimeout(function() {
                            refreshIndicator.fadeOut(300, function() {
                                $(this).remove();
                            });
                        }, 15000);
                    }
                }
                
                return;
            }
            
            console.log('Tabel berhasil diperbarui. Tab aktif:', activeTab);
        }
        
        // Handler untuk tab changes
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // Sesuaikan ukuran tabel saat tab berubah
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
        
        // Tambahkan event listener untuk tombol refresh
        $('#manualRefreshBtn').on('click', function() {
            refreshData(true); // force refresh
        });
        
        // Variabel untuk menyimpan interval
        let autoRefreshInterval;
        
        // Counter untuk menghitung berapa kali status diperbarui
        let statusUpdateCounter = 0;
        
        // Tambahkan variabel global untuk menandai sedang melihat detail
        window.isViewingDetails = false;
        
        // Tambahkan event listener untuk mendeteksi navigasi ke halaman detail
        $(document).on('click', '.patient-name', function(e) {
            // Tandai sedang melihat detail
            window.isViewingDetails = true;
            
            // Simpan URL yang akan dibuka
            const targetUrl = $(this).attr('href');
            
            // Nonaktifkan auto refresh
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                console.log('Auto refresh dinonaktifkan karena navigasi ke halaman detail');
            }
            
            // Cegah penutupan halaman saat navigasi dengan timeout
            e.preventDefault();
            
            // Tambahkan toast notifikasi
            toastr.info('Membuka halaman pemeriksaan...', '', {timeOut: 1500});
            
            // Navigasi ke URL target setelah delay kecil
            setTimeout(function() {
                window.location.href = targetUrl;
            }, 100);
        });
        
        // Fungsi untuk mengatur auto-refresh
        function setupAutoRefresh() {
            // Hapus interval yang ada jika sudah diset
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
            
            // Set interval untuk auto-refresh data setiap 60 detik (1 menit)
            // Interval diperlambat secara signifikan untuk mengurangi beban server
            autoRefreshInterval = setInterval(function() {
                if (!window.isViewingDetails && !isRefreshPending) {
                    refreshData(false); // regular refresh
                }
            }, 60000); // diperlambat menjadi 60 detik (1 menit)
        }
        
        // Aktifkan auto refresh
        setupAutoRefresh();
        
        // Handler untuk tombol panggil pasien
        $(document).on('click', '.btn-panggil', function(e) {
            e.preventDefault(); // Mencegah default action
            const noReg = $(this).data('no-reg');
            const nama = $(this).data('nama');
            const poli = $(this).data('poli');
            const noRawat = $(this).data('no-rawat');
            
            // Konfirmasi dengan custom dialog
            const confirmDialog = $(`
                <div class="modal fade" id="confirmPanggilModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Panggilan</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Panggil pasien ${nama}?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-primary" id="btnConfirmPanggil">Oke</button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            
            // Tampilkan dialog
            confirmDialog.modal('show');
            
            // Handler untuk tombol konfirmasi
            confirmDialog.find('#btnConfirmPanggil').on('click', function() {
                // Tutup dialog
                confirmDialog.modal('hide');
                
                // Tampilkan loading
                const loadingOverlay = $(`
                    <div class="loading-overlay">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                `).appendTo('body');
                
                // Panggil pasien via AJAX
                $.ajax({
                    url: '{{ route("ralan.panggil-pasien") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        no_reg: noReg,
                        nama: nama,
                        poli: poli,
                        no_rawat: noRawat,
                        is_ulang: false
                    },
                    success: function(response) {
                        if (response.success) {
                            // Tampilkan notifikasi sukses
                            toastr.success('Berhasil memanggil pasien');
                            
                            // Refresh data tabel
                            refreshData(true);
                            
                            // Broadcast event ke display antrian
                            if (window.Echo) {
                                window.Echo.channel('antrian')
                                    .whisper('antrian.dipanggil', {
                                        no_reg: noReg,
                                        nama: nama,
                                        poli: poli,
                                        is_ulang: false
                                    });
                            }
                        } else {
                            toastr.error(response.message || 'Gagal memanggil pasien');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        toastr.error('Terjadi kesalahan saat memanggil pasien');
                    },
                    complete: function() {
                        // Hapus loading overlay
                        loadingOverlay.remove();
                    }
                });
            });
        });
        
        // Handler untuk tombol ulang panggil
        $(document).on('click', '.btn-ulang-panggil', function(e) {
            e.preventDefault(); // Mencegah default action
            const noReg = $(this).data('no-reg');
            const nama = $(this).data('nama');
            const poli = $(this).data('poli');
            const noRawat = $(this).data('no-rawat');
            
            // Langsung panggil tanpa konfirmasi
            panggilPasien(noReg, nama, poli, noRawat, true);
        });
        
        // Fungsi untuk memanggil pasien
        function panggilPasien(noReg, nama, poli, noRawat, isUlang) {
            // Tampilkan loading
            const loadingOverlay = $(`
                <div class="loading-overlay">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            `).appendTo('body');
            
            $.ajax({
                url: '{{ route("ralan.panggil-pasien") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    no_reg: noReg,
                    nama: nama,
                    poli: poli,
                    no_rawat: noRawat,
                    is_ulang: isUlang
                },
                success: function(response) {
                    if (response.success) {
                        // Tampilkan notifikasi sukses
                        toastr.success(isUlang ? 'Berhasil memanggil ulang pasien' : 'Berhasil memanggil pasien');
                        
                        // Refresh data tabel
                        refreshData(true);
                        
                        // Broadcast event ke display antrian
                        if (window.Echo) {
                            window.Echo.channel('antrian')
                                .whisper('antrian.dipanggil', {
                                    no_reg: noReg,
                                    nama: nama,
                                    poli: poli,
                                    is_ulang: isUlang
                                });
                        }
                    } else {
                        toastr.error(response.message || 'Gagal memanggil pasien');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    toastr.error('Terjadi kesalahan saat memanggil pasien');
                },
                complete: function() {
                    // Hapus loading overlay
                    loadingOverlay.remove();
                }
            });
        }
        
        // Initial refresh setelah halaman dimuat
        setTimeout(function() {
            refreshData(true); // force initial refresh
        }, 1000);
        
        // Fungsi helper untuk menghasilkan URL pemeriksaan yang konsisten
        function generatePemeriksaanUrl(noRawat, noRM) {
            // Pastikan parameter tidak null
            noRawat = noRawat || '';
            noRM = noRM || '';
            
            // Bersihkan parameter dari karakter khusus yang mungkin menyebabkan masalah
            noRawat = noRawat.trim();
            noRM = noRM.trim();
            
            // Gunakan encoding yang konsisten dengan sistem
            // Metode 1: btoa + encodeURIComponent (yang biasa digunakan)
            const encodedNoRawat = encodeURIComponent(btoa(noRawat));
            const encodedNoRM = encodeURIComponent(btoa(noRM));
            
            // Daripada hanya mengandalkan URL dengan parameter,
            // kita tambahkan secara langsung untuk memastikan format yang benar
            let urlParams = new URLSearchParams();
            urlParams.append('no_rawat', encodedNoRawat);
            urlParams.append('no_rm', encodedNoRM);
            
            // Buat URL dengan parameter yang dienkripsi
            return `{{url('ralan/pemeriksaan')}}?${urlParams.toString()}`;
        }
        
        // Fungsi untuk refresh data - dengan pengecekan lebih ketat
        function refreshData(forceRefresh = false) {
            // Cek jika sedang dalam proses pencarian, jangan refresh
            if (isSearchActive) {
                console.log('Pencarian sedang aktif, refresh dibatalkan');
                return;
            }
            
            // Cek jika sedang dalam proses melihat detail, jangan refresh
            if (window.isViewingDetails) {
                console.log('Sedang melihat detail, refresh dibatalkan');
                return;
            }
            
            // Tambahkan debounce untuk mencegah multiple refresh bersamaan
            if (isRefreshPending) {
                console.log('Refresh sudah dalam proses, menunggu selesai');
                return;
            }
            
            // Periksa waktu terakhir refresh, jangan refresh jika belum 15 detik
            // kecuali jika forceRefresh = true (refresh manual)
            const currentTime = new Date().getTime();
            if (!forceRefresh && (currentTime - lastRefreshTime < 15000)) {
                console.log('Refresh terlalu cepat, dibatalkan. Silakan tunggu minimal 15 detik.');
                return;
            }
            
            // Set flag bahwa refresh sedang berjalan
            isRefreshPending = true;
            lastRefreshTime = currentTime;
            
            // Tambahkan indikator loading
            $('.quick-stats').addClass('loading-stats');
            $('#manualRefreshBtn').addClass('rotating');
            
            // Dapatkan tab aktif
            const activeTab = $('.tab-pane.active').attr('id');
            
            // Tandai waktu request untuk monitoring
            const requestTime = new Date().getTime();
            
            // Lakukan AJAX request untuk mendapatkan data terbaru dengan timeout yang lebih lama
            $.ajax({
                url: '{{ route("ralan.refresh-data") }}',
                method: 'GET',
                data: {
                    tanggal: '{{ $tanggal }}',
                    _token: '{{ csrf_token() }}',
                    sort: currentSortOption,
                    force: forceRefresh ? 1 : 0,
                    last_count: pasienData.length, 
                    request_time: requestTime,
                    last_refresh_time: lastRefreshTime
                },
                dataType: 'json',
                timeout: 30000, // Tambahkan timeout 30 detik untuk memberikan waktu server merespon
                success: function(response) {
                    // Reset flag refresh pending
                    isRefreshPending = false;
                    
                    // Periksa apakah pencarian diaktifkan selama menunggu response
                    if (isSearchActive) {
                        console.log('Pencarian aktif selama proses refresh, hanya update statistik');
                        // Update statistik saja, jangan ubah data tabel
                        if (response.success) {
                            $('#totalPasien .stat-value').text(response.statistik.total);
                            $('#selesaiPasien .stat-value').text(response.statistik.selesai);
                            $('#menungguPasien .stat-value').text(response.statistik.menunggu);
                        }
                        return;
                    }
                    
                    const responseTime = new Date().getTime();
                    const elapsed = responseTime - requestTime;
                    
                    console.log(`Menerima data refresh dalam ${elapsed}ms:`, response);
                    
                    if (response.success) {
                        // Update statistik - selalu dilakukan
                        $('#totalPasien .stat-value').text(response.statistik.total);
                        $('#selesaiPasien .stat-value').text(response.statistik.selesai);
                        $('#menungguPasien .stat-value').text(response.statistik.menunggu);
                        
                        // Periksa apakah server mengembalikan data lengkap
                        if (response.returnFullData) {
                            // Jika server mengembalikan data lengkap, perbarui UI
                            const newCount = response.dataCount;
                            const currentCount = pasienData.length;
                            
                            console.log('Memperbarui UI: Data count changed from ' + currentCount + ' to ' + newCount);
                            
                            // Perbarui data tabel dengan data dari response
                            updateTableWithNewData(response, activeTab);
                            
                            // Notifikasi sukses dengan animasi yang lebih halus
                            if (forceRefresh) {
                                toastr.success('Data berhasil diperbarui. Total: ' + response.statistik.total + ' pasien');
                            } else if (newCount > currentCount) {
                                toastr.info('Terdapat data pasien baru. Total: ' + response.statistik.total + ' pasien');
                            } else if (newCount < currentCount) {
                                toastr.warning('Jumlah pasien berkurang. Total: ' + response.statistik.total + ' pasien');
                            }
                            
                            // Jika returnFullData benar tapi jumlah data tidak berubah, mungkin status pasien berubah
                            if (newCount === currentCount && !forceRefresh) {
                                // Hanya tampilkan notifikasi status diperbarui setiap 5 kali update
                                statusUpdateCounter++;
                                if (statusUpdateCounter % 5 === 0 || forceRefresh) {
                                    toastr.info('Status pasien telah diperbarui');
                                    statusUpdateCounter = 0; // Reset counter setelah notifikasi muncul
                                }
                            }
                        } else {
                            // Jika server tidak mengembalikan data lengkap, data tidak berubah
                            console.log('Tidak ada perubahan data, hanya update statistik');
                        }
                    } else {
                        console.error('Gagal memperbarui data: Response tidak sukses');
                        if (forceRefresh) {
                            toastr.error('Gagal memperbarui data');
                        }
                    }
                },
                error: function(error) {
                    // Reset flag refresh pending
                    isRefreshPending = false;
                    
                    console.error('Gagal memperbarui data:', error);
                    if (forceRefresh) {
                        toastr.error('Gagal memperbarui data: ' + error.statusText);
                    }
                    
                    // Jika terjadi error, restart auto-refresh
                    setupAutoRefresh();
                },
                complete: function() {
                    // Hapus indikator loading
                    $('.quick-stats').removeClass('loading-stats');
                    $('#manualRefreshBtn').removeClass('rotating');
                }
            });
        }
    });
</script>

<script>
    // Script untuk memastikan link Daftar PCare menggunakan parameter yang benar
    $(document).ready(function() {
        // Delegate event untuk link Daftar PCare
        $(document).on('click', '.btn-daftar-pcare', function(e) {
            e.preventDefault(); // Mencegah aksi default link
            
            // Ambil nomor rekam medis dari data attribute
            const noRkmMedis = $(this).data('no-rkm-medis');
            
            // Tambahkan timestamp untuk menghindari cache
            const timestamp = new Date().getTime();
            
            // Log untuk debugging
            console.log('Membuka pendaftaran PCare untuk pasien dengan RM:', noRkmMedis);
            
            // Buka URL dengan parameter yang benar
            const url = `/pcare/form-pendaftaran?no_rkm_medis=${noRkmMedis}&ts=${timestamp}&clear_cache=true`;
            
            // Buka di tab yang sama
            window.location.href = url;
        });
    });
</script>
@stop
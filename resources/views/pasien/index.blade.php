@extends('adminlte::page')

@section('title', 'Data Pasien')

@section('content_header')
<div class="d-flex flex-row justify-content-between align-items-center">
    <div class="header-title-container">
        <h1 class="page-title">
            <i class="fas fa-user-injured text-primary animated-icon"></i>
            <span class="text-gradient">DATA PASIEN</span>
            <div class="badge badge-pill badge-primary ml-2 pulse-badge">
                {{ $totalPasien ?? 0 }} <small>Pasien</small>
            </div>
        </h1>
        <p class="text-muted header-subtitle">Kelola data pasien dengan mudah dan efisien</p>
    </div>

    <div class="action-buttons">
        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-tambah-pasien" data-toggle="modal"
                data-target="#modalTambahPasien">
                <i class="fas fa-plus mr-1"></i> TAMBAH PASIEN
            </button>
            <button type="button" class="btn btn-info btn-export" onclick="exportData()"
                style="background-color: #00b8d4; border-color: #00b8d4;">
                <i class="fas fa-file-excel mr-1"></i> EXPORT
            </button>
            <button type="button" class="btn btn-secondary btn-cetak" onclick="cetakData()">
                <i class="fas fa-print mr-1"></i> CETAK
            </button>
        </div>
    </div>
</div>
@stop

@section('content')
<!-- Tambahkan Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('status'))
<div class="alert alert-info alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
    <i class="fas fa-info-circle mr-2"></i> {{ session('status') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<!-- Komponen Livewire untuk pencarian dan tabel pasien -->
<livewire:pasien-table-search />

<div class="dashboard-stats mb-4">
    <div class="row">
        <div class="col-md-3">
            <div class="info-box bg-gradient-primary">
                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Pasien</span>
                    <span class="info-box-number">{{ $totalPasien ?? 0 }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        <i class="fas fa-database"></i> Data Terekam
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-gradient-success">
                <span class="info-box-icon"><i class="fas fa-user-plus"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pasien Baru</span>
                    <span class="info-box-number">{{ $pasienBaru ?? 0 }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        <i class="fas fa-clock"></i> 10 Pasien Terakhir
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-gradient-warning">
                <span class="info-box-icon"><i class="fas fa-procedures"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kunjungan</span>
                    <span class="info-box-number">{{ $kunjunganHariIni ?? 0 }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        <i class="fas fa-calendar-day"></i> Hari Ini
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-gradient-danger">
                <span class="info-box-icon"><i class="fas fa-heartbeat"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pasien BPJS</span>
                    <span class="info-box-number">{{ $pasienBPJS ?? 0 }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        <i class="fas fa-percentage"></i> Dari Total Pasien
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" role="dialog" aria-labelledby="quickViewModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="quickViewModalLabel"><i class="fas fa-eye"></i> Detail Pasien</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="avatar-circle">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h4 class="mt-2" id="patientName">Nama Pasien</h4>
                    <p class="text-muted" id="patientRM">No. RM: -</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <i class="fas fa-id-card text-primary"></i>
                            <div>
                                <label>No. KTP</label>
                                <p id="patientKTP">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <i class="fas fa-phone text-primary"></i>
                            <div>
                                <label>No. Telepon</label>
                                <p id="patientPhone">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            <div>
                                <label>Tanggal Lahir</label>
                                <p id="patientDOB">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <i class="fas fa-venus-mars text-primary"></i>
                            <div>
                                <label>Jenis Kelamin</label>
                                <p id="patientGender">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <i class="fas fa-ring text-primary"></i>
                            <div>
                                <label>Status Pernikahan</label>
                                <p id="patientMaritalStatus">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <i class="fas fa-user-clock text-primary"></i>
                            <div>
                                <label>Umur</label>
                                <p id="patientAge">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <i class="fas fa-briefcase text-primary"></i>
                            <div>
                                <label>Pekerjaan</label>
                                <p id="patientJob">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <i class="fas fa-pray text-primary"></i>
                            <div>
                                <label>Agama</label>
                                <p id="patientReligion">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <div>
                                <label>Alamat</label>
                                <p id="patientAddress">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi BPJS -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-info">
                                <h5 class="card-title mb-0"><i class="fas fa-hospital-user"></i> Informasi BPJS</h5>
                            </div>
                            <div class="card-body" id="bpjsInfo">
                                <div class="text-center" id="bpjsLoading">
                                    <i class="fas fa-spinner fa-spin"></i> Mengecek status BPJS...
                                </div>
                                <div id="bpjsContent" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <i class="fas fa-id-card-alt text-info"></i>
                                                <div>
                                                    <label>No. Kartu BPJS</label>
                                                    <p id="bpjsNoKartu">-</p>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-user-check text-info"></i>
                                                <div>
                                                    <label>Status Kepesertaan</label>
                                                    <p id="bpjsStatus">-</p>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-layer-group text-info"></i>
                                                <div>
                                                    <label>Jenis Peserta</label>
                                                    <p id="bpjsJenisPeserta">-</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <i class="fas fa-hospital text-info"></i>
                                                <div>
                                                    <label>Faskes Tingkat 1</label>
                                                    <p id="bpjsFaskes">-</p>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-bed text-info"></i>
                                                <div>
                                                    <label>Kelas Rawat</label>
                                                    <p id="bpjsKelas">-</p>
                                                </div>
                                            </div>
                                            <div class="info-item">
                                                <i class="fas fa-calendar-check text-info"></i>
                                                <div>
                                                    <label>Berlaku Sampai</label>
                                                    <p id="bpjsBerlaku">-</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-center" id="bpjsStatusIndicator">
                                        <div class="badge badge-pill"
                                            style="background: linear-gradient(45deg, #4CAF50, #388E3C); color: white; font-size: 1rem; padding: 8px 15px; box-shadow: 0 3px 5px rgba(0,0,0,0.1);">
                                            BPJS Aktif
                                        </div>
                                    </div>

                                    <div class="mt-3 text-center" id="kunjunganSehatContainer" style="display: none;">
                                        <button type="button" class="btn btn-warning btn-block mt-3"
                                            id="btnKunjunganSehat" onclick="daftarKunjunganSehat()">
                                            <i class="fas fa-heartbeat mr-2"></i> Daftar Kunjungan Sehat
                                        </button>
                                        <small class="text-muted">Layanan ini akan mendaftarkan pasien untuk kunjungan
                                            sehat ke BPJS</small>
                                    </div>

                                    <div class="mt-3" id="icareBpjsContainer">
                                        <x-ralan.icare-bpjs :noPeserta="''" />
                                    </div>
                                </div>
                                <div id="bpjsError" class="alert alert-info" style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle mr-2" style="font-size: 1.5rem;"></i>
                                        <span id="bpjsErrorMessage" style="font-size: 1rem;">-</span>
                                    </div>
                                    <div class="mt-2 text-center" id="bpjsRetryButtonContainer">
                                        <button type="button" class="btn btn-sm btn-outline-info" id="retryBpjsButton">
                                            <i class="fas fa-sync"></i> Coba Lagi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <a id="btnEditPasien" href="#" class="btn btn-primary">Edit Data</a>
                <a id="btnDaftarKunjungan" href="#" class="btn btn-success">
                    <i class="fas fa-notes-medical mr-1"></i> Daftar Kunjungan
                </a>
            </div>
        </div>
    </div>
</div>

<x-adminlte-modal id="modalTambahPasien" title="Tambah Pasien Baru" size="xl" theme="primary" icon="fas fa-user-plus"
    v-centered scrollable>
    <livewire:pasien.form-pendaftaran />
</x-adminlte-modal>
@stop

@section('plugins.TempusDominusBs4', true)
@section('plugins.Sweetalert2', true)
@section('plugins.Chartjs', true)

@section('css')
<style>
    /* Animasi dan efek visual */
    .animated-icon {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Sembunyikan tombol I-Care BPJS */
    #icareBpjsContainer,
    .btn-icare-bpjs,
    [id^="i-care-bpjs"],
    button[onclick*="showIcareHistory"],
    button[id*="icareButton"],
    a[href*="icare"],
    button:contains("I-CARE BPJS"),
    .i-care-button,
    button.icare,
    .btn-icare,
    a.icare,
    [class*="icare-bpjs"],
    button:contains("i-Care BPJS"),
    .btn-success.btn-block[id^="btnIcareBpjs"],
    form[action*="regperiksa"] .btn-success.btn-block,
    button[onclick*="showIcareHistory('"],
    [id*="icareBpjs"],
    div.mb-3 .btn-success.btn-block,
    a[class*="i-care"],
    .btn-block.btn-success,
    /* Selector super spesifik untuk halaman regperiksa/create */
    body form button.btn-success.btn-block,
    button.btn-success.btn-block[onclick],
    a.btn-success.btn-block,
    .btn.btn-success.btn-block,
    /* Selector berdasarkan konten teks tombol */
    button:contains("iCare"),
    button:contains("i-Care"),
    a:contains("iCare"),
    a:contains("i-Care") {
        display: none !important;
        visibility: hidden !important;
        width: 0 !important;
        height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        position: absolute !important;
        overflow: hidden !important;
        clip: rect(0 0 0 0) !important;
    }

    .text-gradient {
        background: linear-gradient(45deg, #007bff, #6610f2);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;
    }

    .page-title {
        display: flex;
        align-items: center;
        font-size: 1.8rem;
        margin-bottom: 0.2rem;
    }

    .header-subtitle {
        margin-top: 0;
        font-size: 1rem;
    }

    .info-box {
        transition: all 0.3s ease;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        opacity: 0;
    }

    .info-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeIn 0.5s ease-out forwards;
    }

    .pulse-badge {
        animation: pulse-badge 2s infinite;
    }

    @keyframes pulse-badge {
        0% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
        }
    }

    .search-panel {
        margin-bottom: 1.5rem;
    }

    .search-button {
        transition: all 0.3s ease;
    }

    .search-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn {
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .ripple-effect {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.4);
        width: 100px;
        height: 100px;
        margin-top: -50px;
        margin-left: -50px;
        animation: ripple 0.8s;
        opacity: 0;
        z-index: -1;
    }

    @keyframes ripple {
        0% {
            transform: scale(0);
            opacity: 0.5;
        }

        100% {
            transform: scale(3);
            opacity: 0;
        }
    }

    .btn-tambah-pasien {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .btn-tambah-pasien:hover {
        background: linear-gradient(45deg, #0056b3, #003d80);
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
    }

    /* Empty state styling */
    .empty-state-container {
        padding: 40px 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        max-width: 600px;
        margin: 0 auto;
    }

    .empty-state-container i {
        color: #6c757d;
    }

    .empty-state-container h4 {
        margin-bottom: 10px;
        font-weight: 500;
    }

    /* Responsif */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }

        .action-buttons {
            margin-top: 1rem;
        }

        .d-flex.flex-row {
            flex-direction: column !important;
        }
    }

    /* BPJS Info Styling */
    #bpjsError.alert-info {
        background-color: #e3f2fd;
        border-color: #b3e5fc;
        color: #0277bd;
    }

    #bpjsError.alert-warning {
        background-color: #fff8e1;
        border-color: #ffecb3;
        color: #ff8f00;
    }

    #bpjsError i.fa-info-circle {
        color: #0277bd;
    }

    #bpjsError i.fa-exclamation-circle {
        color: #ff8f00;
    }

    .btn-outline-info {
        border-color: #0277bd;
        color: #0277bd;
    }

    .btn-outline-info:hover {
        background-color: #0277bd;
        color: white;
    }

    .info-item {
        display: flex;
        margin-bottom: 15px;
    }

    .info-item i {
        margin-right: 10px;
        margin-top: 5px;
        font-size: 1.2rem;
    }

    .info-item label {
        font-weight: bold;
        margin-bottom: 0;
        color: #555;
        font-size: 0.9rem;
    }

    .info-item p {
        margin-bottom: 0;
        font-size: 1rem;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Animasi untuk elemen saat halaman dimuat
        $('.info-box').each(function(index) {
            $(this).css({
                'animation-delay': (index * 0.1) + 's',
                'animation': 'fadeIn 0.6s ease-out forwards'
            });
        });
        
        // Event handler untuk tombol retry BPJS
        $(document).on('click', '#retryBpjsButton', function() {
            const noKartu = $('#bpjsNoKartu').text();
            if (noKartu && noKartu !== '-') {
                // Validasi panjang nomor BPJS
                if (noKartu.length === 13) {
                    $('#bpjsLoading').show();
                    $('#bpjsContent').hide();
                    $('#bpjsError').hide();
                    checkBPJSStatus(noKartu);
                } else {
                    $('#bpjsLoading').hide();
                    $('#bpjsContent').hide();
                    $('#bpjsError').show();
                    $('#bpjsError').removeClass('alert-danger alert-warning').addClass('alert-info');
                    $('#bpjsErrorMessage').html('<b>Informasi:</b> Nomor BPJS tidak valid (harus 13 digit). Nomor saat ini: ' + noKartu.length + ' digit');
                    $('#bpjsRetryButtonContainer').hide();
                }
            } else {
                // Cari dari data pasien terakhir
                const patientRM = $('#patientRM').text().replace('No. RM: ', '');
                if (patientRM && patientRM !== '-') {
                    $('#bpjsLoading').show();
                    $('#bpjsError').hide();
                    
                    // Ambil data peserta lagi
                    $.ajax({
                        url: '/pasien/' + patientRM,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data.no_peserta) {
                                // Validasi panjang nomor BPJS
                                if (data.no_peserta.length === 13) {
                                    checkBPJSStatus(data.no_peserta);
                                } else {
                                    $('#bpjsLoading').hide();
                                    $('#bpjsContent').hide();
                                    $('#bpjsError').show();
                                    $('#bpjsError').removeClass('alert-danger alert-warning').addClass('alert-info');
                                    $('#bpjsErrorMessage').html('<b>Informasi:</b> Nomor BPJS tidak valid (harus 13 digit). Nomor saat ini: ' + data.no_peserta.length + ' digit');
                                    $('#bpjsRetryButtonContainer').hide();
                                }
                            } else {
                                $('#bpjsLoading').hide();
                                $('#bpjsError').show();
                                $('#bpjsError').removeClass('alert-danger alert-warning').addClass('alert-info');
                                $('#bpjsErrorMessage').html('<b>Informasi:</b> Pasien ini tidak terdaftar sebagai peserta BPJS');
                                $('#bpjsRetryButtonContainer').hide();
                            }
                        },
                        error: function() {
                            $('#bpjsLoading').hide();
                            $('#bpjsContent').hide();
                            $('#bpjsError').show();
                            $('#bpjsError').removeClass('alert-info').addClass('alert-warning');
                            $('#bpjsErrorMessage').html('<b>Gagal mengambil data pasien.</b><br>Silakan coba lagi nanti.');
                            $('#bpjsRetryButtonContainer').show();
                        }
                    });
                }
            }
        });
        
        // Efek ripple pada tombol
        $(document).on('click', '.btn', function(e) {
            var x = e.pageX - $(this).offset().left;
            var y = e.pageY - $(this).offset().top;
            
            var ripple = $('<span class="ripple-effect"></span>');
            ripple.css({
                left: x + 'px',
                top: y + 'px'
            });
            
            $(this).append(ripple);
            
            setTimeout(function() {
                ripple.remove();
            }, 800);
        });
        
        // Fungsi untuk melihat detail pasien
        window.viewPatient = function(patientId) {
            // Tampilkan indikator loading di dalam modal sebelum AJAX request
            $('#quickViewModal').modal('show');
            $('#patientName').html('<i class="fas fa-spinner fa-spin"></i> Memuat data...');
            $('#patientRM').text('Mohon tunggu...');
            
            // AJAX untuk mendapatkan data pasien
            $.ajax({
                url: '/pasien/' + patientId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Isi modal dengan data pasien
                    populatePatientModal(data);
                    
                    // Set URL untuk tombol Daftar Kunjungan
                    $('#btnDaftarKunjungan').attr('href', '/regperiksa/create/' + data.no_rkm_medis);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching patient data:', error);
                    
                    // Tampilkan pesan error yang lebih informatif dalam modal
                    $('#patientName').text('Error');
                    $('#patientRM').text('Gagal memuat data pasien');
                    $('#patientKTP').text('-');
                    $('#patientPhone').text('-');
                    $('#patientDOB').text('-');
                    $('#patientGender').text('-');
                    $('#patientMaritalStatus').text('-');
                    $('#patientJob').text('-');
                    $('#patientReligion').text('-');
                    $('#patientAddress').text('-');
                    $('#patientAge').text('-');
                    
                    // Tampilkan detail error jika tersedia
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal memuat data pasien. Silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        }
        
        // Fungsi untuk mengisi data di modal
        function populatePatientModal(patient) {
            $('#patientName').text(patient.nm_pasien || '-');
            $('#patientRM').text('No. RM: ' + patient.no_rkm_medis || '-');
            $('#patientKTP').text(patient.no_ktp || '-');
            $('#patientPhone').text(patient.no_tlp || '-');
            $('#patientDOB').text(patient.tgl_lahir || '-');
            $('#patientGender').text(patient.jk === 'L' ? 'Laki-laki' : (patient.jk === 'P' ? 'Perempuan' : '-'));
            $('#patientMaritalStatus').text(patient.stts_nikah || '-');
            $('#patientJob').text(patient.pekerjaan || '-');
            $('#patientReligion').text(patient.agama || '-');
            $('#patientAddress').text(patient.alamat || '-');
            
            // Reset tampilan BPJS
            $('#bpjsLoading').show();
            $('#bpjsContent').hide();
            $('#bpjsError').hide();
            $('#icareBpjsContainer').hide(); // Sembunyikan tombol i-Care
            
            // Jika ada nomor BPJS, cek status kepesertaan
            if (patient.no_peserta) {
                // Validasi panjang nomor BPJS harus 13 digit
                if (patient.no_peserta.length === 13) {
                    checkBPJSStatus(patient.no_peserta);
                } else {
                    $('#bpjsLoading').hide();
                    $('#bpjsContent').hide();
                    $('#bpjsError').show();
                    $('#bpjsError').removeClass('alert-danger alert-warning').addClass('alert-info');
                    $('#bpjsErrorMessage').html('<b>Informasi:</b> Nomor BPJS tidak valid (harus 13 digit). Nomor saat ini: ' + patient.no_peserta.length + ' digit');
                    $('#bpjsRetryButtonContainer').hide();
                }
            } else {
                $('#bpjsLoading').hide();
                $('#bpjsContent').hide();
                $('#bpjsError').show();
                $('#bpjsError').removeClass('alert-danger alert-warning').addClass('alert-info');
                $('#bpjsErrorMessage').html('<b>Informasi:</b> Pasien ini tidak terdaftar sebagai peserta BPJS');
                $('#bpjsRetryButtonContainer').hide();
            }
            
            // Hitung dan tampilkan umur
            if (patient.tgl_lahir) {
                const birthDate = new Date(patient.tgl_lahir);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                
                $('#patientAge').text(age + ' tahun');
            } else {
                $('#patientAge').text('-');
            }
            
            // Set RM untuk tombol edit
            $('#btnEditPasien').attr('href', '/data-pasien/' + patient.no_rkm_medis + '/edit');
        }
        
        // Fungsi untuk mengecek status BPJS
        function checkBPJSStatus(noKartu) {
            $.ajax({
                url: '/api/pcare/peserta/' + noKartu,
                type: 'GET',
                success: function(response) {
                    $('#bpjsLoading').hide();
                    
                    if (response.metaData.code === 200 && response.response) {
                        const data = response.response;
                        
                        // Tampilkan data BPJS
                        $('#bpjsContent').show();
                        $('#bpjsNoKartu').text(data.noKartu || '-');
                        $('#bpjsStatus').html(
                            `<span class="badge badge-pill badge-primary" style="background: linear-gradient(45deg, #2196F3, #1976D2); font-size: 0.85rem; padding: 5px 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">${data.statusPeserta && data.statusPeserta.keterangan ? data.statusPeserta.keterangan : 'AKTIF'}</span>`
                        );
                        $('#bpjsJenisPeserta').text(data.jnsPeserta && data.jnsPeserta.nama ? data.jnsPeserta.nama : '-');
                        $('#bpjsFaskes').text(data.kdProviderPst && data.kdProviderPst.nmProvider ? data.kdProviderPst.nmProvider : '-');
                        $('#bpjsKelas').text(data.jnsKelas && data.jnsKelas.nama ? data.jnsKelas.nama : '-');
                        
                        // Perbarui nomor BPJS pada komponen i-Care (tetap diatur tapi tidak ditampilkan)
                        $('#icareBpjsContainer button').attr('onclick', `showIcareHistory('${data.noKartu}', '102')`);
                        
                        // Tampilkan tanggal akhir berlaku
                        $('#bpjsBerlaku').text(data.tglAkhirBerlaku || '-');
                        
                        // Update status indicator
                        if (data.aktif) {
                            $('#bpjsStatusIndicator').html(
                                `<div class="badge badge-pill" style="background: linear-gradient(45deg, #4CAF50, #388E3C); color: white; font-size: 1rem; padding: 8px 15px; box-shadow: 0 3px 5px rgba(0,0,0,0.1);">
                                    BPJS Aktif
                                </div>`
                            );
                            // Tampilkan tombol kunjungan sehat jika BPJS aktif
                            $('#kunjunganSehatContainer').show();
                        } else {
                            $('#bpjsStatusIndicator').html(
                                `<div class="badge badge-pill" style="background: linear-gradient(45deg, #F44336, #D32F2F); color: white; font-size: 1rem; padding: 8px 15px; box-shadow: 0 3px 5px rgba(0,0,0,0.1);">
                                    BPJS Tidak Aktif
                                </div>`
                            );
                            // Sembunyikan tombol kunjungan sehat jika BPJS tidak aktif
                            $('#kunjunganSehatContainer').hide();
                        }
                    } else {
                        $('#bpjsError').show();
                        $('#bpjsContent').hide();
                        
                        // Klasifikasi pesan error berdasarkan kode dan pesan
                        if (response.metaData.code === 401 || response.metaData.code === 403 || 
                            (response.metaData.message && response.metaData.message.includes('Password Pcare'))) {
                            // Error authentication/credential
                            $('#bpjsError').removeClass('alert-info alert-danger').addClass('alert-warning');
                            $('#bpjsErrorMessage').html('<i class="fas fa-exclamation-triangle"></i> <b>' + response.metaData.message + '</b>');
                            $('#bpjsRetryButtonContainer').hide();
                            $('#icareBpjsContainer').hide(); // Sembunyikan tombol i-Care
                        } else if (response.metaData.code === 201) {
                            // Data tidak ditemukan (biasanya kode 201 di BPJS)
                            $('#bpjsError').removeClass('alert-danger alert-warning').addClass('alert-info');
                            $('#bpjsErrorMessage').html('<b>Informasi:</b> Nomor kartu BPJS <b>' + noKartu + '</b> tidak terdaftar di database BPJS');
                            $('#bpjsRetryButtonContainer').hide();
                            $('#icareBpjsContainer').hide(); // Sembunyikan tombol i-Care
                        } else if (response.metaData.message && 
                            (response.metaData.message.includes('tidak ditemukan') || 
                             response.metaData.message.includes('Peserta tidak ditemukan'))) {
                            // Pesan error mengandung kata "tidak ditemukan"
                            $('#bpjsError').removeClass('alert-danger alert-warning').addClass('alert-info');
                            $('#bpjsErrorMessage').html('<b>Informasi:</b> ' + response.metaData.message);
                            $('#bpjsRetryButtonContainer').hide();
                            $('#icareBpjsContainer').hide(); // Sembunyikan tombol i-Care
                        } else if (response.metaData.code >= 500) {
                            // Error server (500+)
                            $('#bpjsError').removeClass('alert-info alert-warning').addClass('alert-danger');
                            $('#bpjsErrorMessage').html('<b>Server BPJS mengalami masalah.</b><br>Kode: ' + response.metaData.code + '<br>Pesan: ' + response.metaData.message);
                            $('#bpjsRetryButtonContainer').show();
                        } else if (response.metaData.code >= 400 && response.metaData.code < 500) {
                            // Error permintaan (400+)
                            $('#bpjsError').removeClass('alert-info alert-danger').addClass('alert-warning');
                            $('#bpjsErrorMessage').html('<b>Permintaan tidak valid.</b><br>Kode: ' + response.metaData.code + '<br>Pesan: ' + response.metaData.message);
                            $('#bpjsRetryButtonContainer').show();
                        } else {
                            // Error umum
                            $('#bpjsError').removeClass('alert-info').addClass('alert-warning');
                            $('#bpjsErrorMessage').html('<b>Gagal mendapatkan data BPJS.</b><br>Kode: ' + response.metaData.code + '<br>Pesan: ' + response.metaData.message);
                            $('#bpjsRetryButtonContainer').show();
                        }
                    }
                },
                error: function(xhr) {
                    $('#bpjsLoading').hide();
                    $('#bpjsContent').hide();
                    $('#bpjsError').show();
                    
                    // Cek jika error authentication/credential
                    if (xhr.status === 401 || xhr.status === 403 || 
                        (xhr.responseJSON && xhr.responseJSON.metaData && 
                         xhr.responseJSON.metaData.message && 
                         xhr.responseJSON.metaData.message.includes('Password Pcare'))) {
                        
                        $('#bpjsError').removeClass('alert-info alert-danger').addClass('alert-warning');
                        $('#bpjsErrorMessage').html('<i class="fas fa-exclamation-triangle"></i> <b>' + 
                            (xhr.responseJSON?.metaData?.message || 'Maaf Cek Kembali Password Pcare Anda') + '</b>');
                        $('#bpjsRetryButtonContainer').hide();
                        $('#icareBpjsContainer').hide(); // Sembunyikan tombol i-Care
                    }
                    // Cek jika error karena nomor kartu tidak ditemukan
                    else if (xhr.responseJSON && xhr.responseJSON.metaData && 
                        (xhr.responseJSON.metaData.message.includes('tidak ditemukan') || 
                         xhr.responseJSON.metaData.message.includes('Peserta tidak ditemukan'))) {
                        
                        $('#bpjsError').removeClass('alert-danger alert-warning').addClass('alert-info');
                        $('#bpjsErrorMessage').html('<b>Informasi:</b> Nomor kartu BPJS <b>' + noKartu + '</b> tidak ditemukan di database BPJS');
                        $('#bpjsRetryButtonContainer').hide();
                    } 
                    // Cek jika error koneksi (404, timeout, atau status 0)
                    else if (xhr.status === 0 || xhr.status === 408 || xhr.status === 504 || xhr.status === 599) {
                        $('#bpjsError').removeClass('alert-info alert-danger').addClass('alert-warning');
                        $('#bpjsErrorMessage').html('<b>Koneksi ke server BPJS terputus.</b><br>Server BPJS mungkin sedang dalam pemeliharaan atau jaringan internet mengalami gangguan. Silakan coba beberapa saat lagi.');
                        $('#bpjsRetryButtonContainer').show();
                    }
                    // Untuk error internal server (500)
                    else if (xhr.status === 500) {
                        $('#bpjsError').removeClass('alert-info').addClass('alert-danger');
                        $('#bpjsErrorMessage').html('<b>Server BPJS mengalami masalah internal.</b><br>Mohon coba lagi nanti atau hubungi administrator jika masalah berlanjut.');
                        $('#bpjsRetryButtonContainer').show();
                    }
                    // Error lainnya
                    else {
                        $('#bpjsError').removeClass('alert-info').addClass('alert-warning');
                        $('#bpjsErrorMessage').html('<b>Tidak dapat memperoleh data BPJS.</b><br>' + 
                                                  (xhr.responseJSON?.metaData?.message || 'Gagal menghubungi server BPJS dengan kode error: ' + xhr.status));
                        $('#bpjsRetryButtonContainer').show();
                        
                        // Coba alternatif dengan endpoint lain jika gagal
                        if (xhr.status === 404 || xhr.status === 400) {
                            retryWithAlternativeEndpoint(noKartu);
                        }
                    }
                }
            });
        }
        
        // Fungsi untuk mencoba endpoint alternatif jika endpoint utama gagal
        function retryWithAlternativeEndpoint(noKartu) {
            console.log('Mencoba endpoint alternatif untuk nomor kartu: ' + noKartu);
            
            $.ajax({
                url: '/api/bpjs/peserta/' + noKartu,
                type: 'GET',
                success: function(response) {
                    $('#bpjsLoading').hide();
                    
                    if (response.metaData.code === 200 && response.response) {
                        const data = response.response;
                        
                        // Tampilkan data BPJS
                        $('#bpjsContent').show();
                        $('#bpjsNoKartu').text(data.noKartu || '-');
                        $('#bpjsStatus').html(
                            `<span class="badge badge-pill" style="background: linear-gradient(45deg, ${data.aktif ? '#4CAF50, #388E3C' : '#F44336, #D32F2F'}); color: white; font-size: 0.85rem; padding: 5px 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                ${data.ketAktif || '-'}
                            </span>`
                        );
                        $('#bpjsJenisPeserta').text(data.jnsPeserta && data.jnsPeserta.nama ? data.jnsPeserta.nama : '-');
                        $('#bpjsFaskes').text(data.kdProviderPst && data.kdProviderPst.nmProvider ? data.kdProviderPst.nmProvider : '-');
                        $('#bpjsKelas').text(data.jnsKelas && data.jnsKelas.nama ? data.jnsKelas.nama : '-');
                        
                        // Perbarui nomor BPJS pada komponen i-Care (tetap diatur tapi tidak ditampilkan)
                        $('#icareBpjsContainer button').attr('onclick', `showIcareHistory('${data.noKartu}', '102')`);
                        
                        // Tampilkan tanggal akhir berlaku
                        $('#bpjsBerlaku').text(data.tglAkhirBerlaku || '-');
                        
                        // Update status indicator
                        if (data.aktif) {
                            $('#bpjsStatusIndicator').html(
                                `<div class="badge badge-pill" style="background: linear-gradient(45deg, #4CAF50, #388E3C); color: white; font-size: 1rem; padding: 8px 15px; box-shadow: 0 3px 5px rgba(0,0,0,0.1);">
                                    BPJS Aktif
                                </div>`
                            );
                            // Tampilkan tombol kunjungan sehat jika BPJS aktif
                            $('#kunjunganSehatContainer').show();
                        } else {
                            $('#bpjsStatusIndicator').html(
                                `<div class="badge badge-pill" style="background: linear-gradient(45deg, #F44336, #D32F2F); color: white; font-size: 1rem; padding: 8px 15px; box-shadow: 0 3px 5px rgba(0,0,0,0.1);">
                                    BPJS Tidak Aktif
                                </div>`
                            );
                            // Sembunyikan tombol kunjungan sehat jika BPJS tidak aktif
                            $('#kunjunganSehatContainer').hide();
                        }
                    } else {
                        $('#bpjsError').show();
                        $('#bpjsContent').hide();
                        
                        // Klasifikasi pesan error berdasarkan kode dan pesan
                        if (response.metaData.code === 401 || response.metaData.code === 403 || 
                            (response.metaData.message && response.metaData.message.includes('Password Pcare'))) {
                            // Error authentication/credential
                            $('#bpjsError').removeClass('alert-info alert-danger').addClass('alert-warning');
                            $('#bpjsErrorMessage').html('<i class="fas fa-exclamation-triangle"></i> <b>' + response.metaData.message + '</b>');
                            $('#bpjsRetryButtonContainer').hide();
                            $('#icareBpjsContainer').hide(); // Sembunyikan tombol i-Care
                        } else if (response.metaData.code === 201) {
                            // Data tidak ditemukan (biasanya kode 201 di BPJS)
                            $('#bpjsError').removeClass('alert-danger alert-warning').addClass('alert-info');
                            $('#bpjsErrorMessage').html('<b>Informasi:</b> Nomor kartu BPJS <b>' + noKartu + '</b> tidak terdaftar di database BPJS');
                            $('#bpjsRetryButtonContainer').hide();
                            $('#icareBpjsContainer').hide(); // Sembunyikan tombol i-Care
                        } else if (response.metaData.message && 
                            (response.metaData.message.includes('tidak ditemukan') || 
                             response.metaData.message.includes('Peserta tidak ditemukan'))) {
                            // Pesan error mengandung kata "tidak ditemukan"
                            $('#bpjsError').removeClass('alert-danger alert-warning').addClass('alert-info');
                            $('#bpjsErrorMessage').html('<b>Informasi:</b> ' + response.metaData.message);
                            $('#bpjsRetryButtonContainer').hide();
                            $('#icareBpjsContainer').hide(); // Sembunyikan tombol i-Care
                        } else if (response.metaData.code >= 500) {
                            // Error server (500+)
                            $('#bpjsError').removeClass('alert-info alert-warning').addClass('alert-danger');
                            $('#bpjsErrorMessage').html('<b>Server BPJS mengalami masalah.</b><br>Kode: ' + response.metaData.code + '<br>Pesan: ' + response.metaData.message);
                            $('#bpjsRetryButtonContainer').show();
                        } else if (response.metaData.code >= 400 && response.metaData.code < 500) {
                            // Error permintaan (400+)
                            $('#bpjsError').removeClass('alert-info alert-danger').addClass('alert-warning');
                            $('#bpjsErrorMessage').html('<b>Permintaan tidak valid.</b><br>Kode: ' + response.metaData.code + '<br>Pesan: ' + response.metaData.message);
                            $('#bpjsRetryButtonContainer').show();
                        } else {
                            // Error umum
                            $('#bpjsError').removeClass('alert-info').addClass('alert-warning');
                            $('#bpjsErrorMessage').html('<b>Gagal mendapatkan data BPJS.</b><br>Kode: ' + response.metaData.code + '<br>Pesan: ' + response.metaData.message);
                            $('#bpjsRetryButtonContainer').show();
                        }
                    }
                },
                error: function() {
                    // Jika kedua endpoint gagal, tetap tampilkan pesan error
                    $('#bpjsLoading').hide();
                    $('#bpjsContent').hide();
                    $('#bpjsError').show();
                    $('#bpjsError').removeClass('alert-info').addClass('alert-warning');
                    $('#bpjsErrorMessage').html(`
                        <b>Gagal menghubungi server BPJS melalui semua endpoint.</b><br>
                        <ul class="mt-2 mb-0">
                            <li>Server BPJS mungkin sedang tidak dapat diakses</li>
                            <li>Koneksi internet mungkin bermasalah</li>
                            <li>Jika masalah berlanjut, hubungi administrator</li>
                        </ul>
                    `);
                    $('#bpjsRetryButtonContainer').show();
                }
            });
        }
        
        // Mencegah event propagation dari tombol-tombol aksi
        $(document).on('click', '.btn-group button, .btn-group a', function(e) {
            e.stopPropagation();
        });

        // Reset modal saat ditutup
        $('#quickViewModal').on('hidden.bs.modal', function() {
            $('#patientDetailLoader').show();
            $('#patientDetailContent').hide();
            $('#bpjsLoading').show();
            $('#bpjsContent').hide();
            $('#bpjsError').hide();
            $('#icareBpjsContainer').hide();
        });

        // Fungsi untuk mendaftarkan kunjungan sehat
        window.daftarKunjunganSehat = function() {
            // Ambil nomor kartu BPJS
            const noKartu = $('#bpjsNoKartu').text();
            if (!noKartu || noKartu === '-') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Nomor kartu BPJS tidak valid',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            
            // Konfirmasi pendaftaran
            Swal.fire({
                title: 'Daftar Kunjungan Sehat',
                text: 'Apakah Anda yakin ingin mendaftarkan kunjungan sehat untuk pasien ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Daftarkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses',
                        text: 'Mendaftarkan kunjungan sehat...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Kirim request ke server
                    $.ajax({
                        url: '/api/pcare/pendaftaran',
                        type: 'POST',
                        dataType: 'json',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            kdProviderPeserta: '{{ $kdProviderPeserta }}',
                            noKartu: noKartu,
                            tglDaftar: moment().format('DD-MM-YYYY'),
                            kdPoli: '021',
                            kunjSakit: false,
                            keluhan: 'Konsultasi Kesehatan',
                            sistole: 0,
                            diastole: 0,
                            beratBadan: 0,
                            tinggiBadan: 0,
                            respRate: 0,
                            heartRate: 0,
                            lingkarPerut: 0,
                            rujukBalik: 0,
                            kdTkp: '10', 
                            kdSadar: '01'
                        }),
                        success: function(response) {
                            Swal.close();
                            if (response.metaData && (response.metaData.code === 200 || response.metaData.code === 201)) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Kunjungan sehat berhasil didaftarkan dengan nomor antrian: ' + response.response.message,
                                    confirmButtonColor: '#3085d6'
                                });
                            } else {
                                let errorMessage = 'Gagal mendaftarkan kunjungan sehat';
                                if (response.metaData && response.metaData.message) {
                                    errorMessage = response.metaData.message;
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage,
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            let errorMessage = 'Gagal mendaftarkan kunjungan sehat';
                            
                            if (xhr.responseJSON && xhr.responseJSON.metaData && xhr.responseJSON.metaData.message) {
                                errorMessage = xhr.responseJSON.metaData.message;
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: errorMessage,
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    });
                }
            });
        };
    });
    
    // Fungsi untuk export data
    function exportData() {
        window.location.href = '/data-pasien/export';
    }
    
    // Fungsi untuk cetak data
    function cetakData() {
        Swal.fire({
            title: 'Cetak Data Pasien',
            text: 'Untuk menghindari masalah memori, hanya 100 data teratas yang akan dicetak.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim parameter kosong untuk menghindari error "property name on null"
                window.open('/data-pasien/cetak?name=&rm=&address=', '_blank');
            }
        });
    }
</script>
@stop
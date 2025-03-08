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
<!-- Notifikasi -->
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <a id="btnEditPasien" href="#" class="btn btn-primary">Edit Data</a>
                <button type="button" class="btn btn-success">Daftar Kunjungan</button>
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
        
        // Mencegah event propagation dari tombol-tombol aksi
        $(document).on('click', '.btn-group button, .btn-group a', function(e) {
            e.stopPropagation();
        });
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
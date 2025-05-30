@extends('adminlte::page')

@section('title', 'Referensi Dokter PCare')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-user-md text-primary"></i> Referensi Dokter PCare</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Referensi Dokter PCare</li>
    </ol>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Data Jadwal Praktek Dokter</h3>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <form id="filter-form" class="form-inline">
                            <div class="form-group mr-2">
                                <label for="kodePoli" class="mr-2">Poli:</label>
                                <select class="form-control" id="kodePoli" name="kodePoli" required>
                                    <option value="">Pilih Poli</option>
                                    <option value="003">POLI KIA</option>
                                    <option value="004">POLI GIGI</option>
                                    <option value="008">POLI UMUM</option>
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <label for="tanggal" class="mr-2">Tanggal:</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <button type="button" id="reset-filter" class="btn btn-secondary ml-2">
                                <i class="fas fa-sync"></i> Reset
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="btn-group">
                            <button type="button" id="export-excel" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>
                            <button type="button" id="export-pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Loading & Error Alerts -->
                <div id="loading-alert" class="alert alert-info alert-dismissible d-none">
                    <h5><i class="icon fas fa-info"></i> Memuat Data</h5>
                    <div class="d-flex align-items-center">
                        <div class="spinner-border text-info mr-2" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <span>Sedang mengambil data dari server BPJS...</span>
                    </div>
                </div>

                <div id="error-alert" class="alert alert-danger alert-dismissible d-none">
                    <h5><i class="icon fas fa-ban"></i> Error</h5>
                    <span id="error-message"></span>
                </div>

                <!-- Table Section -->
                <div class="table-responsive">
                    <table id="tabel-dokter" class="table table-bordered table-striped table-hover">
                        <thead class="bg-primary">
                            <tr>
                                <th class="text-center" style="width: 50px">No</th>
                                <th>Nama Dokter</th>
                                <th class="text-center" style="width: 120px">Kode Dokter</th>
                                <th class="text-center" style="width: 150px">Jam Praktek</th>
                                <th class="text-center" style="width: 100px">Kapasitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .table th {
        white-space: nowrap;
        background-color: #f4f6f9;
    }

    .table td {
        vertical-align: middle;
    }

    .form-inline .form-group {
        margin-bottom: 10px;
    }

    @media (max-width: 768px) {
        .form-inline {
            flex-direction: column;
            align-items: stretch;
        }

        .form-inline .form-group {
            margin-right: 0 !important;
            margin-bottom: 10px;
        }

        .form-inline .btn {
            width: 100%;
            margin-bottom: 5px;
        }

        .btn-group {
            display: flex;
            margin-top: 10px;
        }

        .btn-group .btn {
            flex: 1;
        }
    }

    #loading-alert,
    #error-alert {
        margin-bottom: 1rem;
    }

    .spinner-border {
        width: 1.5rem;
        height: 1.5rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.075);
    }

    .card-primary.card-outline {
        border-top: 3px solid #007bff;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
    // Initialize DataTable
    const table = $('#tabel-dokter').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
            emptyTable: 'Tidak ada data yang tersedia',
            zeroRecords: 'Tidak ditemukan data yang sesuai',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
            infoFiltered: '(difilter dari _MAX_ total data)',
            search: 'Cari:',
            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Selanjutnya',
                previous: 'Sebelumnya'
            }
        },
        ajax: {
            url: function() {
                const kodePoli = $('#kodePoli').val();
                const tanggal = $('#tanggal').val();
                return `/pcare/api/ref/dokter/kodepoli/${kodePoli}/tanggal/${tanggal}`;
            },
            error: function(xhr, error, code) {
                console.error('DataTable AJAX Error:', xhr, error, code);
                let message = 'Gagal memuat data';
                
                if (xhr.responseJSON && xhr.responseJSON.metadata) {
                    message = xhr.responseJSON.metadata.message;
                } else if (xhr.status === 503) {
                    message = 'Layanan BPJS sedang tidak tersedia. Silakan coba beberapa saat lagi.';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            }
        },
        columns: [
            { 
                data: null,
                className: 'text-center',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { 
                data: 'namadokter',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'kodedokter',
                className: 'text-center',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'jampraktek',
                className: 'text-center',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'kapasitas',
                className: 'text-center',
                render: function(data) {
                    return data || '0';
                }
            }
        ],
        order: [[1, 'asc']]
    });

    // Filter form submission
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        if (!$('#kodePoli').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih Poli terlebih dahulu'
            });
            return;
        }
        table.ajax.reload();
    });

    // Reset filter
    $('#reset-filter').on('click', function() {
        $('#kodePoli').val('');
        $('#tanggal').val(moment().format('YYYY-MM-DD'));
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Filter telah direset',
            timer: 1500,
            showConfirmButton: false
        });
        table.ajax.reload();
    });

    // Export Excel
    $('#export-excel').on('click', function() {
        if (!$('#kodePoli').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih Poli terlebih dahulu'
            });
            return;
        }
        const kodePoli = $('#kodePoli').val();
        const tanggal = $('#tanggal').val();
        window.location.href = `/pcare/api/ref/dokter/export/excel?kodepoli=${kodePoli}&tanggal=${tanggal}`;
    });

    // Export PDF
    $('#export-pdf').on('click', function() {
        if (!$('#kodePoli').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih Poli terlebih dahulu'
            });
            return;
        }
        const kodePoli = $('#kodePoli').val();
        const tanggal = $('#tanggal').val();
        window.location.href = `/pcare/api/ref/dokter/export/pdf?kodepoli=${kodePoli}&tanggal=${tanggal}`;
    });

    // Responsive handling
    $(window).on('resize', function() {
        table.columns.adjust().draw();
    });
});
</script>
@stop
@extends('adminlte::page')

@section('title', 'Referensi Poli PCare')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-clinic-medical text-primary"></i> Referensi Poli PCare</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Referensi Poli PCare</li>
    </ol>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Data Poli</h3>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <form id="filter-form" class="form-inline">
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
                    <table id="tabel-poli" class="table table-bordered table-striped table-hover">
                        <thead class="bg-primary">
                            <tr>
                                <th class="text-center" style="width: 50px">No</th>
                                <th class="text-center" style="width: 120px">Kode Poli</th>
                                <th>Nama Poli</th>
                                <th class="text-center" style="width: 120px">Status Poli Sakit</th>
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
        const table = $('#tabel-poli').DataTable({
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
            columns: [
                { 
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { 
                    data: 'kdPoli',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                { 
                    data: 'nmPoli',
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                { 
                    data: 'poliSakit',
                    render: function(data, type, row) {
                        if (data === true) {
                            return '<span class="badge badge-success">Ya</span>';
                        } else if (data === false) {
                            return '<span class="badge badge-secondary">Tidak</span>';
                        }
                        return '<span class="badge badge-warning">-</span>';
                    },
                    className: 'text-center'
                }
            ]
        });

        // Filter form submission
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            
            const tanggal = $('#tanggal').val();

            // Debug log
            console.log('Form Data:', {
                tanggal: tanggal
            });

            // Validasi input
            if (!tanggal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih Tanggal terlebih dahulu'
                });
                return;
            }

            // Show loading alert
            $('#loading-alert').removeClass('d-none');
            $('#error-alert').addClass('d-none');
            table.clear().draw();

            // Make AJAX request menggunakan endpoint yang sudah diuji
            $.ajax({
                url: `/test-poli-fktp/0/100`,
                method: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#loading-alert').addClass('d-none');
                    
                    console.log('Response received:', response);
                    
                    if (response.metaData && response.metaData.code === 200) {
                        if (response.response && Array.isArray(response.response.list)) {
                            const data = response.response.list;
                            
                            if (data.length > 0) {
                                table.clear().rows.add(data).draw();
                                $('#error-alert').addClass('d-none');
                                
                                // Tampilkan notifikasi sukses
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: `Data poli berhasil dimuat (${data.length} data)`,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                $('#error-message').text('Tidak ada data poli yang tersedia');
                                $('#error-alert').removeClass('d-none');
                            }
                        } else {
                            $('#error-message').text('Format data tidak valid');
                            $('#error-alert').removeClass('d-none');
                        }
                    } else {
                        const message = response.metaData ? response.metaData.message : 'Terjadi kesalahan saat memuat data';
                        $('#error-message').text(message);
                        $('#error-alert').removeClass('d-none');
                    }
                },
                error: function(xhr, status, error) {
                    $('#loading-alert').addClass('d-none');
                    
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });

                    let errorMessage = 'Terjadi kesalahan saat memuat data';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.metadata && response.metadata.message) {
                            errorMessage = response.metadata.message;
                        }
                    } catch (e) {
                        console.error('Error parsing error response:', e);
                    }

                    $('#error-message').text(errorMessage);
                    $('#error-alert').removeClass('d-none');

                    // Tampilkan SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        footer: 'Silakan coba lagi atau hubungi administrator jika masalah berlanjut'
                    });
                }
            });
        });

        // Reset filter
        $('#reset-filter').on('click', function() {
            $('#tanggal').val('{{ date("Y-m-d") }}');
            table.clear().draw();
            $('#error-alert').addClass('d-none');
            $('#loading-alert').addClass('d-none');
        });

        // Export Excel
        $('#export-excel').on('click', function() {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: 'Fitur export Excel sedang dalam pengembangan untuk endpoint baru',
                footer: 'Silakan gunakan fitur copy atau print dari browser untuk sementara'
            });
        });

        // Export PDF
        $('#export-pdf').on('click', function() {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: 'Fitur export PDF sedang dalam pengembangan untuk endpoint baru',
                footer: 'Silakan gunakan fitur print dari browser untuk sementara'
            });
        });
    });
</script>
@stop
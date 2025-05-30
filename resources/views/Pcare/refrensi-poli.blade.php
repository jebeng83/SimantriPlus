@extends('adminlte::page')

@section('title', 'Referensi Poli PCare')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-hospital text-primary"></i> Referensi Poli PCare</h1>
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
                <h3 class="card-title">Data Referensi Poli</h3>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
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
                </div>

                <!-- Loading Indicator -->
                <div id="loading-indicator" class="text-center d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data...</p>
                </div>

                <!-- Error Alert -->
                <div id="error-alert" class="alert alert-danger d-none" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="error-message"></span>
                </div>

                <!-- Table Section -->
                <div class="table-responsive">
                    <table id="poli-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 60px">No.</th>
                                <th>Kode Poli</th>
                                <th>Nama Poli</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/vendor/datatables/css/dataTables.bootstrap4.min.css">
<style>
    .table th,
    .table td {
        vertical-align: middle !important;
    }

    #poli-table {
        width: 100% !important;
    }

    #poli-table th:first-child,
    #poli-table td:first-child {
        text-align: center;
        width: 5% !important;
    }

    #poli-table th:nth-child(2),
    #poli-table td:nth-child(2) {
        width: 25% !important;
    }

    #poli-table th:nth-child(3),
    #poli-table td:nth-child(3) {
        width: 70% !important;
    }

    #loading-indicator {
        position: relative;
        padding: 20px;
        margin: 10px 0;
    }

    .alert {
        margin-top: 1rem;
    }

    .btn {
        margin-right: 0.5rem;
    }

    .form-inline .form-group {
        margin-bottom: 0;
    }
</style>
@stop

@section('js')
<script src="/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="/vendor/datatables/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        let table;

        // Initialize DataTable
        function initializeDataTable() {
            if ($.fn.DataTable.isDataTable('#poli-table')) {
                $('#poli-table').DataTable().destroy();
            }

            table = $('#poli-table').DataTable({
                processing: true,
                serverSide: false,
                autoWidth: false,
                ajax: {
                    url: "{{ route('pcare.api.ref.poli') }}",
                    type: 'GET',
                    data: function(d) {
                        d.tanggal = $('#tanggal').val();
                    },
                    beforeSend: function() {
                        $('#loading-indicator').removeClass('d-none');
                        $('#error-alert').addClass('d-none');
                    },
                    dataSrc: function(json) {
                        $('#loading-indicator').addClass('d-none');
                        
                        if (json.metadata && json.metadata.code === 200) {
                            if (json.response && json.response.list && json.response.list.length > 0) {
                                return json.response.list;
                            }
                        }
                        
                        $('#error-message').text('Tidak ada data yang ditemukan');
                        $('#error-alert').removeClass('d-none');
                        return [];
                    },
                    error: function(xhr, error, thrown) {
                        $('#loading-indicator').addClass('d-none');
                        
                        let errorMessage = 'Terjadi kesalahan saat memuat data';
                        if (xhr.responseJSON && xhr.responseJSON.metadata) {
                            errorMessage = xhr.responseJSON.metadata.message;
                        }
                        $('#error-message').text(errorMessage);
                        $('#error-alert').removeClass('d-none');
                        
                        return [];
                    }
                },
                columns: [
                    { 
                        data: null,
                        width: '5%',
                        render: function(data, type, row, meta) {
                            return meta.row + 1 + '.';
                        }
                    },
                    { 
                        data: 'kdPoli',
                        width: '25%'
                    },
                    { 
                        data: 'nmPoli',
                        width: '70%'
                    }
                ],
                language: {
                    processing: 'Memuat data...',
                    search: 'Pencarian:',
                    lengthMenu: 'Tampilkan _MENU_ data per halaman',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data yang ditampilkan',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    zeroRecords: 'Tidak ada data yang cocok',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir', 
                        next: 'Selanjutnya',
                        previous: 'Sebelumnya'
                    }
                },
                order: [[0, 'asc']],
                pageLength: 10,
                responsive: true
            });
        }

        // Initialize DataTable on page load
        initializeDataTable();

        // Handle form submission
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            initializeDataTable();
        });

        // Handle reset button
        $('#reset-filter').on('click', function() {
            $('#tanggal').val('{{ date("Y-m-d") }}');
            initializeDataTable();
        });

        // Global error handling for AJAX requests
        $(document).ajaxError(function(event, jqxhr, settings, thrown) {
            console.error('AJAX Error:', {
                event: event,
                jqxhr: jqxhr,
                settings: settings,
                thrown: thrown
            });
            
            let message = 'Terjadi kesalahan saat memuat data';
            
            if (jqxhr.responseJSON && jqxhr.responseJSON.metadata) {
                message = jqxhr.responseJSON.metadata.message;
            }
            
            $('#error-message').text(message);
            $('#error-alert').removeClass('d-none');
        });
    });
</script>
@stop
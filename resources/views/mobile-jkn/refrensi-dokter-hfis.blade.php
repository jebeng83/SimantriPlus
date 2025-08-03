@extends('adminlte::page')

@section('title', 'Referensi Dokter HFIS BPJS')

@section('content_header')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('plugins.Jquery', true)
@section('plugins.Datatables', true)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-md"></i>
                        Referensi Dokter HFIS BPJS
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="refreshData">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="kodePoli" class="form-label">Kode Poli:</label>
                            <input type="text" class="form-control" id="kodePoli" placeholder="Masukkan kode poli (contoh: 001)">
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal" class="form-label">Tanggal Referensi:</label>
                            <input type="date" class="form-control" id="tanggal" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-success" id="loadData">
                                <i class="fas fa-search"></i> Cari Data
                            </button>
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data referensi dokter...</p>
                    </div>

                    <!-- Error Message -->
                    <div id="errorMessage" class="alert alert-danger" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="errorText"></span>
                    </div>

                    <!-- MetaData Section -->
                    <div id="metaDataContainer" class="mb-3" style="display: none;">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle"></i> Informasi Response
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Status Code:</strong>
                                        <span id="metaCode" class="badge badge-success">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Message:</strong>
                                        <span id="metaMessage">-</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Timestamp:</strong>
                                        <span id="metaTimestamp">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div id="dataContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="dokterTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Dokter</th>
                                        <th>Nama Dokter</th>
                                        <th>Jam Praktek</th>
                                        <th>Kapasitas</th>
                                    </tr>
                                </thead>
                                <tbody id="dokterTableBody">
                                    <!-- Data will be populated here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Summary -->
                        <div class="mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Total: <span id="totalRecords">0</span> dokter ditemukan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
if (typeof $ !== 'undefined') {
    $(document).ready(function() {
        console.log('Document ready - JavaScript loaded');
        
        // Event handlers
        $('#loadData, #refreshData').on('click', function() {
            console.log('Button clicked:', $(this).attr('id'));
            loadDokterData();
        });
        
        $('#kodePoli, #tanggal').on('input change', function() {
            // Auto load when both fields are filled
            if ($('#kodePoli').val() && $('#tanggal').val()) {
                loadDokterData();
            }
        });
    
    function loadDokterData() {
        const kodePoli = $('#kodePoli').val().trim();
        const tanggal = $('#tanggal').val();
        
        if (!kodePoli) {
            showError('Silakan masukkan kode poli terlebih dahulu');
            return;
        }
        
        if (!tanggal) {
            showError('Silakan pilih tanggal terlebih dahulu');
            return;
        }
        
        // Show loading
        $('#loadingIndicator').show();
        $('#errorMessage').hide();
        $('#dataContainer').hide();
        $('#metaDataContainer').hide();
        
        // Make AJAX request
        $.ajax({
            url: '/api/mobile-jkn/referensi-dokter/kodepoli/' + kodePoli + '/tanggal/' + tanggal,
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#loadingIndicator').hide();
                
                console.log('Response received:', response);
                
                // Handle both metaData and metadata formats
                const metadata = response.metaData || response.metadata;
                
                // Display metadata
                displayMetaData(metadata);
                
                if (metadata && (metadata.code === 200 || metadata.code === 1)) {
                    // Handle different response structures
                    let dokterData = [];
                    if (response.response && Array.isArray(response.response.list)) {
                        // BPJS official format: response.response.list
                        dokterData = response.response.list;
                    } else if (response.response && Array.isArray(response.response)) {
                        // Current format: response.response as array
                        dokterData = response.response;
                    } else if (Array.isArray(response.response)) {
                        // Direct array format
                        dokterData = response.response;
                    }
                    
                    displayDokterData(dokterData);
                } else {
                    showError(metadata ? metadata.message : 'Gagal memuat data');
                }
            },
            error: function(xhr, status, error) {
                $('#loadingIndicator').hide();
                let errorMessage = 'Terjadi kesalahan saat memuat data';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMessage = 'Endpoint tidak ditemukan';
                } else if (xhr.status === 500) {
                    errorMessage = 'Kesalahan server internal';
                }
                
                showError(errorMessage);
            }
        });
    }
    
    function displayMetaData(metaData) {
        if (metaData) {
            $('#metaCode').text(metaData.code || '-')
                .removeClass('badge-success badge-warning badge-danger')
                .addClass(metaData.code === 200 ? 'badge-success' : 'badge-danger');
            $('#metaMessage').text(metaData.message || '-');
            $('#metaTimestamp').text(new Date().toLocaleString('id-ID'));
            $('#metaDataContainer').show();
        }
    }
    
    function displayDokterData(dokterList) {
        const tbody = $('#dokterTableBody');
        tbody.empty();
        
        if (dokterList.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        <i class="fas fa-inbox"></i><br>
                        Tidak ada data dokter ditemukan
                    </td>
                </tr>
            `);
        } else {
            dokterList.forEach(function(dokter, index) {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td><span class="badge badge-primary">${dokter.kodedokter || '-'}</span></td>
                        <td><strong>${dokter.namadokter || '-'}</strong></td>
                        <td>${dokter.jampraktek || '-'}</td>
                        <td><span class="badge badge-success">${dokter.kapasitas || '-'}</span></td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
        
        $('#totalRecords').text(dokterList.length);
        $('#dataContainer').show();
    }
    
    function showError(message) {
        $('#errorText').text(message);
        $('#errorMessage').show();
        $('#dataContainer').hide();
        $('#metaDataContainer').hide();
    }
    
    }); // End of $(document).ready
} else {
    alert('jQuery not available, using vanilla JavaScript');
    // Add vanilla JavaScript fallback here if needed
}
</script>
@endsection

@push('styles')
<style>
.card {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
}
</style>
@endpush
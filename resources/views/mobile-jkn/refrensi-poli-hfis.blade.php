@extends('adminlte::page')

@section('title', 'Referensi Poli HFIS BPJS')

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
                        <i class="fas fa-hospital"></i>
                        Referensi Poli HFIS BPJS
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
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal Referensi:</label>
                            <input type="date" class="form-control" id="tanggal" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
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
                        <p class="mt-2">Memuat data referensi poli...</p>
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
                            <table class="table table-bordered table-striped" id="poliTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Poli</th>
                                        <th>Nama Poli</th>
                                    </tr>
                                </thead>
                                <tbody id="poliTableBody">
                                    <!-- Data will be populated here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Summary -->
                        <div class="mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Total: <span id="totalRecords">0</span> poli ditemukan
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
        
        // Load data on page load
        loadPoliData();
        
        // Event handlers
        $('#loadData, #refreshData').on('click', function() {
            console.log('Button clicked:', $(this).attr('id'));
            loadPoliData();
        });
        
        $('#tanggal').on('change', function() {
            loadPoliData();
        });
    
    function loadPoliData() {
        const tanggal = $('#tanggal').val();
        
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
            url: '/api/mobile-jkn/referensi-poli/' + tanggal,
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
                    let poliData = [];
                    if (response.response && Array.isArray(response.response.list)) {
                        // BPJS official format: response.response.list
                        poliData = response.response.list;
                    } else if (response.response && Array.isArray(response.response)) {
                        // Current format: response.response as array
                        poliData = response.response;
                    } else if (Array.isArray(response.response)) {
                        // Direct array format
                        poliData = response.response;
                    }
                    
                    displayPoliData(poliData);
                } else {
                    showError(metadata ? metadata.message : 'Gagal memuat data');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                
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
                .addClass(metaData.code === 200 || metaData.code === 1 ? 'badge-success' : 'badge-danger');
            $('#metaMessage').text(metaData.message || '-');
            $('#metaTimestamp').text(new Date().toLocaleString('id-ID'));
            $('#metaDataContainer').show();
        }
    }
    
    function displayPoliData(poliList) {
        const tbody = $('#poliTableBody');
        tbody.empty();
        
        console.log('Displaying poli data:', poliList);
        
        if (!poliList || poliList.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        <i class="fas fa-inbox"></i><br>
                        Tidak ada data poli ditemukan
                    </td>
                </tr>
            `);
        } else {
            poliList.forEach(function(poli, index) {
                // Handle different field names from BPJS response
                const kodePoli = poli.kodepoli || poli.kdPoli || poli.kode || '-';
                const namaPoli = poli.namapoli || poli.nmPoli || poli.nama || '-';
                
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td><span class="badge badge-primary">${kodePoli}</span></td>
                        <td><strong>${namaPoli}</strong></td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
        
        $('#totalRecords').text(poliList ? poliList.length : 0);
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
    
    .card-tools {
        margin-top: 0.5rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>
@endpush
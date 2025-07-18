@extends('adminlte::page')

@section('title', 'Referensi Poli - BPJS PCare')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Referensi Poli BPJS PCare</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" onclick="loadPoliData()">
                            <i class="fas fa-sync-alt"></i> Refresh Data
                        </button>
                        <div class="btn-group ml-2">
                            <button type="button" class="btn btn-success btn-sm" onclick="exportExcel()">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="exportPdf()">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading indicator -->
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data poli...</p>
                    </div>

                    <!-- Error message -->
                    <div id="error-message" class="alert alert-danger" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="error-text"></span>
                    </div>

                    <!-- Success message -->
                    <div id="success-message" class="alert alert-success" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        <span id="success-text"></span>
                    </div>

                    <!-- Data table -->
                    <div id="data-container" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="poli-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Poli</th>
                                        <th>Nama Poli</th>
                                        <th>Poliklinik</th>
                                    </tr>
                                </thead>
                                <tbody id="poli-tbody">
                                    <!-- Data will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
    loadPoliData();
});

function showLoading() {
    $('#loading').show();
    $('#error-message').hide();
    $('#success-message').hide();
    $('#data-container').hide();
}

function hideLoading() {
    $('#loading').hide();
}

function showError(message) {
    hideLoading();
    $('#error-text').text(message);
    $('#error-message').show();
    $('#success-message').hide();
    $('#data-container').hide();
}

function showSuccess(message) {
    hideLoading();
    $('#success-text').text(message);
    $('#success-message').show();
    $('#error-message').hide();
}

function loadPoliData() {
    showLoading();
    
    $.ajax({
        url: '/pcare/api/ref/poli',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            hideLoading();
            
            if (response.success && response.data) {
                displayPoliData(response.data);
                showSuccess('Data poli berhasil dimuat dari ' + (response.source || 'API'));
            } else {
                showError('Tidak ada data poli yang ditemukan');
            }
        },
        error: function(xhr, status, error) {
            hideLoading();
            let errorMessage = 'Gagal memuat data poli';
            
            // Handle authentication error
            if (xhr.status === 401) {
                if (xhr.responseJSON && xhr.responseJSON.login_required) {
                    alert('Sesi login telah berakhir. Anda akan diarahkan ke halaman login.');
                    window.location.href = '/login';
                    return;
                }
            }
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage += ': ' + xhr.responseJSON.message;
            } else {
                errorMessage += ': ' + error;
            }
            
            showError(errorMessage);
        }
    });
}

function displayPoliData(data) {
    const tbody = $('#poli-tbody');
    tbody.empty();
    
    if (Array.isArray(data) && data.length > 0) {
        data.forEach(function(item, index) {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.kdPoli || '-'}</td>
                    <td>${item.nmPoli || '-'}</td>
                    <td>${item.poliklinik || '-'}</td>
                </tr>
            `;
            tbody.append(row);
        });
        
        $('#data-container').show();
        
        // Initialize DataTable if not already initialized
        if (!$.fn.DataTable.isDataTable('#poli-table')) {
            $('#poli-table').DataTable({
                responsive: true,
                pageLength: 25,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                }
            });
        }
    } else {
        showError('Data poli kosong atau format tidak valid');
    }
}

function exportExcel() {
    window.open('/pcare/api/ref/poli/export/excel', '_blank');
}

function exportPdf() {
    window.open('/pcare/api/ref/poli/export/pdf', '_blank');
}
</script>
@endpush
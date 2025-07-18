@extends('adminlte::page')

@section('title', 'Referensi Dokter - BPJS PCare')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Referensi Dokter BPJS PCare</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" onclick="loadDokterData()">
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
                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form id="filter-form" class="form-inline">
                                <div class="form-group mr-2">
                                    <label for="kodepoli" class="mr-2">Poli:</label>
                                    <select class="form-control" id="kodepoli" name="kodepoli">
                                        <option value="">Semua Poli</option>
                                        @foreach($poliList as $kode => $nama)
                                        <option value="{{ $kode }}">{{ $nama }}</option>
                                        @endforeach
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
                    </div>

                    <!-- Loading indicator -->
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data dokter...</p>
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
                            <table class="table table-bordered table-striped" id="dokter-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Dokter</th>
                                        <th>Nama Dokter</th>
                                        <th>Kode Poli</th>
                                        <th>Nama Poli</th>
                                        <th>Jam Praktek</th>
                                        <th>Kapasitas</th>
                                    </tr>
                                </thead>
                                <tbody id="dokter-tbody">
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
        // Initialize form submission
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            loadDokterData();
        });

        // Reset filter
        $('#reset-filter').on('click', function() {
            $('#kodepoli').val('');
            $('#tanggal').val('{{ date("Y-m-d") }}');
            $('#data-container').hide();
            $('#error-message').hide();
            $('#success-message').hide();
        });
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

    function loadDokterData() {
        const tanggal = $('#tanggal').val();
        const kodepoli = $('#kodepoli').val();

        if (!tanggal) {
            showError('Silakan pilih tanggal terlebih dahulu');
            return;
        }

        showLoading();
        
        // Build URL with parameters
        let url = `/pcare/api/ref/dokter/tanggal/${tanggal}`;
        if (kodepoli) {
            url += `?kodepoli=${kodepoli}`;
        }
        
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response);
                
                if (response.success && response.data) {
                    displayDokterData(response.data);
                    const source = response.source || 'API';
                    showSuccess(`Data dokter berhasil dimuat dari ${source}`);
                } else if (response.metadata && response.metadata.code === 200) {
                    // Handle old response format
                    const data = response.response?.list || response.data || [];
                    displayDokterData(data);
                    showSuccess('Data dokter berhasil dimuat');
                } else {
                    const message = response.message || (response.metadata ? response.metadata.message : 'Tidak ada data dokter yang ditemukan');
                    showError(message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', { status, error, response: xhr.responseText });
                
                let errorMessage = 'Gagal memuat data dokter';
                
                // Handle authentication error
                if (xhr.status === 401) {
                    if (xhr.responseJSON && xhr.responseJSON.login_required) {
                        alert('Sesi login telah berakhir. Anda akan diarahkan ke halaman login.');
                        window.location.href = '/login';
                        return;
                    }
                }
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage += ': ' + response.message;
                    } else if (response.metadata && response.metadata.message) {
                        errorMessage += ': ' + response.metadata.message;
                    }
                } catch (e) {
                    errorMessage += ': ' + error;
                }
                
                showError(errorMessage);
            }
        });
    }

    function displayDokterData(data) {
        const tbody = $('#dokter-tbody');
        tbody.empty();
        
        if (Array.isArray(data) && data.length > 0) {
            data.forEach(function(item, index) {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.kdDokter || '-'}</td>
                        <td>${item.nmDokter || '-'}</td>
                        <td>${item.kdPoli || '-'}</td>
                        <td>${item.nmPoli || '-'}</td>
                        <td>${item.jamPraktek || '-'}</td>
                        <td>${item.kapasitas || '0'}</td>
                    </tr>
                `;
                tbody.append(row);
            });
            
            $('#data-container').show();
            
            // Initialize DataTable if not already initialized
            if (!$.fn.DataTable.isDataTable('#dokter-table')) {
                $('#dokter-table').DataTable({
                    responsive: true,
                    pageLength: 25,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                    }
                });
            }
        } else {
            showError('Data dokter kosong atau format tidak valid');
        }
    }

    function exportExcel() {
        const tanggal = $('#tanggal').val();
        const kodepoli = $('#kodepoli').val();
        
        if (!tanggal) {
            alert('Silakan pilih tanggal terlebih dahulu');
            return;
        }
        
        let url = `/pcare/api/ref/dokter/export/excel?tanggal=${tanggal}`;
        if (kodepoli) {
            url += `&kodepoli=${kodepoli}`;
        }
        
        window.open(url, '_blank');
    }

    function exportPdf() {
        const tanggal = $('#tanggal').val();
        const kodepoli = $('#kodepoli').val();
        
        if (!tanggal) {
            alert('Silakan pilih tanggal terlebih dahulu');
            return;
        }
        
        let url = `/pcare/api/ref/dokter/export/pdf?tanggal=${tanggal}`;
        if (kodepoli) {
            url += `&kodepoli=${kodepoli}`;
        }
        
        window.open(url, '_blank');
    }
</script>
@endpush
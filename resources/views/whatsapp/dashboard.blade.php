@extends('adminlte::page')

@section('title', 'WhatsApp Gateway Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fab fa-whatsapp text-success"></i>
                        WhatsApp Gateway Dashboard
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Status Session -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-signal"></i>
                                        Status Koneksi
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="session-status">
                                        <div class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <p class="mt-2">Memeriksa status koneksi...</p>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-success btn-sm" id="btn-create-session">
                                            <i class="fas fa-plus"></i> Buat Session
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm" id="btn-refresh-status">
                                            <i class="fas fa-sync"></i> Refresh
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" id="btn-delete-session">
                                            <i class="fas fa-trash"></i> Hapus Session
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-qrcode"></i>
                                        QR Code
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    <div id="qr-code-container">
                                        <p class="text-muted">QR Code akan muncul di sini jika diperlukan</p>
                                    </div>
                                    <button type="button" class="btn btn-info btn-sm mt-2" id="btn-get-qr">
                                        <i class="fas fa-qrcode"></i> Dapatkan QR Code
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Kirim Pesan -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-paper-plane"></i>
                                        Test Kirim Pesan
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form id="test-message-form">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="test-phone">Nomor WhatsApp</label>
                                                    <input type="text" class="form-control" id="test-phone"
                                                        placeholder="628123456789" required>
                                                    <small class="form-text text-muted">Format: 628xxxxxxxxx</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="test-type">Tipe Pesan</label>
                                                    <select class="form-control" id="test-type" required>
                                                        <option value="text">Teks</option>
                                                        <option value="pdf">PDF (perlu ID pemeriksaan)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="test-id">ID Pemeriksaan (opsional)</label>
                                                    <input type="number" class="form-control" id="test-id"
                                                        placeholder="Untuk PDF atau template">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="test-message">Pesan</label>
                                            <textarea class="form-control" id="test-message" rows="3"
                                                placeholder="Masukkan pesan test...">Halo, ini adalah pesan test dari WhatsApp Gateway Edokter.</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-paper-plane"></i> Kirim Test
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3 id="stat-sent">-</h3>
                                    <p>Pesan Terkirim Hari Ini</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3 id="stat-pending">-</h3>
                                    <p>Pesan Pending</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3 id="stat-failed">-</h3>
                                    <p>Pesan Gagal</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3 id="stat-total">-</h3>
                                    <p>Total Pesan</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Log Aktivitas -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list"></i>
                                        Log Aktivitas Terbaru
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="activity-log">
                                        <p class="text-muted text-center">Memuat log aktivitas...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk QR Code -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode"></i>
                    Scan QR Code
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="qr-modal-content">
                    <p>Loading QR Code...</p>
                </div>
                <p class="text-muted mt-3">
                    Scan QR Code ini dengan WhatsApp di ponsel Anda untuk menghubungkan session.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btn-refresh-qr">
                    <i class="fas fa-sync"></i> Refresh QR
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
    // CSRF Token and API Testing Header
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-API-Testing': 'true'
        }
    });

    // Load initial data
    checkSessionStatus();
    loadStatistics();
    loadActivityLog();

    // Auto refresh every 30 seconds
    setInterval(function() {
        checkSessionStatus();
        loadStatistics();
    }, 30000);

    // Event handlers
    $('#btn-refresh-status').click(function() {
        checkSessionStatus();
    });

    $('#btn-create-session').click(function() {
        createSession();
    });

    $('#btn-delete-session').click(function() {
        if (confirm('Apakah Anda yakin ingin menghapus session WhatsApp?')) {
            deleteSession();
        }
    });

    $('#btn-get-qr').click(function() {
        getQRCode();
    });

    $('#btn-refresh-qr').click(function() {
        getQRCode();
    });

    $('#test-message-form').submit(function(e) {
        e.preventDefault();
        sendTestMessage();
    });

    // Functions
    function checkSessionStatus() {
        $.get('/ilp/whatsapp/session/status')
            .done(function(response) {
                updateSessionStatus(response);
            })
            .fail(function() {
                $('#session-status').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Gagal memeriksa status session
                    </div>
                `);
            });
    }

    function updateSessionStatus(response) {
        let statusHtml = '';
        let statusClass = '';
        let statusIcon = '';
        
        if (response.success && response.data && response.data.status === 'connected') {
            statusClass = 'success';
            statusIcon = 'fas fa-check-circle';
            statusHtml = `
                <div class="alert alert-success">
                    <i class="${statusIcon}"></i>
                    <strong>Terhubung</strong><br>
                    Session ID: ${response.data.session_id || 'default'}<br>
                    Status: ${response.data.status}
                </div>
            `;
        } else if (response.success && response.data && response.data.status === 'connecting') {
            statusClass = 'warning';
            statusIcon = 'fas fa-spinner fa-spin';
            statusHtml = `
                <div class="alert alert-warning">
                    <i class="${statusIcon}"></i>
                    <strong>Menghubungkan...</strong><br>
                    Silakan scan QR Code untuk melanjutkan
                </div>
            `;
        } else {
            statusClass = 'danger';
            statusIcon = 'fas fa-times-circle';
            statusHtml = `
                <div class="alert alert-danger">
                    <i class="${statusIcon}"></i>
                    <strong>Tidak Terhubung</strong><br>
                    Session belum dibuat atau terputus
                </div>
            `;
        }
        
        $('#session-status').html(statusHtml);
    }

    function createSession() {
        const btn = $('#btn-create-session');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Membuat...');
        
        $.post('/ilp/whatsapp/session/create')
            .done(function(response) {
                if (response.success) {
                    showAlert('success', 'Session berhasil dibuat');
                    setTimeout(checkSessionStatus, 2000);
                } else {
                    showAlert('danger', response.error || 'Gagal membuat session');
                }
            })
            .fail(function() {
                showAlert('danger', 'Terjadi kesalahan saat membuat session');
            })
            .always(function() {
                btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Buat Session');
            });
    }

    function deleteSession() {
        const btn = $('#btn-delete-session');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
        
        $.ajax({
            url: '/ilp/whatsapp/session/delete',
            method: 'DELETE'
        })
        .done(function(response) {
            if (response.success) {
                showAlert('success', 'Session berhasil dihapus');
                checkSessionStatus();
            } else {
                showAlert('danger', response.error || 'Gagal menghapus session');
            }
        })
        .fail(function() {
            showAlert('danger', 'Terjadi kesalahan saat menghapus session');
        })
        .always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Hapus Session');
        });
    }

    function getQRCode() {
        $('#qr-modal-content').html('<div class="spinner-border" role="status"></div><p class="mt-2">Loading QR Code...</p>');
        $('#qrModal').modal('show');
        
        $.get('/ilp/whatsapp/session/qr')
            .done(function(response) {
                if (response.success && response.data && response.data.qr) {
                    $('#qr-modal-content').html(`<img src="${response.data.qr}" class="img-fluid" alt="QR Code">`);
                    $('#qr-code-container').html(`<img src="${response.data.qr}" class="img-fluid" style="max-width: 200px;" alt="QR Code">`);
                } else {
                    $('#qr-modal-content').html(`<div class="alert alert-warning">${response.message || 'QR Code tidak tersedia'}</div>`);
                }
            })
            .fail(function() {
                $('#qr-modal-content').html('<div class="alert alert-danger">Gagal memuat QR Code</div>');
            });
    }

    function sendTestMessage() {
        const btn = $('#test-message-form button[type="submit"]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mengirim...');
        
        const data = {
            phone: $('#test-phone').val(),
            type: $('#test-type').val(),
            message: $('#test-message').val(),
            id: $('#test-id').val() || null
        };
        
        $.post('/ilp/whatsapp/send', data)
            .done(function(response) {
                if (response.success) {
                    showAlert('success', 'Pesan test berhasil dikirim');
                    $('#test-message-form')[0].reset();
                    loadActivityLog();
                } else {
                    showAlert('danger', response.error || 'Gagal mengirim pesan');
                }
            })
            .fail(function() {
                showAlert('danger', 'Terjadi kesalahan saat mengirim pesan');
            })
            .always(function() {
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Kirim Test');
            });
    }

    function loadStatistics() {
        // Placeholder untuk statistik
        // Implementasi sesuai kebutuhan aplikasi
        $('#stat-sent').text('0');
        $('#stat-pending').text('0');
        $('#stat-failed').text('0');
        $('#stat-total').text('0');
    }

    function loadActivityLog() {
        // Placeholder untuk log aktivitas
        // Implementasi sesuai kebutuhan aplikasi
        $('#activity-log').html('<p class="text-muted text-center">Belum ada aktivitas</p>');
    }

    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of card body
        $('.card-body').first().prepend(alertHtml);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endpush

@push('styles')
<style>
    .small-box {
        border-radius: 0.25rem;
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        display: block;
        margin-bottom: 20px;
        position: relative;
    }

    .small-box>.inner {
        padding: 10px;
    }

    .small-box>.small-box-footer {
        background: rgba(0, 0, 0, .1);
        color: rgba(255, 255, 255, .8);
        display: block;
        padding: 3px 0;
        position: relative;
        text-align: center;
        text-decoration: none;
        z-index: 10;
    }

    .small-box>.icon {
        color: rgba(255, 255, 255, .15);
        z-index: 0;
    }

    .small-box>.icon>i {
        font-size: 70px;
        position: absolute;
        right: 15px;
        top: 15px;
        transition: transform .3s linear;
    }

    .small-box:hover {
        text-decoration: none;
        color: #fff;
    }

    .small-box:hover .icon>i {
        transform: scale(1.1);
    }

    .bg-info {
        background-color: #17a2b8 !important;
        color: #fff;
    }

    .bg-warning {
        background-color: #ffc107 !important;
        color: #212529;
    }

    .bg-danger {
        background-color: #dc3545 !important;
        color: #fff;
    }

    .bg-success {
        background-color: #28a745 !important;
        color: #fff;
    }
</style>
@endpush
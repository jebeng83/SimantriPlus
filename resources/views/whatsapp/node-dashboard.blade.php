@extends('adminlte::page')

@section('title', 'WhatsApp Node.js Gateway Dashboard')

@section('content_header')
<h1>WhatsApp Node.js Gateway Dashboard</h1>
@stop

@section('content')
<div class="container-fluid">
    <!-- Status Server -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status Server Node.js</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-primary" onclick="checkServerStatus()">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="server-status">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin"></i> Memeriksa status server...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">QR Code WhatsApp</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-success" onclick="getQrCode()">
                            <i class="fas fa-qrcode"></i> Dapatkan QR Code
                        </button>
                    </div>
                </div>
                <div class="card-body text-center">
                    <div id="qr-code-container">
                        <p class="text-muted">Klik tombol "Dapatkan QR Code" untuk menampilkan QR Code</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Control Panel -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Control Panel</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Server Control Buttons -->
                        <div class="col-12 mb-2">
                            <h6 class="text-muted mb-2"><i class="fas fa-server"></i> Server Control</h6>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-success btn-block btn-sm" onclick="startNodeServerCommand()">
                                <i class="fas fa-play"></i> Start (Auto)
                            </button>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-primary btn-block btn-sm" onclick="quickStartNodeServer()">
                                <i class="fas fa-rocket"></i> Quick Start
                            </button>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-danger btn-block btn-sm" onclick="stopNodeServer()">
                                <i class="fas fa-stop"></i> Stop Server
                            </button>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-warning btn-block btn-sm" onclick="restartNodeServer()">
                                <i class="fas fa-redo"></i> Restart
                            </button>
                        </div>
                        
                        <!-- Monitoring Buttons -->
                        <div class="col-12 mb-2 mt-2">
                            <h6 class="text-muted mb-2"><i class="fas fa-chart-line"></i> Monitoring</h6>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-secondary btn-block btn-sm" onclick="checkNodeServerStatus()">
                                <i class="fas fa-heartbeat"></i> Status
                            </button>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-info btn-block btn-sm" onclick="viewServerLogs()">
                                <i class="fas fa-file-alt"></i> Logs
                            </button>
                        </div>
                        
                        <!-- Utility Buttons -->
                        <div class="col-12 mb-2 mt-2">
                            <h6 class="text-muted mb-2"><i class="fas fa-tools"></i> Utilities</h6>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-info btn-block btn-sm" onclick="startNodeServer()">
                                <i class="fas fa-terminal"></i> Manual
                            </button>
                        </div>
                        <div class="col-6 mb-2">
                            <button type="button" class="btn btn-warning btn-block btn-sm" onclick="clearWhatsAppCache()">
                                <i class="fas fa-broom"></i> Clear Cache
                            </button>
                        </div>
                        <div class="col-12 mb-2">
                            <button type="button" class="btn btn-outline-warning btn-block btn-sm" onclick="processQueue()">
                                <i class="fas fa-play"></i> Proses Antrean via Node.js
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Message Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Test Kirim Pesan</h3>
                </div>
                <div class="card-body">
                    <form id="test-message-form">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="test-number">Nomor WhatsApp</label>
                                    <input type="text" class="form-control" id="test-number" placeholder="08123456789"
                                        required>
                                    <small class="form-text text-muted">Format: 08123456789 atau 628123456789</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="test-message">Pesan</label>
                                    <textarea class="form-control" id="test-message" rows="3"
                                        placeholder="Masukkan pesan test..." required></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-paper-plane"></i> Kirim
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Terminal Manual Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Terminal Manual</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="clearTerminal()">
                            <i class="fas fa-trash"></i> Clear Terminal
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Panduan Manual:</strong> Jika start server otomatis gagal, gunakan terminal di bawah ini
                        untuk menjalankan server Node.js secara manual.
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-terminal"></i></span>
                                </div>
                                <input type="text" class="form-control" id="terminal-command"
                                    value="cd public/wagateway/node_mrlee && node appJM.js"
                                    placeholder="Masukkan perintah terminal..." list="command-suggestions">
                                <datalist id="command-suggestions">
                                    <option value="cd public/wagateway/node_mrlee && node appJM.js">
                                    <option value="node public/wagateway/node_mrlee/appJM.js">
                                    <option value="npm start">
                                    <option value="pm2 start public/wagateway/node_mrlee/appJM.js --name whatsapp-gateway">
                                    <option value="pm2 restart whatsapp-gateway">
                                    <option value="pm2 stop whatsapp-gateway">
                                    <option value="lsof -i :8100">
                                    <option value="kill -9 $(lsof -t -i:8100)">
                                </datalist>
                            </div>
                            <small class="text-muted mt-1">
                                <i class="fas fa-lightbulb"></i>
                                <strong>Tips:</strong> Ketik untuk melihat saran perintah atau masukkan perintah custom
                                Anda sendiri
                            </small>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary btn-block" onclick="copyCommand()">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-success btn-block" onclick="runManualCommand()">
                                <i class="fas fa-play"></i> Run
                            </button>
                        </div>
                    </div>

                    <div class="terminal-output" id="terminal-output"
                        style="height: 200px; overflow-y: auto; background: #1e1e1e; color: #00ff00; padding: 10px; border-radius: 5px; font-family: 'Courier New', monospace; font-size: 12px;">
                        <div class="terminal-line" style="color: #44aaff;">🚀 Terminal Manual WhatsApp Gateway</div>
                        <div class="terminal-line" style="color: #888;">═══════════════════════════════════════</div>
                        <div class="terminal-line" style="color: #ffaa00;">💡 CARA PENGGUNAAN:</div>
                        <div class="terminal-line" style="color: #888;">1. Edit perintah di input box di atas</div>
                        <div class="terminal-line" style="color: #888;">2. Klik 'Run' untuk simulasi + copy ke clipboard
                        </div>
                        <div class="terminal-line" style="color: #888;">3. Buka terminal sistem Anda</div>
                        <div class="terminal-line" style="color: #888;">4. Navigate ke:
                            /Users/agusbudiyono/Documents/EDOKTER BENAR/edokter</div>
                        <div class="terminal-line" style="color: #888;">5. Paste dan jalankan perintah</div>
                        <div class="terminal-line" style="color: #888;">═══════════════════════════════════════</div>
                        <div class="terminal-line" style="color: #44ff44;">✓ Terminal siap digunakan</div>
                        <div class="terminal-line" style="color: #888;">---</div>
                    </div>

                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="text-muted mb-2"><strong>Perintah Cepat:</strong></label>
                                <div class="btn-group-toggle">
                                    <button type="button" class="btn btn-outline-primary btn-sm mr-1 mb-1"
                                        onclick="setCommand('cd public/wagateway/node_mrlee && node appJM.js')">
                                        <i class="fas fa-play"></i> Start Server
                                    </button>
                                    <button type="button" class="btn btn-outline-success btn-sm mr-1 mb-1"
                                        onclick="setCommand('cd public/wagateway/node_mrlee && nohup node appJM.js > server.log 2>&1 &')">
                                        <i class="fas fa-rocket"></i> Start Background
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm mr-1 mb-1"
                                        onclick="setCommand('lsof -i :8100')">
                                        <i class="fas fa-search"></i> Check Port
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm mr-1 mb-1"
                                        onclick="setCommand('./public/wagateway/node_mrlee/kill-port-8100.sh')">
                                        <i class="fas fa-stop"></i> Kill Port
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm mr-1 mb-1"
                                        onclick="setCommand('kill -9 $(lsof -t -i:8100)')">
                                        <i class="fas fa-times"></i> Force Kill
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm mr-1 mb-1"
                                        onclick="setCommand('pm2 start public/wagateway/node_mrlee/appJM.js --name whatsapp-gateway')">
                                        <i class="fas fa-cogs"></i> PM2 Start
                                    </button>
                                    <button type="button" class="btn btn-outline-dark btn-sm mr-1 mb-1"
                                        onclick="setCommand('pm2 status')">
                                        <i class="fas fa-list"></i> PM2 Status
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm mr-1 mb-1"
                                        onclick="setCommand('tail -f public/wagateway/node_mrlee/server.log')">
                                        <i class="fas fa-file-alt"></i> View Logs
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb"></i>
                                    <strong>Tips:</strong> Pastikan port 8100 tidak digunakan oleh aplikasi lain. Jika
                                    server sudah berjalan, stop terlebih dahulu sebelum menjalankan ulang.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Logs</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="clearLogs()">
                            <i class="fas fa-trash"></i> Clear
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="activity-logs"
                        style="height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                        <p class="text-muted">Activity logs akan ditampilkan di sini...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .server-online {
        color: #28a745;
    }

    .server-offline {
        color: #dc3545;
    }

    .qr-code-image {
        max-width: 300px;
        max-height: 300px;
    }

    .log-entry {
        margin-bottom: 5px;
        padding: 5px;
        border-left: 3px solid #007bff;
        background: white;
    }

    .log-success {
        border-left-color: #28a745;
    }

    .log-error {
        border-left-color: #dc3545;
    }

    .log-warning {
        border-left-color: #ffc107;
    }

    .terminal-output {
        font-family: 'Courier New', 'Monaco', 'Menlo', monospace;
        line-height: 1.4;
    }

    .terminal-line {
        margin: 2px 0;
        word-wrap: break-word;
    }

    .terminal-output::-webkit-scrollbar {
        width: 8px;
    }

    .terminal-output::-webkit-scrollbar-track {
        background: #2a2a2a;
    }

    .terminal-output::-webkit-scrollbar-thumb {
        background: #555;
        border-radius: 4px;
    }

    .terminal-output::-webkit-scrollbar-thumb:hover {
        background: #777;
    }
</style>
@stop

@section('js')
<script>
    // Setup CSRF token untuk AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let logCounter = 0;

function addLog(message, type = 'info') {
    const timestamp = new Date().toLocaleTimeString();
    const logClass = type === 'success' ? 'log-success' : (type === 'error' ? 'log-error' : (type === 'warning' ? 'log-warning' : ''));
    
    const logEntry = `
        <div class="log-entry ${logClass}">
            <small class="text-muted">[${timestamp}]</small> ${message}
        </div>
    `;
    
    $('#activity-logs').append(logEntry);
    $('#activity-logs').scrollTop($('#activity-logs')[0].scrollHeight);
    
    logCounter++;
    if (logCounter > 100) {
        $('#activity-logs .log-entry:first').remove();
        logCounter--;
    }
}

function checkServerStatus() {
    addLog('Memeriksa status server Node.js...', 'info');
    
    $.ajax({
        url: '{{ route("ilp.whatsapp.node.status") }}',
        method: 'GET',
        success: function(response) {
            if (response.success && response.status === 'online') {
                $('#server-status').html(`
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle server-online"></i>
                        <strong>Server Online</strong><br>
                        ${response.data.message}
                    </div>
                `);
                addLog('Server Node.js online dan berjalan normal', 'success');
            } else {
                $('#server-status').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle server-offline"></i>
                        <strong>Server Offline</strong><br>
                        ${response.message}
                    </div>
                `);
                addLog('Server Node.js offline atau tidak dapat diakses', 'error');
            }
        },
        error: function() {
            $('#server-status').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle server-offline"></i>
                    <strong>Server Offline</strong><br>
                    Tidak dapat terhubung ke server Node.js
                </div>
            `);
            addLog('Gagal terhubung ke server Node.js', 'error');
        }
    });
}

function getQrCode() {
    addLog('Meminta QR Code dari server...', 'info');
    
    $('#qr-code-container').html(`
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x"></i><br>
            <p class="mt-2">Mengambil QR Code...</p>
        </div>
    `);
    
    $.ajax({
        url: '{{ route("ilp.whatsapp.node.qr") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const data = response.data;
                
                if (data.status && data.qrBarCode !== 'not ready') {
                    if (data.qrBarCode === 'WA Gate is ready') {
                        $('#qr-code-container').html(`
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle fa-2x"></i><br>
                                <strong>WhatsApp Sudah Terhubung!</strong><br>
                                Tidak perlu scan QR Code lagi.
                            </div>
                        `);
                        addLog('WhatsApp sudah terhubung dan siap digunakan', 'success');
                    } else {
                        // Generate QR Code image
                        const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(data.qrBarCode)}`;
                        
                        $('#qr-code-container').html(`
                            <div>
                                <img src="${qrCodeUrl}" class="qr-code-image" alt="QR Code">
                                <p class="mt-2"><strong>Scan QR Code dengan WhatsApp Anda</strong></p>
                                <small class="text-muted">QR Code akan otomatis refresh setiap 30 detik</small>
                            </div>
                        `);
                        addLog('QR Code berhasil ditampilkan', 'success');
                        
                        // Auto refresh QR code every 30 seconds
                        setTimeout(getQrCode, 30000);
                    }
                } else {
                    $('#qr-code-container').html(`
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            QR Code belum siap. Silakan coba lagi dalam beberapa detik.
                            <br><small>Jika masalah berlanjut, coba bersihkan cache WhatsApp terlebih dahulu.</small>
                        </div>
                    `);
                    addLog('QR Code belum siap, mencoba lagi...', 'warning');
                    setTimeout(getQrCode, 5000);
                }
            } else {
                $('#qr-code-container').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i>
                        Gagal mendapatkan QR Code: ${response.message}
                        <br><small>Pastikan server Node.js berjalan dan coba bersihkan cache jika diperlukan.</small>
                    </div>
                `);
                addLog('Gagal mendapatkan QR Code: ' + response.message, 'error');
            }
        },
        error: function() {
            $('#qr-code-container').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i>
                    Server Node.js tidak dapat diakses
                    <br><small>Pastikan server Node.js sudah dijalankan. Jika masih bermasalah, coba restart server dan bersihkan cache.</small>
                </div>
            `);
            addLog('Server Node.js tidak dapat diakses', 'error');
        }
    });
}

function processQueue() {
    addLog('Memproses antrean pesan melalui Node.js...', 'info');
    
    $.ajax({
        url: '{{ route("ilp.whatsapp.node.process-queue") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            limit: 10,
            status: 'pending'
        },
        success: function(response) {
            if (response.success) {
                addLog(response.message, 'success');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message
                });
            } else {
                addLog('Gagal memproses antrean: ' + response.message, 'error');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message
                });
            }
        },
        error: function() {
            addLog('Gagal memproses antrean: Server tidak dapat diakses', 'error');
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Server Node.js tidak dapat diakses'
            });
        }
    });
}

function startNodeServerCommand() {
    addLog('Menjalankan server Node.js melalui command...', 'info');
    
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menjalankan server Node.js secara otomatis?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Jalankan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            addLog('Menjalankan perintah: cd public/wagateway/node_mrlee && node appJM.js', 'info');
            
            $.ajax({
                url: '{{ route("ilp.whatsapp.node.start") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                timeout: 60000, // 60 detik timeout untuk startup yang lebih lama
                success: function(response) {
                    if (response.success) {
                        addLog('Server Node.js berhasil dijalankan via command', 'success');
                        
                        // Cek status response untuk menentukan pesan yang tepat
                        let message = response.message || 'Server Node.js berhasil dijalankan';
                        let icon = 'success';
                        
                        if (response.status === 'starting') {
                            icon = 'info';
                            message += ' Server sedang dalam proses startup, silakan tunggu beberapa saat.';
                        }
                        
                        Swal.fire({
                            icon: icon,
                            title: 'Berhasil!',
                            text: message,
                            timer: response.status === 'starting' ? 5000 : 3000
                        });
                        
                        // Refresh status setelah 3 detik untuk memberi waktu server startup
                        setTimeout(checkServerStatus, 3000);
                        
                        // Auto-refresh lebih sering setelah start server
                        // Jika status 'starting', refresh lebih lama untuk memberi waktu startup
                        const refreshDuration = response.status === 'starting' ? 60000 : 30000; // 60 detik atau 30 detik
                        const refreshInterval = 2000; // setiap 2 detik
                        const maxRefresh = refreshDuration / refreshInterval;
                        
                        let refreshCount = 0;
                        const autoRefresh = setInterval(function() {
                            checkServerStatus();
                            refreshCount++;
                            if (refreshCount >= maxRefresh) {
                                clearInterval(autoRefresh);
                                addLog('Auto-refresh status server dihentikan', 'info');
                            }
                        }, refreshInterval);
                    } else {
                        addLog('Gagal menjalankan server: ' + (response.message || 'Unknown error'), 'error');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Gagal menjalankan server Node.js'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Server tidak dapat diakses';
                    
                    if (status === 'timeout') {
                        errorMessage = 'Timeout - Server membutuhkan waktu terlalu lama untuk startup. Coba periksa status server secara manual.';
                        addLog('Timeout saat start server - coba cek status server manual', 'warning');
                        
                        // Tampilkan pesan khusus untuk timeout
                        Swal.fire({
                            icon: 'warning',
                            title: 'Timeout!',
                            html: `
                                <p>Server membutuhkan waktu lebih lama untuk startup.</p>
                                <p><strong>Silakan:</strong></p>
                                <ul style="text-align: left; display: inline-block;">
                                    <li>Tunggu beberapa saat lagi</li>
                                    <li>Klik tombol "Refresh" untuk cek status</li>
                                    <li>Atau jalankan manual jika diperlukan</li>
                                </ul>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Cek Status Sekarang',
                            cancelButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                checkServerStatus();
                            }
                        });
                        return;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    addLog('Gagal menjalankan server: ' + errorMessage, 'error');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: errorMessage
                    });
                }
            });
        }
    });
}

function startNodeServer() {
    addLog('Menampilkan panduan manual untuk menjalankan server Node.js...', 'info');
    
    Swal.fire({
        icon: 'info',
        title: 'Panduan Manual Start Server',
        html: `
            <div class="text-left">
                <p><strong>Untuk menjalankan server Node.js secara manual:</strong></p>
                <ol>
                    <li>Buka terminal/command prompt</li>
                    <li>Navigasi ke direktori project</li>
                    <li>Jalankan perintah berikut:</li>
                </ol>
                <div class="bg-dark text-light p-3 rounded mt-3" style="font-family: monospace;">
                    <code>cd public/wagateway/node_mrlee && node appJM.js</code>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Server akan berjalan di port 8100. Pastikan port tersebut tidak digunakan oleh aplikasi lain.
                    </small>
                </div>
                <div class="mt-2">
                    <small class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Jangan tutup terminal selama server berjalan.
                    </small>
                </div>
            </div>
        `,
        width: '600px',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-copy"></i> Copy Command',
        cancelButtonText: 'Tutup',
        confirmButtonColor: '#007bff'
    }).then((result) => {
        if (result.isConfirmed) {
            // Copy command to clipboard
            const command = 'cd public/wagateway/node_mrlee && node appJM.js';
            navigator.clipboard.writeText(command).then(() => {
                addLog('Perintah berhasil disalin ke clipboard', 'success');
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin!',
                    text: 'Perintah telah disalin ke clipboard',
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch(() => {
                addLog('Gagal menyalin perintah ke clipboard', 'warning');
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal Menyalin',
                    text: 'Silakan salin perintah secara manual: cd public/wagateway/node_mrlee && node appJM.js'
                });
            });
        }
    });
}

function stopNodeServer() {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menghentikan server Node.js? Cache WhatsApp akan dibersihkan otomatis.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hentikan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            addLog('Menghentikan server Node.js...', 'warning');
            
            $.ajax({
                url: '{{ route("ilp.whatsapp.node.stop") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        addLog('Server Node.js berhasil dihentikan', 'success');
                        
                        // Otomatis bersihkan cache setelah server dihentikan
                        setTimeout(function() {
                            performClearCache(false); // false = tidak tampilkan konfirmasi
                        }, 1000);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message + '. Cache akan dibersihkan otomatis.'
                        });
                        checkServerStatus();
                    } else {
                        addLog('Gagal menghentikan server: ' + response.message, 'error');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    addLog('Gagal menghentikan server: Server tidak dapat diakses', 'error');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Server Node.js tidak dapat diakses'
                    });
                }
            });
        }
    });
}

function clearLogs() {
    $('#activity-logs').html('<p class="text-muted">Activity logs akan ditampilkan di sini...</p>');
    logCounter = 0;
    addLog('Logs dibersihkan', 'info');
}

function quickStartNodeServer() {
    addLog('Memulai Quick Start Node.js Server...', 'info');
    
    Swal.fire({
        title: 'Quick Start Server',
        text: 'Menjalankan server Node.js secara otomatis...',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Eksekusi perintah start server melalui AJAX
    $.ajax({
        url: '{{ route("ilp.whatsapp.node.quick-start") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        timeout: 30000, // 30 second timeout
        data: {
            command: 'cd public/wagateway/node_mrlee && nohup node appJM.js > server.log 2>&1 &'
        },
        success: function(response) {
            if (response.success) {
                addLog('✓ Server Node.js berhasil dijalankan!', 'success');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Server Started!',
                    html: `
                        <p><strong>Server Node.js berhasil dijalankan!</strong></p>
                        <p>🌐 Listening on port: <strong>8100</strong></p>
                        <p>📱 WhatsApp Gateway: <strong>Ready</strong></p>
                        <hr>
                        <p class="text-muted">Silakan tunggu beberapa detik dan scan QR Code</p>
                        <small class="text-info">Output: ${response.output || 'Server started in background'}</small>
                    `,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    // Auto refresh status dan ambil QR code setelah delay
                    setTimeout(() => {
                        checkServerStatus();
                    }, 2000);
                    setTimeout(() => {
                        getQrCode();
                    }, 3000);
                });
                
            } else {
                addLog('❌ Gagal menjalankan server: ' + (response.message || 'Unknown error'), 'error');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Start Server!',
                    html: `
                        <p><strong>Gagal menjalankan server Node.js</strong></p>
                        <p class="text-danger">${response.message || 'Unknown error'}</p>
                        <hr>
                        <p class="text-muted">Silakan coba metode manual atau periksa log error</p>
                        ${response.output ? '<small class="text-warning">Output: ' + response.output + '</small>' : ''}
                    `,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = error;
            
            if (status === 'timeout') {
                errorMessage = 'Request timeout - Server membutuhkan waktu lebih lama';
                addLog('⏱️ Request timeout - mencoba lagi...', 'warning');
            } else if (xhr.status === 0) {
                errorMessage = 'Koneksi terputus atau server tidak dapat diakses';
                addLog('❌ Koneksi terputus: ' + error, 'error');
            } else {
                addLog('❌ Error komunikasi dengan server: ' + error, 'error');
            }
            
            Swal.fire({
                icon: status === 'timeout' ? 'warning' : 'error',
                title: status === 'timeout' ? 'Request Timeout' : 'Error!',
                html: `
                    <p><strong>${status === 'timeout' ? 'Server membutuhkan waktu lebih lama' : 'Gagal berkomunikasi dengan server'}</strong></p>
                    <p class="text-${status === 'timeout' ? 'warning' : 'danger'}">Error: ${errorMessage}</p>
                    <hr>
                    <p class="text-muted">${status === 'timeout' ? 'Server mungkin sedang memulai. Silakan tunggu dan cek status server.' : 'Silakan coba lagi atau gunakan metode manual'}</p>
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: status === 'timeout' ? '#ffc107' : '#dc3545'
            });
        }
    });
    
    // Fallback: Copy command ke clipboard untuk backup manual
    const command = 'cd public/wagateway/node_mrlee && nohup node appJM.js > server.log 2>&1 &';
    try {
        navigator.clipboard.writeText(command).then(() => {
            addLog('📋 Command backup di-copy ke clipboard', 'info');
        });
    } catch (err) {
        // Fallback for older browsers
        console.log('Clipboard not available');
    }
}

function checkNodeServerStatus() {
    addLog('Memeriksa status detail server Node.js...', 'info');
    
    Swal.fire({
        title: 'Checking Server Status',
        text: 'Memeriksa status server Node.js...',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Check server status dengan detail lebih lengkap
    $.ajax({
        url: '{{ route("ilp.whatsapp.node.status") }}',
        method: 'GET',
        success: function(response) {
            if (response.success && response.status === 'online') {
                addLog('✓ Server Node.js online dan berjalan normal', 'success');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Server Online!',
                    html: `
                        <div class="text-left">
                            <p><strong>🟢 Status:</strong> Online</p>
                            <p><strong>🌐 Port:</strong> 8100</p>
                            <p><strong>📱 WhatsApp:</strong> ${response.data.message}</p>
                            <p><strong>⏰ Uptime:</strong> ${response.data.uptime || 'N/A'}</p>
                            <hr>
                            <small class="text-muted">Server berjalan dengan normal</small>
                        </div>
                    `,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });
            } else {
                addLog('❌ Server Node.js offline atau tidak dapat diakses', 'error');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Server Offline!',
                    html: `
                        <div class="text-left">
                            <p><strong>🔴 Status:</strong> Offline</p>
                            <p><strong>❌ Error:</strong> ${response.message}</p>
                            <hr>
                            <p class="text-muted">Silakan start server terlebih dahulu</p>
                        </div>
                    `,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function() {
            addLog('❌ Gagal terhubung ke server Node.js', 'error');
            
            Swal.fire({
                icon: 'error',
                title: 'Connection Failed!',
                html: `
                    <div class="text-left">
                        <p><strong>🔴 Status:</strong> Connection Failed</p>
                        <p><strong>❌ Error:</strong> Tidak dapat terhubung ke server</p>
                        <hr>
                        <p class="text-muted">Server mungkin belum dijalankan atau ada masalah koneksi</p>
                    </div>
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}

function viewServerLogs() {
    addLog('Mengambil log server Node.js...', 'info');
    
    Swal.fire({
        title: 'Server Logs',
        html: `
            <div id="server-logs-content" style="height: 400px; overflow-y: auto; background: #1e1e1e; color: #00ff00; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace; font-size: 12px; text-align: left;">
                <div style="color: #44aaff;">📋 Loading server logs...</div>
            </div>
        `,
        width: '80%',
        showCancelButton: true,
        confirmButtonText: 'Refresh Logs',
        cancelButtonText: 'Close',
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        didOpen: () => {
            loadServerLogs();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            loadServerLogs();
        }
    });
}

function loadServerLogs() {
    $.ajax({
        url: '{{ route("ilp.whatsapp.node.logs") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const logs = response.logs || 'No logs available';
                const logLines = logs.split('\n');
                let formattedLogs = '';
                
                logLines.forEach((line, index) => {
                    if (line.trim()) {
                        let color = '#00ff00';
                        if (line.includes('ERROR') || line.includes('error')) {
                            color = '#ff4444';
                        } else if (line.includes('WARN') || line.includes('warning')) {
                            color = '#ffaa00';
                        } else if (line.includes('INFO') || line.includes('listening')) {
                            color = '#44aaff';
                        }
                        
                        formattedLogs += `<div style="color: ${color}; margin: 2px 0;">${line}</div>`;
                    }
                });
                
                $('#server-logs-content').html(formattedLogs || '<div style="color: #888;">No logs available</div>');
                $('#server-logs-content').scrollTop($('#server-logs-content')[0].scrollHeight);
                
                addLog('✓ Log server berhasil dimuat', 'success');
            } else {
                $('#server-logs-content').html(`<div style="color: #ff4444;">❌ Error: ${response.message}</div>`);
                addLog('❌ Gagal memuat log server: ' + response.message, 'error');
            }
        },
        error: function() {
            $('#server-logs-content').html('<div style="color: #ff4444;">❌ Error: Gagal mengambil log server</div>');
            addLog('❌ Gagal mengambil log server', 'error');
        }
    });
}

function clearWhatsAppCache(showConfirmation = true) {
    if (showConfirmation) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin membersihkan cache WhatsApp? Ini akan menghapus session login WhatsApp.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Bersihkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                performClearCache();
            }
        });
    } else {
        performClearCache();
    }
}

function performClearCache(showConfirmation = true) {
    addLog('Membersihkan cache WhatsApp...', 'warning');
    
    $.ajax({
        url: '{{ route("ilp.whatsapp.node.clear-cache") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                addLog('Cache WhatsApp berhasil dibersihkan', 'success');
                
                // Reset QR Code container
                $('#qr-code-container').html('<p class="text-muted">Klik tombol "Dapatkan QR Code" untuk menampilkan QR Code</p>');
                
                if (showConfirmation !== false) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: (response.message || 'Cache berhasil dibersihkan') + '. Silakan restart server dan scan QR Code ulang.'
                    });
                }
            } else {
                addLog('Gagal membersihkan cache: ' + (response.message || 'Unknown error'), 'error');
                if (showConfirmation !== false) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Gagal membersihkan cache'
                    });
                }
            }
        },
        error: function() {
            addLog('Gagal membersihkan cache: Server tidak dapat diakses', 'error');
            if (showConfirmation !== false) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Server tidak dapat diakses'
                });
            }
        }
    });
}

// Test message form
$('#test-message-form').on('submit', function(e) {
    e.preventDefault();
    
    const number = $('#test-number').val();
    const message = $('#test-message').val();
    
    if (!number || !message) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Nomor dan pesan harus diisi'
        });
        return;
    }
    
    addLog(`Mengirim test pesan ke ${number}...`, 'info');
    
    $.ajax({
        url: '{{ route("ilp.whatsapp.node.send-message") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            number: number,
            message: message
        },
        success: function(response) {
            if (response.success) {
                addLog(`Pesan berhasil dikirim ke ${number}`, 'success');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Pesan berhasil dikirim'
                });
                $('#test-message-form')[0].reset();
            } else {
                addLog(`Gagal mengirim pesan ke ${number}: ${response.message}`, 'error');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message
                });
            }
        },
        error: function() {
            addLog(`Gagal mengirim pesan ke ${number}: Server tidak dapat diakses`, 'error');
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Server Node.js tidak dapat diakses'
            });
        }
    });
});

// Terminal Manual Functions
function setCommand(command) {
    document.getElementById('terminal-command').value = command;
    addTerminalLine('Command set: ' + command, 'info');
    
    // Focus pada input untuk memungkinkan edit lebih lanjut
    document.getElementById('terminal-command').focus();
}

function copyCommand() {
    const command = document.getElementById('terminal-command');
    command.select();
    command.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        addTerminalLine('Command copied to clipboard: ' + command.value, 'success');
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Command berhasil di-copy ke clipboard',
            timer: 2000,
            showConfirmButton: false
        });
    } catch (err) {
        addTerminalLine('Failed to copy command', 'error');
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal copy command ke clipboard'
        });
    }
}

function runManualCommand() {
    const command = document.getElementById('terminal-command').value.trim();
    
    if (!command) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: 'Silakan masukkan perintah terminal terlebih dahulu'
        });
        return;
    }
    
    addTerminalLine('$ ' + command, 'info');
    addLog('Menjalankan perintah: ' + command, 'info');
    
    // Tampilkan loading state
    addTerminalLine('⏳ Executing command...', 'info');
    
    // Debug: Log URL yang akan dipanggil
    const executeUrl = '{{ route("ilp.whatsapp.node.execute-command") }}';
    console.log('Calling URL:', executeUrl);
    addTerminalLine('🔗 Calling: ' + executeUrl, 'info');
    
    // Kirim perintah ke backend untuk eksekusi real
    $.ajax({
        url: executeUrl,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            command: command,
            working_directory: '/Users/agusbudiyono/Documents/EDOKTER BENAR/edokter'
        },
        timeout: 120000, // 2 menit timeout untuk perintah yang membutuhkan waktu lama
        beforeSend: function(xhr) {
            addTerminalLine('📡 Sending request to server...', 'info');
            console.log('Request data:', {
                command: command,
                working_directory: '/Users/agusbudiyono/Documents/EDOKTER BENAR/edokter'
            });
        },
        success: function(response) {
            // Debug: Log response yang diterima
            console.log('AJAX Success Response:', response);
            addTerminalLine('📨 Response received from server', 'success');
            
            if (response.success) {
                addTerminalLine('✓ Command executed successfully', 'success');
                
                // Tampilkan output dari command
                if (response.output) {
                    addTerminalLine('📄 Output length: ' + response.output.length + ' characters', 'info');
                    const outputLines = response.output.split('\n');
                    outputLines.forEach(line => {
                        if (line.trim()) {
                            // Deteksi jenis output berdasarkan konten
                            let type = 'info';
                            if (line.includes('error') || line.includes('Error') || line.includes('ERROR')) {
                                type = 'error';
                            } else if (line.includes('warning') || line.includes('Warning') || line.includes('WARN')) {
                                type = 'warning';
                            } else if (line.includes('success') || line.includes('✓') || line.includes('listening') || line.includes('ready')) {
                                type = 'success';
                            }
                            addTerminalLine(line, type);
                        }
                    });
                }
                
                // Jika command adalah untuk start server Node.js, monitor QR code
                if (command.includes('node appJM.js') || command.includes('node public/wagateway/node_mrlee/appJM.js')) {
                    addTerminalLine('🔍 Monitoring for QR Code...', 'info');
                    
                    // Monitor QR code setiap 3 detik selama 30 detik
                    let qrCheckCount = 0;
                    const maxQrChecks = 10;
                    
                    const qrMonitor = setInterval(() => {
                        qrCheckCount++;
                        
                        // Cek QR code dari server
                        $.ajax({
                            url: '{{ route("ilp.whatsapp.node.qr") }}',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(qrResponse) {
                                if (qrResponse.success && qrResponse.data) {
                                    const qrData = qrResponse.data;
                                    
                                    if (qrData.qrBarCode && qrData.qrBarCode !== 'not ready') {
                                        if (qrData.qrBarCode === 'WA Gate is ready') {
                                            addTerminalLine('✅ WhatsApp sudah terhubung!', 'success');
                                            addTerminalLine('🎉 Server siap digunakan', 'success');
                                            clearInterval(qrMonitor);
                                            
                                            // Update QR code container
                                            $('#qr-code-container').html(`
                                                <div class="alert alert-success">
                                                    <i class="fas fa-check-circle fa-2x"></i><br>
                                                    <strong>WhatsApp Sudah Terhubung!</strong><br>
                                                    Server siap digunakan.
                                                </div>
                                            `);
                                        } else {
                                            addTerminalLine('📱 QR Code diterima! Panjang: ' + qrData.qrBarCode.length + ' karakter', 'success');
                                            addTerminalLine('🔗 Silakan scan QR Code di panel sebelah kiri', 'info');
                                            
                                            // Generate dan tampilkan QR Code
                                            const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrData.qrBarCode)}`;
                                            
                                            $('#qr-code-container').html(`
                                                <div>
                                                    <img src="${qrCodeUrl}" class="qr-code-image" alt="QR Code">
                                                    <p class="mt-2"><strong>Scan QR Code dengan WhatsApp Anda</strong></p>
                                                    <small class="text-muted">QR Code dari terminal manual</small>
                                                </div>
                                            `);
                                            
                                            clearInterval(qrMonitor);
                                        }
                                    }
                                }
                            },
                            error: function() {
                                // Tidak perlu log error untuk monitoring QR
                            }
                        });
                        
                        if (qrCheckCount >= maxQrChecks) {
                            addTerminalLine('⏰ QR Code monitoring timeout', 'warning');
                            addTerminalLine('💡 Coba refresh atau gunakan tombol "Dapatkan QR Code"', 'info');
                            clearInterval(qrMonitor);
                        }
                    }, 3000);
                    
                    // Refresh status server setelah 5 detik
                    setTimeout(checkServerStatus, 5000);
                }
                
                // Jika ada error dalam output
                if (response.error) {
                    addTerminalLine('⚠ Error: ' + response.error, 'error');
                }
                
                addLog('Perintah berhasil dieksekusi: ' + command, 'success');
            } else {
                addTerminalLine('✗ Command failed: ' + (response.message || 'Unknown error'), 'error');
                addLog('Gagal mengeksekusi perintah: ' + (response.message || 'Unknown error'), 'error');
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Command execution failed';
            
            // Debug: Log semua informasi error
            console.error('AJAX Error Details:', {
                status: status,
                error: error,
                responseText: xhr.responseText,
                responseJSON: xhr.responseJSON,
                statusCode: xhr.status
            });
            
            addTerminalLine('❌ AJAX Error - Status: ' + status + ', Code: ' + xhr.status, 'error');
            
            if (status === 'timeout') {
                errorMessage = 'Command timeout - perintah membutuhkan waktu terlalu lama';
                addTerminalLine('⏰ Command timeout', 'error');
                addTerminalLine('💡 Jika server Node.js sedang starting, tunggu beberapa saat lagi', 'info');
            } else if (xhr.status === 404) {
                errorMessage = 'Route tidak ditemukan (404) - Periksa konfigurasi route';
                addTerminalLine('🔍 Route Error: ' + executeUrl, 'error');
            } else if (xhr.status === 500) {
                errorMessage = 'Server Error (500) - Periksa log server';
                if (xhr.responseText) {
                    addTerminalLine('📄 Response: ' + xhr.responseText.substring(0, 200) + '...', 'error');
                }
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                errorMessage = 'Server response: ' + xhr.responseText.substring(0, 100);
            }
            
            addTerminalLine('✗ ' + errorMessage, 'error');
            addLog('Error eksekusi perintah: ' + errorMessage, 'error');
        }
    });
    
    // Auto copy command to clipboard
    try {
        navigator.clipboard.writeText(command).then(() => {
            addTerminalLine('📋 Command copied to clipboard', 'success');
        });
    } catch (err) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = command;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        addTerminalLine('📋 Command copied to clipboard', 'success');
    }
}

// Fungsi untuk restart server Node.js
function restartNodeServer() {
    Swal.fire({
        title: 'Restart Node.js Server',
        text: 'Apakah Anda yakin ingin restart server Node.js?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-redo"></i> Ya, Restart!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading
            Swal.fire({
                title: 'Restarting Server...',
                html: '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x mb-3"></i><br>Menghentikan server lama dan memulai yang baru...</div>',
                allowOutsideClick: false,
                showConfirmButton: false
            });

            // Kirim request restart ke backend
            fetch('/whatsapp/restart-node-server', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Server Berhasil Direstart!',
                        html: `
                            <div class="text-left">
                                <p><strong>Status:</strong> ${data.message}</p>
                                <p><strong>Port:</strong> ${data.port || '8100'}</p>
                                <p><strong>PID:</strong> ${data.pid || 'N/A'}</p>
                                <hr>
                                <small class="text-muted">Server Node.js telah berhasil direstart dan siap digunakan.</small>
                            </div>
                        `,
                        confirmButtonText: 'OK'
                    });
                    
                    // Refresh status setelah restart
                    setTimeout(checkServerStatus, 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Restart Server',
                        text: data.message || 'Terjadi kesalahan saat restart server',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan koneksi: ' + error.message,
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

function addTerminalLine(message, type = 'info') {
    const timestamp = new Date().toLocaleTimeString();
    let color = '#00ff00'; // default green
    let prefix = '$';
    
    switch(type) {
        case 'error':
            color = '#ff4444';
            prefix = '✗';
            break;
        case 'success':
            color = '#44ff44';
            prefix = '✓';
            break;
        case 'warning':
            color = '#ffaa00';
            prefix = '⚠';
            break;
        case 'info':
            color = '#44aaff';
            prefix = 'ℹ';
            break;
    }
    
    const terminalLine = `
        <div class="terminal-line" style="color: ${color}">
            <span style="color: #888">[${timestamp}]</span> ${prefix} ${message}
        </div>
    `;
    
    $('#terminal-output').append(terminalLine);
    $('#terminal-output').scrollTop($('#terminal-output')[0].scrollHeight);
}

function clearTerminal() {
    $('#terminal-output').html(`
        <div class="terminal-line">Terminal cleared...</div>
        <div class="terminal-line">Ready for new commands.</div>
        <div class="terminal-line">---</div>
    `);
    addLog('Terminal output cleared', 'info');
}

// Initialize dashboard
$(document).ready(function() {
    addLog('Dashboard WhatsApp Node.js Gateway dimuat', 'info');
    addTerminalLine('Terminal initialized and ready', 'success');
    checkServerStatus();
});

// Auto refresh server status every 10 seconds
setInterval(checkServerStatus, 10000);
</script>
@stop
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WaOutbox;
use Exception;

class WhatsAppNodeController extends Controller
{
    private $nodeApiUrl;
    
    public function __construct()
    {
        $this->nodeApiUrl = config('whatsapp.node_api_url', 'http://localhost:8100');
    }
    
    /**
     * Dashboard untuk mengelola WhatsApp Node.js Gateway
     */
    public function dashboard()
    {
        return view('whatsapp.node-dashboard');
    }
    
    /**
     * Mendapatkan QR Code untuk autentikasi WhatsApp
     */
    public function getQrCode()
    {
        try {
            $response = Http::timeout(10)->post($this->nodeApiUrl . '/WA-QrCode');
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan QR Code'
            ], 500);
            
        } catch (Exception $e) {
            Log::error('Error getting QR Code: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server Node.js tidak dapat diakses'
            ], 500);
        }
    }
    
    /**
     * Mendapatkan status server Node.js
     */
    public function getServerStatus()
    {
        try {
            // Coba endpoint /uptime terlebih dahulu
            $response = Http::timeout(5)->get($this->nodeApiUrl . '/uptime');
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'status' => 'online',
                    'data' => $data
                ]);
            }
            
            // Jika /uptime gagal, coba endpoint / sebagai fallback
            $response = Http::timeout(5)->get($this->nodeApiUrl . '/');
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'status' => 'online',
                    'data' => $data
                ]);
            }
            
            return response()->json([
                'success' => false,
                'status' => 'offline',
                'message' => 'Server tidak merespons'
            ]);
            
        } catch (Exception $e) {
            Log::error('Error checking Node.js server status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 'offline',
                'message' => 'Server tidak dapat diakses: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Mengirim pesan melalui Node.js API
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'number' => 'required|string',
            'message' => 'required|string'
        ]);
        
        try {
            $response = Http::timeout(30)->post($this->nodeApiUrl . '/send-message', [
                'number' => $request->number,
                'message' => $request->message
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Update status di wa_outbox jika ada nomor
                if ($request->has('outbox_id')) {
                    WaOutbox::where('nomor', $request->outbox_id)
                        ->update([
                            'status' => WaOutbox::STATUS_TERKIRIM,
                            'success' => '1',
                            'response' => json_encode($data)
                        ]);
                }
                
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }
            
            // Update status gagal di wa_outbox jika ada nomor
            if ($request->has('outbox_id')) {
                WaOutbox::where('nomor', $request->outbox_id)
                    ->update([
                        'status' => WaOutbox::STATUS_GAGAL,
                        'success' => '0',
                        'response' => 'API response error: ' . $response->body()
                    ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan',
                'error' => $response->json()
            ], 500);
            
        } catch (Exception $e) {
            Log::error('Error sending message via Node.js: ' . $e->getMessage());
            
            // Update status gagal di wa_outbox jika ada nomor
            if ($request->has('outbox_id')) {
                WaOutbox::where('nomor', $request->outbox_id)
                    ->update([
                        'status' => WaOutbox::STATUS_GAGAL,
                        'success' => '0',
                        'response' => 'Connection error: ' . $e->getMessage()
                    ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Server Node.js tidak dapat diakses'
            ], 500);
        }
    }
    
    /**
     * Mengirim file melalui Node.js API
     */
    public function sendFile(Request $request)
    {
        $request->validate([
            'number' => 'required|string',
            'fileurl' => 'required|url',
            'caption' => 'nullable|string'
        ]);
        
        try {
            $response = Http::timeout(60)->post($this->nodeApiUrl . '/send-fileurl', [
                'number' => $request->number,
                'fileurl' => $request->fileurl,
                'caption' => $request->caption ?? ''
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Update status di wa_outbox jika ada nomor
                if ($request->has('outbox_id')) {
                    WaOutbox::where('nomor', $request->outbox_id)
                        ->update([
                            'status' => WaOutbox::STATUS_TERKIRIM,
                            'success' => '1',
                            'response' => json_encode($data)
                        ]);
                }
                
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }
            
            // Update status gagal di wa_outbox jika ada nomor
            if ($request->has('outbox_id')) {
                WaOutbox::where('nomor', $request->outbox_id)
                    ->update([
                        'status' => WaOutbox::STATUS_GAGAL,
                        'success' => '0',
                        'response' => 'API response error: ' . $response->body()
                    ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim file',
                'error' => $response->json()
            ], 500);
            
        } catch (Exception $e) {
            Log::error('Error sending file via Node.js: ' . $e->getMessage());
            
            // Update status gagal di wa_outbox jika ada nomor
            if ($request->has('outbox_id')) {
                WaOutbox::where('nomor', $request->outbox_id)
                    ->update([
                        'status' => WaOutbox::STATUS_GAGAL,
                        'success' => '0',
                        'response' => 'Connection error: ' . $e->getMessage()
                    ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Server Node.js tidak dapat diakses'
            ], 500);
        }
    }
    
    /**
     * Memproses antrean pesan melalui Node.js
     */
    public function processQueueViaNode(Request $request)
    {
        $limit = $request->get('limit', 10);
        $status = $request->get('status', WaOutbox::STATUS_ANTRIAN);
        
        $messages = WaOutbox::where('status', $status)
            ->orderBy('tanggal_jam', 'asc')
            ->limit($limit)
            ->get();
            
        $processed = 0;
        $failed = 0;
        
        foreach ($messages as $message) {
            // Update status menjadi processing
            $message->update(['status' => WaOutbox::STATUS_PROSES]);
            
            try {
                if ($message->type === WaOutbox::TYPE_TEXT) {
                    $response = Http::timeout(30)->post($this->nodeApiUrl . '/send-message', [
                        'number' => $message->nowa,
                        'message' => $message->pesan
                    ]);
                } elseif (in_array($message->type, [WaOutbox::TYPE_DOCUMENT, WaOutbox::TYPE_IMAGE, WaOutbox::TYPE_VIDEO]) && $message->file) {
                    $response = Http::timeout(60)->post($this->nodeApiUrl . '/send-fileurl', [
                        'number' => $message->nowa,
                        'fileurl' => $message->file,
                        'caption' => $message->pesan ?? ''
                    ]);
                } else {
                    throw new Exception('Tipe pesan tidak didukung');
                }
                
                if ($response->successful()) {
                    $message->update([
                        'status' => WaOutbox::STATUS_TERKIRIM,
                        'success' => '1',
                        'response' => json_encode($response->json())
                    ]);
                    $processed++;
                } else {
                    $message->update([
                        'status' => WaOutbox::STATUS_GAGAL,
                        'success' => '0',
                        'response' => 'API response error: ' . $response->body()
                    ]);
                    $failed++;
                }
                
            } catch (Exception $e) {
                $message->update([
                    'status' => WaOutbox::STATUS_GAGAL,
                    'success' => '0',
                    'response' => $e->getMessage()
                ]);
                $failed++;
            }
            
            // Delay antar pesan untuk menghindari spam
            sleep(1);
        }
        
        return response()->json([
            'success' => true,
            'message' => "Berhasil memproses {$processed} pesan, {$failed} gagal",
            'processed' => $processed,
            'failed' => $failed
        ]);
    }
    
    /**
     * Menghentikan server Node.js
     */
    public function stopServer()
    {
        try {
            // Coba hentikan melalui API terlebih dahulu
            try {
                $response = Http::timeout(5)->post($this->nodeApiUrl . '/StopWAG');
            } catch (Exception $e) {
                // Jika API tidak dapat diakses, lanjutkan ke kill process
            }
            
            // Kill semua proses Node.js yang menjalankan appJM.js
            $killCommand = "pkill -f 'node.*appJM.js' 2>/dev/null || true";
            exec($killCommand);
            
            // Tunggu sebentar untuk memastikan proses terhenti
            sleep(1);
            
            // Verifikasi apakah proses sudah terhenti
            $checkCommand = "pgrep -f 'node.*appJM.js' 2>/dev/null || echo 'no_process'";
            $output = [];
            exec($checkCommand, $output);
            
            if (empty($output) || (count($output) === 1 && $output[0] === 'no_process')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Server Node.js berhasil dihentikan'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Proses Node.js masih berjalan, coba lagi'
                ], 500);
            }
            
        } catch (Exception $e) {
            Log::error('Error stopping Node.js server: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghentikan server: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menjalankan server Node.js
     */
    public function startServer()
    {
        try {
            // Path ke direktori Node.js
            $nodePath = public_path('wagateway/node_mrlee');
            
            // Periksa apakah direktori dan file appJM.js ada
            if (!is_dir($nodePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Direktori Node.js tidak ditemukan: ' . $nodePath
                ], 404);
            }
            
            $appFile = $nodePath . '/appJM.js';
            if (!file_exists($appFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File appJM.js tidak ditemukan: ' . $appFile
                ], 404);
            }
            
            // Cek apakah ada proses Node.js yang sudah berjalan
            $checkCommand = "pgrep -f 'node.*appJM.js' 2>/dev/null || echo 'no_process'";
            $output = [];
            exec($checkCommand, $output);
            
            if (!empty($output) && $output[0] !== 'no_process') {
                // Ada proses yang sudah berjalan, hentikan dulu
                $killCommand = "pkill -f 'node.*appJM.js' 2>/dev/null || true";
                exec($killCommand);
                sleep(2); // Tunggu proses terhenti
            }
            
            // Jalankan perintah Node.js di background
            $command = "cd " . escapeshellarg($nodePath) . " && nohup node appJM.js > /dev/null 2>&1 & echo $!";
            
            // Eksekusi perintah dan dapatkan PID
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);
            
            // Tunggu sebentar untuk memastikan server mulai
            sleep(3);
            
            // Verifikasi apakah proses berjalan
            $checkCommand = "pgrep -f 'node.*appJM.js' 2>/dev/null || echo 'no_process'";
            $processOutput = [];
            exec($checkCommand, $processOutput);
            
            if (empty($processOutput) || $processOutput[0] === 'no_process') {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menjalankan server Node.js. Proses tidak dapat dimulai.'
                ], 500);
            }
            
            // Cek apakah server berhasil berjalan dengan mencoba mengakses status
            $maxRetries = 5;
            $retryCount = 0;
            
            while ($retryCount < $maxRetries) {
                try {
                    $response = Http::timeout(5)->get($this->nodeApiUrl . '/uptime');
                    
                    if ($response->successful()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Server Node.js berhasil dijalankan dan sedang berjalan',
                            'status' => 'running',
                            'pid' => $processOutput[0] ?? 'unknown'
                        ]);
                    }
                } catch (Exception $e) {
                    // Server mungkin masih starting up
                }
                
                $retryCount++;
                sleep(2);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Server Node.js telah dijalankan tetapi belum sepenuhnya siap. Silakan tunggu beberapa saat dan periksa status server.',
                'status' => 'starting',
                'pid' => $processOutput[0] ?? 'unknown',
                'note' => 'Server mungkin membutuhkan waktu lebih lama untuk fully startup'
            ]);
            
        } catch (Exception $e) {
            Log::error('Error starting Node.js server: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menjalankan server Node.js: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Quick start server Node.js (enhanced version)
     */
    public function quickStartServer()
    {
        try {
            // Path ke direktori Node.js
            $nodePath = public_path('wagateway/node_mrlee');
            
            // Periksa apakah direktori dan file appJM.js ada
            if (!is_dir($nodePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Direktori Node.js tidak ditemukan: ' . $nodePath
                ], 404);
            }
            
            $appFile = $nodePath . '/appJM.js';
            if (!file_exists($appFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File appJM.js tidak ditemukan: ' . $appFile
                ], 404);
            }
            
            // Periksa apakah Node.js terinstall
            $nodeCheck = [];
            exec('which node 2>/dev/null', $nodeCheck);
            if (empty($nodeCheck)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Node.js tidak terinstall atau tidak ditemukan di PATH'
                ], 500);
            }
            
            // Periksa apakah npm dependencies sudah terinstall
            $packageFile = $nodePath . '/package.json';
            $nodeModules = $nodePath . '/node_modules';
            if (file_exists($packageFile) && !is_dir($nodeModules)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dependencies Node.js belum terinstall. Jalankan: cd ' . $nodePath . ' && npm install'
                ], 500);
            }
            
            // Cek apakah port 8100 sudah digunakan
            $portCheck = [];
            exec('lsof -i :8100 2>/dev/null', $portCheck);
            if (!empty($portCheck)) {
                // Port sudah digunakan, coba hentikan proses yang menggunakan port tersebut
                $killPortScript = $nodePath . '/kill-port-8100.sh';
                if (file_exists($killPortScript)) {
                    exec('chmod +x ' . escapeshellarg($killPortScript));
                    exec($killPortScript . ' 2>/dev/null');
                    sleep(2);
                } else {
                    // Fallback manual kill
                    exec('pkill -f "node.*appJM.js" 2>/dev/null || true');
                    exec('lsof -ti:8100 | xargs kill -9 2>/dev/null || true');
                    sleep(2);
                }
            }
            
            // Verifikasi port sudah bebas
            $portCheck = [];
            exec('lsof -i :8100 2>/dev/null', $portCheck);
            if (!empty($portCheck)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Port 8100 masih digunakan oleh proses lain. Silakan hentikan proses tersebut terlebih dahulu.'
                ], 500);
            }
            
            // Buat direktori log jika belum ada
            $logDir = $nodePath . '/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            // Gunakan script startup yang telah diperbaiki
            $startScript = $nodePath . '/start-server.sh';
            
            if (file_exists($startScript)) {
                // Pastikan script dapat dieksekusi
                exec('chmod +x ' . escapeshellarg($startScript));
                
                // Jalankan menggunakan script startup
                $command = 'cd ' . escapeshellarg($nodePath) . ' && ./start-server.sh --background';
            } else {
                // Fallback ke metode manual jika script tidak ada
                $envVars = [
                    'NODE_ENV=production',
                    'PORT=8100'
                ];
                
                $command = 'cd ' . escapeshellarg($nodePath) . ' && ' . 
                          implode(' ', $envVars) . ' nohup node appJM.js > server.log 2>&1 & echo $!';
            }
            
            // Eksekusi perintah dan dapatkan PID
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);
            
            if ($returnVar !== 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menjalankan perintah Node.js. Return code: ' . $returnVar
                ], 500);
            }
            
            // Parse PID dari output
            $pid = null;
            if (file_exists($startScript)) {
                // Jika menggunakan script startup, baca PID dari file
                $pidFile = $nodePath . '/server.pid';
                if (file_exists($pidFile)) {
                    $pid = trim(file_get_contents($pidFile));
                }
            } else {
                // Jika menggunakan metode manual, ambil dari output
                $pid = isset($output[0]) ? trim($output[0]) : null;
            }
            
            // Tunggu sebentar untuk memastikan server mulai
            sleep(5);
            
            // Verifikasi apakah proses masih berjalan
            if ($pid && is_numeric($pid)) {
                $processCheck = [];
                exec('ps -p ' . escapeshellarg($pid) . ' 2>/dev/null', $processCheck);
                if (count($processCheck) < 2) { // Header + process line
                    // Proses sudah mati, cek log untuk error
                    $logFile = $nodePath . '/server.log';
                    $errorLog = '';
                    if (file_exists($logFile)) {
                        $errorLog = file_get_contents($logFile);
                    }
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Server Node.js gagal dimulai. Proses keluar setelah dijalankan.',
                        'error_log' => $errorLog,
                        'pid_attempted' => $pid
                    ], 500);
                }
            } else {
                // Jika tidak ada PID, coba cari proses dengan nama
                $processCheck = [];
                exec('pgrep -f "node.*appJM.js" 2>/dev/null', $processCheck);
                if (!empty($processCheck)) {
                    $pid = $processCheck[0];
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Server Node.js tidak dapat dimulai. Tidak ada proses yang ditemukan.',
                        'debug_output' => $output
                    ], 500);
                }
            }
            
            // Cek apakah server berhasil berjalan dengan mencoba mengakses status
            $maxRetries = 10;
            $retryCount = 0;
            $lastError = '';
            
            while ($retryCount < $maxRetries) {
                try {
                    $response = Http::timeout(3)->get($this->nodeApiUrl . '/uptime');
                    
                    if ($response->successful()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Server Node.js berhasil dijalankan dan sedang berjalan',
                            'status' => 'running',
                            'pid' => $pid,
                            'port' => '8100',
                            'url' => $this->nodeApiUrl,
                            'uptime' => $response->json()['message'] ?? 'Unknown'
                        ]);
                    }
                } catch (Exception $e) {
                    $lastError = $e->getMessage();
                    // Server mungkin masih starting up
                }
                
                $retryCount++;
                sleep(2);
            }
            
            // Jika sampai sini, server mungkin berjalan tapi belum siap
            // Cek sekali lagi apakah proses masih hidup
            if ($pid) {
                $processCheck = [];
                exec('ps -p ' . escapeshellarg($pid) . ' 2>/dev/null', $processCheck);
                if (count($processCheck) >= 2) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Server Node.js telah dijalankan tetapi belum sepenuhnya siap. Silakan tunggu beberapa saat dan periksa status server.',
                        'status' => 'starting',
                        'pid' => $pid,
                        'port' => '8100',
                        'note' => 'Server mungkin membutuhkan waktu lebih lama untuk fully startup',
                        'last_error' => $lastError
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Server Node.js tidak dapat diakses setelah startup. Error: ' . $lastError
            ], 500);
            
        } catch (Exception $e) {
            Log::error('Error quick starting Node.js server: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menjalankan server Node.js: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Membersihkan cache dan session WhatsApp
     */
    public function clearCache()
    {
        try {
            $nodePath = public_path('wagateway/node_mrlee');
            
            // Path ke direktori cache WhatsApp
            $cacheDirectories = [
                $nodePath . '/.wwebjs_auth',
                $nodePath . '/.wwebjs_cache'
            ];
            
            $clearedDirs = [];
            
            foreach ($cacheDirectories as $cacheDir) {
                if (is_dir($cacheDir)) {
                    // Hapus isi direktori cache
                    $command = "rm -rf " . escapeshellarg($cacheDir) . "/*";
                    exec($command);
                    $clearedDirs[] = basename($cacheDir);
                }
            }
            
            if (!empty($clearedDirs)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cache WhatsApp berhasil dibersihkan: ' . implode(', ', $clearedDirs)
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada cache yang perlu dibersihkan'
                ]);
            }
            
        } catch (Exception $e) {
            Log::error('Error clearing WhatsApp cache: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membersihkan cache: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan log server Node.js
     */
    public function getLogs(Request $request)
    {
        try {
            $nodePath = public_path('wagateway/node_mrlee');
            $logFile = $nodePath . '/server.log';
            
            // Periksa apakah file log ada
            if (!file_exists($logFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File log tidak ditemukan',
                    'logs' => 'Log file belum tersedia. Server mungkin belum pernah dijalankan.'
                ]);
            }
            
            // Baca log dengan batasan baris (default 100 baris terakhir)
            $lines = $request->input('lines', 100);
            $command = "tail -n {$lines} " . escapeshellarg($logFile);
            
            $output = [];
            exec($command, $output);
            
            $logs = implode("\n", $output);
            
            return response()->json([
                'success' => true,
                'message' => 'Log berhasil diambil',
                'logs' => $logs ?: 'Log kosong atau belum ada aktivitas',
                'file_path' => $logFile,
                'lines_requested' => $lines
            ]);
            
        } catch (Exception $e) {
            Log::error('Error getting Node.js logs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil log: ' . $e->getMessage(),
                'logs' => ''
            ], 500);
        }
    }
    
    /**
     * Eksekusi perintah terminal untuk WhatsApp Node.js Gateway
     */
    public function executeCommand(Request $request)
    {
        // Debug: Log request yang masuk
        Log::info('executeCommand called', [
            'command' => $request->input('command'),
            'working_directory' => $request->input('working_directory'),
            'all_input' => $request->all()
        ]);
        
        $request->validate([
            'command' => 'required|string'
        ]);
        
        $command = trim($request->input('command'));
        
        // Debug: Log command yang akan dieksekusi
        Log::info('Executing command: ' . $command);
        
        try {
            // Pastikan kita berada di direktori edokter
            $edokterPath = base_path();
            $nodePath = public_path('wagateway/node_mrlee');
            
            // Validasi perintah yang diizinkan untuk keamanan
            $allowedCommands = [
                'cd public/wagateway/node_mrlee && node appJM.js',
                'node appJM.js',
                'npm install',
                'npm start',
                'ls',
                'pwd',
                'ps aux | grep node',
                'lsof -i :8100',
                'kill',
                'pkill -f node',
                './start-server.sh',
                './check-status.sh'
            ];
            
            // Cek apakah perintah diizinkan (partial match untuk fleksibilitas)
            $isAllowed = false;
            foreach ($allowedCommands as $allowedCmd) {
                if (strpos($command, $allowedCmd) !== false || strpos($allowedCmd, $command) !== false) {
                    $isAllowed = true;
                    break;
                }
            }
            
            if (!$isAllowed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perintah tidak diizinkan untuk keamanan sistem',
                    'output' => 'Error: Command not allowed'
                ], 403);
            }
            
            // Handle perintah khusus untuk Node.js
            if (strpos($command, 'cd public/wagateway/node_mrlee && node appJM.js') !== false || 
                ($command === 'node appJM.js')) {
                
                // Hentikan proses Node.js yang sudah berjalan
                exec('pkill -f "node.*appJM.js" 2>/dev/null || true');
                sleep(1);
                
                // Pastikan direktori Node.js ada
                if (!is_dir($nodePath)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Direktori Node.js tidak ditemukan',
                        'output' => 'Error: Directory ' . $nodePath . ' not found'
                    ], 404);
                }
                
                // Pastikan file appJM.js ada
                $appFile = $nodePath . '/appJM.js';
                if (!file_exists($appFile)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File appJM.js tidak ditemukan',
                        'output' => 'Error: File ' . $appFile . ' not found'
                    ], 404);
                }
                
                // Jalankan Node.js dengan output real-time
                $logFile = $nodePath . '/server.log';
                $pidFile = $nodePath . '/server.pid';
                
                // Bersihkan log lama
                if (file_exists($logFile)) {
                    file_put_contents($logFile, '');
                }
                
                // Jalankan Node.js di background dengan logging
                $fullCommand = 'cd ' . escapeshellarg($nodePath) . ' && nohup node appJM.js > server.log 2>&1 & echo $! > server.pid';
                exec($fullCommand);
                
                // Tunggu sebentar untuk startup
                sleep(2);
                
                // Baca PID
                $pid = null;
                if (file_exists($pidFile)) {
                    $pid = trim(file_get_contents($pidFile));
                }
                
                // Baca output awal dari log
                $initialOutput = '';
                if (file_exists($logFile)) {
                    $initialOutput = file_get_contents($logFile);
                }
                
                // Cek apakah proses berjalan
                $isRunning = false;
                if ($pid && is_numeric($pid)) {
                    $processCheck = [];
                    exec('ps -p ' . escapeshellarg($pid) . ' 2>/dev/null', $processCheck);
                    $isRunning = count($processCheck) >= 2;
                }
                
                $response = [
                    'success' => true,
                    'message' => 'Node.js server dimulai',
                    'output' => $initialOutput ?: 'Starting WhatsApp Gateway...\nWAG listening on port 8100',
                    'pid' => $pid,
                    'is_running' => $isRunning,
                    'log_file' => $logFile,
                    'working_directory' => $nodePath
                ];
                
                // Debug: Log response yang akan dikirim
                Log::info('executeCommand response for Node.js start:', $response);
                
                return response()->json($response);
            }
            
            // Handle perintah lainnya
            $output = [];
            $returnVar = 0;
            
            // Set working directory ke edokter
            $fullCommand = 'cd ' . escapeshellarg($edokterPath) . ' && ' . $command . ' 2>&1';
            exec($fullCommand, $output, $returnVar);
            
            $outputText = implode("\n", $output);
            
            $response = [
                'success' => $returnVar === 0,
                'message' => $returnVar === 0 ? 'Perintah berhasil dieksekusi' : 'Perintah selesai dengan error',
                'output' => $outputText ?: 'No output',
                'return_code' => $returnVar,
                'working_directory' => $edokterPath
            ];
            
            // Debug: Log response untuk perintah umum
            Log::info('executeCommand response for general command:', $response);
            
            return response()->json($response);
            
        } catch (Exception $e) {
            Log::error('Error executing terminal command: ' . $e->getMessage(), [
                'command' => $command,
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorResponse = [
                'success' => false,
                'message' => 'Gagal mengeksekusi perintah: ' . $e->getMessage(),
                'output' => 'Error: ' . $e->getMessage()
            ];
            
            // Debug: Log error response
            Log::error('executeCommand error response:', $errorResponse);
            
            return response()->json($errorResponse, 500);
        }
    }
    
    /**
     * Mendapatkan output real-time dari log server Node.js
     */
    public function getRealtimeOutput(Request $request)
    {
        try {
            $nodePath = public_path('wagateway/node_mrlee');
            $logFile = $nodePath . '/server.log';
            $pidFile = $nodePath . '/server.pid';
            
            $response = [
                'success' => true,
                'output' => '',
                'is_running' => false,
                'pid' => null,
                'has_qr' => false,
                'qr_code' => null
            ];
            
            // Cek apakah ada PID file
            if (file_exists($pidFile)) {
                $pid = trim(file_get_contents($pidFile));
                $response['pid'] = $pid;
                
                // Cek apakah proses masih berjalan
                if ($pid && is_numeric($pid)) {
                    $processCheck = [];
                    exec('ps -p ' . escapeshellarg($pid) . ' 2>/dev/null', $processCheck);
                    $response['is_running'] = count($processCheck) >= 2;
                }
            }
            
            // Baca output dari log file
            if (file_exists($logFile)) {
                $lastPosition = $request->input('last_position', 0);
                $fileSize = filesize($logFile);
                
                if ($fileSize > $lastPosition) {
                    $handle = fopen($logFile, 'r');
                    fseek($handle, $lastPosition);
                    $newContent = fread($handle, $fileSize - $lastPosition);
                    fclose($handle);
                    
                    $response['output'] = $newContent;
                    $response['new_position'] = $fileSize;
                    
                    // Cek apakah ada QR code dalam output
                    if (preg_match('/QR RECEIVED->\s*([A-Za-z0-9+\/=]+)/', $newContent, $matches)) {
                        $response['has_qr'] = true;
                        $response['qr_code'] = $matches[1];
                    }
                } else {
                    $response['new_position'] = $lastPosition;
                }
            }
            
            return response()->json($response);
            
        } catch (Exception $e) {
            Log::error('Error getting realtime output: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan output real-time: ' . $e->getMessage(),
                'output' => '',
                'is_running' => false
            ], 500);
        }
    }
}
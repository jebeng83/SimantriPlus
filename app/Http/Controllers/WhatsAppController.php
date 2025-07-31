<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsAppGatewayService;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Exception;

class WhatsAppController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppGatewayService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Kirim pesan WhatsApp (teks atau dokumen)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'type' => 'required|in:text,pdf,document',
            'message' => 'nullable|string',
            'id' => 'nullable|numeric',
            'template' => 'nullable|string',
            'session_id' => 'nullable|string'
        ]);

        $phone = $request->phone;
        $type = $request->type;
        $sessionId = $request->session_id ?? config('whatsapp.default_session');

        try {
            switch ($type) {
                case 'text':
                    return $this->sendTextMessage($request, $sessionId);
                case 'pdf':
                    return $this->sendPdfDocument($request, $sessionId);
                case 'document':
                    return $this->sendDocument($request, $sessionId);
                default:
                    return response()->json([
                        'success' => false,
                        'error' => 'Tipe pesan tidak valid'
                    ], 400);
            }
        } catch (Exception $e) {
            Log::error('WhatsApp send message error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kirim pesan teks WhatsApp
     *
     * @param Request $request
     * @param string $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    private function sendTextMessage(Request $request, $sessionId)
    {
        $phone = $request->phone;
        $message = $request->message;
        $template = $request->template;
        $id = $request->id;

        // Jika menggunakan template
        if ($template && $id) {
            $message = $this->generateTemplateMessage($template, $id);
            if (!$message) {
                return response()->json([
                    'success' => false,
                    'error' => 'Gagal membuat pesan dari template'
                ], 400);
            }
        }

        // Jika tidak ada pesan dan tidak ada template, buat ringkasan otomatis
        if (!$message && $id) {
            $message = $this->generateSummaryMessage($id);
            if (!$message) {
                return response()->json([
                    'success' => false,
                    'error' => 'Gagal membuat ringkasan pesan'
                ], 400);
            }
        }

        if (!$message) {
            return response()->json([
                'success' => false,
                'error' => 'Pesan tidak boleh kosong'
            ], 400);
        }

        $result = $this->whatsappService->sendTextMessage($phone, $message, $sessionId);
        
        return response()->json($result);
    }

    /**
     * Kirim dokumen PDF WhatsApp
     *
     * @param Request $request
     * @param string $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    private function sendPdfDocument(Request $request, $sessionId)
    {
        $phone = $request->phone;
        $id = $request->id;

        if (!$id) {
            return response()->json([
                'success' => false,
                'error' => 'ID pemeriksaan diperlukan untuk mengirim PDF'
            ], 400);
        }

        // Generate PDF
        $pdfResult = $this->generatePdf($id);
        if (!$pdfResult['success']) {
            return response()->json($pdfResult);
        }

        $filePath = $pdfResult['file_path'];
        $caption = $pdfResult['caption'];

        // Kirim dokumen
        $result = $this->whatsappService->sendDocument($phone, $filePath, $caption, $sessionId);
        
        // Hapus file temporary jika berhasil dikirim
        if ($result['success'] && isset($pdfResult['is_temporary']) && $pdfResult['is_temporary']) {
            Storage::delete($filePath);
        }

        return response()->json($result);
    }

    /**
     * Kirim dokumen umum WhatsApp
     *
     * @param Request $request
     * @param string $sessionId
     * @return \Illuminate\Http\JsonResponse
     */
    private function sendDocument(Request $request, $sessionId)
    {
        $request->validate([
            'file_path' => 'required|string',
            'caption' => 'nullable|string'
        ]);

        $phone = $request->phone;
        $filePath = $request->file_path;
        $caption = $request->caption ?? '';

        $result = $this->whatsappService->sendDocument($phone, $filePath, $caption, $sessionId);
        
        return response()->json($result);
    }

    /**
     * Generate PDF hasil pemeriksaan
     *
     * @param int $id
     * @return array
     */
    private function generatePdf($id)
    {
        try {
            // Ambil data pemeriksaan
            $pemeriksaan = DB::table('ilp_dewasa')
                ->where('id', $id)
                ->first();

            if (!$pemeriksaan) {
                return [
                    'success' => false,
                    'error' => 'Data pemeriksaan tidak ditemukan'
                ];
            }

            // Ambil data pasien
            $pasien = DB::table('pasien')
                ->where('no_rkm_medis', $pemeriksaan->no_rkm_medis)
                ->first();

            if (!$pasien) {
                return [
                    'success' => false,
                    'error' => 'Data pasien tidak ditemukan'
                ];
            }

            // Format tanggal pemeriksaan
            $tanggal = date('d-m-Y', strtotime($pemeriksaan->tanggal_pemeriksaan ?? now()));

            // Siapkan data untuk view PDF
            $data = [
                'pemeriksaan' => $pemeriksaan,
                'pasien' => $pasien,
                'tanggal' => $tanggal
            ];

            // Generate PDF
            $pdf = PDF::loadView('ilp.pdf.hasil_pemeriksaan', $data);
            
            // Simpan PDF ke storage temporary
            $filename = 'whatsapp/temp/hasil_pemeriksaan_' . $pemeriksaan->no_rkm_medis . '_' . date('YmdHis') . '.pdf';
            Storage::put($filename, $pdf->output());

            // Buat caption
            $caption = "Hasil Pemeriksaan ILP\n";
            $caption .= "Nama: {$pasien->nm_pasien}\n";
            $caption .= "No. RM: {$pasien->no_rkm_medis}\n";
            $caption .= "Tanggal: {$tanggal}\n\n";
            $caption .= "Terima kasih telah menggunakan layanan kami.";

            return [
                'success' => true,
                'file_path' => $filename,
                'caption' => $caption,
                'is_temporary' => true
            ];

        } catch (Exception $e) {
            Log::error('Error generating PDF for WhatsApp', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Gagal membuat PDF: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate pesan dari template
     *
     * @param string $templateName
     * @param int $id
     * @return string|null
     */
    private function generateTemplateMessage($templateName, $id)
    {
        try {
            $templates = config('whatsapp.templates');
            
            if (!isset($templates[$templateName])) {
                return null;
            }

            $template = $templates[$templateName]['message'];
            
            // Ambil data pemeriksaan dan pasien
            $pemeriksaan = DB::table('ilp_dewasa')
                ->where('id', $id)
                ->first();

            if (!$pemeriksaan) {
                return null;
            }

            $pasien = DB::table('pasien')
                ->where('no_rkm_medis', $pemeriksaan->no_rkm_medis)
                ->first();

            if (!$pasien) {
                return null;
            }

            // Replace placeholder dengan data aktual
            $replacements = [
                '{nama_pasien}' => $pasien->nm_pasien,
                '{tanggal_pemeriksaan}' => date('d-m-Y', strtotime($pemeriksaan->tanggal_pemeriksaan ?? now())),
                '{nama_instansi}' => config('app.name', 'Edokter'),
                '{ringkasan_hasil}' => $this->generateSummaryText($pemeriksaan)
            ];

            return str_replace(array_keys($replacements), array_values($replacements), $template);

        } catch (Exception $e) {
            Log::error('Error generating template message', [
                'template' => $templateName,
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Generate ringkasan pesan otomatis
     *
     * @param int $id
     * @return string|null
     */
    private function generateSummaryMessage($id)
    {
        try {
            // Ambil data pemeriksaan
            $pemeriksaan = DB::table('ilp_dewasa')
                ->where('id', $id)
                ->first();

            if (!$pemeriksaan) {
                return null;
            }

            // Ambil data pasien
            $pasien = DB::table('pasien')
                ->where('no_rkm_medis', $pemeriksaan->no_rkm_medis)
                ->first();

            if (!$pasien) {
                return null;
            }

            $summary = "*HASIL PEMERIKSAAN ILP*\n\n";
            $summary .= "Nama: {$pasien->nm_pasien}\n";
            $summary .= "No. RM: {$pasien->no_rkm_medis}\n";
            $summary .= "Tanggal: " . date('d-m-Y', strtotime($pemeriksaan->tanggal_pemeriksaan ?? now())) . "\n\n";
            
            $summary .= $this->generateSummaryText($pemeriksaan);
            
            $summary .= "\n\nTerima kasih telah menggunakan layanan kami.\n\n";
            $summary .= "*" . config('app.name', 'Edokter') . "*";

            return $summary;

        } catch (Exception $e) {
            Log::error('Error generating summary message', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Generate ringkasan teks hasil pemeriksaan
     *
     * @param object $pemeriksaan
     * @return string
     */
    private function generateSummaryText($pemeriksaan)
    {
        $summary = "HASIL PEMERIKSAAN:\n";
        
        // Vital Signs
        if (isset($pemeriksaan->td) && !empty($pemeriksaan->td)) {
            $summary .= "• Tekanan Darah: {$pemeriksaan->td} mmHg\n";
        }
        if (isset($pemeriksaan->nadi) && !empty($pemeriksaan->nadi)) {
            $summary .= "• Nadi: {$pemeriksaan->nadi} x/menit\n";
        }
        if (isset($pemeriksaan->rr) && !empty($pemeriksaan->rr)) {
            $summary .= "• Respirasi: {$pemeriksaan->rr} x/menit\n";
        }
        if (isset($pemeriksaan->suhu) && !empty($pemeriksaan->suhu)) {
            $summary .= "• Suhu: {$pemeriksaan->suhu} °C\n";
        }
        if (isset($pemeriksaan->bb) && !empty($pemeriksaan->bb)) {
            $summary .= "• Berat Badan: {$pemeriksaan->bb} kg\n";
        }
        if (isset($pemeriksaan->tb) && !empty($pemeriksaan->tb)) {
            $summary .= "• Tinggi Badan: {$pemeriksaan->tb} cm\n";
        }
        if (isset($pemeriksaan->imt) && !empty($pemeriksaan->imt)) {
            $summary .= "• IMT: {$pemeriksaan->imt}\n";
        }

        // Kesimpulan
        if (isset($pemeriksaan->skilas) && !empty($pemeriksaan->skilas)) {
            $summary .= "\nKESIMPULAN:\n" . $pemeriksaan->skilas . "\n";
        }

        return $summary;
    }

    /**
     * Cek status session WhatsApp
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSessionStatus(Request $request)
    {
        $sessionId = $request->session_id ?? config('whatsapp.default_session');
        
        $result = $this->whatsappService->getSessionStatus($sessionId);
        
        return response()->json($result);
    }

    /**
     * Proses antrian pesan WhatsApp
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processQueue(Request $request)
    {
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        $limit = $request->limit ?? 10;
        $result = $this->whatsappService->processQueue($limit);
        
        return response()->json($result);
    }

    /**
     * Dapatkan statistik antrian WhatsApp
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQueueStats(Request $request)
    {
        $result = $this->whatsappService->getQueueStats();
        return response()->json($result);
    }

    /**
     * Dapatkan daftar pesan dalam antrian
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQueueList(Request $request)
    {
        try {
            $request->validate([
                'status' => 'nullable|in:ANTRIAN,TERKIRIM,GAGAL,PROSES',
                'limit' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1'
            ]);

            $query = \App\Models\WaOutbox::query();

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $limit = $request->limit ?? 20;
            $messages = $query->orderBy('tanggal_jam', 'desc')
                             ->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => $messages
            ]);
        } catch (Exception $e) {
            Log::error('Error getting queue list', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Gagal mendapatkan daftar antrian: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus pesan dari antrian
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFromQueue(Request $request, $id)
    {
        try {
            $message = \App\Models\WaOutbox::find($id);
            
            if (!$message) {
                return response()->json([
                    'success' => false,
                    'error' => 'Pesan tidak ditemukan'
                ], 404);
            }

            $message->delete();

            Log::info('Message deleted from queue', [
                'nomor' => $id,
                'nowa' => $message->nowa
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dihapus dari antrian'
            ]);
        } catch (Exception $e) {
            Log::error('Error deleting message from queue', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus pesan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retry pesan yang gagal
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function retryMessage(Request $request, $id)
    {
        try {
            $message = \App\Models\WaOutbox::find($id);
            
            if (!$message) {
                return response()->json([
                    'success' => false,
                    'error' => 'Pesan tidak ditemukan'
                ], 404);
            }

            // Reset status ke antrian
            $message->update([
                'status' => \App\Models\WaOutbox::STATUS_ANTRIAN,
                'success' => null,
                'response' => null
            ]);

            Log::info('Message reset for retry', [
                'nomor' => $id,
                'nowa' => $message->nowa
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikembalikan ke antrian'
            ]);
        } catch (Exception $e) {
            Log::error('Error retrying message', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengulang pesan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dapatkan QR Code untuk koneksi WhatsApp
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQRCode(Request $request)
    {
        $sessionId = $request->session_id ?? config('whatsapp.default_session');
        
        $result = $this->whatsappService->getQRCode($sessionId);
        
        return response()->json($result);
    }

    /**
     * Buat session WhatsApp baru
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createSession(Request $request)
    {
        $sessionId = $request->session_id ?? config('whatsapp.default_session');
        
        $result = $this->whatsappService->createSession($sessionId);
        
        return response()->json($result);
    }

    /**
     * Hapus session WhatsApp
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSession(Request $request)
    {
        $sessionId = $request->session_id ?? config('whatsapp.default_session');
        
        $result = $this->whatsappService->deleteSession($sessionId);
        
        return response()->json($result);
    }

    /**
     * Webhook untuk menerima pesan masuk dari WhatsApp
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request)
    {
        try {
            // Verifikasi webhook jika diaktifkan
            if (config('whatsapp.settings.webhook_enabled')) {
                $signature = $request->header('X-Webhook-Signature');
                $secret = config('whatsapp.settings.webhook_secret');
                
                if ($signature && $secret) {
                    $expectedSignature = hash_hmac('sha256', $request->getContent(), $secret);
                    if (!hash_equals($signature, $expectedSignature)) {
                        return response()->json(['error' => 'Invalid signature'], 401);
                    }
                }
            }

            // Log pesan masuk
            Log::info('WhatsApp webhook received', $request->all());

            // Proses pesan masuk di sini
            // Implementasi sesuai kebutuhan aplikasi

            return response()->json(['status' => 'success']);

        } catch (Exception $e) {
            Log::error('WhatsApp webhook error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
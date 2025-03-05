<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class IlpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Mendapatkan ringkasan hasil pemeriksaan untuk dikirim via WhatsApp
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSummary(Request $request)
    {
        // Validasi input
        $request->validate([
            'id' => 'required|numeric'
        ]);
        
        $id = $request->id;
        
        try {
            // Ambil data pemeriksaan dari tabel ilp_dewasa
            $pemeriksaan = DB::table('ilp_dewasa')
                ->where('id', $id)
                ->first();
                
            if (!$pemeriksaan) {
                return response()->json(['error' => 'Data pemeriksaan tidak ditemukan'], 404);
            }
            
            // Ambil data pasien
            $pasien = DB::table('pasien')
                ->where('no_rkm_medis', $pemeriksaan->no_rkm_medis)
                ->first();
                
            if (!$pasien) {
                return response()->json(['error' => 'Data pasien tidak ditemukan'], 404);
            }
            
            // Format tanggal pemeriksaan
            $tanggal = date('d-m-Y', strtotime($pemeriksaan->tanggal_pemeriksaan ?? now()));
            
            // Buat ringkasan pemeriksaan
            $summary = "HASIL PEMERIKSAAN ILP\n\n";
            $summary .= "Tanggal: " . $tanggal . "\n";
            $summary .= "Nama: " . $pasien->nm_pasien . "\n";
            $summary .= "No. RM: " . $pasien->no_rkm_medis . "\n\n";
            
            // Pemeriksaan Fisik
            $summary .= "PEMERIKSAAN FISIK:\n";
            $summary .= "- BB: " . ($pemeriksaan->berat_badan ?? '-') . " kg\n";
            $summary .= "- TB: " . ($pemeriksaan->tinggi_badan ?? '-') . " cm\n";
            $summary .= "- IMT: " . ($pemeriksaan->imt ?? '-') . "\n";
            $summary .= "- TD: " . ($pemeriksaan->td ?? '-') . " mmHg\n";
            $summary .= "- LP: " . ($pemeriksaan->lp ?? '-') . " cm\n";
            
            // Pemeriksaan Lab
            if (isset($pemeriksaan->gula_darah) || isset($pemeriksaan->kolesterol) || isset($pemeriksaan->asam_urat)) {
                $summary .= "\nHASIL LABORATORIUM:\n";
                
                if (isset($pemeriksaan->gula_darah)) {
                    $summary .= "- Gula Darah: " . $pemeriksaan->gula_darah . " mg/dL\n";
                }
                
                if (isset($pemeriksaan->kolesterol)) {
                    $summary .= "- Kolesterol: " . $pemeriksaan->kolesterol . " mg/dL\n";
                }
                
                if (isset($pemeriksaan->asam_urat)) {
                    $summary .= "- Asam Urat: " . $pemeriksaan->asam_urat . " mg/dL\n";
                }
                
                if (isset($pemeriksaan->trigliserida)) {
                    $summary .= "- Trigliserida: " . $pemeriksaan->trigliserida . " mg/dL\n";
                }
            }
            
            // Kesimpulan
            if (isset($pemeriksaan->skilas) && !empty($pemeriksaan->skilas)) {
                $summary .= "\nKESIMPULAN:\n" . $pemeriksaan->skilas . "\n";
            }
            
            $summary .= "\nUntuk hasil lengkap, silakan lihat PDF yang terlampir.";
            
            return response()->json(['summary' => $summary]);
        } catch (\Exception $e) {
            Log::error('Error generating summary: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat membuat ringkasan'], 500);
        }
    }

    /**
     * Mengirim file PDF hasil pemeriksaan melalui WhatsApp
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendPdf(Request $request)
    {
        // Validasi input
        $request->validate([
            'id' => 'required|numeric',
            'phone' => 'required|string'
        ]);
        
        $id = $request->id;
        $phone = $request->phone;
        
        try {
            // Cek apakah data pemeriksaan ada
            $pemeriksaan = DB::table('ilp_dewasa')
                ->where('id', $id)
                ->first();
                
            if (!$pemeriksaan) {
                return response()->json(['error' => 'Data pemeriksaan tidak ditemukan'], 404);
            }
            
            // Ambil data pasien
            $pasien = DB::table('pasien')
                ->where('no_rkm_medis', $pemeriksaan->no_rkm_medis)
                ->first();
                
            if (!$pasien) {
                return response()->json(['error' => 'Data pasien tidak ditemukan'], 404);
            }
            
            // Generate PDF
            $pdf = $this->generatePdf($id);
            
            if (!$pdf) {
                return response()->json(['error' => 'Gagal membuat PDF'], 500);
            }
            
            // Simpan PDF ke storage
            $filename = 'hasil_pemeriksaan_' . $pemeriksaan->no_rkm_medis . '_' . date('Ymd') . '.pdf';
            Storage::put('public/pdf/' . $filename, $pdf->output());
            
            // Buat URL untuk PDF
            $pdfUrl = url('storage/pdf/' . $filename);
            
            // Format nomor telepon
            $phone = $this->formatPhoneNumber($phone);
            
            // Buat pesan WhatsApp
            $message = "Halo " . $pasien->nm_pasien . ",\n\n";
            $message .= "Berikut adalah hasil pemeriksaan ILP Anda pada tanggal " . date('d-m-Y', strtotime($pemeriksaan->tanggal_pemeriksaan ?? now())) . ".\n\n";
            $message .= "Silakan unduh PDF hasil pemeriksaan melalui link berikut:\n";
            $message .= $pdfUrl . "\n\n";
            $message .= "Terima kasih telah menggunakan layanan kami.";
            
            // Encode pesan untuk URL
            $encodedMessage = urlencode($message);
            
            // Buat URL WhatsApp
            $whatsappUrl = "https://api.whatsapp.com/send?phone=" . $phone . "&text=" . $encodedMessage;
            
            return response()->json([
                'success' => true,
                'whatsapp_url' => $whatsappUrl,
                'pdf_url' => $pdfUrl
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengirim PDF: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Generate PDF hasil pemeriksaan
     *
     * @param  int  $id
     * @return \Barryvdh\DomPDF\PDF
     */
    private function generatePdf($id)
    {
        try {
            // Ambil data pemeriksaan dari tabel ilp_dewasa
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
            return $pdf;
            
        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Cetak hasil pemeriksaan dalam bentuk PDF
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cetak($id)
    {
        try {
            $pdf = $this->generatePdf($id);
            
            if (!$pdf) {
                return redirect()->back()->with('error', 'Gagal membuat file PDF');
            }
            
            return $pdf->stream('hasil_pemeriksaan_' . $id . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('Error printing PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Kirim pesan WhatsApp (teks atau PDF)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendWhatsApp(Request $request)
    {
        // Validasi input
        $request->validate([
            'id' => 'required|numeric',
            'phone' => 'required|string',
            'type' => 'required|in:text,pdf'
        ]);
        
        $id = $request->id;
        $phone = $request->phone;
        $type = $request->type;
        
        try {
            // Format nomor telepon
            $phone = $this->formatPhoneNumber($phone);
            
            // Kirim sesuai tipe yang diminta
            if ($type === 'text') {
                // Ambil ringkasan teks
                $response = $this->getSummary(new Request(['id' => $id]));
                $content = json_decode($response->getContent(), true);
                
                if (isset($content['error'])) {
                    return response()->json(['error' => $content['error']], 400);
                }
                
                $summary = $content['summary'] ?? '';
                
                // Encode pesan untuk URL
                $encodedMessage = urlencode($summary);
                
                // Buat URL WhatsApp
                $whatsappUrl = "https://api.whatsapp.com/send?phone=" . $phone . "&text=" . $encodedMessage;
                
                return response()->json([
                    'success' => true,
                    'whatsapp_url' => $whatsappUrl
                ]);
            } else {
                // Kirim PDF
                return $this->sendPdf(new Request([
                    'id' => $id,
                    'phone' => $phone
                ]));
            }
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengirim WhatsApp: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Format nomor telepon untuk WhatsApp
     * 
     * @param string $phone
     * @return string
     */
    private function formatPhoneNumber($phone)
    {
        // Hapus karakter non-numerik
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Hapus awalan 0 dan ganti dengan kode negara Indonesia (62)
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Jika belum ada kode negara, tambahkan kode negara Indonesia
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}

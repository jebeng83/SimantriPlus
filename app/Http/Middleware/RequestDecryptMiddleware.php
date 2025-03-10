<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Crypt;
use Closure;
use Illuminate\Http\Request;
use App\Traits\EnkripsiData;
use Illuminate\Support\Facades\Log;

class RequestDecryptMiddleware
{
    use EnkripsiData;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->has('no_rawat')) {
            $encryptedValue = $request->get('no_rawat');
            Log::info('Nilai no_rawat sebelum didekripsi', ['nilai' => $encryptedValue]);
            
            try {
                // Coba dekripsi dengan Laravel Crypt
                $decrypted = $this->decryptData($encryptedValue);
                
                // Cek apakah hasil dekripsi masih dalam format base64
                if (preg_match('/^[A-Za-z0-9+\/=]+$/', $decrypted)) {
                    $base64Decoded = base64_decode($decrypted);
                    if ($base64Decoded !== false) {
                        Log::info('Dekripsi ganda: no_rawat di-base64 decode setelah didekripsi', [
                            'setelah_decrypt' => $decrypted,
                            'setelah_base64_decode' => $base64Decoded
                        ]);
                        // Gunakan nilai yang sudah di-base64 decode
                        $decrypted = $base64Decoded;
                    }
                }
                
                // Bersihkan hasil dekripsi dari karakter tidak perlu
                $decrypted = trim($decrypted);
                
                $request->merge(['no_rawat' => $decrypted]);
                Log::info('Berhasil mendekripsi no_rawat dengan Crypt: ' . $decrypted);
            } catch (\Exception $e) {
                // Jika gagal, coba dengan base64 decode (untuk nilai dari JavaScript btoa())
                try {
                    Log::info('Mencoba base64 decode untuk no_rawat: ' . $encryptedValue);
                    $decoded = base64_decode(urldecode($encryptedValue));
                    if ($decoded !== false) {
                        // Bersihkan hasil dekripsi
                        $decoded = trim($decoded);
                        
                        $request->merge(['no_rawat' => $decoded]);
                        Log::info('Berhasil decode no_rawat dengan base64: ' . $decoded);
                    } else {
                        // Jika masih gagal, gunakan nilai asli
                        Log::warning('Base64 decode gagal untuk no_rawat, menggunakan nilai asli: ' . $encryptedValue);
                        // Coba gunakan nilai asli
                        $request->merge(['no_rawat' => $encryptedValue]);
                    }
                } catch (\Exception $e2) {
                    Log::error('Kedua metode dekripsi gagal untuk no_rawat: ' . $e2->getMessage());
                    // Gunakan nilai asli jika semua metode gagal
                    $request->merge(['no_rawat' => $encryptedValue]);
                }
            }
        }
        
        if($request->has('no_rm')){
            $encryptedValue = $request->get('no_rm');
            Log::info('Nilai no_rm sebelum didekripsi', ['nilai' => $encryptedValue]);
            
            try {
                // Coba dekripsi dengan Laravel Crypt
                $decrypted = $this->decryptData($encryptedValue);
                
                // Cek apakah hasil dekripsi masih dalam format base64
                if (preg_match('/^[A-Za-z0-9+\/=]+$/', $decrypted)) {
                    $base64Decoded = base64_decode($decrypted);
                    if ($base64Decoded !== false) {
                        Log::info('Dekripsi ganda: no_rm di-base64 decode setelah didekripsi', [
                            'setelah_decrypt' => $decrypted,
                            'setelah_base64_decode' => $base64Decoded
                        ]);
                        // Gunakan nilai yang sudah di-base64 decode
                        $decrypted = $base64Decoded;
                    }
                }
                
                // Bersihkan hasil dekripsi dari karakter tidak perlu
                $decrypted = trim($decrypted);
                
                $request->merge(['no_rm' => $decrypted]);
                Log::info('Berhasil mendekripsi no_rm dengan Crypt: ' . $decrypted);
            } catch (\Exception $e) {
                // Jika gagal, coba dengan base64 decode (untuk nilai dari JavaScript btoa())
                try {
                    Log::info('Mencoba base64 decode untuk no_rm: ' . $encryptedValue);
                    $decoded = base64_decode(urldecode($encryptedValue));
                    if ($decoded !== false) {
                        // Bersihkan hasil dekripsi
                        $decoded = trim($decoded);
                        
                        $request->merge(['no_rm' => $decoded]);
                        Log::info('Berhasil decode no_rm dengan base64: ' . $decoded);
                    } else {
                        // Jika masih gagal, gunakan nilai asli
                        Log::warning('Base64 decode gagal untuk no_rm, menggunakan nilai asli: ' . $encryptedValue);
                        // Coba gunakan nilai asli
                        $request->merge(['no_rm' => $encryptedValue]);
                    }
                } catch (\Exception $e2) {
                    Log::error('Kedua metode dekripsi gagal untuk no_rm: ' . $e2->getMessage());
                    // Gunakan nilai asli jika semua metode gagal
                    $request->merge(['no_rm' => $encryptedValue]);
                }
            }
        }
        
        return $next($request);    
    }
}

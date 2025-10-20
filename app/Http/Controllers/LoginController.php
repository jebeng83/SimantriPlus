<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $config = [
            "title" => "Pilih Poliklinik",
            "liveSearch" => true,
            "liveSearchPlaceholder" => "Cari...",
            "showTick" => true,
            "actionsBox" => true,
        ];
        $poli = DB::table('poliklinik')->where('status', '1')->get();
        return view('auth.login-premium',['poli'=>$poli, 'config'=>$config]);
    }

    public function customLogin(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required',
                'password' => 'required',
                'poli' => 'required',
            ], [
                'username.required' => 'ID Khanza tidak boleh kosong',
                'password.required' => 'Kata sandi tidak boleh kosong',
                'poli.required' => 'Poli tidak boleh kosong',
            ]);

            $inputUsername = trim($request->username);
            $inputPassword = (string) $request->password;
            $kdPoli = $request->poli;

            // Validasi poli yang dipilih
            $poli = DB::table('poliklinik')->where('kd_poli', $kdPoli)->where('status', '1')->first();
            if (!$poli) {
                return back()->withErrors(['message' => 'Poliklinik tidak valid atau tidak aktif'])->withInput();
            }

            // Validasi kredensial terhadap tabel `user`
            // Mendukung data yang disimpan terenkripsi (AES_ENCRYPT) dan plaintext untuk kompatibilitas
            $user = DB::table('user')
                ->where(function ($q) use ($inputUsername) {
                    $q->whereRaw('id_user = AES_ENCRYPT(?, "nur")', [$inputUsername])
                      ->orWhere('id_user', $inputUsername)
                      ->orWhereRaw('AES_DECRYPT(id_user, "nur") = ?', [$inputUsername]);
                })
                ->where(function ($q) use ($inputPassword) {
                    $q->whereRaw('password = AES_ENCRYPT(?, "windi")', [$inputPassword])
                      ->orWhere('password', $inputPassword)
                      ->orWhereRaw('AES_DECRYPT(password, "windi") = ?', [$inputPassword]);
                })
                ->first();

            if (!$user) {
                Log::warning('Percobaan login gagal: kredensial tidak valid', [
                    'id_user_input' => $inputUsername,
                    'kd_poli' => $kdPoli,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return back()->withErrors(['message' => 'ID Khanza atau kata sandi salah'])->withInput();
            }

            // Ambil data user sebagai array untuk menghindari error "Undefined property" pada stdClass
            $userData = is_object($user) ? (array) $user : [];

            // Gunakan NIP dari tabel user sebagai identitas dokter yang aktif di sesi jika tersedia
            // Banyak modul mengacu ke session('username') sebagai NIK/KD_DOKTER
            $kdDokter = $userData['nip'] ?? $inputUsername;

            // Tentukan nama tampilan dengan fallback yang aman
            $displayName = $userData['nama'] ?? $userData['name'] ?? $kdDokter;

            // Tentukan hak akses dengan fallback
            $userType = $userData['hak_akses'] ?? 'user';

            // Set session data
            session([
                'username' => $kdDokter,
                'kd_poli' => $kdPoli,
                'poli' => $kdPoli,
                'logged_in' => true,
                'login_time' => now()->format('Y-m-d H:i:s'),
                'name' => $displayName,
                'user_type' => $userType,
                'login_user_id' => $userData['id_user'] ?? $inputUsername, // fallback untuk mencegah null
            ]);

            // Pastikan session disimpan
            session()->save();

            // Log untuk debugging
            Log::info('Login berhasil melalui validasi tabel user', [
                'nip' => $kdDokter,
                'kd_poli' => $kdPoli,
                'session_id' => session()->getId(),
                'user_fields_present' => array_keys($userData),
            ]);

            return Redirect::intended('home')
                ->with('success', 'Login berhasil');
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::route('login')
                ->with('error', 'Terjadi kesalahan saat login. Silakan coba lagi.');
        }
    }


    public function username()
    {
        return 'username';
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
            'poli' => 'required',
        ],[
            'username.required' => 'NIP Dokter tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
            'poli.required' => 'Poli tidak boleh kosong',
        ]);
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
}

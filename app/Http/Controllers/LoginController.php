<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Log;

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
        return view('auth.login',['poli'=>$poli, 'config'=>$config]);
    }

    public function customLogin(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required',
                'password' => 'required',
                'poli' => 'required',
            ]);

            // Set session data
            session([
                'username' => $request->username,
                'kd_poli' => $request->poli,
                'poli' => $request->poli,
                'logged_in' => true,
                'login_time' => now()->format('Y-m-d H:i:s')
            ]);

            // Pastikan session disimpan
            session()->save();

            // Log untuk debugging
            Log::info('Login: User logged in successfully', [
                'username' => $request->username,
                'kd_poli' => $request->poli,
                'poli' => $request->poli,
                'session_id' => session()->getId(),
                'session_data' => session()->all(),
                'cookies' => $request->cookies->all(),
                'headers' => $request->headers->all(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->intended('home')
                ->with('success', 'Login berhasil');
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login')
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

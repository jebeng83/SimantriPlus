<?php

namespace App\Http\Controllers\Ranap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    /**
     * Show the Laporan Program dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function laporanProgram()
    {
        return view('ranap.laporan.program');
    }
}
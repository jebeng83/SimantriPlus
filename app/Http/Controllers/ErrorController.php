<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * Tampilkan halaman error umum
     */
    public function index()
    {
        return view('errors.500');
    }
    
    /**
     * Tampilkan halaman error 404
     */
    public function notFound()
    {
        return view('errors.404');
    }
    
    /**
     * Tampilkan halaman error 403
     */
    public function forbidden()
    {
        return view('errors.403');
    }
} 
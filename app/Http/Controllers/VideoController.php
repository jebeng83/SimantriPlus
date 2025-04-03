<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class VideoController extends Controller
{
    public function getVideos()
    {
        $videoPath = public_path('assets/vidio');
        
        // Cek apakah direktori ada
        if (!File::exists($videoPath)) {
            return response()->json([]);
        }
        
        // Ambil semua file video
        $videos = File::files($videoPath);
        
        // Filter hanya file video
        $videoFiles = array_filter(array_map(function($file) {
            $extension = strtolower($file->getExtension());
            if (in_array($extension, ['mp4', 'webm', 'ogg', 'mov'])) {
                return $file->getFilename();
            }
        }, $videos));
        
        return response()->json(array_values($videoFiles));
    }
} 
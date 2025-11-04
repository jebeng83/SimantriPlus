<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;

class VideoController extends Controller
{
    /**
     * Mengambil daftar nama file video dari direktori sumber.
     * Default diarahkan ke resources/js/pages/Display/video
     */
    public function getVideos()
    {
        $videoPath = base_path('resources/js/pages/Display/video');

        if (!File::exists($videoPath)) {
            return response()->json([]);
        }

        $videos = File::files($videoPath);

        $videoFiles = array_filter(array_map(function ($file) {
            $extension = strtolower($file->getExtension());
            if (in_array($extension, ['mp4', 'webm', 'ogg', 'mov'])) {
                return $file->getFilename();
            }
            return null;
        }, $videos));

        return response()->json(array_values($videoFiles));
    }

    /**
     * Streaming/menyajikan file video dari resources agar bisa diputar di browser.
     * URL: /display/videos/{filename}
     */
    public function stream(string $filename)
    {
        $dir = base_path('resources/js/pages/Display/video');
        // Decode nama file yang mungkin mengandung spasi
        $decoded = urldecode($filename);

        $baseReal = realpath($dir);
        $fullPath = realpath($dir . DIRECTORY_SEPARATOR . $decoded);

        if (!$baseReal || !$fullPath || strpos($fullPath, $baseReal) !== 0 || !File::exists($fullPath)) {
            abort(404);
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'mov' => 'video/quicktime',
            default => 'application/octet-stream',
        };

        return response()->file($fullPath, [
            'Content-Type' => $mime,
        ]);
    }
}
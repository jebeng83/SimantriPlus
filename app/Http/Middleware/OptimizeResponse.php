<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OptimizeResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Hanya untuk response HTML
        if ($response instanceof Response && 
            str_contains($response->headers->get('Content-Type', ''), 'text/html')) {
            
            // Compress HTML output
            $content = $response->getContent();
            if ($content) {
                // Minify HTML
                $content = $this->minifyHtml($content);
                $response->setContent($content);
            }

            // Set cache headers untuk static assets
            if ($this->isStaticAsset($request)) {
                $response->headers->set('Cache-Control', 'public, max-age=31536000'); // 1 year
                $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
            }

            // Set security headers
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

            // Enable GZIP compression
            if (!headers_sent() && extension_loaded('zlib') && !ob_get_level()) {
                ob_start('ob_gzhandler');
            }
        }

        return $response;
    }

    /**
     * Minify HTML content
     *
     * @param string $html
     * @return string
     */
    private function minifyHtml($html)
    {
        // Skip minification in development
        if (config('app.debug')) {
            return $html;
        }

        // Remove HTML comments (except IE conditional comments)
        $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);
        
        // Remove extra whitespace
        $html = preg_replace('/\s+/', ' ', $html);
        
        // Remove whitespace around tags
        $html = preg_replace('/\s*(<[^>]+>)\s*/', '$1', $html);
        
        // Remove whitespace before closing tags
        $html = preg_replace('/\s*(<\/[^>]+>)/', '$1', $html);
        
        return trim($html);
    }

    /**
     * Check if request is for static asset
     *
     * @param Request $request
     * @return bool
     */
    private function isStaticAsset(Request $request)
    {
        $path = $request->getPathInfo();
        $extensions = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.eot'];
        
        foreach ($extensions as $ext) {
            if (str_ends_with($path, $ext)) {
                return true;
            }
        }
        
        return false;
    }
}
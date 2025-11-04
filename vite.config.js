import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    // Server configuration for development
    server: {
        host: '0.0.0.0', // Allow external connections
        port: 5174,
        strictPort: false, // Izinkan pindah port jika 5174 dipakai; hot file akan menunjuk port baru
        // Biarkan Vite menentukan pengaturan HMR (host/port) secara otomatis
        // agar sinkron dengan port yang dipilih ketika strictPort: false.
        // Gunakan CORS default (origin: *). Ini mencegah mismatch origin ketika
        // Laravel berjalan pada port berbeda (mis. 8002) dari daftar yang diizinkan.
        // Jika Anda butuh credentials, buat fungsi dinamis yang memantulkan origin permintaan.
        cors: true
        // Contoh jika butuh credentials:
        // cors: {
        //   origin: (origin) => origin ?? '*',
        //   credentials: true
        // }
    },
    // Build configuration for production
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/app.jsx',
            ]
        }
    },
    plugins: [
        // Disable Fast Refresh to avoid requiring the React preamble during development
        // This prevents the '@vitejs/plugin-react can't detect preamble' runtime guard from triggering
        // while we stabilize script loading order. You can re-enable later by setting fastRefresh: true.
        react({ fastRefresh: false }),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/app.jsx',
            ],
            refresh: true,
            // Ensure Laravel can detect the dev server by writing the hot file.
            // Use an absolute path to avoid any CWD or path resolution issues.
            hotFile: path.resolve(__dirname, 'public', 'hot'),
        }),
    ],
});

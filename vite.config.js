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
        host: 'localhost',
        port: 5174,
        strictPort: false,
        https: false,
        hmr: {
            protocol: 'http',
            host: 'localhost',
        },
        cors: true
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

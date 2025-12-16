import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: 'localhost', // Ganti dari 0.0.0.0 ke localhost
        port: 5173,
        strictPort: true,
        cors: true,
        hmr: {
            host: 'localhost', // Pastikan host HMR juga localhost
            port: 5173,
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
    define: {
        global: 'globalThis',
    },
});

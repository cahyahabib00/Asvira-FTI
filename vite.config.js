import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        hmr: {
            host: 'localhost',
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['tailwindcss'],
                },
            },
        },
        cssCodeSplit: true,
        minify: 'terser',
        sourcemap: false,
    },
    optimizeDeps: {
        include: ['tailwindcss'],
    },
});

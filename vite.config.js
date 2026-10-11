import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { fileURLToPath } from 'node:url';

export default defineConfig(({ command }) => ({
    publicDir: 'public',
    build: { copyPublicDir: false },
    experimental: {
        // Laravel serves public assets from /assets; only bundles live in /build.
        renderBuiltUrl(filename, { type }) {
            if (type === 'public') return `/${filename}`;
        },
    },
    resolve: {
        alias: { '@': fileURLToPath(new URL('./resources/react', import.meta.url)) },
    },
    plugins: [
        react(),
        laravel({
            input: [
                'resources/react/main.tsx',
                'resources/css/app.css',
                'resources/css/pages/errors.css',
                'resources/css/pages/about-us.css',
                'resources/css/pages/dashboard.css',
                'resources/js/app.js',
                'resources/js/pages/dashboard.js',
                'resources/js/components/photos.js',
                'resources/js/pages/specialized-educational-support/attendance-record-form.js',
                'resources/js/pages/specialized-educational-support/session.js',
                'resources/js/pages/inclusive-radar/barriers.js',
                'resources/js/pages/inclusive-radar/institutions.js',
                'resources/js/pages/inclusive-radar/locations.js',
                'resources/js/pages/inclusive-radar/evidence-uploader.js',
                'resources/js/pages/inclusive-radar/assistive-technologies.js',
                'resources/js/pages/inclusive-radar/accessible-educational-materials.js',
                'resources/js/pages/inclusive-radar/loans.js',
                'resources/js/components/dynamicFilters.js',
                'resources/js/pages/inclusive-radar/file-uploader.js',
                'resources/js/pages/inclusive-radar/waitlists.js',
            ],
            refresh: command === 'serve',
        }),
    ],
    ...(command === 'serve' ? {
        server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            protocol: 'ws'
        },
        watch: {
            usePolling: true,
            ignored: [
                '**/vendor/**',
                '**/storage/**',
                '**/node_modules/**',
                '**/public/build/**',
                '**/.git/**',
            ]
        }
        }
    } : {})
}));

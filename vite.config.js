import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pages/inclusive-radar/barriers.js',
                'resources/js/pages/inclusive-radar/institutions.js',
                'resources/js/pages/inclusive-radar/locations.js',
                'resources/js/pages/inclusive-radar/image-uploader.js',
                'resources/js/pages/inclusive-radar/assistive-technologies.js',
                'resources/js/pages/inclusive-radar/accessible-educational-materials.js',
                'resources/js/pages/inclusive-radar/loans.js',
                'resources/js/components/dynamicFilters.js',
                'resources/js/pages/inclusive-radar/file-uploader.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
});

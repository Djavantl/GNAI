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
            input: ['resources/react/main.tsx'],
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

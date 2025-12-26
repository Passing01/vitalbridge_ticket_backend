import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'build',
        manifest: true,
    },
    // Indique à Vite que le dossier public est la racine (et non public/)
    publicDir: false,
});

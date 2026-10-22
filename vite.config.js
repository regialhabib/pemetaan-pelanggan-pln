import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/Pages/adminMap.js',
                'resources/js/Pages/petugasMap.js'
            ],
            refresh: true,
        }),
    ],
});

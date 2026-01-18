import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'public/assets/scss/app.scss',
            ],
            refresh: true,
        }),
    ],
});

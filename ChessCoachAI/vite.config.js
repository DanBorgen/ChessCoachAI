import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        // laravel-vite-plugin handles asset versioning and hot reload
        // equivalent to the asset pipeline / bundler config in a .NET project
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true, // auto-reload browser on Blade/PHP file changes
        }),
        // compiles .vue Single File Components
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
})

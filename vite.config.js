import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue2';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    define: {
        global: 'globalThis',
        'process.env': {}
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
            '~': resolve(__dirname, 'resources'),
            'vue': 'vue/dist/vue.esm.js'
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
            interval: 1000,
        },
    },
    build: {
        outDir: 'public/build',
        assetsDir: '',
        manifest: true,
        rollupOptions: {
            input: {
                app: resolve(__dirname, 'resources/js/app.js'),
                style: resolve(__dirname, 'resources/sass/app.scss'),
            },
            output: {
                manualChunks: {
                    vendor: ['vue', 'vue-router', 'vuex', 'axios'],
                },
            },
        },
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `@import "resources/sass/_variables.scss";`,
            },
        },
    },
});

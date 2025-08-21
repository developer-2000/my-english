const mix = require('laravel-mix');

// Development configuration
if (!mix.inProduction()) {
    mix.webpackConfig({
        watchOptions: {
            ignored: /node_modules/,
            poll: 1000,
            aggregateTimeout: 300
        },
        devServer: {
            hot: true,
            liveReload: true,
            watchFiles: {
                paths: [
                    'resources/**/*',
                    'app/**/*.php',
                    'resources/views/**/*.php'
                ],
                options: {
                    usePolling: true,
                    interval: 1000
                }
            }
        }
    });
}

// Production optimizations
if (mix.inProduction()) {
    mix.options({
        terser: {
            extractComments: false,
            terserOptions: {
                compress: {
                    drop_console: true,
                    drop_debugger: true
                }
            }
        },
        cssNano: {
            preset: ['default', {
                discardComments: {
                    removeAll: true
                },
                normalizeWhitespace: true,
                colormin: true,
                minifyFontValues: true,
                minifySelectors: true
            }]
        }
    });
}

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .vue({ version: 2 })
    .version(); // Add versioning for cache busting

// Development server configuration
if (!mix.inProduction()) {
    mix.browserSync({
        host: 'localhost',
        proxy: 'english.my',
        open: false, // Don't auto-open browser
        injectChanges: true,
        logSnippet: false, // Reduce console noise
        watchOptions: {
            usePolling: true,
            interval: 1000,
            poll: true,
            ignored: /node_modules/
        },
        files: [
            'app/**/*.php',
            'resources/views/**/*.php',
            'resources/js/**/*.js',
            'resources/sass/**/*.scss',
            'resources/js/components/**/*.vue',
            'public/js/**/*.js',
            'public/css/**/*.css'
        ],
        reloadDelay: 100,
        reloadDebounce: 500
    });
}


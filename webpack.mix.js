const mix = require('laravel-mix');

// mix.js('resources/js/app.js', 'public/js')
//     .vue({ version: 2 })
//     .postCss('resources/css/app.css', 'public/css')
//     .sass('resources/sass/app.scss', 'public/css');



mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .postCss('resources/css/app.css', 'public/css')
    .browserSync({
        host: 'localhost',
        proxy: 'english.my',
        open: true,
        injectChanges: true,
        logSnippet: true,
        watchOptions: {
            usePolling: true,
            interval: 500,
            poll: true,
            ignored: /node_modules/
        },
        files: [
            'app/**/*.php',
            'resources/views/*.php',
            'resources/js/app.js',
            'resources/css/app.css',
            'resources/js/components/Pages/*.vue',
            'packages/mixdinternet/frontend/src/**/*.php',
            'public/js/**/*.js',
            'public/css/**/*.css'
        ]
    })
    .vue({ version: 2 });

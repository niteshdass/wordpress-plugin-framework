const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'assets/js/admin.js')
   .vue({ version: 3 }) // Ensure Vue 3 compatibility
//    .sass('resources/css/app.scss', 'assets/css/admin.css')
   .sass('resources/scss/admin/app.scss', 'assets/css/admin.css')
   .sourceMaps();

// Explicitly configure Webpack resolve.extensions
mix.webpackConfig({
    resolve: {
        extensions: ['.*']
    }
});
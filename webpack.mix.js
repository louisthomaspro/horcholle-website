const path = require('path');
const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('node_modules/bootstrap/js/dist/util.js', 'public/js');
mix.js('node_modules/bootstrap/js/dist/modal.js', 'public/js');
mix.js('node_modules/bootstrap/js/dist/collapse.js', 'public/js');

mix.js('resources/js/app.js', 'public/js');
mix.sass('resources/sass/app.scss', 'public/css', {
	 includePaths: [path.resolve(__dirname, 'node_modules')]
	})
  .options({
      processCssUrls: false
   });

  
const config = require('./node_modules/laravel-mix/setup/webpack.config');
const mix = require('laravel-mix');
require('laravel-mix-eslint');
require('dotenv').config(); 

mix.webpackConfig(config);

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

mix.options({
    processCssUrls: false,
})
.js('resources/js/app.js', 'public/js').vue()
.sass('resources/sass/app.scss', 'public/css');

if (mix.inProduction()) 
{
    mix.version();
} 
else 
{
    if (process.env.LARAVUE_USE_ESLINT === 'true') {
        mix.eslint();
    }
    // Development settings
    // mix
    // .sourceMaps()
    // .webpackConfig({
    //     devtool: 'cheap-eval-source-map', 
    // });
}

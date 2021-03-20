let mix = require('laravel-mix');

// Set paths
const src  = 'web/assets/src';
const dist = 'web/assets/dist';

// Run Mix
mix
    .webpackConfig({
        module: {
            rules: [
                { test: /\.vue$/, use: 'vue-loader' }
            ]
        }
    })

    // Compile all Sass
    .sass(`${src}/sass/address.scss`, `${dist}/css`)

    // Compile all JavaScript
    .js(`${src}/js/address.js`, `${dist}/js`)
    .js(`${src}/js/address-settings.js`, `${dist}/js`)
    .js(`${src}/js/settings.js`, `${dist}/js`)

    // Copy Sortable JS into plugin's assets folder
    .copy('node_modules/sortablejs/Sortable.min.js', `${dist}/js`)

    .disableNotifications();

// // If running in production, append version
// if (mix.config.production) {
//     mix.version();
// }

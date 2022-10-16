let mix = require('laravel-mix');

// Set paths
const src  = 'web/assets/src';
const dist = 'web/assets/dist';

// Run Mix
mix

    .vue({version: 3})

    // Webpack config
    .webpackConfig({
        module: {
            rules: [
                {test: /\\.vue$/, loader: 'vue-loader'}
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

    // Disable build notifications
    .disableNotifications()
;

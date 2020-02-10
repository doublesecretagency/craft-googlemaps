module.exports = {

    // markdown: {
    //     slugify: ???
    // },



    base: '/google-maps/',
    title: 'Google Maps plugin for Craft CMS',
    plugins: [
        [
            'vuepress-plugin-clean-urls',
            {
                normalSuffix: '/',
                indexSuffix: '/',
                notFoundPath: '/404.html',
            },
        ],
    ],
    themeConfig: {
        logo: '/images/dsa-circle.png',
        searchMaxSuggestions: 10,
        nav: [
            {text: 'Getting Started', link: '/getting-started/'},
            {
                text: 'Major Concepts',
                items: [
                    {text: 'Address Field', link: '/address-field/'},
                    {text: 'Maps', link: '/maps/'},
                    {text: 'Proximity Search', link: '/proximity-search/'},
                    {text: 'Geocoding (Address Lookups)', link: '/geocoding/'},
                    {text: 'Visitor Geolocation', link: '/geolocation/'},
                ]
            },
            {
                text: 'Architecture',
                items: [
                    {text: 'Models', link: '/models/'},
                    {text: 'Services', link: '/services/'},
                    {text: 'Twig Object', link: '/twig-object/'},
                    {text: 'JavaScript Object', link: '/javascript-object/'},
                    {text: 'Config File', link: '/config/'},
                ]
            },
            {
                text: 'Guides',
                items: [
                    {text: 'Importing Addresses', link: '/guides/importing-addresses/'},
                    {text: 'Internationalization Support', link: '/guides/internationalization-support/'},
                    {text: 'Address in a Matrix Field', link: '/guides/address-in-a-matrix-field/'},
                    {text: 'Styling a Map', link: '/guides/styling-a-map/'},
                    {text: 'Setting Marker Icons', link: '/guides/setting-marker-icons/'},
                    {text: 'Complex JS in Twig', link: '/guides/complex-js-in-twig/'},
                    {text: 'KML Files', link: '/guides/kml-files/'},
                    {text: 'Filter by Fields and Subfields', link: '/guides/filter-by-fields-and-subfields/'},
                    {text: 'Region Biasing', link: '/guides/region-biasing/'},
                    {text: 'Complex Geolocation', link: '/guides/complex-geolocation/'},
                ]
            }
        ],
        sidebar: {
            // Getting Started
            '/getting-started/': [
                '',
                'api-keys',
                'diagnostics',
            ],

            // Major Concepts
            '/address-field/': [
                '',
                'field-settings',
                'editing-an-address',
                'using-an-address-in-twig',
                'frontend-form',
                'vue',
            ],
            '/maps/': [
                '',
                'dynamic',
                'static',
                'info-windows',
                'linking-to-google',
            ],
            '/proximity-search/': [
                '',
                'options',
                'in-twig',
                'in-php',
                'in-javascript',
                'in-graphql',
            ],
            '/geolocation/': [
                '',
                'providers',
                'html5',
                'cookie',
                'diagnostics',
            ],
            '/geocoding/': [
                '',
                'in-twig',
                'in-php',
                'via-ajax',
            ],

            // Architecture
            '/models/': [
                '',
                'address-model',
                'visitor-model',
                'location-model',
                'lookup-model',
                'ipstack-model',
                'maxmind-model',
                'coordinates',
            ],
            '/services/': [
                '',
                'address-field-service',
                'api-service',
                'geocoding-service',
                'geolocation-service',
                'maps-javascript-service',
                'maps-static-service',
                'proximity-search-service',
            ],
            '/twig-object/': [
                '',
            ],
            '/javascript-object/': [
                '',
                'google-maps-objects',
            ],
            '/config/': [
                '',
            ],

            // Guides
            '/guides/': [
                '',
                'importing-addresses',
                'internationalization-support',
                'address-in-a-matrix-field',
                'styling-a-map',
                'setting-marker-icons',
                'complex-js-in-twig',
                'kml-files',
                'filter-by-fields-and-subfields',
                'region-biasing',
                'complex-geolocation',
            ],

            // // fallback
            // '/': [
            //     '',        /* / */
            //     'contact', /* /contact.html */
            //     'about',   /* /about.html */
            // ]
        }
    }
};

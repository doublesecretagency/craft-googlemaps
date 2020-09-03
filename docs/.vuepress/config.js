module.exports = {

    markdown: {
        anchor: { level: [2, 3] },
        extendMarkdown(md) {
            let markup = require("vuepress-theme-craftdocs/markup");
            md.use(markup);
        },
    },

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
    theme: "craftdocs",
    themeConfig: {
        codeLanguages: {
            php: "PHP",
            twig: "Twig",
            js: "JavaScript",
        },
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
                    {text: 'Settings', link: '/settings/'},
                    {text: 'Helper', link: '/helper/'},
                    {text: 'Models', link: '/models/'},
                    {text: 'Services', link: '/services/'},
                    {text: 'Events', link: '/events/'},
                    {text: 'JavaScript Object', link: '/javascript-object/'},
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
                    {text: 'Filter by Subfields', link: '/guides/filter-by-subfields/'},
                    {text: 'Region Biasing', link: '/guides/region-biasing/'},
                    {text: 'Complex Geolocation', link: '/guides/complex-geolocation/'},
                    {text: 'Bermuda Triangle', link: '/guides/bermuda-triangle/'},
                    {text: 'Updating from Smart Map', link: '/guides/updating-from-smart-map/'},
                ]
            }
        ],
        sidebar: {
            // Getting Started
            '/getting-started/': [
                '',
                'api-keys',
                // 'diagnostics', // TODO: Add diagnostics tools
            ],

            // Major Concepts
            '/address-field/': [
                '',
                'settings',
                'using-an-address-in-twig',
                'frontend-form',
                // 'vue', // TODO: Probably delete this page?
            ],
            '/maps/': [
                '',
                'dynamic',
                'static',
                'info-windows',
                'linking-to-google',


                'chaining',
                'php',
                'twig',
                'twig-static',



            ],
            '/proximity-search/': [
                '',
                'query-parameters',
                'options',
                // 'in-javascript', // TODO: Support JS methods
                // 'in-graphql', // TODO: Support GraphQL
            ],
            '/geolocation/': [
                '',
                'service-providers',
                // 'html5', // TODO: Add HTML5 geolocation (?)
                // 'cookie', // TODO: Refine cookie & cache implementation
                // 'diagnostics', // TODO: Add diagnostics tools
            ],
            '/geocoding/': [
                '',
                'parameters',
                'methods',
                // 'via-ajax', // TODO: Support AJAX endpoints
            ],

            // Architecture
            '/settings/': [
                '',
                'config',
            ],
            '/helper/': [
                '',
            ],
            '/models/': [
                '',
                'address-model',
                'visitor-model',
                'location-model',
                'lookup-model',
                'ipstack-model',
                'maxmind-model',
                'settings-model',
                'coordinates',
            ],
            '/services/': [
                '',
                'address-service',
                'api-service',
                'geocoding-service',
                'geolocation-service',
                'maps-javascript-service',
                'maps-static-service',
                'proximity-search-service',
            ],
            '/events/': [
                '',
                'geocoding-event',
                'geolocation-event',
            ],
            '/javascript-object/': [
                '',
                'google-maps-objects',
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
                'filter-by-subfields',
                'region-biasing',
                'complex-geolocation',
                'bermuda-triangle',
                'updating-from-smart-map',
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

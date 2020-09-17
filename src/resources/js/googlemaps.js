// Load AJAX library
var ajax = window.superagent;

// Google Maps plugin JS object
window.googleMaps = {


    // ========================================================================= //

    // // Default action url
    // actionUrl: '/actions/',
    // // No CSRF token by default
    // csrfToken: false,
    // // Submit AJAX with fresh CSRF token
    // getCsrf: function (callback) {
    //     // Make object available to callback
    //     var that = this;
    //     // Fetch a new CSRF token
    //     ajax
    //         .get(this.actionUrl+'google-maps/page/csrf')
    //         .end(function(err, res){
    //             // If something went wrong, bail
    //             if (!res.ok) {
    //                 console.warn('[GM] Error retrieving CSRF token:', err);
    //                 return;
    //             }
    //             // Set global CSRF token
    //             that.csrfToken = res.body;
    //             // Run callback
    //             callback();
    //         })
    //     ;
    // },

    // ========================================================================= //


    // Initialize collection
    _maps: {},

    // Initialize empty defaults
    _defaults: {},
    // _defaultMarkerOptions: {},
    // _defaultInfoWindowOptions: {},

    // Internal instance of object
    _instance: null,

    // Initialize specified maps
    init: function (selection) {

        // Initialize
        var dna;

        // Get selected map containers
        var containers = this._whichMaps(selection);

        // Loop through containers
        for (var i in containers) {

            // Get DNA of each map
            dna = containers[i].dataset.dna;

            // If no DNA exists, skip this container
            if (!dna) {
                continue;
            }

            // Render each map
            this._unpackDna(dna);

        }

    },

    // ========================================================================= //

    // Create a new map object
    map: function(locations, options) {

        // Ensure options are valid
        options = options || {};

        // Set default values
        options.zoom = options.zoom || null;

        // Get map container
        var container = document.getElementById(options.id);

        // If container does not exist
        if (!container) {

            // If no map ID exists, generate one
            if (!options.id) {
                options.id = this._generateId('map');
            }

            // Create new container from scratch
            container = document.createElement('div');


            // TEMP
            // TODO: This belongs somewhere else?
            var supercontainerName = 'just-js';
            var supercontainer = document.getElementById(supercontainerName);
            if (supercontainer) {
                supercontainer.appendChild(container);
            } else {
                console.warn('[GM] Unable to find parent container:', supercontainerName);
            }
            // ENDTEMP


        }

        // Configure map container
        container.id = options.id;
        container.classList.add('gm-map');
        container.style.display = 'block';

        // Optionally set container height
        if (options.height) {
            container.style.height = `${options.height}px`;
        }

        // Optionally set width of container
        if (options.width) {
            container.style.width = `${options.width}px`;
        }

        // Create a new Google Map object
        this._createMap(container, options);

        // If locations were specified, add markers
        if (locations) {
            this.markers(locations);
        }

        // Fit map boundaries to markers
        this.fit(options);

        // Keep the party going
        return this;
    },

    // Create a set of marker objects
    markers: function(locations, options) {

        // Ensure options are valid
        options = options || {};

        // Set map
        options.map = this._instance.map;

        // Force locations to be an array structure
        if (!Array.isArray(locations)) {
            locations = [locations];
        }

        // Loop through all locations
        for (var i in locations) {

            // Get individual coordinates
            var coords = locations[i];

            // If coordinates are not valid, skip
            if (!coords.hasOwnProperty('lat') || !coords.hasOwnProperty('lng')) {
                continue;
            }

            // Create a new marker
            this._createMarker(coords, options);

        }

        // Keep the party going
        return this;
    },

    // kml: function() {
    //     // Do whatever
    //     return this;
    // },

    // styles: function() {
    //     // Do whatever
    //     return this;
    // },

    // ========================================================================= //

    // Fit map according to bounds
    fit: function(options) {

        /*
         * NOTE: Zoom & center values are required
         * to render a map without fitting bounds.
         */

        // Get map data
        var data = this.getMap(options.id);

        if (!data) {
            return;
        }

        // If neither zoom nor center was specified
        if (!options.zoom && !options.center) {
            // Just fit to boundaries and bail
            data.map.fitBounds(data.bounds);
            return;
        }

        // Set fallback zoom and center
        options.zoom = options.zoom || 4;
        options.center = options.center || data.bounds.getCenter();

        // Center and zoom map
        data.map.setCenter(options.center);
        data.map.setZoom(options.zoom);

    },

    // ========================================================================= //

    // Get a specified map object
    getMap: function(mapId) {
        return this._maps[mapId];
    },

    // Get a specified marker object
    getMarker: function(mapId, markerId) {
        return this._maps[mapId].markers[markerId];
    },

    // ========================================================================= //

    // Create a new map object
    _createMap: function(container, options) {

        // Get the specified map ID
        var mapId = options.id;

        // Initialize map data
        var data = {
            map: new google.maps.Map(container, options),
            bounds: new google.maps.LatLngBounds(),
            markers: []
        }

        // Add map externally
        this._maps[mapId] = data;

        // Add map internally
        this._instance = data;

    },

    // Create a new marker object
    _createMarker: function(coords, options) {

        // If marker ID exists with coordinates, use it as a fallback
        if (coords.hasOwnProperty('id')) {
            options.id = options.id || coords.id;
        }

        // Set marker position based on coordinates
        options.position = coords;

        // Extend map boundaries
        this._instance.bounds.extend(coords);

        // Get the map ID
        var mapId = this._instance.map.id;

        // Get map data
        var data = this._maps[mapId];


        // TEMP
        // TODO: COMPILE `markerId` IN PHP
        // var elementId = 16;
        // var fieldHandle = 'address';
        // var markerId = `${elementId}.${fieldHandle}`;
        // ENDTEMP

        // Get marker ID or generate a random one
        var markerId = options.id || this._generateId('marker');

        // Ensure map is accounted for
        if (!data) {
            console.warn(`[GM] Unable to attach marker "${markerId}" to map "${mapId}".`);
            return;
        }

        // Initialize marker object
        var marker = new google.maps.Marker(options);

        // Add marker to external array
        this._maps[mapId].markers[markerId] = marker;
        // data.markers[markerId] = marker;

        // Add marker to internal array
        this._instance.markers[markerId] = marker;

    },

    // ========================================================================= //

    // Determine which maps to compile
    _whichMaps: function (selection) {

        // No map containers by default
        var containers = [];

        // Switch according to how map IDs were specified
        switch (typeof selection) {

            // Individual map
            case 'string':
                containers = [document.getElementById(selection)];
                break;

            // Selection of maps
            case 'object':

                // Add each map container to collection
                var c;
                for (var i in selection) {
                    c = document.getElementById(selection[i]);
                    containers.push(c);
                }
                break;

            // All maps
            case 'undefined':
                var allMaps = document.getElementsByClassName('gm-map');
                containers = Array.prototype.slice.call(allMaps);
                break;

            // Something went wrong
            default:
                containers = [];
                break;

        }

        // Return collection
        return containers;
    },

    // Unpack and initialize map DNA
    _unpackDna: function (dna) {

        // Unpack the DNA sequence
        var sequence = JSON.parse(dna);

        // If no DNA exists, error and bail
        if (!sequence) {
            console.warn('[GM] No map DNA provided.');
            return;
        }

        // Get map DNA
        var map = sequence[0];

        // If first block is not a map, error and bail
        if ('map' !== map.type) {
            console.warn('[GM] Map DNA is misconfigured.');
            return;
        }

        // Render a map from DNA, store internally
        this.map(map.locations, map.options);
    },

    // Generate a new random map ID
    _generateId: function(prefix) {

        // Initialize random ID
        var randomId = '';

        // Create an array of the alphabet
        var alpha = 'abcdefghijklmnopqrstuvwxyz';
        var alphabet = alpha.split('');

        // Add six randomly selected characters
        for (char = 0; char < 6; char++) {
            var i = Math.floor(Math.random() * 25);
            randomId += alphabet[i];
        }

        // Return new ID (with optional prefix)
        return (prefix ? `${prefix}-${randomId}` : randomId);
    },

};

// ========================================================================= //
// ========================================================================= //

// On page load, initialize all maps on the page
addEventListener('load', function () {
    googleMaps.init();
});

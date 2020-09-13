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
    //                 console.error('[GM] Error retrieving CSRF token:', err);
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


    // Initialize collections
    _maps: {},
    _markers: {},

    // Initialize empty defaults
    _defaultMarkerOptions: {},
    _defaultInfoWindowOptions: {},

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
            // Get map DNA
            dna = containers[i].dataset.dna;
            // If no DNA exists, skip this container
            if (!dna) {
                continue;
            }
            // Render the map
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
                options.id = this._generateId();
            }

            // Create new container from scratch
            container = document.createElement('div');


            // TEMP: This belongs somewhere else?
            document.getElementById('just-js').appendChild(container);
            // ENDTEMP


        }

        // Ensure height property exists // TODO: Should this be removed? Set height via CSS?
        var height = options.height || 400;

        // Configure map container
        container.id = options.id;
        container.classList.add('gm-map');
        container.style.display = 'block';
        container.style.height = `${height}px`;

        // Optionally set width of container
        if (options.width) {
            container.style.width = `${options.width}px`;
        }

        // Create a new Google Map object
        this._createMap(options.id, container, options);

        // If locations were specified, add markers
        if (locations) {
            this.markers(locations);
        }

        // If no zoom specified, fit according to bounds
        // if (!options.zoom) {
            this.fitBounds(options.id);
        // }

        // Keep the party going
        return this;
    },

    // Create a set of marker objects
    markers: function(locations, options) {

        // Ensure options are valid
        options = options || {};

        // Set map
        // options.map = options.map || this.getMap(mapId);
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

    // ========================================================================= //

    // Automatically fit map according to bounds
    fitBounds: function(mapId) {
        var map = this.getMap(mapId);
        map.map.fitBounds(map.bounds);
    },

    // ========================================================================= //

    // Get a specified map object
    getMap: function(mapId) {
        return this._maps[mapId];
    },
    // Get a specified marker object
    getMarker: function(mapId, markerId) {
        return this._markers[mapId][markerId];
    },

    // ========================================================================= //

    // Create a new map object
    _createMap: function(mapId, container, options) {

        // Initialize map object
        var map = {
            container: container,
            map: new google.maps.Map(container, options),
            markers: {},
            bounds: new google.maps.LatLngBounds()
        }

        // Add map to master collection
        this._maps[mapId] = map;

        // Set internal map instance
        this._instance = map;

    },

    // Create a new marker object
    _createMarker: function(coords, options) {

        // Set marker position based on coordinates
        options.position = coords;

        // Extend map boundaries
        this._instance.bounds.extend(coords);

        // Get the map ID
        var mapId = this._instance.map.id;


        console.log(this._instance);

        // TEMP
        var elementId = 16;
        var fieldHandle = 'address';
        // ENDTEMP


        // Set marker ID
        var markerId = `${elementId}.${fieldHandle}`;


        // Initialize marker object
        var marker = new google.maps.Marker(options);

        // Ensure map is accounted for
        // if (!this._markers[mapId]) {
        //     this._markers[mapId] = {};
        // }

        // Add marker to master collection
        // return this._markers[mapId][markerId];

        // Add marker to internal instance
        // this._instance.markers.push(marker);

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
            console.error('[GM] No map DNA provided.');
            return;
        }

        // Get map DNA
        var map = sequence[0];

        // If first block is not a map, error and bail
        if ('map' !== map.type) {
            console.error('[GM] Map DNA is misconfigured.');
            return;
        }

        // Render a map from DNA, store internally
        this.map(map.locations, map.options);
    },

    // Generate a new random map ID
    _generateId: function() {

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

        // Return randomly generated map ID
        return `gm-map-${randomId}`;
    },

};

// ========================================================================= //
// ========================================================================= //

// On page load, initialize all maps on the page
addEventListener('load', function () {
    googleMaps.init();
});

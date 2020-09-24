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


    // Initialize collections
    _maps: {},
    _markers: {},

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
        // this.fit(options); // TODO: DELETE?
        this.fit();

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

    // Generate a complete map
    tag: function(parentId) {

        // If no valid parent container specified, return the element as-is
        if (!parentId || 'string' !== typeof parentId) {
            return this._instance.container;
        }

        // Get specified parent container
        var parent = document.getElementById(parentId);

        // If parent container exists, populate it
        if (parent) {
            parent.appendChild(this._instance.container);
        } else {
            console.warn(`[GM] Unable to find target container #${parentId}`);
        }

        // Return map container
        return this._instance.container;
    },

    // ========================================================================= //

    // Hide a marker
    hideMarker: function(markerId) {

        // Get specified marker
        var marker = this.getMarker(markerId);

        // Detach marker from map
        marker.setMap(null);

        // Keep the party going
        return this;
    },

    // Show a marker
    showMarker: function(markerId) {

        // Get specified marker
        var marker = this.getMarker(markerId);

        // Attach marker to current map
        marker.setMap(this._instance.map);

        // Keep the party going
        return this;
    },

    // Pan map to center on a specific marker
    panToMarker: function(markerId) {

        // Get specified marker
        var marker = this.getMarker(markerId);

        // Pan map to marker position
        this._instance.map.panTo(marker.position);

        // Keep the party going
        return this;
    },

    // ========================================================================= //

    // Apply a batch of styles to the map
    styles: function(stylesArray) {

        // Ensure styles are valid
        stylesArray = stylesArray || {};

        // Apply collection of styles
        this._instance.map.setOptions({styles: stylesArray});

        // Keep the party going
        return this;
    },

    // Zoom map to specified level
    zoom: function(level) {

        // Ensure level is valid
        level = level || 4;

        // Set zoom level of current map
        this._instance.map.setZoom(level);

        // Keep the party going
        return this;
    },

    // Fit map according to bounds
    fit: function() {

        // Fit bounds of current map
        this._instance.map.fitBounds(this._instance.bounds);

        // Keep the party going
        return this;
    },

    // Refresh the map
    refresh: function() {

        // Refresh the current map
        google.maps.event.trigger(this._instance.map, 'resize');

        // Keep the party going
        return this;
    },

    // PROBABLY SOME USEFUL STUFF IN HERE?

    // // Fit map according to bounds
    // fit_OLD: function(options) {
    //
    //     /*
    //      * NOTE: Zoom & center values are required
    //      * to render a map without fitting bounds.
    //      */
    //
    //     // Ensure options are valid
    //     options = options || {};
    //
    //     // Get map data
    //     var map = this.getMap(options.id);
    //
    //     // If no map exists, bail
    //     if (!map) {
    //         return;
    //     }
    //
    //     // Get map data
    //     var data = map._instance;
    //
    //     // If neither zoom nor center was specified
    //     if (!options.zoom && !options.center) {
    //         // Just fit to boundaries and bail
    //         data.map.fitBounds(data.bounds);
    //         return;
    //     }
    //
    //     // Set fallback zoom and center
    //     options.zoom = options.zoom || 4;
    //     options.center = options.center || data.bounds.getCenter();
    //
    //     // Center and zoom map
    //     data.map.setCenter(options.center);
    //     data.map.setZoom(options.zoom);
    //
    // },

    // ========================================================================= //

    // Get a specified map object
    getMap: function(mapId) {
        this._instance = this._maps[mapId];
        return this;
    },

    // Get a specified marker object
    getMarker: function(markerId) {
        var mapId = this._instance.id;
        return this._markers[mapId][markerId];
    },

    // ========================================================================= //

    // Create a new map object
    _createMap: function(container, options) {

        // Get the specified map ID
        var mapId = options.id;

        // Ensure mapOptions are valid
        options.mapOptions = options.mapOptions || {};

        // Initialize map data
        var data = {
            id: mapId,
            container: container,
            map: new google.maps.Map(container, options.mapOptions),
            bounds: new google.maps.LatLngBounds(),
            markers: []
        }

        // Add map internally
        this._instance = data;

        // Add map externally
        this._maps[mapId] = data;

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


        // TEMP
        // TODO: COMPILE `markerId` IN PHP
        // var elementId = 16;
        // var fieldHandle = 'address';
        // var markerId = `${elementId}-${fieldHandle}`; // 16-address
        //
        // If no marker ID exists, generate a random one:
        // "marker-{random}"
        // ENDTEMP


        // Get map ID
        var mapId = this._instance.id;

        // Get marker ID or generate a random one
        var markerId = options.id || this._generateId('marker');

        // // Ensure map is accounted for
        // if (!this._instance) {
        //     console.warn(`[GM] Unable to attach marker "${markerId}" to map "${mapId}".`);
        //     return;
        // }

        // Initialize marker object
        var marker = new google.maps.Marker(options);

        // Add marker internally
        this._instance.markers[markerId] = marker;

        // Add marker externally
        this._markers[mapId] = this._markers[mapId] || {};
        this._markers[mapId][markerId] = marker;

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

// Google Maps plugin JS object
window.googleMaps = {

    // Logs progress to console (enabled in devMode)
    _log: false,

    // Initialize collections
    _maps: {},
    _markers: {},
    _kmls: {},

    // Initialize empty defaults
    _default: {
        zoom: 4,
        center: null,
        markerOptions: {},
        infoWindowOptions: {},
    },

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

        // Fit map to marker boundaries
        this.fit();

        // Pass object to nested functions
        var mapObject = this;

        // Wait for fitbounds to finish
        google.maps.event.addListenerOnce(this._instance.map, 'bounds_changed', function() {

            // Optionally zoom the map
            if (options.zoom) {
                mapObject.zoom(options.zoom);
            }

            // Optionally center the map
            if (options.center) {
                mapObject.center(options.center);
            }

        });

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

    kml: function(url, options) {

        // Ensure options are valid
        options = options || {};

        // Apply file URL
        options.url = options.url || url;

        // Set map
        options.map = this._instance.map;

        // Create a new KML layer
        this._createKml(url, options);

        // Keep the party going
        return this;
    },

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

        // Log status
        if (this._log) {
            console.log(`Rendered map "${this._instance.id}"`);
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

    // Set the icon of an existing marker
    setMarkerIcon: function(markerId, icon) {

        // Get specified marker
        var marker = this.getMarker(markerId);

        // Set marker icon
        marker.setIcon(icon);

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

        // Log status
        if (this._log) {
            console.log(`Setting map "${this._instance.id}" styles:`, stylesArray);
        }

        // Apply collection of styles
        this._instance.map.setOptions({styles: stylesArray});

        // Keep the party going
        return this;
    },

    // Zoom map to specified level
    zoom: function(level) {

        // Ensure level is valid
        level = level || this._default.zoom;

        // Log status
        if (this._log) {
            console.log(`Setting map "${this._instance.id}" zoom level:`, level);
        }

        // Set zoom level of current map
        this._instance.map.setZoom(level);

        // Update default zoom level
        this._default.zoom = level;

        // Keep the party going
        return this;
    },

    // Center the map on a set of coordinates
    center: function(coords) {

        // Ensure coordinates are valid
        coords = coords || this._default.center || this._instance.bounds.getCenter();

        // Log status
        if (this._log) {
            console.log(`Setting map "${this._instance.id}" center coordinates:`, coords);
        }

        // Re-center current map
        this._instance.map.setCenter(coords);

        // Update default center coordinates
        this._default.center = coords;

        // Keep the party going
        return this;
    },

    // Fit map according to bounds
    fit: function() {

        // Log status
        if (this._log) {
            console.log(`Fitting map "${this._instance.id}" to existing marker boundaries`);
        }

        // Fit bounds of current map
        this._instance.map.fitBounds(this._instance.bounds);

        // Keep the party going
        return this;
    },

    // Refresh the map
    refresh: function() {

        // Log status
        if (this._log) {
            console.log(`Refreshing map "${this._instance.id}"`);
        }

        // Refresh the current map
        google.maps.event.trigger(this._instance.map, 'resize');

        // Keep the party going
        return this;
    },

    // ========================================================================= //

    // Get a specified map object
    getMap: function(mapId) {

        // Log status
        if (this._log) {
            console.log(`Getting existing map "${mapId}"`);
        }

        // Load the specified map
        this._instance = this._maps[mapId];

        // Return map
        return this;
    },

    // Get a specified marker object
    getMarker: function(markerId) {

        // Get the current map ID
        var mapId = this._instance.id;

        // Log status
        if (this._log) {
            console.log(`Getting existing marker "${markerId}" from map "${mapId}"`);
        }

        // Return marker
        return this._markers[mapId][markerId];
    },

    // ========================================================================= //

    // Create a new map object
    _createMap: function(container, options) {

        // Get the specified map ID
        var mapId = options.id;

        // Log status
        if (this._log) {
            console.log(`Creating map "${mapId}"`);
        }

        // Ensure mapOptions are valid
        options.mapOptions = options.mapOptions || {};

        // Initialize map data
        var data = {
            id: mapId,
            container: container,
            map: new google.maps.Map(container, options.mapOptions),
            bounds: new google.maps.LatLngBounds(),
            markers: [],
            kmls: []
        }

        // Add map internally
        this._instance = data;

        // Add map externally
        this._maps[mapId] = data;

    },

    // Create a new marker object
    _createMarker: function(coords, options) {

        // Get map ID
        var mapId = this._instance.id;

        // If marker ID is hiding in coordinates, use it
        if (coords.hasOwnProperty('id')) {
            options.id = coords.id;
        }

        // Get marker ID or generate a random one
        var markerId = options.id || this._generateId('marker');

        // Log status
        if (this._log) {
            console.log(`Adding to map "${mapId}", marker "${markerId}"`);
        }

        // Set marker position based on coordinates
        options.position = coords;

        // Extend map boundaries
        this._instance.bounds.extend(coords);

        // Initialize marker object
        var marker = new google.maps.Marker(options);

        // Add marker internally
        this._instance.markers[markerId] = marker;

        // Add marker externally
        this._markers[mapId] = this._markers[mapId] || {};
        this._markers[mapId][markerId] = marker;

    },

    // Create a new KML layer
    _createKml: function(url, options) {

        // Get map ID
        var mapId = this._instance.id;

        // Get KML ID or generate a random one
        var kmlId = options.id || this._generateId('kml');

        // Log status
        if (this._log) {
            console.log(`Adding to map "${mapId}", KML layer "${kmlId}"`);
        }

        // Initialize KML object
        var kml = new google.maps.KmlLayer(options);

        // Add KML internally
        this._instance.kmls[kmlId] = kml;

        // Add KML externally
        this._kmls[mapId] = this._kmls[mapId] || {};
        this._kmls[mapId][kmlId] = kml;

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

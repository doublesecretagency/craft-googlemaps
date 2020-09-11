// Load AJAX library
var ajax = window.superagent;

// Google Maps plugin JS object
window.googleMaps = {


    // Initialize collection of maps
    maps: {},

    // Create a new internal map object
    _createMap: function(mapId, container, options) {

        // Initialize internal map object
        this.maps[mapId] = {
            map: new google.maps.Map(container, options),
            markers: {},
            bounds: new google.maps.LatLngBounds()
        }

    },

    // Create a new map object
    map: function(mapOptions, locations, markerOptions) {

        // Set default values
        mapOptions.id = mapOptions.id || 'gm-map-1';
        mapOptions.zoom = mapOptions.zoom || null;


        var mapId = mapOptions.id; // TEMP


        // Get map container
        var container = document.getElementById('googleMap');

        // Create a new Google Map object
        this._createMap(mapId, container, mapOptions);

        // If locations were specified, add markers
        if (locations) {
            this.markers(locations, markerOptions);
        }

        // If no zoom specified, fit according to bounds
        if (!mapOptions.zoom) {
            this.fitBounds(mapId);
        }

        // Keep the party going
        return this;
    },

    // Create a set of marker objects
    markers: function(locations, options) {

        var mapId = options.mapId || 'gm-map-1'; // TEMP

        // Array.isArray([1, 2, 3]);  // true
        // Array.isArray({foo: 123}); // false


        // Invalid locations, move along
        if (!Array.isArray(locations)) {
            return this;
        }

        // Loop through all specified locations
        for (var i in locations) {

            // Get individual coordinates
            var coords = locations[i];

            // Create a marker on specified map
            this._renderMarker(mapId, coords, markerOptions);

        }

        // Keep the party going
        return this;
    },

    // kml: function() {
    //     // Do whatever
    //     return this;
    // },

    // Automatically fit map according to bounds
    fitBounds: function(mapId) {
        var map = this.maps[mapId];
        map.map.fitBounds(map.bounds);
    },



    // Get a specified map object
    getMap: function(mapId) {
        return this.maps[mapId].map;
    },
    // Get a specified marker object
    getMarker: function(mapId, markerId) {
        return this.maps[mapId].markers[markerId];
    },










    // ========================================================================= //
    // ========================================================================= //
    // ========================================================================= //



    // Default action url
    actionUrl: '/actions/',
    // No CSRF token by default
    csrfToken: false,
    // Initialize specified maps
    init: function (selection) {
        // Initialize
        var dna;
        // Get selected map containers
        var containers = this._whichMaps(selection);
        // Loop through containers
        for (var i in containers) {
            // Get map DNA
            dna = JSON.parse(containers[i].dataset.dna);
            // Render the map
            this._renderMap(dna.map);
            // Render the markers
            this._renderMarkers(dna.markers);
        }
    },
    // Submit AJAX with fresh CSRF token
    getCsrf: function (callback) {
        // Make object available to callback
        var that = this;
        // Fetch a new CSRF token
        ajax
            .get(this.actionUrl+'google-maps/page/csrf')
            .end(function(err, res){
                // If something went wrong, bail
                if (!res.ok) {
                    console.log('Error retrieving CSRF token:', err);
                    return;
                }
                // Set global CSRF token
                that.csrfToken = res.body;
                // Run callback
                callback();
            })
        ;
    },
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
                var c;
                // Add map container to collection
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
    // Render a specific map
    _renderMap: function (dna) {

        var mapId = dna.id;

        var container = document.getElementById(mapId);
        var height = dna.height || 400;

        // Configure map container
        container.style.display = 'block';
        container.style.height = `${height}px`;

        // Optionally set width of container
        if (dna.width) {
            container.style.width = `${dna.width}px`;
        }

        // Initialize internal map object
        this.maps[mapId] = {
            map: null,
            markers: {},
            bounds: new google.maps.LatLngBounds()
        }



        // PARSE OUT THE `dna` VALUE TO CONFIGURE THE MAP.
        // LOOP OVER THE MARKERS TO CONFIGURE EACH ONE.


        // ========================================== //
        // TEMP

        var mapOptions = {
            center: {lat: 33.397, lng: -118.644},
            zoom: dna.zoom || null
        };

        // ENDTEMP
        // ========================================== //


        // Initialize map
        this._createMap(mapId, container, mapOptions);

        // If no zoom specified, fit according to bounds
        if (!mapOptions.zoom) {
            this.fitBounds(mapId);
        }


    },
    // Render a group of markers
    _renderMarkers: function (dna, options) {

        console.table(dna);


        // Loop through markers
        for (var i in dna) {
            // Get marker DNA
            marker = dna[i];
            // Render the map
            this._renderMarker(mapId, marker.coords, options);
        }
    },
    // Render a specific marker
    _renderMarker: function (coords, options) {

        // Get map ID from marker options
        var mapId = options.mapId;


        console.log('did we make it here?');


        // If coordinates are not valid, bail
        // object.hasOwnProperty('lat')

        // TEMP
        var elementId = 16;
        var fieldHandle = 'address';
        // ENDTEMP


        // Set marker ID
        var markerId = `${elementId}.${fieldHandle}`;

        // Ensure options are valid
        options = options || {};

        // Set map and marker position
        options.map = options.map || this.getMap(mapId);
        options.position = options.position || coords;

        // Put a new marker on the map
        this.maps[mapId].markers[markerId] = new google.maps.Marker(options);

        // Extend bounds
        this.maps[mapId].bounds.extend(coords);

    },
};

// ========================================================================= //

// On page load, initialize all maps on the page
addEventListener('load', function () {
    googleMaps.init();
});

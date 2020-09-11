// Load AJAX library
var ajax = window.superagent;

// Google Maps plugin JS object
window.googleMaps = {


    // Initialize collection of maps
    maps: {},

    // Internal instance of object
    instance: null,

    // Create a new internal map object
    _createMap: function(mapId, container, options) {

        // Initialize internal map object
        this.instance = this.maps[mapId] = {
            map: new google.maps.Map(container, options),
            markers: {},
            bounds: new google.maps.LatLngBounds()
        }

    },

    // Create a new map object
    map: function(locations, options) {

        // Set default values
        options.id = options.id || 'gm-map-1';
        options.zoom = options.zoom || null;


        var mapId = options.id; // TEMP





        // Get map container
        var container = document.getElementById(mapId);

        // Ensure height property exists // TODO: Should this be removed? Set height via CSS?
        var height = options.height || 400;

        // Configure map container
        container.style.display = 'block';
        container.style.height = `${height}px`;

        // Optionally set width of container
        if (options.width) {
            container.style.width = `${options.width}px`;
        }

        // Create a new Google Map object
        this._createMap(mapId, container, options);

        // If locations were specified, add markers
        if (locations) {
            this.markers(locations);
        }


        console.table(locations);
        console.table(options);


        // If no zoom specified, fit according to bounds
        // if (!options.zoom) {
            this.fitBounds(mapId);
        // }

        // Keep the party going
        return this;
    },

    // Create a set of marker objects
    markers: function(locations, options) {

        // Force array structure
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

            // Create a marker on specified map
            this._renderMarker(coords, options);

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
            this._unpackDna(dna);
            // // Render the map
            // this._renderMap(dna.map);
            // // Render the markers
            // this._renderMarkers(dna.markers);
        }
    },
    // Unpack and initialize map DNA
    _unpackDna: function (dna) {

        // If no DNA exists, error and bail
        if (!dna) {
            console.error('No map DNA provided.');
            return;
        }

        // Get map DNA
        var map = dna[0];

        // If first block is not a map, error and bail
        if ('map' !== map.type) {
            console.error('Map DNA is misconfigured.');
            return;
        }

        // this._renderMap(map);

        // Render a map from DNA, store internally
        this.map(map.locations, map.options);


        // console.table(dna);
        // console.log(this.instance);
    },
    // Render a specific map
    _renderMap: function (mapDna) {

        // var mapId = mapDna.map.id;

        // var container = document.getElementById(mapId);

        if (!container) {
            console.error(`Unable to find map container "${mapDna.map.id}".`);
        }




        // Initialize internal map object
        this.maps[mapId] = {
            map: null,
            markers: {},
            bounds: new google.maps.LatLngBounds()
        }



        // PARSE OUT THE `mapDna` VALUE TO CONFIGURE THE MAP.
        // LOOP OVER THE MARKERS TO CONFIGURE EACH ONE.


        // ========================================== //
        // TEMP

        var mapOptions = {
            center: {lat: 33.397, lng: -118.644},
            zoom: mapDna.zoom || null
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

        // console.table(dna);


        // Loop through markers
        for (var i in dna) {
            // Get marker DNA
            marker = dna[i];
            // Render the map
            this._renderMarker(marker.coords, options);
        }
    },
    // Render a specific marker
    _renderMarker: function (coords, options) {

        // Get the map ID
        var mapId = this.instance.map.id;

        // console.log('did we make it here?');


        // console.log(coords);
        // debugger;


        // TEMP
        var elementId = 16;
        var fieldHandle = 'address';
        // ENDTEMP


        // Set marker ID
        var markerId = `${elementId}.${fieldHandle}`;

        // Ensure options are valid
        options = options || {};

        // Set map
        options.map = this.instance.map;

        // Set position
        // options.map = options.map || this.getMap(mapId);
        options.position = options.position || coords;




        // console.log(coords);
        // console.log(options);
        // console.log(this.instance.markers);









        // Put a new marker on the map
        this.maps[mapId].markers[markerId] = new google.maps.Marker(options);

        // this.instance.markers.push(this.maps[mapId].markers[markerId]);

        // Extend bounds
        // this.maps[mapId].bounds.extend(coords);
        this.instance.bounds.extend(coords);

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
};

// ========================================================================= //

// On page load, initialize all maps on the page
addEventListener('load', function () {
    googleMaps.init();
});

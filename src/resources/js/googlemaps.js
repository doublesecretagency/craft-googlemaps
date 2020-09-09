// Load AJAX library
var ajax = window.superagent;

// Google Maps plugin JS object
window.googleMaps = {


    // Initialize collection of maps
    maps: {},

    testMapId: 'gm-map-1',

    // Create a new map object
    map: function(mapOptions, locations, markerOptions) {

        // Set default values
        mapOptions.zoom = mapOptions.zoom || 5;



        var mapId = this.testMapId; // TEMP



        // Initialize internal map object
        this.maps[mapId] = {
            map: null,
            markers: [],
            bounds: new google.maps.LatLngBounds()
        }

        // Get map container
        var container = document.getElementById('googleMap');

        // Initialize map
        this.maps[mapId].map = new google.maps.Map(container,mapOptions);

        // If locations were specified, add markers
        if (locations) {
            this.markers(locations, markerOptions);
        }

        // Fit according to bounds
        this.fitBounds(mapId);

        // Keep the party going
        return this;
    },

    // Create a set of marker objects
    markers: function(locations, options) {

        var mapId = this.testMapId; // TEMP

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

            // object.hasOwnProperty('lat')

            // var markerOptions = options['markerOptions'];
            var markerOptions = {
                position: coords,
                map: this.maps[mapId].map
            };

            // Extend bounds
            this.maps[mapId].bounds.extend(coords);

            // Put a new marker on the map
            var marker = new google.maps.Marker(markerOptions);

            // Add to marker collection
            this.maps[mapId].markers.push(marker);

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
        this.maps[mapId].map.fitBounds(this.maps[mapId].bounds);
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
            this._renderMap(dna);
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

        var container = document.getElementById(dna.id);
        var height = dna.height || 400;

        // Set height of map
        container.setAttribute('style','display:block; height:'+height+'px');
        container.style.height = height+'px';



        // PARSE OUT THE `dna` VALUE TO CONFIGURE THE MAP.
        // LOOP OVER THE MARKERS TO CONFIGURE EACH ONE.


        // ========================================== //
        // TEMP

        var coords = {lat: 33.397, lng: -118.644};

        var map = new google.maps.Map(container, {
            center: coords,
            zoom: 8
        });

        // Put a new marker on the map
        var marker = new google.maps.Marker({
            position: coords,
            map: map
        });

        // ENDTEMP
        // ========================================== //




        console.table(dna);

    },
};

// ========================================================================= //

// On page load, initialize all maps on the page
addEventListener('load', function () {
    googleMaps.init();
});

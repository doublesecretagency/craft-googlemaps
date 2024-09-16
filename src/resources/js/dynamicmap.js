// Dynamic Map model for Google Maps plugin
function DynamicMap(locations, options) {

    // Safely omit both parameters
    locations = locations || [];
    options = options || {};

    // Initialize properties
    this.id = null;
    this.div = null;
    this._map = null;

    // Initialize collections
    this._markers = {};
    this._infoWindows = {};
    this._circles = {};
    this._kmls = {};

    // Initialize marker cluster
    this._cluster = false;

    // Initialize defaults
    this._d = {};

    // Set a comfortable default zoom level
    this._comfortableZoom = 11;

    // ========================================================================= //

    // Create a new map object
    this.__construct = function(locations, options) {

        // Ensure options are valid
        options = options || {};

        // If no map ID was specified, generate one
        this.id = options.id || this._generateId('map');

        // Get map container
        this.div = document.getElementById(options.id);

        // If container does not exist
        if (!this.div) {
            // Create new container from scratch
            this.div = document.createElement('div');
        }

        // Configure map container
        this.div.id = this.id;
        this.div.classList.add('gm-map');
        this.div.style.display = 'block';

        // Set defaults (with fallbacks)
        this._d.zoom              = options.zoom              || null;
        this._d.center            = options.center            || null;
        this._d.markerOptions     = options.markerOptions     || {};
        this._d.circleOptions     = options.circleOptions     || {};
        this._d.infoWindowOptions = options.infoWindowOptions || {};
        this._d.markerLink        = options.markerLink        || null;
        this._d.markerClick       = options.markerClick       || null;

        // Internalize clustering preference
        this._cluster = options.cluster || false;

        // Optionally set container height
        if (options.height) {
            this.div.style.height = `${options.height}px`;
        }

        // Optionally set width of container
        if (options.width) {
            this.div.style.width = `${options.width}px`;
        }

        // Create a new Google Map object
        this._createMap(options.mapOptions || {});

        // Optionally set styles of map
        if (options.styles) {
            this.styles(options.styles);
        }

        // If locations were specified
        if (locations && locations.length > 0) {
            // Add markers (using default markerOptions & infoWindowOptions)
            this.markers(locations);
        }
    };

    // ========================================================================= //

    // Add a set of markers to the map
    this.markers = function(locations, options) {

        // If no locations, bail
        if (!locations) {
            return;
        }

        // Ensure options are valid
        options = options || {};

        // Ensure marker options are valid
        options.markerOptions = options.markerOptions || this._d.markerOptions;

        // Ensure info window options are valid
        options.infoWindowOptions = options.infoWindowOptions || this._d.infoWindowOptions;

        // Ensure marker link is valid
        options.markerLink = options.markerLink || this._d.markerLink;

        // Ensure marker click event is valid
        options.markerClick = options.markerClick || this._d.markerClick;

        // Set map
        options.markerOptions.map = this._map;

        // If icon specified, copy into markerOptions
        if (options.icon) {
            options.markerOptions.icon = options.icon;
        }

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

            // Get marker ID or generate a random one
            var markerId = options.id || coords.id || this._generateId('marker');

            // Set marker ID back to coordinates object
            coords.id = markerId;

            // Create a new marker
            this._createMarker(coords, options.markerOptions);

            // If content provided, create a new info window
            if (options.infoWindowOptions.content) {
                this._initInfoWindow(coords.id, options.infoWindowOptions);
            }

            // If callback is a boolean true
            if (true === options.markerClick) {
                // Suppress click and link
                options.markerClick = false;
                options.markerLink = false;
            }

            // If click event specified
            if (options.markerClick) {

                // Attach listener for specified function
                this._initMarkerClick(markerId, options.markerClick);

            // Else if marker link specified and valid
            } else if (options.markerLink && 'string' === typeof options.markerLink) {

                // Attach listener which directs to specified link
                this._initMarkerClick(markerId, function () {
                    window.location.href = options.markerLink;
                });

            }

        }

        // Keep the party going
        return this;
    };

    // Add a set of circles to the map
    this.circles = function(locations, options) {

        // If no locations, bail
        if (!locations) {
            return;
        }
        // Ensure options are valid
        options = options || {};

        // Ensure circle options are valid
        options.circleOptions = options.circleOptions || this._d.circleOptions;

        // Ensure radius is valid (default to 50 kilometers)
        options.circleOptions.radius = options.circleOptions.radius || 50000;

        // Set map
        options.circleOptions.map = this._map;

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

            // Get circle ID or generate a random one
            var circleId = options.id || coords.id || this._generateId('circle');

            // Set circle ID back to coordinates object
            coords.id = circleId;

            // Create a new circle
            this._createCircle(coords, options.circleOptions);
        }

        // Keep the party going
        return this;
    };

    // Add a KML layer to the map
    this.kml = function(url, options) {

        // Ensure options are valid
        options = options || {};

        // Apply file URL
        options.url = options.url || url;

        // Set map
        options.map = this._map;

        // Create a new KML layer
        this._createKml(url, options);

        // Keep the party going
        return this;
    };

    // ========================================================================= //

    // Apply a batch of styles to the map
    this.styles = function(styleSet) {

        // Ensure styles are valid
        styleSet = styleSet || {};

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Styling map`);
        }

        // Apply collection of styles
        this._map.setOptions({styles: styleSet});

        // Keep the party going
        return this;
    };

    // Zoom map to specified level
    this.zoom = function(level, assumeSuccess) {

        // Ensure level is valid
        level = level || this._d.zoom;

        // Update default zoom level
        this._d.zoom = level;

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Zooming map to level`, level);
        }

        // Pass object to callback function
        var mapObject = this;

        // Set zoom level of current map
        mapObject._map.setZoom(level);

        // Repeat after fitbounds has finished (if it's running)
        google.maps.event.addListenerOnce(this._map, 'bounds_changed', function() {
            mapObject._map.setZoom(level);
        });

        // Keep the party going
        return this;
    };

    // Center the map on a set of coordinates
    this.center = function(coords, assumeSuccess) {

        // Ensure coordinates are valid
        coords = coords
            || this._determineBounds().getCenter()
            || this._d.center;

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Centering map on coordinates`, coords);
        }

        // If coordinates do not exist, emit warning and bail
        if (!coords) {
            console.warn(`[GM] Unable to center map, invalid coordinates:`, coords);
            return this;
        }

        // Ensure coordinates are float values, fallback to zero
        coords.lat = (coords.lat ? parseFloat(coords.lat) : 0);
        coords.lng = (coords.lng ? parseFloat(coords.lng) : 0);

        // Update default center coordinates
        this._d.center = coords;

        // Pass object to callback function
        var mapObject = this;

        // Re-center current map
        mapObject._map.setCenter(coords);

        // Repeat after bounds have changed (if they are still changing)
        google.maps.event.addListenerOnce(mapObject._map, 'bounds_changed', function() {
            mapObject._map.setCenter(coords);
        });

        // Keep the party going
        return this;
    };

    // Fit map according to bounds
    this.fit = function(assumeSuccess) {

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Fitting map to existing boundaries`);
        }

        // Fit bounds of current map
        this._map.fitBounds(this._determineBounds());

        // Get the total number of markers and circles
        const totalMarkers = Object.keys(this._markers).length;
        const totalCircles = Object.keys(this._circles).length;

        // If no markers or circles exist
        if (!totalMarkers && !totalCircles) {
            // Example coordinates
            const zeroZero = {'lat':0,'lng':0};
            // Log error message
            console.error(`[GM] No items on the map, it will be centered in the middle of the ocean! 🐙`, zeroZero);
            // We are in the ocean, zoom out
            this.zoom(2, true);
        }

        // Keep the party going
        return this;
    };

    // Refresh the map
    this.refresh = function() {

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Refreshing map`);
        }

        // Refresh the current map
        google.maps.event.trigger(this._map, 'resize');

        // Keep the party going
        return this;
    };

    // ========================================================================= //

    // Pan map to center on a specific marker
    this.panToMarker = function(markerId) {

        // Get specified marker
        var marker = this.getMarker(markerId);

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Panning to marker "${markerId}"`);
        }

        // If invalid marker, bail
        if (!marker) {
            console.warn(`[GM] Unable to pan to marker "${markerId}"`);
            return this;
        }

        // Pass object to callback function
        var mapObject = this;

        // Pan map to marker position
        mapObject._map.panTo(marker.position);

        // Repeat after bounds have changed (if they are still changing)
        google.maps.event.addListenerOnce(mapObject._map, 'bounds_changed', function() {
            mapObject._map.panTo(marker.position);
        });

        // Keep the party going
        return this;
    };

    // Set the icon of an existing marker
    this.setMarkerIcon = function(markerId, icon, assumeSuccess) {

        // If setting icon for multiple markers
        if (Array.isArray(markerId)) {
            // Log status (if success is not assumed)
            if (googleMaps.log && !assumeSuccess) {
                console.log(`[${this.id}] Setting icon for multiple markers`);
            }
            // Set each marker icon individually
            for (var i in markerId) {
                this.setMarkerIcon(markerId[i], icon);
            }
            // Our work here is done
            return this;
        }

        // If setting icon for all markers
        if ('*' === markerId) {
            // Log status (if success is not assumed)
            if (googleMaps.log && !assumeSuccess) {
                console.log(`[${this.id}] Setting icon for all markers:`, icon);
            }
            // Set each marker icon individually
            for (var key in this._markers) {
                this.setMarkerIcon(key, icon, true);
            }
            // Our work here is done
            return this;
        }

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Setting icon for marker "${markerId}":`, icon);
        }

        // Get specified marker
        var marker = this.getMarker(markerId, true);

        // If invalid marker, bail
        if (!marker) {
            console.warn(`[GM] Unable to set icon of marker "${markerId}"`);
            return this;
        }

        // Set marker icon
        marker.setIcon(icon);

        // Keep the party going
        return this;
    };

    // ========================================================================= //

    // Hide a marker
    this.hideMarker = function(markerId, assumeSuccess) {

        // If hiding multiple markers
        if (Array.isArray(markerId)) {
            // Log status
            if (googleMaps.log) {
                console.log(`[${this.id}] Hiding multiple markers`);
            }
            // Hide each marker individually
            for (var i in markerId) {
                this.hideMarker(markerId[i]);
            }
            // Our work here is done
            return this;
        }

        // If hiding all markers
        if ('*' === markerId) {
            // Log status
            if (googleMaps.log && !assumeSuccess) {
                console.log(`[${this.id}] Hiding all markers`);
            }
            // Hide each marker individually
            for (var key in this._markers) {
                this.hideMarker(key, true);
            }
            // Our work here is done
            return this;
        }

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Hiding marker "${markerId}"`);
        }

        // Get specified marker
        var marker = this.getMarker(markerId);

        // If invalid marker, bail
        if (!marker) {
            console.warn(`[GM] Unable to hide marker "${markerId}"`);
            return this;
        }

        // If clustering
        if (this._cluster) {
            // Remove marker from clusters
            this._cluster.removeMarker(marker);
        } else {
            // Remove marker from map
            marker.setMap(null);
        }

        // Keep the party going
        return this;
    };

    // Show a marker
    this.showMarker = function(markerId, assumeSuccess) {

        // If showing multiple markers
        if (Array.isArray(markerId)) {
            // Log status
            if (googleMaps.log) {
                console.log(`[${this.id}] Showing multiple markers`);
            }
            // Show each marker individually
            for (var i in markerId) {
                this.showMarker(markerId[i]);
            }
            // Our work here is done
            return this;
        }

        // If showing all markers
        if ('*' === markerId) {
            // Log status
            if (googleMaps.log && !assumeSuccess) {
                console.log(`[${this.id}] Showing all markers`);
            }
            // Show each marker individually
            for (var key in this._markers) {
                this.showMarker(key, true);
            }
            // Our work here is done
            return this;
        }

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Showing marker "${markerId}"`);
        }

        // Get specified marker
        var marker = this.getMarker(markerId);

        // If invalid marker, bail
        if (!marker) {
            console.warn(`[GM] Unable to show marker "${markerId}"`);
            return this;
        }

        // If clustering
        if (this._cluster) {
            // Add marker to clusters
            this._cluster.addMarker(marker);
        } else {
            // Add marker directly to map
            marker.setMap(this._map);
        }

        // Keep the party going
        return this;
    };

    // Open an info window
    this.openInfoWindow = function(markerId, assumeSuccess) {

        // If opening multiple info windows
        if (Array.isArray(markerId)) {
            // Log status
            if (googleMaps.log) {
                console.log(`[${this.id}] Opening multiple info windows`);
            }
            // Open each info window individually
            for (var i in markerId) {
                this.openInfoWindow(markerId[i]);
            }
            // Our work here is done
            return this;
        }

        // If opening all info windows
        if ('*' === markerId) {
            // Log status
            if (googleMaps.log && !assumeSuccess) {
                console.log(`[${this.id}] Opening all info windows`);
            }
            // Open each info window individually
            for (var key in this._infoWindows) {
                this.openInfoWindow(key, true);
            }
            // Our work here is done
            return this;
        }

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Opening info window "${markerId}"`);
        }

        // Get marker and info window objects
        var marker     = this.getMarker(markerId, true);
        var infoWindow = this.getInfoWindow(markerId, true);

        // If invalid marker, bail
        if (!marker) {
            console.warn(`[GM] Unable to find marker "${markerId}"`);
            return this;
        }

        // If invalid info window, bail
        if (!infoWindow) {
            console.warn(`[GM] Unable to open info window "${markerId}"`);
            return this;
        }

        // Open the specified info window
        infoWindow.open({
            'map': this._map,
            'anchor': marker
        });

        // Keep the party going
        return this;
    };

    // Close an info window
    this.closeInfoWindow = function(markerId, assumeSuccess) {

        // If closing multiple info windows
        if (Array.isArray(markerId)) {
            // Log status
            if (googleMaps.log) {
                console.log(`[${this.id}] Closing multiple info windows`);
            }
            // Close each info window individually
            for (var i in markerId) {
                this.closeInfoWindow(markerId[i]);
            }
            // Our work here is done
            return this;
        }

        // If closing all info windows
        if ('*' === markerId) {
            // Log status
            if (googleMaps.log && !assumeSuccess) {
                console.log(`[${this.id}] Closing all info windows`);
            }
            // Close each info window individually
            for (var key in this._infoWindows) {
                this.closeInfoWindow(key, true);
            }
            // Our work here is done
            return this;
        }

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Closing info window "${markerId}"`);
        }

        // Get info window object
        var infoWindow = this.getInfoWindow(markerId, true);

        // If invalid info window, bail
        if (!infoWindow) {
            console.warn(`[GM] Unable to close info window "${markerId}"`);
            return this;
        }

        // Close info window
        infoWindow.close();

        // Keep the party going
        return this;
    };

    // Hide a circle
    this.hideCircle = function(circleId, assumeSuccess) {

        // If hiding multiple circles
        if (Array.isArray(circleId)) {
            // Log status
            if (googleMaps.log) {
                console.log(`[${this.id}] Hiding multiple circles`);
            }
            // Hide each circle individually
            for (var i in circleId) {
                this.hideCircle(circleId[i]);
            }
            // Our work here is done
            return this;
        }

        // If hiding all circles
        if ('*' === circleId) {
            // Log status
            if (googleMaps.log && !assumeSuccess) {
                console.log(`[${this.id}] Hiding all circles`);
            }
            // Hide each circle individually
            for (var key in this._circles) {
                this.hideCircle(key, true);
            }
            // Our work here is done
            return this;
        }

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Hiding circle "${circleId}"`);
        }

        // Get specified circle
        var circle = this.getCircle(circleId, true);

        // If invalid circle, bail
        if (!circle) {
            console.warn(`[GM] Unable to hide circle "${circleId}"`);
            return this;
        }

        // Detach circle from map
        circle.setMap(null);

        // Keep the party going
        return this;
    };

    // Show a circle
    this.showCircle = function(circleId, assumeSuccess) {

        // If showing multiple circles
        if (Array.isArray(circleId)) {
            // Log status
            if (googleMaps.log) {
                console.log(`[${this.id}] Showing multiple circles`);
            }
            // Show each circle individually
            for (var i in circleId) {
                this.showCircle(circleId[i]);
            }
            // Our work here is done
            return this;
        }

        // If showing all circles
        if ('*' === circleId) {
            // Log status
            if (googleMaps.log && !assumeSuccess) {
                console.log(`[${this.id}] Showing all circles`);
            }
            // Show each circle individually
            for (var key in this._circles) {
                this.showCircle(key, true);
            }
            // Our work here is done
            return this;
        }

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Showing circle "${circleId}"`);
        }

        // Get specified circle
        var circle = this.getCircle(circleId, true);

        // If invalid circle, bail
        if (!circle) {
            console.warn(`[GM] Unable to show circle "${circleId}"`);
            return this;
        }

        // Attach circle to current map
        circle.setMap(this._map);

        // Keep the party going
        return this;
    };

    // Hide a KML layer
    this.hideKml = function(kmlId, assumeSuccess) {

        // If hiding multiple KML layers
        if (Array.isArray(kmlId)) {
            // Log status
            if (googleMaps.log) {
                console.log(`[${this.id}] Hiding multiple KML layers`);
            }
            // Hide each KML layer individually
            for (var i in kmlId) {
                this.hideKml(kmlId[i]);
            }
            // Our work here is done
            return this;
        }

        // If hiding all KML layers
        if ('*' === kmlId) {
            // Log status
            if (googleMaps.log && !assumeSuccess) {
                console.log(`[${this.id}] Hiding all KML layers`);
            }
            // Hide each KML layer individually
            for (var key in this._kmls) {
                this.hideKml(key, true);
            }
            // Our work here is done
            return this;
        }

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Hiding KML layer "${kmlId}"`);
        }

        // Get specified KML layer
        var kml = this.getKml(kmlId, true);

        // If invalid KML layer, bail
        if (!kml) {
            console.warn(`[GM] Unable to hide KML layer "${kmlId}"`);
            return this;
        }

        // Detach KML layer from map
        kml.setMap(null);

        // Keep the party going
        return this;
    };

    // Show a KML layer
    this.showKml = function(kmlId, assumeSuccess) {

        // If showing multiple KML layers
        if (Array.isArray(kmlId)) {
            // Log status
            if (googleMaps.log) {
                console.log(`[${this.id}] Showing multiple KML layers`);
            }
            // Show each KML layer individually
            for (var i in kmlId) {
                this.showKml(kmlId[i]);
            }
            // Our work here is done
            return this;
        }

        // If showing all KML layers
        if ('*' === kmlId) {
            // Log status
            if (googleMaps.log && !assumeSuccess) {
                console.log(`[${this.id}] Showing all KML layers`);
            }
            // Show each KML layer individually
            for (var key in this._kmls) {
                this.showKml(key, true);
            }
            // Our work here is done
            return this;
        }

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Showing KML layer "${kmlId}"`);
        }

        // Get specified KML layer
        var kml = this.getKml(kmlId, true);

        // If invalid KML layer, bail
        if (!kml) {
            console.warn(`[GM] Unable to show KML layer "${kmlId}"`);
            return this;
        }

        // Attach KML layer to current map
        kml.setMap(this._map);

        // Keep the party going
        return this;
    };

    // ========================================================================= //

    // Get a specific Google Maps marker object
    this.getMarker = function(markerId, assumeSuccess) {

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Getting existing marker "${markerId}"`);
        }

        // Get existing marker
        var marker = this._markers[markerId];

        // If marker does not exist, emit warning
        if (!marker) {
            console.warn(`[GM] Unable to find marker "${markerId}"`);
        }

        // Return marker
        return marker;
    };

    // Get a specific Google Maps info window object
    this.getInfoWindow = function(markerId, assumeSuccess) {

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Getting existing info window "${markerId}"`);
        }

        // Get existing info window
        var infoWindow = this._infoWindows[markerId];

        // If info window does not exist, emit warning
        if (!infoWindow) {
            console.warn(`[GM] Unable to find info window "${markerId}"`);
        }

        // Return info window
        return infoWindow;
    };

    // Get a specific Google Maps circle object
    this.getCircle = function(circleId, assumeSuccess) {

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Getting existing circle "${circleId}"`);
        }

        // Get existing circle
        var circle = this._circles[circleId];

        // If circle does not exist, emit warning
        if (!circle) {
            console.warn(`[GM] Unable to find circle "${circleId}"`);
        }

        // Return circle
        return circle;
    };

    // Get a specific Google Maps KML layer object
    this.getKml = function(kmlId, assumeSuccess) {

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Getting existing KML layer "${kmlId}"`);
        }

        // Get existing KML layer
        var kml = this._kmls[kmlId];

        // If KML layer does not exist, emit warning
        if (!kml) {
            console.warn(`[GM] Unable to find KML layer "${kmlId}"`);
        }

        // Return KML layer
        return kml;
    };

    // Get the internal marker clustering object
    this.getMarkerClusterer = function() {

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Getting the marker clustering object`);
        }

        // Return the MarkerClusterer object
        return this._cluster;
    };

    // Get the current zoom level of the map
    this.getZoom = function(assumeSuccess) {

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Getting the current zoom level of the map`);
        }

        // Return zoom level
        return this._map.getZoom();
    };

    // Get the current center point of the map
    this.getCenter = function(assumeSuccess) {

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Getting the current center point of the map`);
        }

        // Return the center coordinates
        return this._map.getCenter();
    };

    // Get the current bounds of the map
    this.getBounds = function() {

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Getting the current bounds of the map`);
        }

        // Return a pair of bounds coordinates
        return this._map.getBounds();
    };

    // ========================================================================= //

    // Generate a complete map element
    this.tag = function(options) {

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Rendering map`);
        }

        // Ensure options are valid
        options = options || {};

        // Get the ID of the parent container
        var parentId = (options.parentId || null);

        // If no valid parent container specified
        if (!parentId || 'string' !== typeof parentId) {
            // Check to ensure the map is visible
            this._checkMapVisibility();
            // Log status
            if (googleMaps.log) {
                console.log(`[${this.id}] Finished initializing map as a detached element 👍`);
            }
            // Return the element as-is
            return this.div;
        }

        // Get specified parent container
        var parent = document.getElementById(parentId);

        // If parent container exists, populate it
        if (parent) {
            parent.appendChild(this.div);
        } else {
            console.warn(`[GM] Unable to find target container #${parentId}`);
        }

        // Check to ensure the map is visible
        this._checkMapVisibility();

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Finished initializing map in container "${parentId}" 👍`);
        }

        // Return map container
        return this.div;
    };

    // ========================================================================= //

    // Create a new map object
    this._createMap = function(mapOptions) {

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Creating map`);
        }

        // Initialize map data
        this._map = new google.maps.Map(this.div, mapOptions);

        // Optionally cluster markers
        this._clusterMarkers();
    };

    // Create a new marker object
    this._createMarker = function(coords, markerOptions) {

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Adding marker "${coords.id}"`);
        }

        // Set marker position based on coordinates
        markerOptions.position = coords;

        // Initialize marker object
        this._markers[coords.id] = new google.maps.Marker(markerOptions);

        // If clustering markers, add new marker to group
        if (this._cluster) {
            this._cluster.addMarker(this._markers[coords.id]);
        }

    };

    // Create a new circle object
    this._createCircle = function(coords, circleOptions) {

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Adding circle "${coords.id}"`);
        }

        // Set circle center based on coordinates
        circleOptions.center = coords;

        // Initialize circle object
        this._circles[coords.id] = new google.maps.Circle(circleOptions);

    };

    // Create a new KML layer
    this._createKml = function(url, options) {

        // Get KML ID or generate a random one
        var kmlId = options.id || this._generateId('kml');

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Adding KML layer "${kmlId}"`);
        }

        // Initialize KML object
        this._kmls[kmlId] = new google.maps.KmlLayer(options);

    };

    // ========================================================================= //

    // Set info window for each marker
    this._initInfoWindow = function(markerId, infoWindowOptions) {

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Adding info window to marker "${markerId}"`);
        }

        // Get related marker
        var marker = this.getMarker(markerId, true);

        // If no related marker exists, bail
        if (!marker) {
            return;
        }

        // Initialize info window object
        this._infoWindows[markerId] = new google.maps.InfoWindow(infoWindowOptions);

        // Pass map object into event listener
        var map = this;

        // Add click event to marker
        google.maps.event.addListener(marker, 'click', function() {
            // Close all info windows
            map.closeInfoWindow('*', true);
            // Open info window for this marker
            map.openInfoWindow(markerId);
        });

    };

    // Set click event for each marker
    this._initMarkerClick = function(markerId, callback) {

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Adding click event callback to marker "${markerId}":`, callback);
        }

        // Get related marker
        var marker = this.getMarker(markerId, true);

        // If no related marker exists, bail
        if (!marker) {
            return;
        }

        // If callback is a boolean true, bail
        if (true === callback) {
            return;
        }

        // If callback is not a function, warn and bail
        if ('function' !== typeof callback) {
            console.warn('[GM] Invalid callback function:', callback);
            return;
        }

        // When marker is clicked, trigger the callback
        google.maps.event.addListener(marker, 'click', callback);

    };

    // ========================================================================= //

    // Get the functional boundaries of the current map
    this._determineBounds = function() {

        // Create a set of map bounds
        var bounds = new google.maps.LatLngBounds();

        // Initialize loop variables
        var key, marker, circle, cluster;

        // Loop through all markers
        for (key in this._markers) {
            // Get each marker
            marker = this._markers[key];
            // If marker is not tied to a map, skip it
            if (null === marker.map) {
                continue;
            }
            // Extend map boundaries to include marker
            bounds.extend(marker.getPosition());
        }

        // Loop through all circles
        for (key in this._circles) {
            // Get each circle
            circle = this._circles[key];
            // If circle is not tied to a map, skip it
            if (null === circle.map) {
                continue;
            }
            // Extend map boundaries to include circle
            bounds.extend(circle.getCenter());
        }

        // If map uses clustering
        if (this._cluster) {
            // Loop through all clusters
            for (key in this._cluster.clusters) {
                // Get each cluster
                cluster = this._cluster.clusters[key];
                // Join map boundaries with cluster boundaries
                bounds.union(cluster.bounds);
            }
        }

        // Return a set of map bounds
        return bounds;
    };

    // Optionally apply marker clustering
    this._clusterMarkers = function() {

        // Whether to use default clustering
        const clusterDefault = (true === this._cluster);

        // Get clustering options data
        let _cluster = (window._gmData ? window._gmData.cluster : []);

        // Get custom Twig/PHP options, fallback to null
        const twigOptions = _cluster[this.id] || null;

        // If provided, get options from Twig/PHP data
        if (clusterDefault && twigOptions) {
            this._cluster = twigOptions;
        }

        // Whether to use custom clustering
        const clusterCustom = ('object' === typeof this._cluster);

        // If not clustering (neither default nor custom), bail
        if (!clusterDefault && !clusterCustom) {
            return;
        }

        // Set clustering options
        const options = {
            'map': this._map,
            'markers': [],
        };

        // If customized clustering
        if (clusterCustom) {
            // If algorithm specified, add to options
            if (this._cluster.algorithm || null) {
                options.algorithm = this._cluster.algorithm;
            }
            // If renderer specified, add to options
            if (this._cluster.renderer || null) {
                options.renderer = this._cluster.renderer;
            }
            // If onClusterClick specified, add to options
            if (this._cluster.onClusterClick || null) {
                options.onClusterClick = this._cluster.onClusterClick;
            }
        }

        // If no onClusterClick defined, use default (native) function
        const onClusterClick =
            options.onClusterClick ||
            markerClusterer.defaultOnClusterClickHandler;

        // Set parent map object
        const parent = this;

        // Wrap onClusterClick with a safeguard function
        options.onClusterClick = function (event, cluster, map) {
            // Log status
            if (googleMaps.log) {
                console.log(`[${parent.id}] Opening cluster of ${cluster.markers.length} markers`);
            }
            // After a brief pause
            setTimeout(() => {
                // Close all info windows
                parent.closeInfoWindow('*', true);
            }, 100);
            // Run original onClusterClick function
            onClusterClick(event, cluster, map);
        }

        // Create the marker clustering object
        this._cluster = new markerClusterer.MarkerClusterer(options);
    };

    // ========================================================================= //

    // Check to ensure the map is visible, emit warnings if necessary
    this._checkMapVisibility = function() {
        // Check the container height
        this._checkHeight();
        // Prevent the map from appearing as a grey box
        this._preventGreyBox();
    };

    // Check the container height, emit warning if necessary
    this._checkHeight = function() {

        // If not logging, skip check
        if (!googleMaps.log) {
            return;
        }

        // Get current height of container div
        var height = this.div.clientHeight;

        // If height is a positive number, check is successful
        if (0 < height) {
            return;
        }

        // Zero pixels tall, emit warning
        var url = 'https://plugins.doublesecretagency.com/google-maps/guides/setting-map-height/';
        console.warn(`[GM] The map is not visible because its parent container is zero pixels tall. More info: ${url}`);

    };

    // Prevent the map from appearing as a grey box, emit warnings if necessary
    this._preventGreyBox = function() {

        // Get current zoom & center
        let zoom   = this.getZoom(true);
        let center = this.getCenter(true);

        // Fallback to default values
        zoom   = zoom   || this._d.zoom;
        center = center || this._d.center;

        // If center was specified
        if (center) {

            // Get coordinates
            let lat = center.lat();
            let lng = center.lng();

            // Sanitize center
            center = {
                'lat': (isNaN(lat) ? 0 : lat),
                'lng': (isNaN(lng) ? 0 : lng)
            }

            // Center on specified coordinates
            this.center(center);

            // If no zoom specified
            if (!zoom) {
                // Set to a comfortable zoom level
                zoom = this._comfortableZoom;
            }

        } else {

            // Fit to the existing items
            this.fit();

            // Get the total number of markers and circles
            const totalMarkers = Object.keys(this._markers).length;
            const totalCircles = Object.keys(this._circles).length;

            // Total number of items on the map
            const total = totalMarkers + totalCircles;

            // If only one item and no zoom specified
            if ((1 === total) && !zoom) {
                // Set to a comfortable zoom level
                zoom = this._comfortableZoom;
            }

        }

        // If level was specified, zoom
        if (zoom) {
            this.zoom(zoom);
        }

    };

    // ========================================================================= //

    // Generate a random ID
    this._generateId = function(prefix) {

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
    };

    // Prepare the object
    this.__construct(locations, options);

}

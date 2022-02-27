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
    this._kmls = {};

    // Initialize marker cluster
    this._cluster = false;

    // Initialize defaults
    this._d = {};

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
        this._d.zoom              = options.zoom              || 4;
        this._d.center            = options.center            || null;
        this._d.markerOptions     = options.markerOptions     || {};
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
            // Fit map to marker boundaries
            this.fit();
        }

        // Optionally zoom the map
        if (options.zoom) {
            this.zoom(options.zoom);
        }

        // Optionally center the map
        if (options.center) {
            this.center(options.center);
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
        options.markerOptions = options.markerOptions
            || this._d.markerOptions
            || {};

        // Ensure info window options are valid
        options.infoWindowOptions = options.infoWindowOptions
            || this._d.infoWindowOptions
            || {};

        // Ensure marker link is valid
        options.markerLink = options.markerLink
            || this._d.markerLink
            || null;

        // Ensure marker click event is valid
        options.markerClick = options.markerClick
            || this._d.markerClick
            || null;

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
                this._createInfoWindow(markerId, options.infoWindowOptions);
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
                this._markerClick(markerId, options.markerClick);

            // Else if marker link specified and valid
            } else if (options.markerLink && 'string' === typeof options.markerLink) {

                // Attach listener which directs to specified link
                this._markerClick(markerId, function () {
                    window.location.href = options.markerLink;
                });

            }

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
            console.log(`Styling map "${this.id}"`);
        }

        // Apply collection of styles
        this._map.setOptions({styles: styleSet});

        // Keep the party going
        return this;
    };

    // Zoom map to specified level
    this.zoom = function(level) {

        // Ensure level is valid
        level = level
            || this._d.zoom
            || 4;

        // Log status
        if (googleMaps.log) {
            console.log(`Zooming map "${this.id}" to level`, level);
        }

        // Pass object to callback function
        var mapObject = this;

        // Set zoom level of current map
        mapObject._map.setZoom(level);

        // Repeat after fitbounds has finished (if it's running)
        google.maps.event.addListenerOnce(this._map, 'bounds_changed', function() {
            mapObject._map.setZoom(level);
        });

        // Update default zoom level
        this._d.zoom = level;

        // Keep the party going
        return this;
    };

    // Center the map on a set of coordinates
    this.center = function(coords) {

        // Ensure coordinates are valid
        coords = coords
            || this._getBounds().getCenter()
            || this._d.center
            || null;

        // Log status
        if (googleMaps.log) {
            console.log(`Centering map "${this.id}" on coordinates`, coords);
        }

        // Pass object to callback function
        var mapObject = this;

        // Re-center current map
        mapObject._map.setCenter(coords);

        // Repeat after bounds have changed (if they are still changing)
        google.maps.event.addListenerOnce(mapObject._map, 'bounds_changed', function() {
            mapObject._map.setCenter(coords);
        });

        // Update default center coordinates
        this._d.center = coords;

        // Keep the party going
        return this;
    };

    // Fit map according to bounds
    this.fit = function() {

        // Log status
        if (googleMaps.log) {
            console.log(`Fitting map "${this.id}" to existing boundaries`);
        }

        // Fit bounds of current map
        this._map.fitBounds(this._getBounds());

        // Keep the party going
        return this;
    };

    // Refresh the map
    this.refresh = function() {

        // Log status
        if (googleMaps.log) {
            console.log(`Refreshing map "${this.id}"`);
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
            console.log(`On map "${this.id}", panning to marker "${markerId}"`);
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

    // Open the info window of a specific marker
    this.openInfoWindow = function(markerId) {

        // Get marker and info window objects
        var marker     = this.getMarker(markerId, true);
        var infoWindow = this.getInfoWindow(markerId, true);

        // Log status
        if (googleMaps.log) {
            console.log(`On map "${this.id}", opening info window "${markerId}"`);
        }

        // Open the specified info window
        infoWindow.open(this._map, marker);

        // Keep the party going
        return this;
    };

    // Close the info window of a specific marker
    this.closeInfoWindow = function(markerId, assumeSuccess) {

        // If closing all info windows
        if ('*' === markerId) {

            // Log status
            if (googleMaps.log) {
                console.log(`On map "${this.id}", closing all info windows`);
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
            console.log(`On map "${this.id}", closing info window "${markerId}"`);
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

    // Set the icon of an existing marker
    this.setMarkerIcon = function(markerId, icon) {

        // Log status
        if (googleMaps.log) {
            console.log(`On map "${this.id}", setting icon for marker "${markerId}":`, icon);
        }

        // Get specified marker
        var marker = this.getMarker(markerId);

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

    // Hide a marker
    this.hideMarker = function(markerId, assumeSuccess) {

        // If hiding all markers
        if ('*' === markerId) {

            // Log status
            if (googleMaps.log) {
                console.log(`On map "${this.id}", hiding all markers`);
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
            console.log(`On map "${this.id}", hiding marker "${markerId}"`);
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

        // If showing all markers
        if ('*' === markerId) {

            // Log status
            if (googleMaps.log) {
                console.log(`On map "${this.id}", showing all markers`);
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
            console.log(`On map "${this.id}", showing marker "${markerId}"`);
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

    // ========================================================================= //

    // Hide a KML layer
    this.hideKml = function(kmlId, assumeSuccess) {

        // If hiding all KML layers
        if ('*' === kmlId) {

            // Log status
            if (googleMaps.log) {
                console.log(`On map "${this.id}", hiding all KML layers`);
            }

            // Detach each KML layer from this map
            for (var key in this._kmls) {
                this.hideKml(key, true);
            }

            // Our work here is done
            return this;
        }

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`On map "${this.id}", hiding KML layer "${kmlId}"`);
        }

        // Get specified KML layer
        var kml = this.getKml(kmlId);

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

        // If showing all KML layers
        if ('*' === kmlId) {

            // Log status
            if (googleMaps.log) {
                console.log(`On map "${this.id}", showing all KML layers`);
            }

            // Attach each KML layer to this map
            for (var key in this._kmls) {
                this.showKml(key, true);
            }

            // Our work here is done
            return this;
        }

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`On map "${this.id}", showing KML layer "${kmlId}"`);
        }

        // Get specified KML layer
        var kml = this.getKml(kmlId);

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
            console.log(`From map "${this.id}", getting existing marker "${markerId}"`);
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
            console.log(`From map "${this.id}", getting existing info window "${markerId}"`);
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

    // Get a specific Google Maps KML layer object
    this.getKml = function(kmlId, assumeSuccess) {

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`From map "${this.id}", getting existing KML layer "${kmlId}"`);
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
            console.log(`From map "${this.id}", getting the marker clustering object`);
        }

        // Return the MarkerClusterer object
        return this._cluster;
    };

    // ========================================================================= //

    // Generate a complete map element
    this.tag = function(options) {

        // Log status
        if (googleMaps.log) {
            console.log(`Rendering map "${this.id}"`);
        }

        // Get the ID of the parent container
        var parentId = (options.parentId || null);

        // If no valid parent container specified
        if (!parentId || 'string' !== typeof parentId) {

            // Log status
            if (googleMaps.log) {
                console.log(`Finished initializing map "${this.id}" as a detached element 👍`);
            }

            // Check the container height
            this._checkHeight();

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

        // Log status
        if (googleMaps.log) {
            console.log(`Finished initializing map "${this.id}" in container "${parentId}" 👍`);
        }

        // Check the container height
        this._checkHeight();

        // Return map container
        return this.div;
    };

    // ========================================================================= //

    // Create a new map object
    this._createMap = function(mapOptions) {

        // Log status
        if (googleMaps.log) {
            console.log(`Creating map "${this.id}"`);
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
            console.log(`On map "${this.id}", adding marker "${coords.id}"`);
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

    // ========================================================================= //

    // Set click event for each marker
    this._markerClick = function(markerId, callback) {

        // Log status
        if (googleMaps.log) {
            console.log(`On marker "${markerId}", adding click event callback:`, callback);
        }

        // Get related marker
        var marker = this._markers[markerId];

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

    // Create a new info window object
    this._createInfoWindow = function(markerId, infoWindowOptions) {

        // Log status
        if (googleMaps.log) {
            console.log(`On map "${this.id}", adding info window "${markerId}"`);
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
            map.closeInfoWindow('*');
            // Open info window for this marker
            map.openInfoWindow(markerId);
        });

    };

    // Create a new KML layer
    this._createKml = function(url, options) {

        // Get KML ID or generate a random one
        var kmlId = options.id || this._generateId('kml');

        // Log status
        if (googleMaps.log) {
            console.log(`On map "${this.id}", adding KML layer "${kmlId}"`);
        }

        // Initialize KML object
        this._kmls[kmlId] = new google.maps.KmlLayer(options);

    };

    // ========================================================================= //

    // Get the functional boundaries of the current map
    this._getBounds = function() {

        // Create a set of map bounds
        var bounds = new google.maps.LatLngBounds();

        // Initialize loop variables
        var key, marker, cluster;

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

    // ========================================================================= //

    // Optionally apply marker clustering
    this._clusterMarkers = function() {

        // Whether to use default clustering
        const clusterDefault = (true === this._cluster);

        // Get custom Twig/PHP options
        const twigOptions = googleMaps._cluster[this.id];

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
            'markers':  [],
        };

        // If customized clustering
        if (clusterCustom) {
            // If algorithm specified, add to options
            if (this._cluster.algorithm ?? null) {
                options.algorithm = this._cluster.algorithm;
            }
            // If renderer specified, add to options
            if (this._cluster.renderer ?? null) {
                options.renderer = this._cluster.renderer;
            }
            // If onClusterClick specified, add to options
            if (this._cluster.onClusterClick ?? null) {
                options.onClusterClick = this._cluster.onClusterClick;
            }
        }

        // Create the marker clustering object
        this._cluster = new markerClusterer.MarkerClusterer(options);
    };

    // ========================================================================= //

    // Check the container height, emit warning if necessary
    this._checkHeight = function() {

        // If not logging, skip check
        if (!googleMaps.log) {
            return;
        }

        // Get current height of container div
        var height = this.div.scrollHeight;

        // If height is a positive number, check is successful
        if (0 < height) {
            return;
        }

        // Zero pixels tall, emit warning
        var url = 'https://plugins.doublesecretagency.com/google-maps/guides/setting-map-height/';
        console.warn(`[GM] The map is not visible because its parent container is zero pixels tall. More info: ${url}`);

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

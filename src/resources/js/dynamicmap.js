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
    this._geoJsons = {};
    this._gpxs = {};
    this._groundOverlays = {};
    this._datasets = {};
    this._boundaries = {};

    // Initialize ground overlays found inside data layers
    this._layerOverlays = {};

    // Initialize styles of datasets and boundaries
    this._featureStyles = {};

    // Initialize the info window shared by all layers
    this._layerInfoWindow = null;

    // Initialize the Map ID (required for datasets and boundaries)
    this._mapId = null;

    // Configure each type of layer
    this._layerTypes = {
        'kml':           {'store': '_kmls',           'label': 'KML layer',      'plural': 'KML layers',      'prefix': 'kml'},
        'geoJson':       {'store': '_geoJsons',       'label': 'GeoJSON layer',  'plural': 'GeoJSON layers',  'prefix': 'geojson'},
        'gpx':           {'store': '_gpxs',           'label': 'GPX layer',      'plural': 'GPX layers',      'prefix': 'gpx'},
        'groundOverlay': {'store': '_groundOverlays', 'label': 'ground overlay', 'plural': 'ground overlays', 'prefix': 'ground-overlay'},
        'dataset':       {'store': '_datasets',       'label': 'dataset',        'plural': 'datasets',        'prefix': 'dataset'},
        'boundaries':    {'store': '_boundaries',     'label': 'boundary layer', 'plural': 'boundary layers', 'prefix': 'boundaries'},
    };

    // Default style of datasets and boundaries
    this._featureDefaults = {
        'strokeColor': '#1a73e8',
        'strokeOpacity': 1,
        'strokeWeight': 2,
        'fillColor': '#1a73e8',
        'fillOpacity': 0.3,
        'pointRadius': 6,
    };

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

        // Get the map container, or the one prepared while waiting for the API
        this.div = options._div || document.getElementById(options.id);

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

            // If an error is present, show in console
            if (coords.error){
                console.error(`[GM] ${coords.error}`);
            }

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

    // Add a KML (or KMZ) layer to the map
    this.kml = function(url, options) {

        // If no URL, bail
        if (!url) {
            return this;
        }

        // Ensure options are valid
        options = options || {};

        // Merge any nested KML layer options
        options = Object.assign({}, options, options.kmlLayerOptions || {});

        // Create a new KML layer
        this._createDataLayer('kml', url, options, () => this._loadKml(url));

        // Keep the party going
        return this;
    };

    // Add a GeoJSON layer to the map
    this.geoJson = function(source, options) {

        // If no source, bail
        if (!source) {
            return this;
        }

        // Ensure options are valid
        options = options || {};

        // Whether the source is a URL
        var isUrl = ('string' === typeof source && !this._isJson(source));

        // Create a new GeoJSON layer
        this._createDataLayer('geoJson', (isUrl ? source : 'inline data'), options, () => this._loadGeoJson(source));

        // Keep the party going
        return this;
    };

    // Add a GPX layer to the map
    this.gpx = function(url, options) {

        // If no URL, bail
        if (!url) {
            return this;
        }

        // Ensure options are valid
        options = options || {};

        // Create a new GPX layer
        this._createDataLayer('gpx', url, options, () => this._loadGpx(url));

        // Keep the party going
        return this;
    };

    // Add a ground overlay (an image placed on the map) to the map
    this.groundOverlay = function(url, bounds, options) {

        // If no URL or bounds, bail
        if (!url || !bounds) {
            return this;
        }

        // Ensure options are valid
        options = options || {};

        // Get ground overlay ID or generate a random one
        var overlayId = options.id || this._generateId('ground-overlay');

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Adding ground overlay "${overlayId}"`);
        }

        // Create a new ground overlay
        this._groundOverlays[overlayId] = this._createGroundOverlay(url, bounds, options, this._map);

        // Keep the party going
        return this;
    };

    // Add a dataset (uploaded to Google Cloud) to the map
    this.dataset = function(datasetId, options) {

        // If no dataset ID, bail
        if (!datasetId) {
            return this;
        }

        // Ensure options are valid
        options = options || {};

        // Get layer ID, fallback to the dataset ID
        var layerId = options.id || datasetId;

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Adding dataset "${layerId}"`);
        }

        // Check whether the map can show datasets
        this._checkDataDrivenStyling();

        // Get the dataset layer
        var layer = this._map.getDatasetFeatureLayer(datasetId);

        // Store the dataset layer
        this._datasets[layerId] = layer;

        // Store the style of the dataset
        this._featureStyles[`dataset:${layerId}`] = {
            'style': options.style || {},
            'visible': true,
        };

        // Style the dataset
        this._applyFeatureStyle('dataset', layerId);

        // If info windows are suppressed, bail
        if (options.suppressInfoWindows || false === options.clickable) {
            return this;
        }

        // When a feature is clicked, open an info window
        layer.addListener('click', (event) => {
            // Get the clicked feature
            var feature = (event.features || [])[0];
            // Get the attributes of that feature
            var attributes = (feature ? feature.datasetAttributes : null) || {};
            // Open the info window
            this._openLayerInfoWindow(attributes.name, attributes.description, event.latLng);
        });

        // Keep the party going
        return this;
    };

    // Add boundaries (countries, states, cities, etc.) to the map
    this.boundaries = function(featureType, placeIds, options) {

        // If no feature type or Place IDs, bail
        if (!featureType || !placeIds) {
            return this;
        }

        // Ensure options are valid
        options = options || {};

        // Ensure the feature type matches Google's constants
        featureType = String(featureType).toUpperCase();

        // Force Place IDs to be an array structure
        if (!Array.isArray(placeIds)) {
            placeIds = [placeIds];
        }

        // Get layer ID, fallback to the feature type
        var layerId = options.id || featureType;

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Adding boundaries "${layerId}"`);
        }

        // Check whether the map can show boundaries
        this._checkDataDrivenStyling();

        // Store the boundary layer
        this._boundaries[layerId] = this._map.getFeatureLayer(featureType);

        // Store the style of the boundaries
        this._featureStyles[`boundaries:${layerId}`] = {
            'style': options.style || {},
            'visible': true,
            'featureType': featureType,
            'placeIds': placeIds,
        };

        // Style the boundaries
        this._applyFeatureStyle('boundaries', layerId);

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
        coords = coords || this._d.center;

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

        // Get bounds
        var bounds = this._determineBounds();

        // If bounds are empty
        if (bounds.isEmpty()) {
            // Example coordinates
            const zeroZero = {'lat':0,'lng':0};
            // Log error message
            console.error(`[GM] No items on the map, it will be centered in the middle of the ocean! 🐙`, zeroZero);
            // Center the map
            this.center(zeroZero, true);
            // We are in the ocean, zoom out
            this.zoom(2, true);
            // Bail
            return this;
        }

        // If bounds are just a single point
        if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
            // Center the map
            this.center(bounds.getCenter().toJSON(), true);
            // Quietly zoom to a comfortable level
            this.zoom(this._comfortableZoom, true);
            // Bail
            return this;
        }

        // Fit bounds of current map
        this._map.fitBounds(bounds);

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
        // Hide the specified KML layer
        return this._toggleLayer('kml', kmlId, false, assumeSuccess);
    };

    // Show a KML layer
    this.showKml = function(kmlId, assumeSuccess) {
        // Show the specified KML layer
        return this._toggleLayer('kml', kmlId, true, assumeSuccess);
    };

    // Hide a GeoJSON layer
    this.hideGeoJson = function(geoJsonId, assumeSuccess) {
        // Hide the specified GeoJSON layer
        return this._toggleLayer('geoJson', geoJsonId, false, assumeSuccess);
    };

    // Show a GeoJSON layer
    this.showGeoJson = function(geoJsonId, assumeSuccess) {
        // Show the specified GeoJSON layer
        return this._toggleLayer('geoJson', geoJsonId, true, assumeSuccess);
    };

    // Hide a GPX layer
    this.hideGpx = function(gpxId, assumeSuccess) {
        // Hide the specified GPX layer
        return this._toggleLayer('gpx', gpxId, false, assumeSuccess);
    };

    // Show a GPX layer
    this.showGpx = function(gpxId, assumeSuccess) {
        // Show the specified GPX layer
        return this._toggleLayer('gpx', gpxId, true, assumeSuccess);
    };

    // Hide a ground overlay
    this.hideGroundOverlay = function(overlayId, assumeSuccess) {
        // Hide the specified ground overlay
        return this._toggleLayer('groundOverlay', overlayId, false, assumeSuccess);
    };

    // Show a ground overlay
    this.showGroundOverlay = function(overlayId, assumeSuccess) {
        // Show the specified ground overlay
        return this._toggleLayer('groundOverlay', overlayId, true, assumeSuccess);
    };

    // Hide a dataset
    this.hideDataset = function(datasetId, assumeSuccess) {
        // Hide the specified dataset
        return this._toggleLayer('dataset', datasetId, false, assumeSuccess);
    };

    // Show a dataset
    this.showDataset = function(datasetId, assumeSuccess) {
        // Show the specified dataset
        return this._toggleLayer('dataset', datasetId, true, assumeSuccess);
    };

    // Hide a boundary layer
    this.hideBoundaries = function(boundariesId, assumeSuccess) {
        // Hide the specified boundary layer
        return this._toggleLayer('boundaries', boundariesId, false, assumeSuccess);
    };

    // Show a boundary layer
    this.showBoundaries = function(boundariesId, assumeSuccess) {
        // Show the specified boundary layer
        return this._toggleLayer('boundaries', boundariesId, true, assumeSuccess);
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

    // Get a specific KML layer (google.maps.Data)
    this.getKml = function(kmlId, assumeSuccess) {
        // Return the specified KML layer
        return this._getLayer('kml', kmlId, assumeSuccess);
    };

    // Get a specific GeoJSON layer (google.maps.Data)
    this.getGeoJson = function(geoJsonId, assumeSuccess) {
        // Return the specified GeoJSON layer
        return this._getLayer('geoJson', geoJsonId, assumeSuccess);
    };

    // Get a specific GPX layer (google.maps.Data)
    this.getGpx = function(gpxId, assumeSuccess) {
        // Return the specified GPX layer
        return this._getLayer('gpx', gpxId, assumeSuccess);
    };

    // Get a specific ground overlay (google.maps.GroundOverlay)
    this.getGroundOverlay = function(overlayId, assumeSuccess) {
        // Return the specified ground overlay
        return this._getLayer('groundOverlay', overlayId, assumeSuccess);
    };

    // Get a specific dataset (google.maps.FeatureLayer)
    this.getDataset = function(datasetId, assumeSuccess) {
        // Return the specified dataset
        return this._getLayer('dataset', datasetId, assumeSuccess);
    };

    // Get a specific boundary layer (google.maps.FeatureLayer)
    this.getBoundaries = function(boundariesId, assumeSuccess) {
        // Return the specified boundary layer
        return this._getLayer('boundaries', boundariesId, assumeSuccess);
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

        // If the API hasn't loaded yet, explain how to wait for it
        if (!window.google?.maps?.Map) {
            throw new Error('[GM] The Google Maps API is not ready yet. Wrap JS-only maps in googleMaps.ready()');
        }

        // Get the Map ID, if one was specified
        this._mapId = mapOptions.mapId || null;

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

    // Create a new data layer (KML, GeoJSON, or GPX)
    this._createDataLayer = function(type, source, options, load) {

        // Get the layer configuration
        var config = this._layerTypes[type];

        // Get layer ID or generate a random one
        var layerId = options.id || this._generateId(config.prefix);

        // Log status
        if (googleMaps.log) {
            console.log(`[${this.id}] Adding ${config.label} "${layerId}"`);
        }

        // Create the layer right away, so it can be hidden while the file loads
        var data = new google.maps.Data({'map': this._map});

        // Store the layer
        this[config.store][layerId] = data;

        // Style each feature (a function style is used as-is)
        data.setStyle('function' === typeof options.style ? options.style : (feature) => {
            return this._dataLayerStyle(feature, type, options);
        });

        // If info windows are allowed
        if (!options.suppressInfoWindows && false !== options.clickable) {
            // When a feature is clicked, open an info window (GPX files call the description "desc")
            data.addListener('click', (event) => {
                var feature = event.feature;
                this._openLayerInfoWindow(feature.getProperty('name'), feature.getProperty('description') || feature.getProperty('desc'), event.latLng);
            });
        }

        // Load the features
        load().then((geoJson) => {

            // Pull out any ground overlays
            geoJson = this._extractGroundOverlays(type, layerId, geoJson, data.getMap());

            // Add the features to the layer
            data.addGeoJson(geoJson);

            // Log status
            if (googleMaps.log) {
                console.log(`[${this.id}] Loaded ${config.label} "${layerId}"`);
            }

            // If the viewport is preserved or the layer is hidden, bail
            if (options.preserveViewport || !data.getMap()) {
                return;
            }

            // Fit the map to the layer
            this._fitLayer(this._extendBounds(new google.maps.LatLngBounds(), type, layerId));

        }).catch((error) => {
            // Explain why the layer couldn't be loaded
            this._layerError(config.label, source, error);
        });
    };

    // Get the style of a single feature in a data layer
    this._dataLayerStyle = function(feature, type, options) {

        // Initialize the style, with a visible line for bare GPX tracks
        var style = ('gpx' === type ? {'strokeColor': '#1a73e8', 'strokeOpacity': 0.9, 'strokeWeight': 4} : {});

        // Get the styles carried by the file itself
        var fromFile = {
            'strokeColor':   feature.getProperty('stroke'),
            'strokeOpacity': feature.getProperty('stroke-opacity'),
            'strokeWeight':  feature.getProperty('stroke-width'),
            'fillColor':     feature.getProperty('fill'),
            'fillOpacity':   feature.getProperty('fill-opacity'),
            'icon':          feature.getProperty('icon'),
        };

        // Loop through the styles from the file
        for (var key in fromFile) {
            // If the file sets this style, use it
            if (undefined !== fromFile[key] && null !== fromFile[key]) {
                style[key] = fromFile[key];
            }
        }

        // If a z-index was specified, apply it
        if (undefined !== options.zIndex) {
            style.zIndex = options.zIndex;
        }

        // If clicks are disabled, make the feature unclickable
        if (false === options.clickable) {
            style.clickable = false;
        }

        // Return the style, with any custom styles on top
        return Object.assign(style, options.style || {});
    };

    // Create a new ground overlay object
    this._createGroundOverlay = function(url, bounds, options, map) {
        return new google.maps.GroundOverlay(url, bounds, {
            'opacity': (undefined !== options.opacity ? options.opacity : 1),
            'clickable': (false !== options.clickable),
            'map': map,
        });
    };

    // Pull ground overlays out of a KML file, and create them separately
    this._extractGroundOverlays = function(type, layerId, geoJson, map) {

        // Initialize the ground overlays of this layer
        var overlays = this._layerOverlays[`${type}:${layerId}`] = [];

        // If there are no features, bail
        if (!geoJson || !Array.isArray(geoJson.features)) {
            return geoJson;
        }

        // Keep every feature except the ground overlays
        geoJson.features = geoJson.features.filter((feature) => {

            // Get the feature properties
            var properties = feature.properties || {};

            // If not a ground overlay, keep it
            if ('groundoverlay' !== properties['@geometry-type']) {
                return true;
            }

            // Get the boundaries of the image [west, south, east, north]
            var box = feature.bbox || this._geometryBox(feature.geometry);

            // If the image and its boundaries exist, create the ground overlay
            if (properties.icon && box) {
                overlays.push(this._createGroundOverlay(properties.icon, {
                    'west': box[0], 'south': box[1], 'east': box[2], 'north': box[3],
                }, {}, map));
            }

            // Remove it from the data layer
            return false;
        });

        // Return the remaining features
        return geoJson;
    };

    // ========================================================================= //

    // Load a KML or KMZ file, and convert it to GeoJSON
    this._loadKml = async function(url) {

        // Get the raw file
        var buffer = await this._fetchFile(url, 'arrayBuffer');

        // Get the bytes of the file
        var bytes = new Uint8Array(buffer);

        // If it's a KMZ (zip) file, unpack it, otherwise read it as text
        var text = (0x50 === bytes[0] && 0x4B === bytes[1])
            ? await this._unzipKmz(buffer)
            : new TextDecoder().decode(bytes);

        // Parse the KML
        var xml = this._parseXml(text);

        // Loop through KML elements which can't be shown
        ['NetworkLink', 'ScreenOverlay', 'PhotoOverlay', 'Model'].forEach((tag) => {
            // If the element exists, explain that it was skipped
            if (xml.getElementsByTagName(tag).length) {
                console.warn(`[GM] KML <${tag}> isn't supported, so it was skipped in "${url}"`);
            }
        });

        // Load the conversion library
        await googleMaps._loadLibrary('togeojson');

        // Return the converted file
        return this._resolveIcons(toGeoJSON.kml(xml, {'skipNullGeometry': true}), url);
    };

    // Unpack the KML file (and images) inside a KMZ file
    this._unzipKmz = async function(buffer) {

        // Load the unzip library
        await googleMaps._loadLibrary('jszip');

        // Open the zip file
        var zip = await JSZip.loadAsync(buffer);

        // Get the main KML file (usually "doc.kml")
        var kmlName = zip.file('doc.kml') ? 'doc.kml' : Object.keys(zip.files).find((name) => /\.kml$/i.test(name));

        // If no KML file was found, throw an error
        if (!kmlName) {
            throw new Error('The KMZ file does not contain a KML file');
        }

        // Get the KML text
        var text = await zip.file(kmlName).async('string');

        // Loop through each image packed inside the KMZ
        for (var name of Object.keys(zip.files).filter((name) => /\.(png|jpe?g|gif|svg|webp|bmp)$/i.test(name))) {
            // Point the KML to a temporary URL of the image
            text = text.split(name).join(URL.createObjectURL(await zip.file(name).async('blob')));
        }

        // Return the KML text
        return text;
    };

    // Load a GPX file, and convert it to GeoJSON
    this._loadGpx = async function(url) {

        // Parse the GPX file
        var xml = this._parseXml(await this._fetchFile(url, 'text'));

        // Load the conversion library
        await googleMaps._loadLibrary('togeojson');

        // Return the converted file
        return toGeoJSON.gpx(xml);
    };

    // Load GeoJSON from a URL, a JSON string, or an object
    this._loadGeoJson = async function(source) {

        // If an object or array, use it directly
        if ('string' !== typeof source) {
            return this._toFeatureCollection(source);
        }

        // If a JSON string, parse it
        if (this._isJson(source)) {
            return this._toFeatureCollection(JSON.parse(source));
        }

        // Return the file from the URL
        return this._resolveIcons(this._toFeatureCollection(await this._fetchFile(source, 'json')), source);
    };

    // Fetch a file, flagging the reason it failed
    this._fetchFile = async function(url, format) {

        // Initialize the response
        var response;

        // Request the file
        try {
            response = await fetch(url);
        } catch (error) {
            // The browser blocked it (usually CORS)
            error.blocked = true;
            throw error;
        }

        // If the server responded with an error, throw it
        if (!response.ok) {
            var error = new Error(`HTTP ${response.status}`);
            error.status = response.status;
            throw error;
        }

        // Return the file in the specified format
        return response[format]();
    };

    // Parse an XML string
    this._parseXml = function(text) {

        // Parse the XML
        var xml = new DOMParser().parseFromString(text, 'text/xml');

        // If the XML is invalid, throw an error
        if (xml.getElementsByTagName('parsererror').length) {
            throw new Error('The file is not valid XML');
        }

        // Return the XML document
        return xml;
    };

    // Point relative icon paths to the folder of the file they came from
    this._resolveIcons = function(geoJson, url) {

        // Get the full URL of the file
        var base = new URL(url, window.location.href).href;

        // Loop through each feature
        (geoJson.features || []).forEach((feature) => {

            // Get the icon of the feature
            var icon = (feature.properties || {}).icon;

            // If no icon, or the icon is already a full URL, skip it
            if (!icon || 'string' !== typeof icon || /^(https?:|blob:|data:|\/\/)/i.test(icon)) {
                return;
            }

            // Resolve the icon against the file URL
            feature.properties.icon = new URL(icon, base).href;
        });

        // Return the GeoJSON
        return geoJson;
    };

    // Wrap any GeoJSON in a feature collection
    this._toFeatureCollection = function(geoJson) {

        // If an array of features, wrap it in a collection
        if (Array.isArray(geoJson)) {
            return {'type': 'FeatureCollection', 'features': geoJson};
        }

        // If a bare geometry, wrap it in a feature
        if (geoJson && geoJson.coordinates) {
            return {'type': 'Feature', 'geometry': geoJson, 'properties': {}};
        }

        // Return it as-is
        return geoJson;
    };

    // Whether a string contains JSON (rather than a URL)
    this._isJson = function(string) {
        return /^\s*[\[{]/.test(string);
    };

    // Get the boundaries [west, south, east, north] of a GeoJSON geometry
    this._geometryBox = function(geometry) {

        // Initialize the boundaries
        var box = [Infinity, Infinity, -Infinity, -Infinity];

        // Extend the boundaries to include every coordinate
        (function walk(coords) {
            // If a single coordinate, extend the boundaries
            if ('number' === typeof coords[0]) {
                box = [Math.min(box[0], coords[0]), Math.min(box[1], coords[1]), Math.max(box[2], coords[0]), Math.max(box[3], coords[1])];
                return;
            }
            // Loop through nested coordinates
            coords.forEach(walk);
        })((geometry || {}).coordinates || []);

        // Return the boundaries (if any were found)
        return (Infinity === box[0] ? null : box);
    };

    // Explain why a layer couldn't be loaded
    this._layerError = function(label, source, error) {

        // Get the help URL
        var url = 'https://plugins.doublesecretagency.com/google-maps/guides/kml-layers/#files-on-another-domain';

        // If the browser blocked the request
        if (error.blocked) {
            console.error(`[GM] Unable to load ${label} "${source}". If the file is on another domain, that server must allow cross-origin requests (CORS). More info: ${url}`);
            return;
        }

        // If the server responded with an error
        if (error.status) {
            console.error(`[GM] Unable to load ${label} "${source}" (HTTP ${error.status})`);
            return;
        }

        // Anything else
        console.error(`[GM] Unable to read ${label} "${source}":`, error);
    };

    // ========================================================================= //

    // Hide or show a layer of any type
    this._toggleLayer = function(type, layerId, visible, assumeSuccess) {

        // Get the layer configuration
        var config = this._layerTypes[type];

        // Get the action (for logging)
        var action = (visible ? 'Showing' : 'Hiding');

        // If toggling multiple layers
        if (Array.isArray(layerId)) {
            // Log status
            if (googleMaps.log) {
                console.log(`[${this.id}] ${action} multiple ${config.plural}`);
            }
            // Toggle each layer individually
            for (var i in layerId) {
                this._toggleLayer(type, layerId[i], visible);
            }
            // Our work here is done
            return this;
        }

        // If toggling all layers
        if ('*' === layerId) {
            // Log status
            if (googleMaps.log && !assumeSuccess) {
                console.log(`[${this.id}] ${action} all ${config.plural}`);
            }
            // Toggle each layer individually
            for (var key in this[config.store]) {
                this._toggleLayer(type, key, visible, true);
            }
            // Our work here is done
            return this;
        }

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] ${action} ${config.label} "${layerId}"`);
        }

        // Get specified layer
        var layer = this[config.store][layerId];

        // If invalid layer, bail
        if (!layer) {
            console.warn(`[GM] Unable to ${visible ? 'show' : 'hide'} ${config.label} "${layerId}"`);
            return this;
        }

        // If a dataset or boundaries, toggle its style (they can't be detached)
        if (this._featureStyles[`${type}:${layerId}`]) {
            this._featureStyles[`${type}:${layerId}`].visible = visible;
            this._applyFeatureStyle(type, layerId);
            return this;
        }

        // Attach or detach the layer
        layer.setMap(visible ? this._map : null);

        // Loop through any ground overlays found inside the layer
        (this._layerOverlays[`${type}:${layerId}`] || []).forEach((overlay) => {
            // Attach or detach each ground overlay
            overlay.setMap(visible ? this._map : null);
        });

        // Keep the party going
        return this;
    };

    // Get a layer of any type
    this._getLayer = function(type, layerId, assumeSuccess) {

        // Get the layer configuration
        var config = this._layerTypes[type];

        // Log status (if success is not assumed)
        if (googleMaps.log && !assumeSuccess) {
            console.log(`[${this.id}] Getting existing ${config.label} "${layerId}"`);
        }

        // Get existing layer
        var layer = this[config.store][layerId];

        // If layer does not exist, emit warning
        if (!layer) {
            console.warn(`[GM] Unable to find ${config.label} "${layerId}"`);
        }

        // Return layer
        return layer;
    };

    // ========================================================================= //

    // Apply the current style of a dataset or boundaries
    this._applyFeatureStyle = function(type, layerId) {

        // Get the stored style
        var entry = this._featureStyles[`${type}:${layerId}`];

        // If boundaries, restyle every boundary of the same type together (they share one layer)
        if ('boundaries' === type) {
            this._styleBoundaries(entry.featureType);
            return;
        }

        // Get the dataset style, with the defaults underneath
        var style = ('function' === typeof entry.style)
            ? entry.style
            : () => Object.assign({}, this._featureDefaults, entry.style);

        // Show the dataset with its style, or hide it with none
        this._datasets[layerId].style = (entry.visible ? style : null);
    };

    // Style every visible boundary of one feature type
    this._styleBoundaries = function(featureType) {

        // Initialize the style of each Place ID
        var styles = {};

        // Loop through every stored style
        for (var key in this._featureStyles) {

            // Get each stored style
            var entry = this._featureStyles[key];

            // If not a visible boundary of this type, skip it
            if (!entry.visible || featureType !== entry.featureType) {
                continue;
            }

            // Loop through each Place ID of the boundary
            entry.placeIds.forEach((placeId) => {
                // Set the style of the Place ID, with the defaults underneath
                styles[placeId] = Object.assign({}, this._featureDefaults, entry.style);
            });
        }

        // Get the boundary layer for this feature type
        var layer = this._map.getFeatureLayer(featureType);

        // Style only the specified Place IDs, or nothing at all
        layer.style = (Object.keys(styles).length ? (params) => (styles[params.feature.placeId] || null) : null);
    };

    // Warn (once per map) if datasets and boundaries can't be shown
    this._checkDataDrivenStyling = function() {

        // If already checked, bail
        if (this._checkedDataDrivenStyling) {
            return;
        }

        // Only check once
        this._checkedDataDrivenStyling = true;

        // Get the help URL
        var url = 'https://plugins.doublesecretagency.com/google-maps/guides/datasets-and-boundaries/';

        // If no Map ID was specified, warn and bail
        if (!this._mapId) {
            console.warn(`[GM] Datasets and boundaries require a vector Map ID. More info: ${url}`);
            return;
        }

        // Get the map details (for the check below)
        var map = this._map;
        var mapId = this._mapId;

        // Whether the check is complete
        var checked = false;

        // Check the map capabilities
        var check = function() {

            // If already checked, bail
            if (checked) {
                return;
            }

            // Mark the check as complete
            checked = true;

            // If data-driven styling is available, bail
            if (map.getMapCapabilities().isDataDrivenStylingAvailable) {
                return;
            }

            // Explain what's wrong
            console.warn(`[GM] Map ID "${mapId}" can't show datasets or boundaries. Make sure it's a vector map, and that its map style has the right feature layers turned on. More info: ${url}`);
        };

        // Check once Google reports what the Map ID can do (it isn't accurate until then)
        google.maps.event.addListenerOnce(map, 'mapcapabilities_changed', check);

        // If Google already reported it, check after a short wait
        setTimeout(check, 10000);
    };

    // ========================================================================= //

    // Open the info window shared by all layers
    this._openLayerInfoWindow = function(name, description, position) {

        // If there's nothing to show, bail
        if (!name && !description) {
            return;
        }

        // If the info window doesn't exist yet, create it
        if (!this._layerInfoWindow) {
            this._layerInfoWindow = new google.maps.InfoWindow();
        }

        // Create the content container
        var content = document.createElement('div');

        // If a name was provided
        if (name) {
            // Add the name as plain text
            var heading = document.createElement('strong');
            heading.textContent = name;
            content.appendChild(heading);
        }

        // If a description was provided
        if (description) {
            // Add the description, with anything unsafe removed
            var body = document.createElement('div');
            body.innerHTML = this._sanitizeHtml(String(description.value || description));
            content.appendChild(body);
        }

        // Fill and open the info window
        this._layerInfoWindow.setContent(content);
        this._layerInfoWindow.setPosition(position);
        this._layerInfoWindow.open({'map': this._map});
    };

    // Remove scripts and event handlers from HTML (descriptions can come from other sites)
    this._sanitizeHtml = function(html) {

        // Parse the HTML without running it
        var doc = new DOMParser().parseFromString(html, 'text/html');

        // Remove any elements which can run code
        doc.querySelectorAll('script, style, iframe, frame, object, embed, link, meta, base, form').forEach((element) => {
            element.remove();
        });

        // Loop through every remaining element
        doc.body.querySelectorAll('*').forEach((element) => {

            // Loop through each attribute
            Array.from(element.attributes).forEach((attribute) => {

                // Get the attribute name and value
                var name = attribute.name.toLowerCase();
                var value = attribute.value.replace(/\s/g, '').toLowerCase();

                // If an event handler, a frame source, or a script URL, remove it
                if (name.startsWith('on') || 'srcdoc' === name || value.startsWith('javascript:')) {
                    element.removeAttribute(attribute.name);
                }
            });
        });

        // Return the safe HTML
        return doc.body.innerHTML;
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

        // Loop through each type of layer with known boundaries
        ['kml', 'geoJson', 'gpx', 'groundOverlay'].forEach((type) => {
            // Loop through each layer of that type
            for (var layerId in this[this._layerTypes[type].store]) {
                // Extend map boundaries to include the layer
                this._extendBounds(bounds, type, layerId);
            }
        });

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

    // Extend a set of bounds to include a visible layer (only what has loaded so far)
    this._extendBounds = function(bounds, type, layerId) {

        // Get the layer
        var layer = this[this._layerTypes[type].store][layerId];

        // If the layer is hidden, skip it
        if (!layer || !layer.getMap()) {
            return bounds;
        }

        // If a ground overlay, include the whole image
        if ('groundOverlay' === type) {
            return bounds.union(layer.getBounds());
        }

        // Loop through each feature of the layer
        layer.forEach((feature) => {

            // Get the shape of the feature
            var geometry = feature.getGeometry();

            // If the feature has a shape, include every point of it
            if (geometry) {
                geometry.forEachLatLng((latLng) => bounds.extend(latLng));
            }
        });

        // Loop through any ground overlays found inside the layer
        (this._layerOverlays[`${type}:${layerId}`] || []).forEach((overlay) => {
            // Include the whole image
            bounds.union(overlay.getBounds());
        });

        // Return the extended bounds
        return bounds;
    };

    // Fit the map to a newly loaded layer
    this._fitLayer = function(bounds) {

        // If the layer has no shapes, bail
        if (bounds.isEmpty()) {
            return;
        }

        // Pass object to callback function
        var mapObject = this;

        // Fit the map to the layer
        var fit = function() {
            // If the layer is a single point, center on it at a comfortable zoom
            if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
                mapObject._map.setCenter(bounds.getCenter());
                mapObject._map.setZoom(mapObject._comfortableZoom);
                return;
            }
            // Fit the map to the layer
            mapObject._map.fitBounds(bounds);
        };

        // If the map has rendered, fit right away
        if (this._map.getBounds()) {
            fit();
            return;
        }

        // Wait for the map to render first, or the initial zoom and center will snap it back
        google.maps.event.addListenerOnce(this._map, 'idle', fit);
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

    // ========================================================================= //

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

            // Get coordinates (a LatLng has methods, a plain object has values)
            let lat = ('function' === typeof center.lat ? center.lat() : center.lat);
            let lng = ('function' === typeof center.lng ? center.lng() : center.lng);

            // Set sanitized center
            this.center({
                'lat': (isNaN(lat) ? 0 : lat),
                'lng': (isNaN(lng) ? 0 : lng)
            });

        } else {

            // Fit to the existing items
            this.fit();

        }

        // If zoom is specified, set it
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

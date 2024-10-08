// Google Maps plugin JS object
window.googleMaps = window.googleMaps || {

    // Log progress to console (enabled in devMode)
    log: (window._gmData ? window._gmData.logging : false),

    // Initialize collection of maps
    _maps: {},

    // ========================================================================= //

    // Create a new map object
    map: function (locations, options) {

        // Log status
        if (this.log) {
            console.log(`============================================================`);
            console.log(`Creating a new map object`);
        }

        // Create a new map object
        var map = new DynamicMap(locations, options);

        // Store map object for future reference
        this._maps[map.id] = map;

        // Return the map object
        return map;
    },

    // Get a specified map object
    getMap: function(mapId, assumeSuccess) {

        // Log status (if success is not assumed)
        if (this.log && !assumeSuccess) {
            console.log(`============================================================`);
            console.log(`[${mapId}] Getting existing map`);
        }

        // Get existing map
        var map = this._maps[mapId];

        // If map does not exist, emit warning
        if (!map) {
            console.warn(`[GM] Unable to find map "${mapId}"`);
        }

        // Return map
        return map;
    },

    // ========================================================================= //

    // Initialize specified maps
    init: function(mapId, callback) {

        // Initialize
        var map, dna, matchingContainers;

        // Get selected map containers
        var containers = this._whichMaps(mapId);

        // If no _gmData object exists, emit warning
        if (!window._gmData) {
            console.warn(`[GM] The window._gmData object has not been defined`);
        }

        // Loop through containers
        for (var i in containers) {

            // Get each map
            map = containers[i];

            // If map doesn't exist, skip it
            if (!map) {
                console.warn(`[GM] Cannot find specified map container #${mapId}`);
                continue;
            }

            // Count the number of matching containers (should ideally be 1)
            matchingContainers = document.querySelectorAll(`#${map.id}`).length;

            // If no matching containers exist, skip it
            if (!matchingContainers) {
                console.warn(`[GM] No DOM element exists using the identifier #${map.id}`);
                continue;
            }

            // If multiple matching containers exist, skip it
            if (1 < matchingContainers) {
                console.warn(`[GM] Multiple DOM elements are using the identifier #${map.id}`);
                continue;
            }

            // Log status
            if (this.log) {
                console.log(`============================================================`);
                console.log(`[${map.id}] Initializing map`);
            }

            // Get DNA of each map
            dna = map.dataset.dna;

            // If no DNA exists, skip it
            if (!dna) {
                console.warn(`[GM] Map container #${map.id} is missing DNA`);
                continue;
            }

            // Render each map
            this._unpack(dna);

            // Get dynamic map
            let dynamicMap = this.getMap(map.id, true);

            // If _gmData object exists
            if (window._gmData ?? null) {

                // Get data for info windows and marker callbacks
                const _infoWindows     = window._gmData.infoWindows[map.id]     || [];
                const _markerCallbacks = window._gmData.markerCallbacks[map.id] || [];

                // If any info windows were specified
                if (Object.keys(_infoWindows).length) {

                    // Log status
                    if (this.log) {
                        console.log(`[${map.id}] Activating all info windows`);
                    }

                    // Loop through info windows of current map
                    for (let markerId in _infoWindows) {
                        // Get info window
                        let infoWindow = _infoWindows[markerId];
                        // Activate info window function
                        dynamicMap._initInfoWindow(markerId, infoWindow);
                    }

                }

                // If any marker callbacks were specified
                if (Object.keys(_markerCallbacks).length) {

                    // Log status
                    if (this.log) {
                        console.log(`[${map.id}] Activating all marker callbacks`);
                    }

                    // Loop through marker callbacks of current map
                    for (let markerId in _markerCallbacks) {
                        // Get marker callback
                        let callback = _markerCallbacks[markerId];
                        // Activate marker callback function
                        dynamicMap._initMarkerClick(markerId, callback);
                    }

                }

            }

        }

        // If map callback was specified and is a function
        if (callback && 'function' === typeof callback) {

            // Log status
            if (this.log) {
                console.log(`[${map.id}] Running map callback function:\n`,callback);
            }

            // Execute map callback
            callback();

        }

    },

    // ========================================================================= //

    // Determine which maps to compile
    _whichMaps: function(selection) {

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
    _unpack: function(dna) {

        // Unpack the DNA sequence
        var sequence = JSON.parse(dna);

        // If no DNA exists, error and bail
        if (!sequence) {
            console.warn('[GM] No map DNA provided.');
            return;
        }

        // Initialize
        var map,block;

        // Loop through DNA sequence
        for (var i = 0; i < sequence.length; i++) {

            // Get map DNA block
            block = sequence[i];

            // If first block is not a map, error and bail
            if (0 === i && 'map' !== block.type) {
                console.warn('[GM] Map DNA is misconfigured.');
                return;
            }

            // Switch according to DNA block type
            switch (block.type) {

                // Create a new map
                case 'map':
                    map = new DynamicMap(block.locations, block.options);
                    break;

                // Add markers to the map
                case 'markers':
                    map.markers(block.locations, block.options);
                    break;

                // Add circles to the map
                case 'circles':
                    map.circles(block.locations, block.options);
                    break;

                // Add KML layer to the map
                case 'kml':
                    map.kml(block.url, block.options);
                    break;

                // Style the map
                case 'styles':
                    map.styles(block.styleSet);
                    break;

                // Zoom the map
                case 'zoom':
                    map.zoom(block.level);
                    break;

                // Center the map
                case 'center':
                    map.center(block.coords);
                    break;

                // Fit the map bounds
                case 'fit':
                    map.fit();
                    break;

                // Refresh the map
                case 'refresh':
                    map.refresh();
                    break;

                // Pan to a specific marker
                case 'panToMarker':
                    map.panToMarker(block.markerId);
                    break;

                // Set icon of an existing marker
                case 'setMarkerIcon':
                    map.setMarkerIcon(block.markerId, block.icon);
                    break;

                // Hide a marker
                case 'hideMarker':
                    map.hideMarker(block.markerId);
                    break;

                // Show a marker
                case 'showMarker':
                    map.showMarker(block.markerId);
                    break;

                // Open the info window of a specific marker
                case 'openInfoWindow':
                    map.openInfoWindow(block.markerId);
                    break;

                // Close the info window of a specific marker
                case 'closeInfoWindow':
                    map.closeInfoWindow(block.markerId);
                    break;

                // Hide a circle
                case 'hideCircle':
                    map.hideCircle(block.circleId);
                    break;

                // Show a circle
                case 'showCircle':
                    map.showCircle(block.circleId);
                    break;

                // Hide a KML layer
                case 'hideKml':
                    map.hideKml(block.kmlId);
                    break;

                // Show a KML layer
                case 'showKml':
                    map.showKml(block.kmlId);
                    break;

            }

        }

        // Check to ensure the map is visible
        map._checkMapVisibility();

        // Log status
        if (this.log) {
            console.log(`[${map.id}] Finished initializing map 👍`);
        }

        // Store map object for future reference
        this._maps[map.id] = map;

        // Return the map object
        return map;
    },

};

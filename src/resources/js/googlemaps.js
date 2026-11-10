// Google Maps plugin JS object
window.googleMaps = window.googleMaps || {

    // Log progress to console (enabled in devMode)
    log: (window._gmData ? window._gmData.logging : false),

    // Initialize collection of maps
    _maps: {},

    // Initialize the promise of a ready API
    _ready: null,

    // Initialize the calls waiting for each map
    _pending: {},

    // How long to wait for the API before giving up
    _readyTimeout: 15000,

    // Map methods which can wait until the API is ready
    _chainable: [
        'markers', 'circles', 'kml', 'geoJson', 'gpx', 'groundOverlay', 'dataset', 'boundaries',
        'styles', 'zoom', 'center', 'fit', 'refresh',
        'panToMarker', 'setMarkerIcon', 'hideMarker', 'showMarker',
        'openInfoWindow', 'closeInfoWindow', 'hideCircle', 'showCircle', 'hideKml', 'showKml',
        'hideGeoJson', 'showGeoJson', 'hideGpx', 'showGpx', 'hideGroundOverlay', 'showGroundOverlay',
        'hideDataset', 'showDataset', 'hideBoundaries', 'showBoundaries',
    ],

    // Map methods which return a value, so can't wait
    _getters: [
        'getMarker', 'getInfoWindow', 'getCircle', 'getKml', 'getGeoJson', 'getGpx',
        'getGroundOverlay', 'getDataset', 'getBoundaries', 'getMarkerClusterer',
        'getZoom', 'getCenter', 'getBounds',
    ],

    // Libraries bundled with the plugin, loaded only when a map needs them
    _libraries: {
        'togeojson':       {'file': 'togeojson-7.1.2.umd.js',       'global': 'toGeoJSON'},
        'jszip':           {'file': 'jszip-3.10.2.min.js',          'global': 'JSZip'},
        'markerclusterer': {'file': 'markerclusterer-2.6.2.min.js', 'global': 'markerClusterer'},
    },

    // Initialize the promise of each library
    _loadedLibraries: {},

    // Get the folder of the bundled libraries (beside this file)
    _libraryUrl: (document.currentScript ? new URL('lib/', document.currentScript.src).href : null),

    // ========================================================================= //

    // Wait until the Google Maps API is ready
    ready: function (libraries) {

        // If not already waiting, start waiting
        if (!this._ready) {
            this._ready = this._waitForApi();
        }

        // If no additional libraries were specified, return the promise
        if (!libraries) {
            return this._ready;
        }

        // Ensure libraries are an array
        libraries = Array.isArray(libraries) ? libraries : [libraries];

        // Get the plugin object
        var gm = this;

        // Return the promise, with each additional library loaded
        return this._ready.then(function () {

            // Load each additional library
            return Promise.all(libraries.map(function (library) {

                // If the library ships with the plugin, load it from there
                if (gm._libraries[library]) {
                    return gm._loadLibrary(library);
                }

                // If the API can't import libraries, skip it
                if (!google.maps.importLibrary) {
                    return;
                }

                // Import the Google library
                return google.maps.importLibrary(library);

            }));

        });
    },

    // ========================================================================= //

    // Create a new map object
    map: function (locations, options) {

        // Log status
        if (this.log) {
            console.log(`============================================================`);
            console.log(`Creating a new map object`);
        }

        // If the API (or the clustering library) isn't ready yet, return a stand-in map
        if (!this._isReady(this._clusters(options))) {
            return this._standIn(locations, options);
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

        // Get selected map containers
        var containers = this._whichMaps(mapId);

        // Whether any of the maps uses marker clustering
        var cluster = this._dnaClusters(containers);

        // If everything is ready, initialize immediately
        if (this._isReady(cluster)) {
            this._init(mapId, callback);
            return Promise.resolve();
        }

        // Loop through containers
        for (var i in containers) {

            // Get each container
            var container = containers[i];

            // If container has no DNA or is already known, skip it
            if (!container || !container.dataset.dna || this._maps[container.id]) {
                continue;
            }

            // Stand in for the map until it's created
            this._standIn(null, {'id': container.id}, false);

        }

        // Initialize once everything is ready (the timeout has already been logged)
        return this._whenReady(cluster).then(() => this._init(mapId, callback), () => {});
    },

    // Initialize specified maps (once the API is ready)
    _init: function(mapId, callback) {

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

            // Replay any calls made while the map was waiting
            this._replay(map.id, dynamicMap);

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

    // Whether the Maps API has finished loading
    _isApiReady: function () {
        return !!(window.google && google.maps && google.maps.Map);
    },

    // Whether the Maps API (and the clustering library, if needed) has finished loading
    _isReady: function (cluster) {
        return this._isApiReady() && (!cluster || !!window.markerClusterer);
    },

    // Wait for the Maps API (and the clustering library, if needed)
    _whenReady: function (cluster) {

        // If not clustering, only wait for the API
        if (!cluster) {
            return this.ready();
        }

        // Wait for both, but still build the map if the clustering library fails
        return Promise.all([
            this.ready(),
            this._loadLibrary('markerclusterer').catch(function (error) {
                console.error(error.message);
            })
        ]);
    },

    // Whether a map's options call for marker clustering
    _clusters: function (options) {
        return !!(options || {}).cluster;
    },

    // Whether any of the map containers calls for marker clustering
    _dnaClusters: function (containers) {

        // Loop through containers
        for (var i in containers) {

            // Get the DNA of each container
            var dna = (containers[i] && containers[i].dataset.dna);

            // If no DNA, skip it
            if (!dna) {
                continue;
            }

            // If the map uses clustering, we're done
            try {
                if (JSON.parse(dna).some(block => 'map' === block.type && this._clusters(block.options))) {
                    return true;
                }
            } catch (e) {
                // Invalid DNA is reported when the map is initialized
            }

        }

        // No clustering found
        return false;
    },

    // Poll until the Maps API has finished loading
    _waitForApi: function () {

        // Get the plugin object
        var gm = this;

        // Get the time we started waiting
        var started = Date.now();

        // Return a promise of the loaded API
        return new Promise(function (resolve, reject) {

            // Check whether the API has loaded
            (function check() {

                // Get the Maps API (if it exists yet)
                var maps = (window.google && google.maps) || {};

                // If the API loaded asynchronously, import the core libraries
                if (maps.importLibrary) {
                    Promise.all([maps.importLibrary('maps'), maps.importLibrary('marker')])
                        .then(function () { resolve(); }, reject);
                    return;
                }

                // If the API loaded directly, it's ready
                if (maps.Map) {
                    resolve();
                    return;
                }

                // If we've waited too long, give up
                if (gm._readyTimeout < (Date.now() - started)) {
                    console.error(`[GM] The Google Maps API did not load within ${gm._readyTimeout / 1000} seconds`);
                    reject(new Error('[GM] The Google Maps API did not load'));
                    return;
                }

                // Check again shortly
                setTimeout(check, 50);

            })();

        });
    },

    // Create a stand-in map, which records each call until the API is ready
    _standIn: function (locations, options, build) {

        // Get the plugin object
        var gm = this;

        // Copy the options
        options = Object.assign({}, options || {});

        // Get the map ID (or generate one)
        var id = options.id || this._generateId('map');

        // Get the map container (or create one)
        var div = document.getElementById(id) || document.createElement('div');

        // Configure the map container
        div.id = id;
        div.classList.add('gm-map');

        // Initialize the calls waiting for this map
        var calls = this._pending[id] = [];

        // Create a stand-in which records each call
        var standIn = new Proxy({}, {
            get: function (target, property) {

                // If asking for the map ID, return it now
                if ('id' === property) {
                    return id;
                }

                // If asking for the map container, return it now
                if ('div' === property) {
                    return div;
                }

                // If rendering the map, place the container now
                if ('tag' === property) {
                    return function (tagOptions) {

                        // Get the ID of the parent container
                        var parentId = (tagOptions || {}).parentId;

                        // Get the parent container
                        var parent = ('string' === typeof parentId ? document.getElementById(parentId) : null);

                        // If the parent container exists, add the map to it
                        if (parent) {
                            parent.appendChild(div);
                        }

                        // Record the call
                        calls.push(['tag', [tagOptions]]);

                        // Return the map container
                        return div;
                    };
                }

                // If the method can wait, record it
                if (gm._chainable.includes(property)) {
                    return function () {

                        // Record the call
                        calls.push([property, Array.prototype.slice.call(arguments)]);

                        // Return the stand-in for chaining
                        return standIn;
                    };
                }

                // If the method returns a value, explain why it can't yet
                if (gm._getters.includes(property)) {
                    return function () {
                        console.warn(`[GM] ${property}() was called before the Google Maps API was ready. See googleMaps.ready()`);
                    };
                }

                // Anything else doesn't exist yet
                return undefined;
            }
        });

        // Store stand-in for future reference
        this._maps[id] = standIn;

        // If the map won't be built elsewhere, build it once everything is ready
        if (false !== build) {
            this._whenReady(this._clusters(options)).then(function () {

                // Build the real map in the same container
                var map = new DynamicMap(locations, Object.assign(options, {'id': id, '_div': div}));

                // Store map object for future reference
                gm._maps[id] = map;

                // Replay any calls made while the map was waiting
                gm._replay(id, map);

            }, function () {});
        }

        // Return the stand-in
        return standIn;
    },

    // Replay the calls made while a map was waiting
    _replay: function (mapId, map) {

        // Get the calls waiting for this map
        var calls = this._pending[mapId];

        // If no calls are waiting, bail
        if (!calls || !map) {
            return;
        }

        // Clear the waiting calls
        delete this._pending[mapId];

        // Loop through each call
        calls.forEach(function (call) {
            // Make each call on the real map
            map[call[0]].apply(map, call[1]);
        });
    },

    // Generate a random ID
    _generateId: function (prefix) {
        return `${prefix}-${Math.random().toString(36).slice(2, 8)}`;
    },

    // ========================================================================= //

    // Load a bundled library (once)
    _loadLibrary: function (name) {

        // Get the library details
        var library = this._libraries[name];

        // If the library is already available, use it
        if (window[library.global]) {
            return Promise.resolve();
        }

        // If the library is already loading, wait for it
        if (this._loadedLibraries[name]) {
            return this._loadedLibraries[name];
        }

        // If the folder of the libraries is unknown, find it via this file
        if (!this._libraryUrl) {
            var script = document.querySelector('script[src*="/googlemaps.js"], script[src*="/googlemaps.min.js"]');
            this._libraryUrl = (script ? new URL('lib/', script.src).href : 'lib/');
        }

        // Get the URL of the library
        var url = this._libraryUrl + library.file;

        // Load the library
        return this._loadedLibraries[name] = new Promise(function (resolve, reject) {

            // Create a script tag
            var script = document.createElement('script');

            // Point to the library
            script.src = url;

            // When loaded, resolve or explain why it can't be used
            script.onload = function () {
                window[library.global] ? resolve() : reject(new Error(`[GM] The ${name} library loaded, but couldn't be used: ${url}`));
            };

            // If it fails, explain why
            script.onerror = function () {
                reject(new Error(`[GM] Unable to load the ${name} library: ${url}`));
            };

            // Add the script to the page
            document.head.appendChild(script);
        });
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

                // Add GeoJSON layer to the map
                case 'geoJson':
                    map.geoJson(block.source, block.options);
                    break;

                // Add GPX layer to the map
                case 'gpx':
                    map.gpx(block.url, block.options);
                    break;

                // Add ground overlay to the map
                case 'groundOverlay':
                    map.groundOverlay(block.url, block.bounds, block.options);
                    break;

                // Add dataset to the map
                case 'dataset':
                    map.dataset(block.datasetId, block.options);
                    break;

                // Add boundaries to the map
                case 'boundaries':
                    map.boundaries(block.featureType, block.placeIds, block.options);
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

                // Hide a GeoJSON layer
                case 'hideGeoJson':
                    map.hideGeoJson(block.geoJsonId);
                    break;

                // Show a GeoJSON layer
                case 'showGeoJson':
                    map.showGeoJson(block.geoJsonId);
                    break;

                // Hide a GPX layer
                case 'hideGpx':
                    map.hideGpx(block.gpxId);
                    break;

                // Show a GPX layer
                case 'showGpx':
                    map.showGpx(block.gpxId);
                    break;

                // Hide a ground overlay
                case 'hideGroundOverlay':
                    map.hideGroundOverlay(block.overlayId);
                    break;

                // Show a ground overlay
                case 'showGroundOverlay':
                    map.showGroundOverlay(block.overlayId);
                    break;

                // Hide a dataset
                case 'hideDataset':
                    map.hideDataset(block.datasetId);
                    break;

                // Show a dataset
                case 'showDataset':
                    map.showDataset(block.datasetId);
                    break;

                // Hide boundaries
                case 'hideBoundaries':
                    map.hideBoundaries(block.boundariesId);
                    break;

                // Show boundaries
                case 'showBoundaries':
                    map.showBoundaries(block.boundariesId);
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

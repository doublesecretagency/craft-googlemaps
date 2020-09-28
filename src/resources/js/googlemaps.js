// Google Maps plugin JS object
window.googleMaps = window.googleMaps || {

    // Log progress to console (enabled in devMode)
    log: false,

    // Initialize collection of maps
    _maps: {},

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
    getMap: function(mapId) {

        // Log status
        if (this.log) {
            console.log(`============================================================`);
            console.log(`Getting existing map "${mapId}"`);
        }

        // Return existing map object
        return this._maps[mapId] || null;
    },

    // Initialize specified maps
    init: function(selection) {

        // Initialize
        var map, dna;

        // Get selected map containers
        var containers = this._whichMaps(selection);

        // Loop through containers
        for (var i in containers) {

            // Get each map
            map = containers[i];

            // Log status
            if (this.log) {
                console.log(`============================================================`);
                console.log(`Initializing map "${map.id}"`);
            }

            // Get DNA of each map
            dna = map.dataset.dna;

            // If no DNA exists, skip this container
            if (!dna) {
                console.warn(`[GM] Map container #${map.id} is missing DNA`);
                continue;
            }

            // Render each map
            this._unpackDna(dna);

            // Log status
            if (this.log) {
                console.log(`Finished initializing map "${map.id}" 👍`);
            }

        }

    },

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
    _unpackDna: function(dna) {

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
        return new DynamicMap(map.locations, map.options);
    },

};

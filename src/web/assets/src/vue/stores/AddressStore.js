import { ref, computed, watch, nextTick } from 'vue';
import { defineStore } from 'pinia';

// Adjust formatting for specific countries
const formatCountries = {
    // Put street number before the street name
    numberFirst: [
        'Australia',
        'Canada',
        'France',
        'Hong Kong',
        'India',
        'Ireland',
        'Malaysia',
        'New Zealand',
        'Pakistan',
        'Singapore',
        'Sri Lanka',
        'Taiwan',
        'Thailand',
        'United Kingdom',
        'United States',
    ],
    // Put comma after the street name
    commaAfterStreet: [
        'Italy',
    ]
};

/**
 * Read a value from a nested object using a dot-notation path.
 */
const getNestedValue = (object, path) => {
    // Break the dot-notation path into individual object keys
    const pathParts = path.split('.');

    // Walk the object one level at a time, stopping if a segment is missing
    return pathParts.reduce((currentValue, key) => {
        return currentValue?.[key];
    }, object);
};

/**
 * Set a value on a nested object using a dot-notation path.
 */
const setNestedValue = (object, path, value) => {
    // Break the dot-notation path into individual object keys
    const pathParts = path.split('.');

    // Extract the final key that will receive the assigned value
    const finalKey = pathParts.pop();

    // Walk the object hierarchy, creating missing levels as needed
    const targetObject = pathParts.reduce((currentObject, key) => {
        // If the current level does not exist, initialize it
        if (currentObject[key] === undefined) {
            currentObject[key] = {};
        }

        return currentObject[key];
    }, object);

    // Assign the value at the resolved path
    targetObject[finalKey] = value;
};

// Define the Pinia store for address fields
export const useAddressStore = defineStore('address', () => {

    // ========================================================================= //
    // State

    // Default settings
    const DEFAULT_SETTINGS = {
        showMap: true,
        visibilityToggle: 'both',
        coordinatesMode: 'editable',
        mapOnSearch: 'noChange',
        subfieldConfig: [],
        coordinatesDefault: null,
        controlSize: 28,
        requireCoordinates: false,
    };

    // Configure the store state
    const namespace = ref({
        id: null,
        name: null,
        handle: null,
    });
    const settings = ref({ ...DEFAULT_SETTINGS });
    const data = ref({ address: {}, coords: {} });
    const images = ref({});
    const isRevision = ref({});
    const formatting = ref(formatCountries);

    // Margin top for the map toggle button
    const toggleWidth = ref(2285);

    // Internal variables
    let _rootEl = null;                         // Root element for this Address field instance
    let _resizeObs = null;                      // ResizeObserver for instruction height changes
    let _unsubscribers = [];                   // Reactive watchers / observers to dispose
    let _domUnbinders = [];                    // DOM event listeners to remove
    let _map = null;                            // google.maps.Map instance
    let _marker = null;                         // Map marker synced to coordinates
    let _autocompletes = [];                   // Active Places Autocomplete instances
    let _formSubmitGuards = new Map(); // formEl -> submit handler mapping
    let _blockNextSubmit = false;            // One-shot guard to prevent accidental submit
    let _suppressDomEvents = false;          // Prevent Store→DOM updates from re-triggering Autocomplete

    // When address data is changed
    watch(data, () => {
        // Normalize the address info
        _normalizeData();
    }, {deep: true});

    // ========================================================================= //
    // Getters

    /**
     * Formatting configuration for the map visibility toggle.
     */
    const configToggle = computed(() => {

        // Get the settings
        const s = settings.value;

        // Read the configured icon assets, if available
        const iconOn  = images.value.iconOn  || null;
        const iconOff = images.value.iconOff || null;
        const hasIcons = !!(iconOn && iconOff);

        // Start from the saved visibility toggle style
        let style = s.visibilityToggle || 'both'; // both | text | icon | hidden

        // If icon assets are missing, fall back to text-only to avoid broken UI
        if (!hasIcons && style !== 'hidden') {
            style = 'text';
        }

        // Build the toggle configuration used by the visibility control
        return {
            style,
            text: s.showMap ? 'Hide Map' : 'Show Map',
            icon: hasIcons
                ? (s.showMap ? iconOff : iconOn)
                : null,
        };
    });

    /**
     * Formatting configuration for the coordinates subfields.
     */
    const configCoords = computed(() => {
        // Read current coordinates mode from settings
        const mode = settings.value.coordinatesMode;

        // Build input configuration based on the selected mode
        return {
            type: ('hidden' === mode ? 'hidden' : 'number'),
            readOnly: !['editable', 'hidden'].includes(mode),
        }
    });

    /**
     * Get the complete set of fully configured subfields.
     */
    const subfields = computed(() => {

        // Get subfield configuration from the field settings
        let subfields = settings.value.subfieldConfig;

        // Loop through all subfields
        subfields.forEach(subfield => {

            // Initialize input styles and width
            let styles = {};

            // If the subfield is disabled
            if (!subfield.enabled) {
                // Render it, but keep it hidden
                styles['display'] = 'none';
            } else {
                // Get subfield width
                let width = subfield.width;
                // Never go over 100%
                if (100 < width) {
                    width = 100;
                }
                // Give up 1% width to the right margin
                styles['width'] = `${--width}%`;
            }

            // Append styles to subfield config
            subfield.styles = styles;
        });

        // Return fully configured subfields
        return subfields;
    });

    // ========================================================================= //
    // Actions

    /**
     * Initialize store state from the already-rendered DOM.
     */
    function initFromDom(rootElement, config) {

        // Store root element for later DOM coordination
        _rootEl = rootElement;

        // Capture namespace metadata used to scope DOM bindings
        namespace.value = {
            id: config.namespace.id,
            name: config.namespace.name,
            handle: config.namespace.handle,
        };

        // Seed settings using defaults, overridden by provided config
        const cfg = config?.settings ?? {};
        settings.value = {
            ...DEFAULT_SETTINGS,
            ...settings.value,
            showMap: cfg.showMap ?? DEFAULT_SETTINGS.showMap,
            visibilityToggle: cfg.visibilityToggle ?? DEFAULT_SETTINGS.visibilityToggle,
            coordinatesMode: cfg.coordinatesMode ?? DEFAULT_SETTINGS.coordinatesMode,
            mapOnSearch: cfg.mapOnSearch ?? DEFAULT_SETTINGS.mapOnSearch,
            subfieldConfig: Array.isArray(cfg.subfieldConfig) ? cfg.subfieldConfig : DEFAULT_SETTINGS.subfieldConfig,
            coordinatesDefault: cfg.coordinatesDefault ?? DEFAULT_SETTINGS.coordinatesDefault,
            controlSize: cfg.controlSize ?? DEFAULT_SETTINGS.controlSize,
            requireCoordinates: cfg.requireCoordinates ?? DEFAULT_SETTINGS.requireCoordinates,
        };

        // Initialize address and coordinate data from namespaced inputs
        data.value = {
            address: { ...(config.data?.address ?? {}) },
            coords:  { ...(config.data?.coords  ?? {}) },
        };

        // Load icon and image assets used by UI controls
        images.value = { ...(config.images ?? {}) };

        // Mark field as read-only when rendering a revision
        isRevision.value = !!config.isRevision;

    }

    /**
     * Wire DOM inputs to reactive state so address data, map state,
     * and settings-driven UI all stay synchronized.
     */
    function connectDom(rootEl) {
        // Capture root element so other helpers can query within this field instance
        _rootEl = rootEl;

        // Define DOM-to-store bindings using name suffixes so this works across Craft namespaces
        // - entry fields: fields[address][street1]
        // - settings preview: types[...][street1] (or other Craft namespaces)
        const bindings = [
            { selector: `input[name$="[street1]"]`,     path: 'address.street1' },
            { selector: `input[name$="[street2]"]`,     path: 'address.street2' },
            { selector: `input[name$="[city]"]`,        path: 'address.city' },
            { selector: `input[name$="[state]"]`,       path: 'address.state' },
            { selector: `input[name$="[zip]"]`,         path: 'address.zip' },
            { selector: `input[name$="[country]"]`,     path: 'address.country' },
            { selector: `input[name$="[countryCode]"]`, path: 'address.countryCode' },

            // Support coords inputs named either [lat] or [coords][lat] (and same for lng/zoom)
            { selector: `input[name$="[lat]"],  input[name$="[coords][lat]"]`,   path: 'coords.lat' },
            { selector: `input[name$="[lng]"],  input[name$="[coords][lng]"]`,   path: 'coords.lng' },
            { selector: `input[name$="[zoom]"], input[name$="[coords][zoom]"]`,  path: 'coords.zoom' },
        ];

        // Bind DOM → Store so user edits immediately update reactive state
        bindings.forEach(({ selector, path }) => {

            // If input does not exist for this binding, bail
            const el = rootEl.querySelector(selector);

            // If no element, bail
            if (!el) {
                return;
            }

            // Manage the zoom input
            let prev = el.value;
            let lastPointerDownAt = 0;
            let isAdjusting = false;
            let spinnerDirection = 0; // +1 = up, -1 = down

            // Helper to check if a value is empty/null/zero
            const isEmptyOrZero = (v) => {
                // If string is null or undefined, return true
                if (v === null || v === undefined) {
                    return true;
                }
                // Trim string
                const s = String(v).trim();
                // If string is empty, return true
                if (s === '') {
                    return true;
                }
                // Convert to number
                const n = Number(s);
                // Return whether number is not finite or zero
                return !Number.isFinite(n) || n === 0;
            };

            // Track pointer activity
            const onPointerDown = (e) => {
                lastPointerDownAt = performance.now();

                const rect = el.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                // Heuristic: spinner buttons live on the right side
                const SPINNER_GUTTER_PX = 22;

                if (x < rect.width - SPINNER_GUTTER_PX) {
                    spinnerDirection = 0;
                    return;
                }

                // Top half = increment, bottom half = decrement
                spinnerDirection = (y < rect.height / 2) ? 1 : -1;
            };
            const onFocus = () => {
                prev = el.value;
            };
            // Adjust zoom via keyboard
            const onKeyDown = (e) => {
                if (e.key === 'ArrowUp') {
                    spinnerDirection = 1;
                    lastPointerDownAt = performance.now();
                } else if (e.key === 'ArrowDown') {
                    spinnerDirection = -1;
                    lastPointerDownAt = performance.now();
                }
            };

            // Wire these helpers for zoom
            if (path === 'coords.zoom') {
                el.addEventListener('pointerdown', onPointerDown);
                el.addEventListener('focus', onFocus);
                el.addEventListener('keydown', onKeyDown);
                _domUnbinders.push(() => {
                    el.removeEventListener('pointerdown', onPointerDown);
                    el.removeEventListener('focus', onFocus);
                    el.removeEventListener('keydown', onKeyDown);
                });
            }

            // Normalize input value so numbers become numbers and empty values become null
            const handler = () => {

                // Ignore synthetic input events
                if (e && e.isTrusted === false) {
                    return;
                }

                // Prevent re-entrancy if we mutate the input while handling input
                if (isAdjusting) {
                    return;
                }

                // Special behavior ONLY for zoom spinner clicks
                if (path === 'coords.zoom' && el.type === 'number') {

                    // Whether the input was changed via spinner click
                    const wasSpinner = (performance.now() - lastPointerDownAt) < 400;

                    // If changed via spinner from empty/zero value
                    if (wasSpinner && spinnerDirection !== 0 && isEmptyOrZero(prev)) {

                        // Mark as adjusting to prevent loops
                        isAdjusting = true;
                        _suppressDomEvents = true;

                        try {
                            // Get current map zoom level
                            const mapZoom = _map?.getZoom?.();
                            const seed = Number.isFinite(+mapZoom) ? +mapZoom : 11;

                            // Set zoom value based on the spinner direction
                            el.value = String(seed + spinnerDirection);

                            // Get existing lat/lng values from the store
                            const existingLat = data.value.coords?.lat;
                            const existingLng = data.value.coords?.lng;

                            // Whether the existing lat/lng values are valid finite numbers
                            const hasLat =
                                existingLat !== null &&
                                existingLat !== undefined &&
                                String(existingLat).trim() !== '' &&
                                Number.isFinite(+existingLat);
                            const hasLng =
                                existingLng !== null &&
                                existingLng !== undefined &&
                                String(existingLng).trim() !== '' &&
                                Number.isFinite(+existingLng);

                            // If either lat or lng is missing, attempt to seed from the map
                            if ((!hasLat || !hasLng) && _map) {
                                // Get marker position if available, otherwise fall back to map center
                                const markerPos = _marker?.getPosition?.();
                                const centerPos = _map.getCenter?.();
                                const pos = markerPos || centerPos;

                                // If we got a position and it has valid finite lat/lng
                                if (pos && Number.isFinite(+pos.lat?.()) && Number.isFinite(+pos.lng?.())) {
                                    // Update store coordinates with the seeded lat/lng
                                    data.value.coords = {
                                        ...data.value.coords,
                                        lat: +pos.lat().toFixed(7),
                                        lng: +pos.lng().toFixed(7),
                                    };
                                }
                            }

                        } finally {
                            _suppressDomEvents = false;
                            isAdjusting = false;
                        }
                    }
                }

                // Get the raw input value
                const raw = el.value;

                // Get the normalized value
                const v = (el.type === 'number')
                    ? (raw === '' ? null : (Number.isFinite(Number(raw)) ? Number(raw) : null))
                    : raw;

                // Set the nested value in the store
                setNestedValue(data.value, path, v);

                // Update previous zoom value
                prev = el.value;
            };

            // Listen to both input and change so Craft/Garnish-style widgets stay in sync
            el.addEventListener('input', handler);
            el.addEventListener('change', handler);
            _domUnbinders.push(() => {
                el.removeEventListener('input', handler);
                el.removeEventListener('change', handler);
            });
        });

        // Bind Store → DOM so programmatic updates (map, autocomplete, preview settings) reflect in inputs
        bindings.forEach(({ selector, path }) => {

            // Get input for this binding
            const el = rootEl.querySelector(selector);

            // If no element, bail
            if (!el) {
                return;
            }

            // Watch the specific nested value so we only update DOM when that value changes
            const stop = watch(
                () => getNestedValue(data.value, path),
                (val) => {

                    // Get the next input value as a string
                    const next = (val ?? '') + '';

                    // If the input value is already correct, bail
                    if (el.value === next) {
                        return;
                    }

                    // Update the input value to reflect the store state
                    el.value = next;

                    // If DOM events are not suppressed, dispatch events so dependent behaviors can react
                    // (e.g. Craft UI, masks, other listeners, etc.)
                    if (!_suppressDomEvents) {
                        el.dispatchEvent(new Event('input', {bubbles: true}));
                        el.dispatchEvent(new Event('change', {bubbles: true}));
                    }
                },
                { immediate: true }
            );
            _unsubscribers.push(stop);
        });

        // Add a delegated click handler so Twig-rendered map toggle controls can call into the store
        // Add data-action="toggle-map" to the toggle element in Twig.
        const clickHandler = (e) => {
            const t = e.target.closest('[data-action="toggle-map"]');
            if (t) {
                changeVisibility();
                _applyVisibilityToDom(rootEl);
            }
        };
        rootEl.addEventListener('click', clickHandler);
        _domUnbinders.push(() => rootEl.removeEventListener('click', clickHandler));

        // Watch map visibility so the DOM wrapper can be shown/hidden without remounting anything
        const stopVis = watch(
            () => settings.value.showMap,
            () => _applyVisibilityToDom(rootEl),
            { immediate: true }
        );
        _unsubscribers.push(stopVis);

        // Watch subfield config so the settings preview can reorder/show/hide inputs immediately
        const stopSubfields = watch(
            () => settings.value.subfieldConfig,
            () => _syncSubfieldsToDom(rootEl),
            { deep: true, immediate: true }
        );
        _unsubscribers.push(stopSubfields);

        // Watch coordinates mode so lat/lng/zoom inputs can switch between editable/readonly/hidden
        const stopCoordsMode = watch(
            () => settings.value.coordinatesMode,
            () => _applyCoordinatesModeToDom(rootEl),
            { immediate: true }
        );
        _unsubscribers.push(stopCoordsMode);

        // Watch requireCoordinates so the UI can communicate required-ness for coordinate inputs
        const stopRequireCoords = watch(
            () => !!settings.value.requireCoordinates,
            () => _applyRequireCoordinatesToDom(rootEl),
            { immediate: true }
        );
        _unsubscribers.push(stopRequireCoords);

        // Defer toggle sizing until layout is stable so measurements reflect final DOM geometry
        requestAnimationFrame(_updateToggleWidth);

        // Recompute toggle sizing when toggle style changes so layout stays visually aligned
        const stopStyle = watch(() => settings.value.visibilityToggle, () => {
            requestAnimationFrame(_updateToggleWidth);
        });
        _unsubscribers.push(stopStyle);
    }

    /**
     * Tear down all DOM bindings and reactive watchers created by connectDom().
     */
    function disconnectDom() {
        // Dispose reactive watchers
        _unsubscribers.forEach(fn => fn());
        _unsubscribers = [];

        // Remove DOM event listeners
        _domUnbinders.forEach(fn => fn());
        _domUnbinders = [];
    }

    /**
     * Connect and initialize the Google Map instance.
     */
    async function connectMap(rootEl) {

        // Set root element reference
        _rootEl = rootEl;

        // Get the map element
        const mapEl = rootEl.querySelector('[data-role="map"]');

        // If no map element, bail
        if (!mapEl) {
            return;
        }

        // Initialize Google Maps
        let maps;

        try {
            // Attempt to load classic Maps constructors
            maps = await _ensureClassicMaps();
        } catch (err) {
            console.warn(err.message);
            return;
        }

        // Get initial center position
        let center = _getFieldCenter();

        // If no center yet, try to use browser geolocation
        if (!center && navigator.geolocation) {
            try {
                // Attempt to get user location (with 5s timeout)
                const pos = await new Promise((res, rej) =>
                    navigator.geolocation.getCurrentPosition(res, rej, { timeout: 5000 })
                );
                // Set center from geolocation results
                center = {
                    lat: pos.coords?.latitude ?? 0,
                    lng: pos.coords?.longitude ?? 0,
                    zoom: 10
                };
                // Update field data with geolocated coords
                data.value.coords = {
                    lat: +center.lat,
                    lng: +center.lng,
                    zoom: center.zoom
                };
            } catch (e) {
                console.warn('[GM] Geolocation unavailable:', e);
            }
        }

        // If still no center, use hardcoded default (Bermuda Triangle)
        if (!center) {
            center = {
                lat: 32.3113966,
                lng: -64.7527469,
                zoom: 6
            };
        }

        // Configure map options
        const mapOptions = {
            center: { lat: +center.lat, lng: +center.lng },
            zoom: _safeZoom(center.zoom),
            streetViewControl: false,
            fullscreenControl: false,
            mapTypeControl: false,
            controlSize: settings.value.controlSize,
        };

        // Build the map
        _map = new window.google.maps.Map(mapEl, mapOptions);

        // Set position of map marker
        const position = {
            lat: +center.lat,
            lng: +center.lng
        };

        // Add a marker to the map
        _marker = new window.google.maps.Marker({
            position: position,
            map: _map,
            draggable: !isRevision.value, // Disable dragging on revisions
        });

        // If viewing as a revision,
        // prevent map from being interactive
        if (isRevision.value) {
            return;
        }

        // Update subfield coords when marker is dragged
        // noinspection JSVoidFunctionReturnValueUsed
        const dragListener = window.google.maps.event.addListener(_marker, 'dragend', () => {

            // Get marker position
            const p = _marker.getPosition();

            // Update store coordinates
            data.value.coords = {
                lat: +p.lat().toFixed(7),
                lng: +p.lng().toFixed(7),
                zoom: _map.getZoom() ?? _safeZoom(11),
            };

            // Center map on marker
            _centerMap();
        });

        // Update subfield zoom when map is zoomed
        // noinspection JSVoidFunctionReturnValueUsed
        const zoomListener = window.google.maps.event.addListener(_map, 'zoom_changed', () => {

            // Get the existing zoom from store
            const existingZoom = +data.value.coords?.zoom;

            // If no existing zoom, bail
            if (!existingZoom) {
                return;
            }

            // Get current map zoom
            const z = _map.getZoom();

            // If zoom is not a finite number, bail
            if (!isFinite(+z)) {
                console.warn('[GM] zoom_changed ignored (non-finite)', z);
                return;
            }

            // If zoom changed
            if (existingZoom !== +z) {
                // Update store zoom
                data.value.coords = { ...data.value.coords, zoom: +z };
            }
        });

        // Helper to remove a listener
        const removeListener = (h) => {
            if (!h) {
                return;
            }
            if (typeof h.remove === 'function') {
                h.remove();
            } else {
                window.google.maps.event?.removeListener?.(h);
            }
        };

        // Cleanup both listeners on disconnect
        _domUnbinders.push(() => removeListener(dragListener));
        _domUnbinders.push(() => removeListener(zoomListener));

        // Stop watching for changes to coordinates
        const stopCoords = watch(
            () => [data.value.coords?.lat, data.value.coords?.lng],
            ([lat, lng]) => {

                // If coordinates are not valid, bail
                if (!_areCoordsValid({ lat:lat, lng:lng })) {
                    return;
                }

                // If coordinates are not finite numbers, bail
                if (!isFinite(+lat) || !isFinite(+lng)) {
                    return;
                }

                // Get current marker position
                const pos = { lat: +lat, lng: +lng };
                const cur = _marker.getPosition?.();

                // Check if position is already correct
                const same = cur && Math.abs(cur.lat() - pos.lat) < 1e-9 && Math.abs(cur.lng() - pos.lng) < 1e-9;

                // If position is already correct, bail
                if (!same) {
                    _marker.setPosition(pos);
                    _centerMap();
                }
            },
            { immediate: true }
        );

        // Stop watching for changes to zoom
        const stopZoom = watch(
            () => data.value.coords?.zoom,
            (zoom) => {

                // Get current map zoom
                if (!isFinite(+zoom) || !_map) {
                    return;
                }

                // Get safe zoom level
                const next = _safeZoom(zoom);

                // If zoom is already correct, bail
                if (_map.getZoom?.() === next) {
                    return;
                }

                // Set map zoom
                _map.setZoom(next);
            },
            { immediate: true }
        );

        // Cleanup watchers on disconnect
        _unsubscribers.push(stopCoords, stopZoom);
    }

    /**
     * Tear down the Google Map and marker instances.
     */
    function disconnectMap() {

        // If a marker exists, remove it from the map and release the reference
        if (_marker) { _marker.setMap(null); _marker = null; }

        // Clear the map reference so it can be garbage collected
        _map = null;
    }

    /**
     * Attach Google Places Autocomplete to configured address subfields.
     *
     * This wires Places selections into store state and installs guards
     * to prevent accidental form submission while the dropdown is active.
     */
    async function connectAutocomplete(rootEl) {

        // If root element is missing or Autocomplete is already wired, bail
        if (!rootEl || _autocompletes.length) {
            return;
        }

        // Ensure the Places library is available before proceeding
        try {
            await _ensurePlaces();
        } catch (err) {
            return;
        }

        // Select inputs that should receive Autocomplete
        const targets = _selectAutocompleteTargets(rootEl);

        // If no targets found, bail
        if (!targets.length) {
            return;
        }

        // Loop through targets and wire up Autocomplete
        targets.forEach(el => {

            // Initialize Places Autocomplete on the input
            const ac = new window.google.maps.places.Autocomplete(el, {
                fields: ['address_components','formatted_address','geometry','name','place_id'],
                // types: ['address'] // optional
            });

            // Apply selected place data into store state
            // noinspection JSVoidFunctionReturnValueUsed
            const placeListener = ac.addListener('place_changed', () => {

                // Get the selected place
                const place = ac.getPlace?.();

                // If no place selected, bail
                if (!place) {
                    return;
                }

                // Temporarily suppress DOM events so programmatic updates
                // do not re-trigger Autocomplete or other listeners
                _suppressDomEvents = true;

                try {
                    // Apply place data to store
                    _applyPlaceToData(place);
                } finally {
                    // Re-enable DOM events after Vue has flushed updates
                    nextTick(() => {
                        _suppressDomEvents = false;
                    });
                }
            });

            // Intercept Enter key presses while the Places menu is open
            // so selection does not implicitly submit the form
            const keydown = (e) => {
                // If the event is part of an IME composition, ignore it
                if (e.isComposing) {
                    return; // IME safety
                }
                // If the key pressed was not the Enter key, bail
                if (e.key !== 'Enter') {
                    return;
                }
                // If the Places menu is open
                if (_isPlacesMenuOpen()) {
                    // Block the next form submit and prevent default Enter behavior
                    _blockNextSubmit = true;
                    e.preventDefault();
                }
            };

            // Attach keydown listener to the input element
            el.addEventListener('keydown', keydown, { capture: true });

            // Install a form-level submit guard as a final backstop
            const formEl = el.form || rootEl.closest('form') || document.querySelector('form');

            // If the form exists, and we haven't already installed a guard on it
            if (formEl && !_formSubmitGuards.has(formEl)) {

                // Define the submit handler
                const onSubmit = (ev) => {
                    // If a Places Enter was just consumed, or the menu is still open, bail
                    if (_blockNextSubmit || _isPlacesMenuOpen()) {
                        ev.preventDefault();
                        _blockNextSubmit = false;
                    }
                };

                // Capture submit early so we can preempt native submission
                formEl.addEventListener('submit', onSubmit, { capture: true });

                // Guard against keypress-driven submits in older browsers
                const onKeypress = (ev) => {
                    if (ev.key === 'Enter' && _isPlacesMenuOpen()) {
                        ev.preventDefault();
                        _blockNextSubmit = true;
                    }
                };

                // Attach keypress listener to the form
                formEl.addEventListener('keypress', onKeypress, { capture: true });

                // Record that we've installed a guard on this form
                _formSubmitGuards.set(formEl, { onSubmit, onKeypress });
            }

            // Track this Autocomplete instance so it can be cleaned up later
            _autocompletes.push({ el, ac, listener: placeListener, keydown, formEl });
        });
    }

    /**
     * Disconnect all Places Autocomplete instances and remove related submit guards.
     */
    function disconnectAutocomplete() {

        // Tear down Autocomplete listeners and input-level key handlers
        _autocompletes.forEach(({ el, ac, listener, keydown }) => {
            try { window.google?.maps?.event?.removeListener?.(listener); } catch {}
            try { window.google?.maps?.event?.clearInstanceListeners?.(ac); } catch {}
            try { el?.removeEventListener?.('keydown', keydown, { capture: true }); } catch {}
        });
        _autocompletes = [];

        // Remove form-level submit guards installed for Autocomplete protection
        _formSubmitGuards.forEach(({ onSubmit, onKeypress }, formEl) => {
            try { formEl?.removeEventListener?.('submit', onSubmit, { capture: true }); } catch {}
            try { formEl?.removeEventListener?.('keypress', onKeypress, { capture: true }); } catch {}
        });
        _formSubmitGuards.clear();

        // Reset one-shot submit guard state
        _blockNextSubmit = false;
    }

    /**
     * Toggle visibility of the map.
     */
    function changeVisibility() {
        // Invert current visibility state
        settings.value.showMap = !settings.value.showMap;
    }

    /**
     * Merge settings updates coming from the settings preview UI.
     */
    function applySettings(partial) {
        // If input is missing or not an object, bail
        if (!partial || typeof partial !== 'object') {
            return;
        }

        // Merge provided settings into existing reactive settings state
        settings.value = {
            ...settings.value,
            ...partial,
        };

        // Autocomplete and DOM wiring are managed elsewhere by watchers
    }

    /**
     * Check whether coordinates are valid.
     */
    function _areCoordsValid(coords) {

        // If coords is missing or not an object, mark as invalid
        if (!coords || typeof coords !== 'object') {
            return false;
        }

        // If either lat or lng is missing, mark as invalid
        if (!('lat' in coords) || !('lng' in coords)) {
            return false;
        }

        // If either lat or lng is null, mark as invalid
        if (coords.lat === null || coords.lng === null) {
            return false;
        }

        // Get lat & lng as numbers
        const lat = Number(coords.lat);
        const lng = Number(coords.lng);

        // If either lat or lng is not a finite number, mark as invalid
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return false;
        }

        // Success, coordinates are valid!
        return true;
    }

    /**
     * Populate address data when Autocomplete selected.
     */
    function _applyPlaceToData(place) {

        // Set address subfield data
        _setAddressData(place.address_components);

        // Get address data
        const address = data.value.address;

        // Whether the `name` value matches the `street1` value
        let boringName = (place.name === address.street1);

        // Append additional data
        address.name      = (!boringName ? place.name : null);
        address.placeId   = place.place_id;
        address.formatted = place.formatted_address;
        address.raw       = JSON.stringify(place);

        // Set coordinates
        let coords = place.geometry.location;
        data.value.coords.lat = parseFloat(coords.lat().toFixed(7));
        data.value.coords.lng = parseFloat(coords.lng().toFixed(7));

        // Zoom fallback to 11 if not already set
        data.value.coords.zoom = data.value.coords.zoom || 11;

        // If coords are invalid, clear meta subfields
        if (!data.value.coords.lat || !data.value.coords.lng) {
            address.placeId   = null;
            address.formatted = null;
            address.raw       = null;
        }

        // If not changing the map visibility, bail
        if ('noChange' === settings.value.mapOnSearch) {
            return;
        }

        // Change map visibility based on settings
        settings.value.showMap = ('open' === settings.value.mapOnSearch);
    }

    /**
     * Apply current map visibility setting to the DOM.
     */
    function _applyVisibilityToDom(rootEl) {

        // Locate the map wrapper rendered by the Twig template
        const mapWrap = rootEl.querySelector('[data-role="map-wrapper"]');

        // If wrapper element is missing, bail
        if (!mapWrap) {
            return;
        }

        // Show or hide the map based on current visibility setting
        mapWrap.style.display = settings.value.showMap ? '' : 'none';
    }

    /**
     * Apply the selected coordinates mode to lat/lng/zoom inputs.
     */
    function _applyCoordinatesModeToDom(rootEl) {

        // If root element is missing, bail
        if (!rootEl) {
            return;
        }

        // Read current coordinates mode from settings
        const mode = settings.value.coordinatesMode; // 'editable' | 'readOnly' | 'hidden'

        // Select a coordinate input by key, supporting both flat and nested names
        const selectInput = (key) =>
            rootEl.querySelector(
                `input[name$="[${key}]"], input[name$="[coords][${key}]"]`
            );

        // Apply mode behavior to each coordinate input
        ['lat', 'lng', 'zoom'].forEach((key) => {
            const el = selectInput(key);

            // If input does not exist for this key, bail
            if (!el) {
                return;
            }

            // If hidden
            if (mode === 'hidden') {
                // Hide input
                el.type = 'hidden';
                el.readOnly = false;
            } else {
                // Show as a number input
                el.type = 'number';
                // If read-only
                if (mode === 'readOnly') {
                    el.readOnly = true;
                    el.classList.add('disabled');
                } else {
                    el.readOnly = false;
                    el.classList.remove('disabled');
                }
            }
        });
    }

    /**
     * Apply required-coordinate UI affordances to lat/lng inputs.
     */
    function _applyRequireCoordinatesToDom(rootEl) {

        // If root element is missing, bail
        if (!rootEl) {
            return;
        }

        // TEMP: Is this feature too ugly to exist?
        return;
        // ENDTEMP

        // Select a coordinate input by key, supporting both flat and nested names
        const selectInput = (key) =>
            rootEl.querySelector(
                `input[name$="[${key}]"], input[name$="[coords][${key}]"]`
            );

        // Read whether coordinates are required from settings
        const requireCoords = !!settings.value.requireCoordinates;

        // Update placeholders to reflect required state
        ['lat', 'lng'].forEach((key) => {
            const el = selectInput(key);

            // If input does not exist for this key, bail
            if (!el) {
                return;
            }

            // Cache the original placeholder so we can toggle the required indicator
            if (!el.dataset.gmBasePlaceholder) {
                el.dataset.gmBasePlaceholder = el.placeholder || (key === 'lat' ? 'Latitude' : 'Longitude');
            }

            // Append or remove required indicator without mutating the base placeholder
            el.placeholder = el.dataset.gmBasePlaceholder + (requireCoords ? ' *' : '');
        });
    }

    /**
     * Synchronize preview subfield DOM inputs with settings.subfieldConfig.
     *
     * This reorders inputs to match configuration order, then applies
     * visibility, placeholder text, and width rules to each subfield.
     */
    function _syncSubfieldsToDom(rootEl) {

        // If root element is missing, bail
        if (!rootEl) {
            return;
        }

        // Read current subfield configuration (fallback to empty array)
        const cfg = settings.value.subfieldConfig || [];

        /**
         * 1) Reorder DOM inputs to match configuration order
         */

        // Initialize shared parent container reference
        let parent = null;

        // Initialize collection of ordered inputs
        const orderedInputs = [];

        // Loop through each configured subfield
        cfg.forEach((sf) => {

            // If subfield config is missing or malformed, bail
            if (!sf || !sf.handle) {
                return;
            }

            // Select input by suffix so outer Craft namespace does not matter
            const input = rootEl.querySelector(`input[name$="[${sf.handle}]"]`);

            // If input does not exist for this subfield, bail
            if (!input) {
                return;
            }

            // Capture the shared parent container on first match
            if (!parent) {
                parent = input.parentElement;
            }

            // Only reorder inputs that live in the same container
            if (input.parentElement === parent) {
                orderedInputs.push(input);
            }
        });

        // If we have a parent and at least one input to reorder
        if (parent && orderedInputs.length) {
            // Re-append inputs in the desired order
            orderedInputs.forEach((input) => {
                // Move each input to the end of the parent to reorder
                parent.appendChild(input);
            });
        }

        /**
         * 2) Apply enabled, label, and width settings to each subfield
         */

        // Loop through each configured subfield
        cfg.forEach((sf) => {

            // If subfield config is missing or malformed, bail
            if (!sf || !sf.handle) {
                return;
            }

            // Select input again after potential reordering
            const input = rootEl.querySelector(`input[name$="[${sf.handle}]"]`);

            // If input does not exist for this subfield, bail
            if (!input) {
                return;
            }

            // Apply visibility based on enabled flag (default to enabled)
            const enabled = (sf.enabled === undefined ? true : !!sf.enabled);
            input.style.display = enabled ? '' : 'none';

            // Apply label and required indicator to placeholder
            if (sf.label !== undefined || sf.required !== undefined) {
                // Get placeholder text (fallback to handle if label is empty)
                let placeholder =
                    sf.label && sf.label.trim().length ? sf.label : sf.handle;

                // If required, append indicator
                if (sf.required) {
                    placeholder += ' *';
                }

                // Set the placeholder on the input
                input.placeholder = placeholder;
            }

            // Apply width percentage, mirroring Twig macro behavior
            if (sf.width !== undefined && sf.width !== null) {

                // Parse width as a number
                let width = Number(sf.width);

                // If width is valid and positive, clamp and apply
                if (!Number.isNaN(width) && width > 0) {

                    // If width exceeds 100%, clamp it
                    if (width > 100) {
                        width = 100;
                    }

                    // Subtract 1% to account for right margin (Twig parity)
                    width = width - 1;
                    input.style.width = `${width}%`;
                } else {

                    // If width is invalid, allow CSS to control layout
                    input.style.removeProperty('width');
                }
            }
        });
    }

    /**
     * Calculate width of visibility toggle.
     */
    function _updateToggleWidth() {

        // Set width based on visibility toggle setting
        switch (settings.value.visibilityToggle) {
            case 'both':
                toggleWidth.value = 94;
                break;
            case 'text':
                toggleWidth.value = 79;
                break;
            case 'icon':
                toggleWidth.value = 21;
                break;
            case 'hidden':
                toggleWidth.value = 0;
                break;
            default:
                toggleWidth.value = 132;
        }
    }

    /**
     * Ensure classic Google Maps constructors are available (google.maps.Map / google.maps.Marker).
     */
    async function _ensureClassicMaps(timeoutMs = 12000, intervalMs = 50) {

        // Poll until classic constructors exist (optionally importing libraries when using the new loader)
        const wait = () => new Promise((resolve, reject) => {

            // Record start time for timeout tracking
            const start = Date.now();

            // Define polling tick function
            const tick = async () => {

                // Get google.maps namespace
                const g = window.google;
                const m = g?.maps;

                // If importLibrary exists and constructors are missing, attempt to load them
                if (m?.importLibrary && (!m.Map || !m.Marker)) {
                    try {
                        await m.importLibrary('maps');
                        await m.importLibrary('marker'); // gives classic Marker where available
                    } catch (e) {
                        // Ignore and continue polling
                    }
                }

                // If constructors exist, resolve with google.maps namespace
                if (m?.Map && m?.Marker) return resolve(m);

                // If timeout is exceeded, reject with a useful error, bail
                if (Date.now() - start > timeoutMs) return reject(new Error('[GM] Maps API not ready (classic constructors missing)'));

                // Schedule next poll
                setTimeout(tick, intervalMs);
            };

            // Start first tick immediately
            tick();
        });

        // Wait for constructors to become available
        const maps = await wait();

        // If constructors are not functions, fail fast with a diagnostic error
        if (typeof maps.Map !== 'function' || typeof maps.Marker !== 'function') {
            throw new Error('[GM] Expected google.maps.Map/Marker constructors, but found: '
                + JSON.stringify({ Map: typeof maps.Map, Marker: typeof maps.Marker }));
        }

        // Return classic constructors
        return maps;
    }

    /**
     * Ensure the Google Places library is available before wiring Autocomplete.
     */
    async function _ensurePlaces(timeoutMs = 12000, intervalMs = 50) {

        // Ensure classic Maps globals exist first so we have a stable base to build on
        await _ensureClassicMaps(timeoutMs, intervalMs);

        // If importLibrary is available, proactively import Places to populate google.maps.places
        try {
            if (window.google?.maps?.importLibrary) {
                await window.google.maps.importLibrary('places');
            }
        } catch (e) {
            // Ignore import failures and fall back to polling for availability
        }

        // Start a timeout window so we don’t wait forever on a misconfigured Maps load
        const start = Date.now();

        // Poll for Places library readiness
        for (;;) {
            // Read current Maps namespace from the global so we observe it as it becomes available
            const maps = window.google && window.google.maps;

            // Check for the Places Autocomplete constructor as the readiness signal
            const hasAutocomplete = !!(maps && maps.places && typeof maps.places.Autocomplete === 'function');

            // If Places is ready, return the Maps namespace for downstream use
            if (hasAutocomplete) return maps;

            // If we’ve exceeded the timeout, throw an actionable error for configuration issues
            if (Date.now() - start >= timeoutMs) {
                throw new Error('[GM] Places library not loaded. Include `libraries=places` or importLibrary("places").');
            }

            // Sleep briefly before checking again to avoid tight-looping the main thread
            await new Promise(r => setTimeout(r, intervalMs));
        }
    }

    /**
     * Determine whether a Places Autocomplete dropdown menu is currently visible.
     */
    function _isPlacesMenuOpen() {

        // Query all possible Places dropdown containers
        const containers = document.querySelectorAll('.pac-container');

        // Look for any container that is visible and has suggestion items
        for (const c of containers) {

            // Check for at least one suggestion item
            const hasItems = !!c.querySelector('.pac-item');

            // Use layout participation or bounds as a visibility signal
            const rect = c.getBoundingClientRect?.() || { width: 0, height: 0 };
            const visible = !!(c.offsetParent || (rect.width && rect.height));

            // If suggestions are visible, treat the menu as open
            if (hasItems && visible) return true;
        }

        // Otherwise, treat the menu as closed
        return false;
    }

    /**
     * Resolve the initial map center using field data or settings defaults.
     */
    function _getFieldCenter() {

        // If coordinates are valid in field data
        if (_areCoordsValid(data.value.coords)) {
            // Extract coordinates from field data
            const { lat, lng, zoom } = data.value.coords;
            // Return normalized coordinates
            return {
                lat: +lat,
                lng: +lng,
                zoom: isFinite(+zoom) ? +zoom : 11
            };
        }

        // Fall back to default coordinates
        const def = settings.value.coordinatesDefault;

        // If default coordinates are valid
        if (def && _areCoordsValid(def)) {
            // Extract default coordinates from settings
            const { lat, lng, zoom } = def;
            // Return normalized coordinates
            return {
                lat: +lat,
                lng: +lng,
                zoom: isFinite(+zoom) ? +zoom : 11
            };
        }

        // No center is defined
        return null;
    }

    /**
     * Center the map on the current coordinate values.
     */
    function _centerMap() {

        // If map or marker is not initialized, bail
        if (!_map || !_marker) {
            return;
        }

        // Read current coordinates from store
        const { lat, lng } = data.value.coords ?? {};

        // If coordinates are not valid numbers, bail
        if (!isFinite(+lat) || !isFinite(+lng)) {
            return;
        }

        // Pan map to the resolved coordinate position
        _map.panTo({ lat: +lat, lng: +lng });
    }

    /**
     * Normalize a zoom value so it is always safe to apply to the map.
     */
    function _safeZoom(z) {

        // Coerce input into an integer zoom value
        const num = parseInt(z, 10);

        // If value is not a finite number, fall back to a reasonable default
        if (!isFinite(num)) {
            return 11;
        }

        // Clamp negative zoom levels to zero
        if (num < 0) {
            return 0;
        }

        // Return normalized zoom value
        return num;
    }

    /**
     * Select which subfield input elements should have autocomplete enabled.
     */
    function _selectAutocompleteTargets(rootEl) {

        // Initialize selected elements array
        const selected = [];

        // Loop over the subfield config
        settings.value.subfieldConfig?.forEach(sf => {
            // If subfield is disabled, skip it
            if (!sf?.enabled) {
                return;
            }
            // If autocomplete is explicitly off, skip it
            if (sf?.autocomplete === false) {
                return;
            }
            // Get the input element for this subfield
            const el = rootEl.querySelector(`input[name$="[${sf.handle}]"]`);
            // If found and visible, add it to the selected list
            if (el && getComputedStyle(el).display !== 'none') {
                selected.push(el);
            }
        });

        // If nothing is explicitly selected
        if (!selected.length) {
            // Fallback to `street1` or `name` field
            for (const handle of ['street1', 'name']) {
                // Get the subfield input element
                const el = rootEl.querySelector(`input[name$="[${handle}]"]`);
                // If found, add it and stop
                if (el) {
                    selected.push(el);
                    break;
                }
            }
        }

        // Return selected subfield input elements
        return selected;
    }

    /**
     * Set the individual subfield values.
     */
    function _setAddressData(components) {

        // Initialize address data
        let apiData = {};

        // Loop through components from API results
        components.forEach(c => {
            // Get component type
            let type = c['types'][0];
            // Set value from component
            switch (type) {
                case 'locality':
                case 'neighborhood':
                    apiData[type] = c['long_name'];
                    break;
                case 'country':
                    apiData[type] = c['long_name'];
                    apiData['countryCode'] = c['short_name'];
                    break;
                default:
                    apiData[type] = c['short_name'];
                    break;
            }
        });

        // Get address data
        const address = data.value.address;

        // Set address data to Vue
        address.street1      = _formatStreet(apiData);
        address.street2      = null;
        address.city         = apiData['locality'];
        address.state        = apiData['administrative_area_level_1'];
        address.zip          = apiData['postal_code'];
        address.neighborhood = apiData['neighborhood'];
        address.county       = apiData['administrative_area_level_2'];
        address.country      = apiData['country'];
        address.countryCode  = apiData['countryCode'];

        // Country-specific adjustments
        switch (apiData['country']) {
            case 'United Kingdom':
                address.city  = apiData['postal_town'];
                address.state = apiData['administrative_area_level_2'];
                break;
        }
    }

    /**
     * Format the main street address.
     */
    function _formatStreet(apiData) {

        // Abbreviate variables
        let streetNumber = apiData.street_number || '';
        let streetName   = apiData.route         || '';
        let country      = apiData.country       || '';

        // Default street format
        let street = `${streetName} ${streetNumber}`;

        // If needed, put street number before the street name
        if (formatting.value.numberFirst.includes(country)) {
            street = `${streetNumber} ${streetName}`;
        }

        // If needed, put comma after the street name
        if (formatting.value.commaAfterStreet.includes(country)) {
            street = `${streetName}, ${streetNumber}`;
        }

        // Return formatted street address
        return street.trim().replace(/,*$/,'');
    }

    /**
     * Normalize the address data when anything changes.
     */
    function _normalizeData() {

        // If coordinates are valid
        if (_areCoordsValid(data.value.coords)) {
            // Continue on normally
            return;
        }

        // Reset the meta fields
        data.value.address['formatted'] = null;
        data.value.address['raw'] = null;
    }

    // ========================================================================= //

    // Return reactive values
    return {
        // State
        namespace, settings, data, images, formatting, toggleWidth,

        // Getters
        configToggle, configCoords, subfields,

        // Actions
        initFromDom,
        connectDom, disconnectDom,
        connectMap, disconnectMap,
        connectAutocomplete, disconnectAutocomplete,

        changeVisibility,
        applySettings,
    }

})

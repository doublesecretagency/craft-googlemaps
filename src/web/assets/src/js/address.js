import { createApp } from 'vue';
import { createPinia } from 'pinia';

// Hydrator component that sets up Address field instances
import AddressHydrator from './address-hydrator';

/**
 * Initialize a single Address field instance.
 */
window.initAddressField = (element) => {

    // If element is missing, bail
    if (!element) {
        // Log a helpful warning so missing mounts are visible during debugging
        console.warn('[GM] The Address field cannot be found.');
        return;
    }

    // Read the JSON config injected by Twig
    const dataConfig = element.getAttribute('data-config');

    // Parse config so we can locate namespace and initial data
    const config = JSON.parse(dataConfig);

    // Use namespace id as a stable key for external access
    const id = config.namespace.id;

    // Create a Vue app for this field, passing root element and config
    const app = createApp(AddressHydrator, {
        rootEl: element,
        config,
    });

    // Install Pinia so AddressHydrator can create and use the store
    app.use(createPinia());

    // Mount into the field root element
    const vm = app.mount(element);

    // Initialize global registry for Address instances
    window.__gmAddressInstances ??= {};

    // Register this instance so other screens (like settings preview) can call into it
    window.__gmAddressInstances[id] = {
        app,
        vm,

        /**
         * Apply a partial settings payload to the mounted store instance.
         */
        applySettings(partial) {

            // Get a reference to the store from the Vue instance
            const store = vm?.addressStore;

            // If settings object is missing, bail
            if (!store?.settings) {
                return;
            }

            // Normalize incoming payload so callers can pass either {settings:{...}} or a raw object
            const s = partial?.settings ?? partial ?? {};

            // Keep a local reference to store settings for clarity
            const storeSettings = store.settings;

            // If subfieldConfig is provided, replace it
            if (Array.isArray(s.subfieldConfig)) {
                storeSettings.subfieldConfig = s.subfieldConfig;
            }
            // If showMap is provided, update visibility state
            if (typeof s.showMap !== 'undefined') {
                storeSettings.showMap = !!s.showMap;
            }
            // If visibilityToggle is provided, update toggle style
            if (s.visibilityToggle) {
                storeSettings.visibilityToggle = s.visibilityToggle;
            }
            // If coordinatesMode is provided, update coords input behavior
            if (s.coordinatesMode) {
                storeSettings.coordinatesMode = s.coordinatesMode;
            }
            // If mapOnSearch is provided, update map-on-search behavior
            if (s.mapOnSearch) {
                storeSettings.mapOnSearch = s.mapOnSearch;
            }

            // Refresh Autocomplete so it matches the latest subfieldConfig
            try {
                store.disconnectAutocomplete();
                store.connectAutocomplete(element);
            } catch (e) {
                // Something went wrong, log and continue so the field stays usable
                console.warn('[GM] Error refreshing autocomplete', e);
            }

        },
    };

    // Return the mounted Vue instance for callers that need direct access
    return vm;
};

/**
 * Mount a root element once, even if it appears multiple times via DOM mutations.
 */
function tryMount(el) {

    // If element is missing or already mounted, bail
    if (!el || el.__gmMounted) {
        return;
    }

    // If initializer is not present yet, bail
    if (typeof window.initAddressField !== 'function') {
        return;
    }

    // Refresh Autocomplete so it matches the latest subfieldConfig
    try {
        // Initialize a single Address field instance.
        window.initAddressField(el);
        // Record mount state on the element so we can skip it in future scans
        el.__gmMounted = true;
    } catch (e) {
        // Something went wrong, log and continue so other fields can mount
        console.error('[GM] Unable to mount the Address field DOM element.', e);
    }
}

/**
 * Scan the document for Address roots and mount any that are not hydrated yet.
 */
function scanAndMount() {
    // Attempt to mount each root once
    document
        .querySelectorAll('[data-gm-address-root]')
        .forEach(tryMount);
}

// Mount once on initial page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', scanAndMount, { once: true });
} else {
    scanAndMount();
}

// Observe DOM mutations so dynamically added fields also get hydrated
const observer = new MutationObserver((mutations) => {
    // Loop through each mutation record
    for (const mutation of mutations) {
        // Loop through each added node
        for (const node of mutation.addedNodes) {
            // If node is not an element, skip
            if (node.nodeType !== 1) {
                continue;
            }
            // If the added node is a root itself, mount it
            if (node.matches?.('[data-gm-address-root]')) {
                tryMount(node);
            }
            // Attempt to mount each root once
            node
                .querySelectorAll?.('[data-gm-address-root]')
                .forEach(tryMount);
        }
    }
});

// Start observing the full document for added nodes
observer.observe(document.documentElement, {
    childList: true,
    subtree: true,
});

// Legacy bootstrap retained for backwards compatibility
(function () {
    // Reuse scanAndMount so legacy bootstrap stays consistent with the main path
    const boot = () => {
        scanAndMount();
    };
    // Mount once on initial page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();

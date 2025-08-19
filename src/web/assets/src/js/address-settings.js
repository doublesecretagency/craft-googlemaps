import { createApp, watch } from 'vue';
import { createPinia } from 'pinia';
import { useAddressSettingsStore } from '../vue/stores/AddressSettingsStore';
import { useAddressStore } from '../vue/stores/AddressStore';

// Hydrator component to wire up the Address preview
import AddressSettingsHydrator from './address-settings-hydrator';

/**
 * Ensure Vue mounts into a fresh child without wiping Twig-rendered DOM.
 */
function ensureEmptyChildMount(container, className) {

    // Try to find an existing mount element
    let mountEl = container.querySelector(`.${className}`);

    // If none exists, create it
    if (!mountEl) {
        mountEl = document.createElement('div');
        mountEl.className = className;
        mountEl.style.display = 'contents';
        container.appendChild(mountEl);
        return mountEl;
    }

    // If it exists but has children, create a fresh child to mount into
    if (mountEl.childNodes.length) {
        const fresh = document.createElement('div');
        fresh.style.display = 'contents';
        mountEl.appendChild(fresh);
        return fresh;
    }

    // Return the existing empty mount element
    return mountEl;
}

/**
 * Parse JSON safely with a fallback value.
 */
function safeParseJson(value, fallback = {}) {
    try {
        // Attempt to parse the JSON value
        return JSON.parse(value);
    } catch (e) {
        // On failure, return the provided fallback
        return fallback;
    }
}

/**
 * Initialize the Address field settings UI and live preview.
 */
function initAddressSettings(rootEl) {

    // If root element is missing, bail
    if (!rootEl) {
        console.warn('[GM] initAddressSettings called without a rootEl');
        return;
    }

    // If settings have already been initialized for this element, bail
    if (rootEl.__gmSettingsInitialized) {
        return;
    }

    // Mark settings as initialized to prevent double-initialization
    rootEl.__gmSettingsInitialized = true;

    // Create a shared Pinia instance for settings and preview
    const pinia = createPinia();

    // Mount the live Address preview first so it can receive updates
    const previewRoot = rootEl.querySelector('[data-gm-preview-root]');

    // If a preview root exists, mount the preview app
    if (previewRoot) {

        // Ensure a fresh mount element under the preview root
        const previewMount = ensureEmptyChildMount(
            previewRoot,
            'gm-address-vue-root'
        );

        // Locate the address root within the preview, or use the preview root itself
        const addressRoot =
            previewRoot.querySelector('[data-gm-address-root]') || previewRoot;

        // Parse base config from the address root dataset
        const baseConfig = safeParseJson(addressRoot.dataset?.config, {});

        // Get or create namespace in the config
        baseConfig.namespace ??= {};

        // Ensure a stable namespace ID for this preview instance
        const id =
            baseConfig.namespace.id ||
            previewRoot.id ||
            'gm-preview';

        // Assign the ID into config and preview root
        baseConfig.namespace.id = id;
        previewRoot.id ||= id;

        // Create the Vue app for the Address preview
        const previewApp = createApp(AddressSettingsHydrator, {
            rootEl: addressRoot,
            config: baseConfig,
        });

        // Install Pinia so the hydrator can create and use the store
        previewApp.use(pinia);

        // Mount the preview app into the mount element
        previewApp.mount(previewMount);

    } else {
        console.warn('[GM] No [data-gm-preview-root] found under settings root');
    }

    // Mount the settings shell that reads DOM and syncs stores
    const settingsWrapper =
        rootEl.querySelector('[data-gm-settings-vue-mount]') || rootEl;

    // Ensure a fresh mount element under the settings wrapper
    const settingsMount = ensureEmptyChildMount(
        settingsWrapper,
        'gm-settings-vue-root'
    );

    // Create the Vue app that will manage settings state
    const settingsApp = createApp({
        setup() {

            // Access the settings and address stores
            const settings = useAddressSettingsStore();
            const address  = useAddressStore();

            // Read initial settings state from DOM
            settings.setFromDom(rootEl);

            // Bind DOM listeners so settings changes update store state
            settings.bindDomListeners(rootEl);

            // Whenever settings.previewConfig changes, apply to preview address
            watch(
                () => settings.previewConfig,
                (config) => {
                    // If no preview address instance, bail
                    if (!config) {
                        return;
                    }
                    // If applySettings method exists, call it with new config
                    if (typeof address.applySettings === 'function') {
                        address.applySettings(config);
                    }
                },
                { deep: true, immediate: true }
            );

            // Whenever address.coords changes, update hidden input fields
            watch(
                () => address?.data?.coords,
                (coords) => {

                    // If no coords, bail
                    if (!coords) {
                        return;
                    }

                    // Helper to set a hidden input value by path
                    const setVal = (path, val) => {

                        // Find the input element by name suffix
                        const el = rootEl.querySelector(`[name$="${path}"]`);

                        // If element is missing, bail
                        if (!el) {
                            return;
                        }

                        // Normalize value to string
                        const next = String(val ?? '');

                        // If value is unchanged, bail
                        if (el.value === next) {
                            return;
                        }

                        // Update value and dispatch events
                        el.value = next;
                        el.dispatchEvent(new Event('input',  { bubbles: true }));
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    };

                    // Update hidden inputs for lat, lng, and zoom
                    setVal('[coordinatesDefault][lat]',  coords.lat);
                    setVal('[coordinatesDefault][lng]',  coords.lng);
                    setVal('[coordinatesDefault][zoom]', coords.zoom);
                },
                { deep: true }
            );

            // Return empty render function
            return {};
        },
        template: '<div></div>', // headless; logic only
    });

    // Use Pinia and mount the settings app
    settingsApp.use(pinia);
    settingsApp.mount(settingsMount);
}

// Expose initializer for Craft to call when settings page loads
window.initAddressSettings = initAddressSettings;
export default initAddressSettings;

// Auto-bootstrap Address settings on DOM ready
(function () {
    // Boot function to initialize all settings roots
    const boot = () => {
        document
            .querySelectorAll('[data-gm-settings-root]')
            .forEach(initAddressSettings);
    };
    // Run boot on DOMContentLoaded, or immediately if already ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();

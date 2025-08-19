// Vue component used to hydrate Address fields
import { defineComponent, onMounted, onBeforeUnmount, watch } from 'vue';

// Pinia store that owns Address field behavior
import { useAddressStore } from '../vue/stores/AddressStore';

/**
 * Hydrate a server-rendered Address field and bind it to the Address store.
 */
export default defineComponent({

    name: 'AddressHydrator',

    // Root element and server-provided config for this field instance
    props: {
        rootEl: { type: HTMLElement, required: true },
        config: { type: Object, required: true },
    },

    /**
     * Initialize store state and wire DOM, map, and autocomplete behavior.
     */
    setup(props) {

        // Create store instance for this Address field
        const store = useAddressStore();

        // Track teardown callbacks for this hydrator
        const cleanups = [];

        // Register a cleanup callback to run on unmount
        const registerCleanup = (fn) => cleanups.push(fn);

        // Utility to set element display style
        const setElDisplay = (el, display) => {
            // If no element, bail
            if (!el) {
                return;
            }
            // Set display style
            el.style.display = display;
        };

        // Utility to synchronize toggle UI with store config
        const syncToggleUi = ({ toggleEl, textEl, iconEl }, cfg) => {

            // If no config, bail
            if (!cfg) {
                return;
            }

            // Get toggle style
            const style = cfg.style || 'both';

            // Show or hide the toggle control
            setElDisplay(toggleEl, style === 'hidden' ? 'none' : '');

            // If text element exists
            if (textEl) {
                // If style is icon-only
                if (style === 'icon') {
                    // Hide text element
                    setElDisplay(textEl, 'none');
                } else {
                    // Show text element and set content
                    setElDisplay(textEl, '');
                    textEl.textContent = cfg.text || '';
                }
            }

            // If icon element exists
            if (iconEl) {
                // If style is text-only or no icon configured
                if (style === 'text' || !cfg.icon) {
                    // Hide icon element and clear src
                    setElDisplay(iconEl, 'none');
                    iconEl.removeAttribute('src');
                } else {
                    // Show icon element and set src
                    setElDisplay(iconEl, '');
                    iconEl.setAttribute('src', cfg.icon);
                }
            }
        };

        // Seed store from DOM and server config
        store.initFromDom(props.rootEl, props.config);

        /**
         * Wire field behavior once the DOM is ready.
         */
        onMounted(() => {

            // Get root element for this field instance
            const root = props.rootEl;

            // Bind DOM inputs to store state
            store.connectDom(root);

            // Enable Places Autocomplete for configured subfields
            store.connectAutocomplete(root);

            // Initialize map and marker bindings
            store.connectMap(root);

            // Locate map visibility toggle rendered by Twig
            const toggleEl = root.querySelector('[data-gm-toggle]');

            // If no toggle element, bail
            if (!toggleEl) {
                return;
            }

            // Group toggle UI elements
            const toggleUi = {
                toggleEl,
                textEl: toggleEl.querySelector('[data-gm-toggle-text]'),
                iconEl: toggleEl.querySelector('[data-gm-toggle-icon]'),
            };

            // Toggle map visibility when user clicks the control
            const onClick = (event) => {
                event.preventDefault();
                store.changeVisibility();
            };

            // Attach click handler and register teardown
            toggleEl.addEventListener('click', onClick);
            registerCleanup(() => toggleEl.removeEventListener('click', onClick));

            // Keep toggle UI synced with store state
            const stopToggleWatch = watch(
                () => store.configToggle,
                (cfg) => syncToggleUi(toggleUi, cfg),
                { immediate: true, deep: true }
            );
            registerCleanup(stopToggleWatch);

            // Keep toggle offset in sync with layout changes
            const stopMarginWatch = watch(
                () => store.marginTop,
                (offset) => {
                    const px = typeof offset === 'number' ? offset : 0;
                    toggleEl.style.marginTop = `${px}px`;
                },
                { immediate: true }
            );
            registerCleanup(stopMarginWatch);

            // Mark the toggle container as hydrated
            const toggleContainer = root.querySelector('.map-toggle-container');

            // If toggle container exists, add hydrated class
            if (toggleContainer) {
                toggleContainer.classList.add('is-hydrated');
            }
        });

        /**
         * Tear down all bindings created by this hydrator.
         */
        onBeforeUnmount(() => {

            // Run all registered cleanup callbacks
            cleanups.forEach((fn) => {
                try {
                    fn();
                } catch (e) {
                }
            });

            // Disconnect Autocomplete instances and guards
            store.disconnectAutocomplete();

            // Disconnect DOM bindings
            store.disconnectDom();

            // Disconnect map resources
            store.disconnectMap();
        });

        // Expose the store instance for external access if needed
        return {
            addressStore: store,
        };
    },
});

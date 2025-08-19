// Import Vue primitives used by the settings preview hydrator
import { defineComponent, h, onMounted, onBeforeUnmount } from 'vue';

// Reuse the main AddressHydrator so preview behavior matches real fields
import AddressHydrator from './address-hydrator';

/**
 * Mount AddressHydrator for the field settings preview.
 */
export default defineComponent({

    name: 'AddressSettingsHydrator',

    // Root element and server-provided config for this field instance
    props: {
        rootEl: { type: HTMLElement, required: true },
        config: { type: Object, required: true },
    },

    /**
     * Render the preview and ensure any global instance is cleaned up on unmount.
     */
    setup(props) {

        // Read namespace id so we can unregister the global preview instance on teardown
        const getNamespaceId = () => props.config?.namespace?.id;

        // Keep the mount hook available for preview-only behavior
        onMounted(() => {
            // Intentionally left blank
        });

        // If a preview instance was registered, remove it when this component is destroyed
        onBeforeUnmount(() => {
            const id = getNamespaceId();
            if (!id || !window.__gmAddressInstances) {
                return;
            }

            // Remove this preview instance so stale references don’t accumulate
            delete window.__gmAddressInstances[id];
        });

        // Render AddressHydrator with the settings root element and config
        return () =>
            h(AddressHydrator, {
                rootEl: props.rootEl,
                config: props.config,
            });
    },
});

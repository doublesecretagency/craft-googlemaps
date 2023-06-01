import { ref, computed, watch } from 'vue';
import { defineStore } from 'pinia';

export const useAddressSettingsStore = defineStore('addressSettings', () => {

    // ========================================================================= //
    // State

    const namespace = ref({
        id: null,
        name: null,
        handle: null,
    });
    const settings = ref({});
    const data = ref({});
    const images = ref({});

    // Options for each dropdown select
    const options = {
        // "Show map on initial load?"
        'mapOnStart': {
            'default' : 'Open if the field has coordinates',
            'open'    : 'Always start open',
            'close'   : 'Always start closed',
        },
        // "Show map when address search is triggered?"
        'mapOnSearch': {
            'open'     : 'Open',
            'close'    : 'Close',
            'noChange' : 'No Change',
        },
        // "Map Visibility Toggle"
        'visibilityToggle': {
            'both'   : 'Text & Icon',
            'text'   : 'Text Only',
            'icon'   : 'Icon Only',
            'hidden' : 'Hidden',
        },
        // "Display Coordinates"
        'coordinatesMode': {
            'editable' : 'Editable',
            'readOnly' : 'Read Only',
            'hidden'   : 'Hidden',
        }
    }

    // ========================================================================= //
    // Getters

    /**
     * Bubble the `mapOnStart` settings value.
     */
    const _mapOnStart = computed(() => {
        // Get deeper settings value
        return settings.value.mapOnStart;
    });

    // ========================================================================= //
    // Watchers

    /**
     * Watch for the map visibility setting to change.
     */
    watch(_mapOnStart, () => {
        // Show or hide the map
        // based on the new `mapOnStart` value
        switch (settings.value.mapOnStart) {
            case 'open':
                // Open the map
                settings.value.showMap = true;
                break;
            case 'close':
                // Close the map
                settings.value.showMap = false;
                break;
            default:
                // Convert coordinates to boolean
                const lat = !!data.value.coords.lat;
                const lng = !!data.value.coords.lng;
                // Open the map if
                // the field has coordinates
                settings.value.showMap = (lat && lng);
                break;
        }
    });

    // ========================================================================= //
    // Actions

    /**
     * Get a namespaced field name.
     */
    function getName(replacement)
    {
        return namespace.value.name.replace(/\[PLACEHOLDER]/, replacement);
    }

    // ========================================================================= //

    // Return reactive values
    return {
        // State
        namespace,
        settings,
        data,
        images,
        options,

        // Actions
        getName,
    }

})

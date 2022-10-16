<template>
    <div class="default-coords">
        <input type="hidden" :name="getName('lat')" v-model="coordinatesDefault['lat']" />
        <input type="hidden" :name="getName('lng')" v-model="coordinatesDefault['lng']" />
        <input type="hidden" :name="getName('zoom')" v-model="coordinatesDefault['zoom']" />
    </div>
</template>

<script>
// Import Pinia
import { mapStores } from 'pinia';
import { useAddressSettingsStore } from '../stores/AddressSettingsStore';

export default {
    props: {
        namespace: Object
    },
    computed: {
        // Load Pinia store
        ...mapStores(useAddressSettingsStore),

        coordinatesDefault() {
            // Get the Pinia store
            const addressSettingsStore = useAddressSettingsStore();

            // Get all potential coordinates options
            const settingsCoords = addressSettingsStore.settings.coordinatesDefault;
            const dataCoords     = addressSettingsStore.data.coords;

            // If coordinates from data are valid, return them
            if (dataCoords['lat'] && dataCoords['lng']) {
                return dataCoords;
            }

            // If coordinates from settings are valid, return them
            if (settingsCoords['lat'] && settingsCoords['lng']) {
                return settingsCoords;
            }

            // Return empty coordinates
            return {
                lat: null,
                lng: null,
                zoom: null
            };
        }
    },
    methods: {
        // Get namespaced field name
        getName(setting) {
            // Get the Pinia store
            const addressSettingsStore = useAddressSettingsStore();
            // Return the namespaced field name
            return addressSettingsStore.getName(`[coordinatesDefault][${setting}]`);
        }
    }
}
</script>

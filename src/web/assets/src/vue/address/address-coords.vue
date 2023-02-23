<template>
    <div>
        <input v-for="input in getInputs()"
            :type="config.type"
            :placeholder="input.label"
            :readonly="config.readOnly"
            v-model.number="addressStore.data.coords[input.key]"
            :name="`${addressStore.namespace.name}[${input.key}]`"
            :class="getInputClasses(input.key)"
            :style="input.styles"
            autocomplete="chrome-off"
        />
    </div>
</template>

<script>
// Import Pinia
import { mapStores } from 'pinia';
import { useAddressStore } from '../stores/AddressStore';

export default {
    computed: {
        // Load Pinia store
        ...mapStores(useAddressStore),

        /**
         * Get coordinates formatting configuration.
         */
        config()
        {
            // Get the Pinia store
            const addressStore = useAddressStore();
            // Return configuration
            return addressStore.configCoords;
        },
    },
    methods: {

        /**
         * Configure the inputs for coordinates.
         */
        getInputs()
        {
            return [
                {
                    key: 'lat',
                    label: 'Latitude',
                    styles: {'width': '43%'}
                },
                {
                    key: 'lng',
                    label: 'Longitude',
                    styles: {'width': '43%'}
                },
                {
                    key: 'zoom',
                    label: 'Zoom',
                    styles: {'width': '11%'}
                }
            ];
        },

        /**
         * Configure the classes for each input.
         */
        getInputClasses(key)
        {
            // Get the Pinia store
            const addressStore = useAddressStore();

            // Get the field settings
            const settings = addressStore.settings;

            // If hidden, return an empty array
            if ('hidden' === settings.coordinatesMode) {
                return [];
            }

            // Whether the coordinates are editable
            const isEditable = ('editable' !== settings.coordinatesMode);

            // Whether the existing coordinates are valid
            const validCoords = addressStore.validateCoords(addressStore.data.coords);

            // Whether to mark coordinates as required
            const requireCoordinates = (
                settings.requireCoordinates
                && (key !== 'zoom')
                && !validCoords
            );

            // Return array of input classes
            return [
                'text',
                'code',
                'fullwidth',
                (isEditable ? 'disabled' : null),
                (requireCoordinates ? 'required' : null),
            ];
        }

    }
}
</script>

<style scoped>
.disabled {
    opacity: 0.60;
    background-color: #e4eaf4;
}
</style>

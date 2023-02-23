<template>
    <div>
        <input v-for="subfield in addressStore.subfields"
            type="text"
            :placeholder="subfield.label + (subfield.required ? ' *' : '')"
            :ref="subfield.handle"
            v-model="addressStore.data.address[subfield.handle]"
            :name="`${addressStore.namespace.name}[${subfield.handle}]`"
            class="text fullwidth"
            :class="{'required': isRequiredAndInvalid(subfield)}"
            :style="subfield.styles"
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
    },
    mounted() {
        // Initialize the Autocomplete functionality
        this.initAutocomplete();
    },
    methods: {

        /**
         * Initialize the Autocomplete functionality.
         */
        initAutocomplete()
        {
            // Get the Pinia store
            const addressStore = useAddressStore();

            try {
                // If google object doesn't exist yet, log message and bail
                if (!window.google) {
                    console.error('[GM] The `google` object has not yet been loaded.');
                    return;
                }

                // Make a list of subfields to activate
                let activate = [];

                // Loop through all subfields
                addressStore.settings.subfieldConfig.forEach(subfield => {
                    // Set fallbacks
                    subfield.enabled = (subfield.enabled || false);
                    subfield.autocomplete = (subfield.autocomplete || false);
                    // If enabled and set to autocomplete, copy to array
                    if (subfield.enabled && subfield.autocomplete) {
                        activate.push(this.$refs[subfield.handle][0]);
                    }
                });

                // Activate autocomplete for selected subfields
                for (let i in activate) {
                    this._activateAutocomplete(activate[i]);
                }

            } catch (error) {

                // Something went wrong
                console.error(error);

            }

        },

        /**
         * Activate Autocomplete for a single input field.
         */
        _activateAutocomplete($input)
        {
            // Get the Pinia store
            const addressStore = useAddressStore();

            // If subfield is hidden, bail
            if ('none' === $input.style.display) {
                return;
            }

            // Create an Autocomplete object
            let autocomplete = new window.google.maps.places.Autocomplete($input, {
                fields: [
                    'address_components',
                    'formatted_address',
                    'geometry.location',
                    'name',
                    'place_id'
                ]
            });

            // Listen for autocomplete trigger
            autocomplete.addListener('place_changed', () => {
                // Get newly selected place
                let place = autocomplete.getPlace();
                // Update data accordingly
                addressStore.updateData(place);
            });

            // Prevent address selection from attempting to submit the form
            $input.addEventListener('keydown', (event) => {
                // If "Return" or "Enter" key was pressed
                if (event.keyCode === 13) {
                    // Do nothing
                    event.preventDefault();
                }
            });
        },

        /**
         * Whether a subfield is both required and empty.
         */
        isRequiredAndInvalid(subfield)
        {
            // If subfield is not required, return false
            if (!subfield.required) {
                return false;
            }

            // Get the Pinia store
            const addressStore = useAddressStore();

            // If subfield is not empty, return false
            if (addressStore.data.address[subfield.handle]) {
                return false;
            }

            // Subfield is required and empty
            return true;
        },

    }
}
</script>

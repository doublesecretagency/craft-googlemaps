<template>
    <div>
        <input
            v-for="subfield in subfieldDisplay()"
            :placeholder="subfield.label"
            :ref="subfield.key"
            v-model="$root.$data.data.address[subfield.key]"
            :class="inputClasses"
            autocomplete="chrome-off"
            :name="`${namespacedName}[${subfield.key}]`"
            :style="subfield.styles"
        />
    </div>
</template>

<script>
    import addressComponents from './../utils/address-components';
    import subfieldDisplay from './../utils/subfield-display';

    export default {
        data() {
            return {
                handle: this.$root.$data.handle,
                namespacedName: this.$root.$data.namespacedName,
                inputClasses: [
                    'text',
                    'fullwidth'
                ]
            }
        },
        mounted() {
            try {
                // If google object doesn't exist yet, log message and bail
                if (!window.google) {
                    console.error('[GM] The `google` object has not yet been loaded.');
                    return;
                }

                // Get field settings
                const settings = this.$root.$data.settings;

                // Initialize loop variables
                let subfield, enabled, autocomplete;
                let first = false; // Key of the FIRST subfield
                let activate = []; // Array of subfields to activate

                // Loop through all subfields
                for (let key in settings.subfieldConfig) {
                    // Get each subfield
                    subfield = settings.subfieldConfig[key];
                    // Get subfield values
                    enabled      = (1 === parseInt(subfield.enabled ?? 0));
                    autocomplete = (1 === parseInt(subfield.autocomplete ?? 0));
                    // Get key of FIRST enabled subfield
                    if (!first && enabled) {
                        first = key;
                    }
                    // If set to autocomplete, copy to array
                    if (autocomplete) {
                        activate.push(this.$refs[key][0]);
                    }
                }

                // If the autocomplete array is empty
                if (!activate.length) {
                    // Activate only the FIRST subfield
                    activate.push(this.$refs[first][0]);
                }

                // Activate autocomplete for selected subfields
                for (let i in activate) {
                    this.setAutocomplete(activate[i]);
                }

            } catch (error) {

                // Something went wrong
                console.error(error);

            }
        },
        methods: {

            // Initialize Autocomplete for a single input field
            setAutocomplete($input) {

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
                    // Configure address data based on place
                    this.setAddressData(place);
                    // Get settings
                    const settings = this.$root.$data.settings;
                    // If not changing the map visibility, bail
                    if ('noChange' === settings.mapOnSearch) {
                        return;
                    }
                    // Change map visibility based on settings
                    this.$root.$data.settings.showMap = ('open' === settings.mapOnSearch);
                });

                // Prevent address selection from attempting to submit the form
                window.google.maps.event.addDomListener($input, 'keydown', (event) => {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                    }
                });

            },

            // Populate address data when Autocomplete selected
            setAddressData(place) {

                // Get data object
                let data = this.$root.$data.data;

                // Get new address info
                let components = place.address_components;
                let coords = place.geometry.location;

                // Set all subfield data
                addressComponents(components, data.address);

                // Append address meta data
                data.address['name']      = place.name;
                data.address['placeId']   = place.place_id;
                data.address['formatted'] = place.formatted_address;
                data.address['raw']       = JSON.stringify(place);

                // Set coordinates
                data.coords.lat = parseFloat(coords.lat().toFixed(7));
                data.coords.lng = parseFloat(coords.lng().toFixed(7));

                // If coords are invalid, clear meta subfields
                if (!data.coords.lat || !data.coords.lng) {
                    data.address['placeId']   = null;
                    data.address['formatted'] = null;
                    data.address['raw']       = null;
                }

            },

            // Get the display array
            subfieldDisplay() {
                // Get the subfield arrangement
                let arrangement = this.$root.$data.settings.subfieldConfig;
                // Return configured arrangement
                return subfieldDisplay(arrangement);
            }
        }
    }
</script>

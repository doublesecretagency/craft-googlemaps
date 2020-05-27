<template>
    <div>
        <input
            v-for="subfield in subfieldDisplay()"
            :placeholder="subfield.label"
            :ref="(subfield.enabled ? 'autocomplete' : '')"
            v-model="$root.$data.data.address[subfield.key]"
            :class="inputClasses"
            autocomplete="chrome-off"
            :name="`fields[${subfield.key}]`"
            :style="subfield.styles"
        />
    </div>
</template>

<script>
    import apiConnection from './utils/api-connection';
    import addressComponents from './utils/address-components';
    import configureArrangement from './utils/configure-arrangement';

    export default {
        data() {
            return {
                autocomplete: false,
                inputClasses: [
                    'text',
                    'fullwidth'
                ]
            }
        },
        methods: {

            // Populate address data when Autocomplete selected
            setAddressData(components, coords) {
                let data = this.$root.$data.data;
                // Set all subfield data
                addressComponents(components, data.address);
                // Set coordinates
                data.coords.lat = parseFloat(coords.lat().toFixed(7));
                data.coords.lng = parseFloat(coords.lng().toFixed(7));
            },

            // Get the display array
            subfieldDisplay() {
                // Get the subfield arrangement
                let arrangement = this.$root.$data.settings.subfieldConfig;
                // Return configured arrangement
                return configureArrangement(arrangement);
            }
        },
        async mounted() {
            try {
                const google = await apiConnection();
                const options = {
                    types: ['geocode'],
                    fields: ['address_components','geometry.location','formatted_address']
                };

                /**
                 * Add a hidden "formatted" field to contain the `formatted_address` data.
                 * Add a new "formatted" column to the database to hold the raw formatted address.
                 * If/when the coordinates are incomplete, erase the "formatted" value.
                 */

                /**
                 * Add a new "raw" column to the database to hold a JSON string of the raw Google data.
                 */

                /**
                 * Add a new "zoom" column to the database to hold an integer of the map zoom level.
                 */

                // If no subfields exist, bail
                if (!this.$refs.autocomplete) {
                    return;
                }

                // Get first subfield
                const $first = this.$refs.autocomplete[0];

                // Create an Autocomplete object
                this.autocomplete = new google.maps.places.Autocomplete($first, options);

                // Listen for autocomplete trigger
                this.autocomplete.addListener('place_changed', () => {
                    let place = this.autocomplete.getPlace();
                    this.setAddressData(place.address_components, place.geometry.location);

                    // Get settings
                    let settings = this.$root.$data.settings;

                    // If not changing the map visibility, bail
                    if ('noChange' === settings.mapOnSearch) {
                        return;
                    }

                    // Change map visibility based on settings
                    this.$root.$data.settings.showMap = ('open' === settings.mapOnSearch);
                });

                // Prevent address selection from attempting to submit the form
                google.maps.event.addDomListener($first, 'keydown', (event) => {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                    }
                });

            } catch (error) {

                // Something went wrong
                console.error(error);

            }
        }
    }
</script>

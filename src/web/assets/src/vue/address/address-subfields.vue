<template>
    <div>
        <input
            v-for="subfield in subfieldDisplay()"
            :placeholder="subfield.label"
            :ref="(subfield.enabled ? 'autocomplete' : '')"
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
                autocomplete: false,
                inputClasses: [
                    'text',
                    'fullwidth'
                ]
            }
        },
        mounted() {
            try {
                const google = window.google;
                const options = {
                    fields: [
                        'formatted_address',
                        'address_components',
                        'geometry.location',
                        'name',
                        'place_id'
                    ]
                };

                // If google object doesn't exist yet, log message and bail
                if (!google) {
                    console.error('The `google` object has not yet been loaded.');
                    return;
                }

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

                    // Get newly selected place
                    let place = this.autocomplete.getPlace();

                    // Configure address data based on place
                    this.setAddressData(place);

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
        },
        methods: {

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

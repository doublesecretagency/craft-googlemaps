<template>
    <div class="address-field">
        <address-toggle></address-toggle>
        <address-subfields></address-subfields>
        <address-coords></address-coords>
        <address-meta></address-meta>
        <div style="clear:both"></div>
        <address-map></address-map>
    </div>
</template>

<script>
    import AddressToggle from './address-toggle.vue';
    import AddressSubfields from './address-subfields.vue';
    import AddressCoords from './address-coords.vue';
    import AddressMeta from './address-meta.vue';
    import AddressMap from './address-map.vue';

    export default {
        name: 'AddressField',
        components: {
            'address-toggle': AddressToggle,
            'address-subfields': AddressSubfields,
            'address-coords': AddressCoords,
            'address-meta': AddressMeta,
            'address-map': AddressMap
        },
        props: ['settings', 'data'],
        // data() {
        //     return {
        //         // google: false,
        //         initialized: false,
        //     }
        // },
        computed: {
            // Compute locally for watching
            lat() {
                return this.data.coords['lat'];
            },
            lng() {
                return this.data.coords['lng'];
            }
        },
        methods: {
            // Check whether coordinates are valid
            validCoords(coords) {

                // If no coordinates specified
                if (!coords) {
                    // Use internal coordinates
                    coords = {
                        'lat': this.data.coords['lat'],
                        'lng': this.data.coords['lng']
                    };
                }

                // Loop through coordinates
                for (let key in coords) {
                    // Ignore the zoom value
                    if ('zoom' === key) {
                        continue;
                    }
                    // Get individual coordinate
                    let coord = coords[key];
                    // If coordinate is not a number or string, return false
                    if (!['number','string'].includes(typeof coord)) {
                        return false;
                    }
                    // If coordinate is not numeric, return false
                    if (isNaN(coord)) {
                        return false;
                    }
                    // If coordinate is an empty string, return false
                    if ('' === coord) {
                        return false;
                    }
                }

                // Coordinates are valid!
                return true;

            }
        }
    }
</script>

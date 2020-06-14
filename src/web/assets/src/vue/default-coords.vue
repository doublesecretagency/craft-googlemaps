<template>
    <div>
        <input type="hidden" :name="fieldName('lat')" v-model="coordinatesDefault['lat']" />
        <input type="hidden" :name="fieldName('lng')" v-model="coordinatesDefault['lng']" />
        <input type="hidden" :name="fieldName('zoom')" v-model="coordinatesDefault['zoom']" />
    </div>
</template>

<script>
    export default {
        data() {
            // let coords = this.$root.$data.settings.coordinatesDefault;
            let coords = {
                'lat': null,
                'lng': null,
                'zoom': null
            }

            return {
                coordinatesDefault: coords
            }
        },
        watch: {
            '$root.$data.data.coords': function (coords) {
                this.updateCoords(coords);
            }
        },

        // mounted: function () {
        //     // this.coordinatesDefault = this.$root.$data.settings.coordinatesDefault;
        // },
        methods: {
            fieldName(subfield) {
                const fieldtype = 'doublesecretagency\\googlemaps\\fields\\AddressField';
                return `types[${fieldtype}][coordinatesDefault][${subfield}]`;
            },
            updateCoords: function (coords) {
                this.coordinatesDefault = {
                    'lat': coords.lat,
                    'lng': coords.lng,
                    'zoom': coords.zoom
                }
            }
        },
        // computed: {
        //     coordinatesDefault: function () {
        //         return this.$root.$data.settings.coordinatesDefault;
        //     }
        // }
    }
</script>

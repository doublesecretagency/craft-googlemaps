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
            return {
                // coordinatesDefault: getMapCenter(subfields)
            }
        },
        computed: {
            coordinatesDefault() {
                return this.$root.$data.data.coords;

                // return getMapCenter(this.$root.$data);
                // return this.$root.$data.settings.coordinatesDefault;
            }
        },
        watch: {
            coordsWatcher: function (coords) {
                this.updateCoords(coords);
            }
        },
        // mounted: function () {
        //
        //     let mapCenter = getMapCenter(this.$root.$data);
        //
        //     this.$root.$data.data.coords = mapCenter;
        //
        //     // console.log(mapCenter);
        //     //
        //     // this.coordinatesDefault = mapCenter;
        //     // this.updateCoords(mapCenter);
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
        }
    }
</script>

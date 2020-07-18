<template>
    <div>
        <input type="text" :name="`fields[${handle}][formatted]`" v-model="$root.$data.data.address['formatted']" />
        <input type="text" :name="`fields[${handle}][raw]`" v-model="$root.$data.data.address['raw']" />
    </div>
</template>

<script>
    export default {
        data() {
            return {
                handle: this.$root.$data.handle
            }
        },
        computed: {
            // coordinatesDefault() {
            //
            //     // Get all potential coordinates options
            //     const settingsCoords = this.$root.$data.settings.coordinatesDefault;
            //     const dataCoords     = this.$root.$data.data.coords;
            //
            //     // If coordinates from data are valid, return them
            //     if (dataCoords['lat'] && dataCoords['lng']) {
            //         return dataCoords;
            //     }
            //
            //     // If coordinates from settings are valid, return them
            //     if (settingsCoords['lat'] && settingsCoords['lng']) {
            //         return settingsCoords;
            //     }
            //
            //     // Return empty coordinates
            //     return {
            //         lat: null,
            //         lng: null,
            //         zoom: null
            //     };
            // }
        },
        watch: {
            // coordsWatcher: function (coords) {
            //     this.updateCoords(coords);
            // }
        },
        methods: {
            // updateCoords: function (coords) {
            //     this.coordinatesDefault = {
            //         'lat': coords.lat,
            //         'lng': coords.lng,
            //         'zoom': coords.zoom
            //     }
            // }
        }
    }
</script>

<template>
    <div class="default-coords">
        <input type="hidden" :name="fieldName('lat')" v-model="coordinatesDefault['lat']" />
        <input type="hidden" :name="fieldName('lng')" v-model="coordinatesDefault['lng']" />
        <input type="hidden" :name="fieldName('zoom')" v-model="coordinatesDefault['zoom']" />
    </div>
</template>

<script>
    export default {
        props: ['namespacedName'],
        data() {
            return {
            }
        },
        computed: {
            coordinatesDefault() {

                // Get all potential coordinates options
                const settingsCoords = this.$root.$data.settings.coordinatesDefault;
                const dataCoords     = this.$root.$data.data.coords;

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
        watch: {
            coordsWatcher: function (coords) {
                this.updateCoords(coords);
            }
        },
        methods: {
            fieldName(subfield) {
                // Set the namespaced field name
                return `${this.namespacedName}[${subfield}]`;
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

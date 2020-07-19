<template>
    <div class="gm-map" v-show="settings.showMap">
        <div>Loading map...</div>
    </div>
</template>

<script>
    export default {
        data() {
            // Make map & marker universally available
            return {
                map: null,
                marker: null,
                settings: this.$root.$data.settings
            }
        },
        computed: {
            // Compute locally for watching
            zoom() {
                return this.$root.$data.data.coords['zoom'];
            }
        },
        watch: {
            // When coordinates are changed, update the marker
            '$parent.lat': function () {
                this.updateMarkerPosition();
                this.$root.$data.data.coords['zoom'] = this.map.getZoom();
            },
            '$parent.lng': function () {
                this.updateMarkerPosition();
                this.$root.$data.data.coords['zoom'] = this.map.getZoom();
            },
            zoom() {
                this.updateZoomLevel();
            }
        },
        async mounted() {

            // Attempt to get map center from field
            let fieldPreference = this.fromField(this.$root.$data);

            // Initialize map using coordinates from field data or settings
            if (fieldPreference) {
                this.initMap(fieldPreference);
                return;
            }

            // Attempt to get map center from user's current location
            let promise = await new Promise(function(resolve, reject) {

                // Output console notification
                console.log('Attempting geolocation...');

                // Attempt geolocation
                navigator.geolocation.getCurrentPosition(resolve, reject, {timeout: 5000});

            }).then(
                result => {

                    // Output console notification
                    console.log('Success!');

                    // If coordinates are invalid, bail
                    if (!result.coords) {
                        return;
                    }

                    // Initialize map based on user's current location
                    this.initMap({
                        lat: result.coords.latitude,
                        lng: result.coords.longitude,
                        zoom: 10
                    });

                },
                error => {

                    // Output error message in console
                    console.log('Unable to perform HTML5 geolocation.');

                    // Nothing else worked, use the fallback
                    this.initMap(this.fromFallback());

                }
            );

        },
        methods: {

            // Update the marker position
            updateMarkerPosition() {
                // If coordinates are invalid, bail
                if (!this.$parent.validCoords()) {
                    return;
                }
                // Get coordinates
                let coords = this.$root.$data.data.coords;
                // Set marker position
                this.marker.setPosition({
                    lat: parseFloat(coords.lat.toFixed(7)),
                    lng: parseFloat(coords.lng.toFixed(7))
                });
                // Center map
                this.centerMap();
            },

            // Attempt to get coordinates
            _getCoords(coords) {
                // If invalid coordinates, return false
                if (!this.$parent.validCoords(coords)) {
                    return false;
                }
                // Return coordinates
                return coords;
            },

            // Attempt to get map center coordinates based on the field data or settings
            fromField(field) {

                // If available, get coords from the existing field data
                let dataCoords = this._getCoords(field.data.coords);
                if (dataCoords) {
                    return dataCoords;
                }

                // If available, get default coords from the field settings
                let settingsCoords = this._getCoords(field.settings.coordinatesDefault);
                if (settingsCoords) {
                    return settingsCoords;
                }

                // Unable to get any coordinates from the field
                return false;

            },

            // Use the generic fallback coordinates
            // https://plugins.doublesecretagency.com/google-maps/guides/bermuda-triangle/
            fromFallback() {
                // Bermuda Triangle
                return {
                    lat: 32.3113966,
                    lng: -64.7527469,
                    zoom: 6
                }
            },

            // Update the zoom level
            updateZoomLevel() {

                // Get zoom level from field data
                let zoom = parseInt(this.$root.$data.data.coords['zoom']);

                // Corrections for incorrect zoom value
                if (0 === zoom || zoom < 0) {
                    // Fallback when zoom is too low
                    zoom = 0;
                } else if (!zoom || isNaN(zoom)) {
                    // Fallback when zoom is invalid
                    zoom = 11;
                }

                // Set map zoom level
                this.map.setZoom(zoom);
            },

            // Center map based on current marker position
            centerMap() {

                // Get coordinates
                let coords = JSON.parse(JSON.stringify(this.$root.$data.data.coords));

                // If missing coordinates, bail
                if (!coords['lat'] || !coords['lng']) {
                    return;
                }

                // Center map on marker coordinates
                this.map.panTo(coords);
            },

            // Initialize map
            initMap(startingPosition) {
                try {
                    const google = window.google;

                    // Determine map center
                    let mapCenter = {
                        lat: parseFloat(startingPosition.lat),
                        lng: parseFloat(startingPosition.lng)
                    }

                    // Create the map
                    this.map = new google.maps.Map(this.$el, {
                        streetViewControl: false,
                        fullscreenControl: false,
                        center: mapCenter,
                        zoom: parseInt(startingPosition.zoom)
                    });

                    // Create a draggable marker
                    this.marker = new google.maps.Marker({
                        position: mapCenter,
                        map: this.map,
                        draggable: true
                    });

                    // When marker is dropped, re-center the map
                    google.maps.event.addListener(this.marker, 'dragend', () => {
                        let position = this.marker.getPosition();
                        this.$root.$data.data.coords = {
                            'lat': parseFloat(position.lat().toFixed(7)),
                            'lng': parseFloat(position.lng().toFixed(7)),
                            'zoom': this.map.getZoom()
                        };
                        this.centerMap();
                    });

                    // When map is zoomed, update zoom value
                    google.maps.event.addListener(this.map, 'zoom_changed', () => {
                        this.$root.$data.data.coords['zoom'] = this.map.getZoom();
                    });

                } catch (error) {

                    // Unable to initialize the map
                    console.error(error);

                }
            }
        }
    };
</script>

<style lang="scss" scoped>
.gm-map {
    height: 240px;
    width: 99%;
    margin-right: 1%;
    margin-top: 2px;
    text-align: center;
    background-color: #f3f7fc;
    border: 1px solid #d7dfe7;
    border-radius: 3px;
    box-shadow: 0 1px 3px 0 rgba(0,0,0,.1), 0 1px 2px 0 rgba(0,0,0,.06);
    div {
        color: #606d7b;
        font-weight: bold;
        font-style: italic;
        margin: 110px auto 0;

    }
}
</style>

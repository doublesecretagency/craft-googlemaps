<template>
    <div class="gm-map" v-show="settings.showMap">
        <div>Loading map...</div>
    </div>
</template>

<script>
    import apiConnection from './utils/api-connection';

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
            // Compute coordinates locally, so we can watch them
            lat() {
                return this.$root.$data.data.coords['lat'];
            },
            lng() {
                return this.$root.$data.data.coords['lng'];
            },
            zoom() {
                return this.$root.$data.data.coords['zoom'];
            }
        },
        watch: {
            // When coordinates are changed, update the marker
            lat() {
                this.updateMarkerPosition();
                this.$root.$data.data.coords['zoom'] = this.map.getZoom();
            },
            lng() {
                this.updateMarkerPosition();
                this.$root.$data.data.coords['zoom'] = this.map.getZoom();
            },
            zoom() {
                this.updateZoomLevel();
            }
        },
        async mounted() {
            try {
                const google = await apiConnection();

                // Get the initial marker position
                let startingPosition = this.getStartingPosition();

                // Create the map
                this.map = new google.maps.Map(this.$el, {
                    streetViewControl: false,
                    fullscreenControl: false,
                    center: startingPosition,
                    zoom: startingPosition.zoom
                });

                // Create a draggable marker
                this.marker = new google.maps.Marker({
                    position: startingPosition,
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
                console.error(error);
            }
        },
        methods: {
            // getGeolocation() {
            //     // Does the browser support geolocation?
            //     if (!('geolocation' in navigator)) {
            //         // 'Geolocation is not available.';
            //         return;
            //     }
            //     console.log('Starting geolocation...');
            //     // This doesn't really work in local. Boo.
            //     navigator.geolocation.getCurrentPosition(position => {
            //         console.log('Geolocation successful');
            //         console.log(position);
            //     },error => {
            //         console.log('Geolocation failed');
            //         console.log(error);
            //     },{
            //         // Not worth waiting more than 3 seconds
            //         timeout: 3000
            //     })
            // },

            // Update the marker position
            updateMarkerPosition() {
                let coords = this.$root.$data.data.coords;
                this.marker.setPosition({
                    lat: parseFloat(coords['lat'].toFixed(7)),
                    lng: parseFloat(coords['lng'].toFixed(7))
                });
                this.centerMap();
            },

            // Update the zoom level
            updateZoomLevel() {
                this.map.setZoom(this.$root.$data.data.coords['zoom']);
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

            // Determine starting point of the marker
            getStartingPosition() {

                // Get universal coordinates
                const coords = this.$root.$data.data.coords;

                // If the field data already has coordinates, use them
                if (coords['lat'] && coords['lng']) {
                    return {
                        lat: coords['lat'],
                        lng: coords['lng'],
                        zoom: coords['zoom']
                    };
                }

                // If a default marker position is set, use that
                if (this.settings.coordinatesDefault) {
                    return JSON.parse(JSON.stringify(this.settings.coordinatesDefault));
                }

                // Attempt to get starting location
                // via HTML 5 user geolocation
                // this.getGeolocation();

                // Nothing else worked, send them to
                // the Bermuda Triangle as a fallback
                return {
                    lat: 32.3113966,
                    lng: -64.7527469,
                    zoom: 6
                };
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
    border: 1px solid #D7DFE7;
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

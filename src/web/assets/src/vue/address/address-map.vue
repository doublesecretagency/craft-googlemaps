<template>
    <div class="gm-map" v-show="addressStore.settings.showMap">
        <div>Loading map...</div>
    </div>
</template>

<script>
// Import Pinia
import { mapStores } from 'pinia';
import { useAddressStore } from '../stores/AddressStore';

export default {
    data() {
        // Make map & marker universally available
        return {
            map: null,
            marker: null,
        }
    },
    computed: {
        // Load Pinia store
        ...mapStores(useAddressStore),
    },
    watch: {
        // When coordinates are changed, update the marker position
        'addressStore.data.coords.lat': function () {
            this._updateMarkerPosition();
        },
        'addressStore.data.coords.lng': function () {
            this._updateMarkerPosition();
        },
        // When zoom level is changed, update the map zoom
        'addressStore.data.coords.zoom': function () {
            this._updateZoomLevel();
        }
    },
    async mounted() {

        // Attempt to get map center from field
        let center = this._getFieldCenter();

        // Initialize map using coordinates from field data or settings
        if (center) {
            this.initMap(center);
            return;
        }

        // Attempt to get map center from user's current location
        await new Promise(function(resolve, reject) {

            // Output console notification
            console.log('Attempting geolocation...');
            // Attempt geolocation
            navigator.geolocation.getCurrentPosition(resolve, reject, {timeout: 5000});

        }).then(

            // SUCCESS
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

            // FAILED
            error => {
                // Output error message in console
                console.warn('[GM] Unable to perform HTML5 geolocation.', error);
                // Use the generic fallback coordinates (Bermuda Triangle)
                // https://plugins.doublesecretagency.com/google-maps/guides/bermuda-triangle/
                this.initMap({
                    lat: 32.3113966,
                    lng: -64.7527469,
                    zoom: 6
                });
            }

        );

    },
    methods: {

        /**
         * Initialize the map.
         */
        initMap(startingPosition)
        {
            // Get the Pinia store
            const addressStore = useAddressStore();

            try {
                const google = window.google;

                // If google object doesn't exist yet, log message and bail
                if (!google) {
                    console.error('[GM] The `google` object has not yet been loaded.');
                    return;
                }

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
                    zoom: parseInt(startingPosition.zoom),
                    controlSize: addressStore.settings.controlSize
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
                    addressStore.data.coords = {
                        'lat': parseFloat(position.lat().toFixed(7)),
                        'lng': parseFloat(position.lng().toFixed(7)),
                        'zoom': this.map.getZoom()
                    };
                    this._centerMap();
                });

                // When map is zoomed, update zoom value
                google.maps.event.addListener(this.map, 'zoom_changed', () => {
                    addressStore.data.coords['zoom'] = this.map.getZoom() || 11;
                });

            } catch (error) {

                // Unable to initialize the map
                console.error(error);

            }
        },

        // ========================================================================= //

        /**
         * Center map based on current marker position.
         */
        _centerMap()
        {
            // Get the Pinia store
            const addressStore = useAddressStore();

            // Get coordinates
            let coords = JSON.parse(JSON.stringify(addressStore.data.coords));

            // If missing coordinates, bail
            if (!coords['lat'] || !coords['lng']) {
                return;
            }

            // Center map on marker coordinates
            this.map.panTo(coords);
        },

        // ========================================================================= //

        /**
         * Attempt to get map center coordinates based on the field data or settings.
         */
        _getFieldCenter()
        {
            // Get the Pinia store
            const a = useAddressStore();

            // If valid, get coords from the existing field data
            if (a.validateCoords(a.data.coords)) {
                return a.data.coords;
            }

            // If valid, get default coords from the field settings
            if (a.validateCoords(a.settings.coordinatesDefault)) {
                return a.settings.coordinatesDefault;
            }

            // Unable to get any coordinates from the field
            return false;
        },

        // ========================================================================= //

        /**
         * Update the marker position.
         */
        _updateMarkerPosition()
        {
            // Get the Pinia store
            const addressStore = useAddressStore()

            // Check whether field has valid coordinates
            const validCoords = addressStore.validateCoords(addressStore.data.coords);

            // If field does not have valid coordinates, bail
            if (!validCoords) {
                return;
            }

            // Get coordinates
            let coords = addressStore.data.coords;

            // Set marker position
            this.marker.setPosition({
                lat: parseFloat(coords.lat.toFixed(7)),
                lng: parseFloat(coords.lng.toFixed(7))
            });

            // Center map
            this._centerMap();

            // Update zoom based on map level
            addressStore.data.coords['zoom'] = this.map.getZoom();
        },

        /**
         * Update the zoom level.
         */
        _updateZoomLevel()
        {
            // Get the Pinia store
            const addressStore = useAddressStore();

            // Get zoom level from field data
            let zoom = parseInt(addressStore.data.coords['zoom']);

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

    }
};
</script>

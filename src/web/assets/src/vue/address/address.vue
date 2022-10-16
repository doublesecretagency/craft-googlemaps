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
// Import Pinia
import { mapStores } from 'pinia';
import { useAddressStore } from '../stores/AddressStore';

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
    props: {
        namespace: Object,
        settings: Object,
        data: Object,
        images: Object
    },
    computed: {
        // Load Pinia store
        ...mapStores(useAddressStore)
    },
    setup(props) {
        // Get the Pinia store
        const addressStore = useAddressStore();

        // Set Pinia values from props
        addressStore.namespace = props.namespace;
        addressStore.settings = props.settings;
        addressStore.data = props.data;
        addressStore.images = props.images;

        // Whether to show the map by default
        addressStore.showMap = props.settings.showMap;
    },
}
</script>

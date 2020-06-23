<template>
    <div class="address-field">
        <address-toggle></address-toggle>
        <address-subfields></address-subfields>
        <address-coords></address-coords>
        <div style="clear:both"></div>
        <address-map></address-map>
    </div>
</template>

<script>
    import AddressToggle from './address-toggle';
    import AddressSubfields from './address-subfields';
    import AddressCoords from './address-coords';
    import AddressMap from './address-map';

    export default {
        name: 'AddressField',
        components: {
            'address-toggle': AddressToggle,
            'address-subfields': AddressSubfields,
            'address-coords': AddressCoords,
            'address-map': AddressMap
        },
        props: ['settings', 'data'],
        // data() {
        //     return {
        //         // google: false,
        //         initialized: false,
        //     }
        // },
        // methods: {
        //     loadGoogleMaps: () => {
        //
        //     }
        // }
    }
</script>

<style lang="scss">
    .address-field {
        width: 101%;
        input {
            margin-right: 1%;
            margin-bottom: 2px;
        }
    }
</style>

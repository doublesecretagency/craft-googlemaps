<template>
    <div>
        <address-expand></address-expand>
        <address-subfields></address-subfields>
        <address-coords></address-coords>
        <div style="clear:both"></div>
        <address-map></address-map>
    </div>
</template>

<script>
    import AddressExpand from './address-expand';
    import AddressSubfields from './address-subfields';
    import AddressCoords from './address-coords';
    import AddressMap from './address-map';

    export default {
        name: 'AddressField',
        components: {
            'address-expand': AddressExpand,
            'address-subfields': AddressSubfields,
            'address-coords': AddressCoords,
            'address-map': AddressMap
        },
        data() {
            return {
            }
        },
        props: ['settings', 'data']
    }
</script>

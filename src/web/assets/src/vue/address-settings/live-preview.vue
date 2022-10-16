<template>
    <div class="field">
        <div class="heading">
            <label>Preview</label>
            <div class="instructions">
                <p>This is how the field will look...</p>
            </div>
        </div>
        <div>
            <address-field
                :namespace="{id:'', name:'', handle:''}"
                :settings="addressSettingsStore.settings"
                :data="addressSettingsStore.data"
                :images="addressSettingsStore.images"
            ></address-field>
            <default-coords></default-coords>
        </div>
    </div>
</template>

<script>
// Import Pinia
import { mapStores } from 'pinia';
import { useAddressSettingsStore } from '../stores/AddressSettingsStore';

import AddressField from '../address/address';
import DefaultCoords from './default-coords';

export default {
    components: {
        AddressField,
        DefaultCoords
    },
    computed: {
        // Load Pinia store
        ...mapStores(useAddressSettingsStore)
    },
}
</script>

<style scoped>
.field .heading {
    margin-right: 94px;
}
</style>

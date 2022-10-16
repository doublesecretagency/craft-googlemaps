<template>
    <div id="address-settings" class="address-settings">
        <div class="address-settings-fields">
            <subfield-manager></subfield-manager>
            <dropdown-fields></dropdown-fields>
        </div>
        <div class="address-settings-preview">
            <live-preview></live-preview>
            <additional-notes></additional-notes>
        </div>
    </div>
</template>

<script>
// Import Pinia
import { mapStores } from 'pinia';
import { useAddressSettingsStore } from '../stores/AddressSettingsStore';

import AdditionalNotes from './additional-notes';
import DropdownFields from './dropdown-fields';
import LivePreview from './live-preview';
import SubfieldManager from './subfield-manager';

export default {
    name: 'AddressSettingsField',
    components: {
        AdditionalNotes,
        DropdownFields,
        LivePreview,
        SubfieldManager,
    },
    computed: {
        // Load Pinia store
        ...mapStores(useAddressSettingsStore)
    },
}
</script>

<style scoped>
.address-settings {
    display: flex;
    flex-direction: row;
}
.address-settings-fields {
    flex-direction: column;
    flex: 4;
    margin-right: 30px;
}
.address-settings-preview {
    flex-direction: column;
    flex: 5;
}

/* Contained within a Matrix field */
.mc-fieldtype-settings .address-settings {
    display: block;
}
.mc-fieldtype-settings .address-settings-fields {
    margin-right: auto;
    margin-bottom: 24px;
}
.mc-fieldtype-settings .address-settings-preview {}
</style>

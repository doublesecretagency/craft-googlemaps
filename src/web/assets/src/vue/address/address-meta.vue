<template>
    <div>
        <input type="hidden" :name="`${addressStore.namespace.name}[formatted]`" v-model="addressStore.data.address['formatted']" />
        <input type="hidden" :name="`${addressStore.namespace.name}[raw]`" v-model="addressStore.data.address['raw']" />
    </div>
</template>

<script>
// Import Pinia
import { mapStores } from 'pinia';
import { useAddressStore } from '../stores/AddressStore';

export default {
    computed: {
        // Load Pinia store
        ...mapStores(useAddressStore),
    }
}
</script>

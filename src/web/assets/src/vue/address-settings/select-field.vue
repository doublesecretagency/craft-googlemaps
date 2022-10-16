<template>
    <div :id="`${namespace.id}-field`" :data-attribute="`${namespace.id}`" class="field">
        <div class="heading">
            <label :id="`${namespace.id}-label`" :for="namespace.id">{{ label }}</label>
        </div>
        <div :id="`${namespace.id}-instructions`" class="instructions">
            <p v-html="instructions"></p>
        </div>
        <div class="input ltr">
            <div class="select">
                <select
                    :id="namespace.id"
                    :name="getName(namespace.name)"
                    :aria-describedby="`${namespace.id}-instructions`"
                    :aria-labelledby="`${namespace.id}-label`"
                    v-model="addressSettingsStore.settings[model]"
                >
                    <option
                        v-for="(opt,val) in addressSettingsStore.options[model]"
                        :value="val"
                    >{{ opt }}</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>
// Import Pinia
import { mapStores } from 'pinia';
import { useAddressSettingsStore } from '../stores/AddressSettingsStore';

export default {
    props: {
        label: String,
        instructions: String,
        model: String,
        namespace: Object,
    },
    computed: {
        // Load Pinia store
        ...mapStores(useAddressSettingsStore),
    },
    methods: {
        // Get namespaced field name
        getName(setting) {
            // Get the Pinia store
            const addressSettingsStore = useAddressSettingsStore();
            // Return the namespaced field name
            return addressSettingsStore.getName(`[${setting}]`);
        },
    }
}
</script>

<template>
    <div :id="`${namespace.id}-field`" :data-attribute="namespace.id" :class="`field ${namespace.id}-field`" ref="container">
        <div class="heading">
            <label :id="`${namespace.id}-label`" :for="namespace.id">
                {{ label }}<span v-if="markRequired" class="visually-hidden">Required</span><span v-if="markRequired" aria-hidden="true" class="required"></span>
            </label>
        </div>
        <div :id="`${namespace.id}-instructions`" class="instructions">
            <p v-html="instructions"></p>
        </div>
        <div class="input ltr">
            <div class="lightswitch-outer-container">
                <span :id="`${namespace.id}-desc`" class="visually-hidden">Check for {{ onLabel }}.</span>
                <div class="lightswitch-inner-container">
                    <button
                        type="button"
                        :id="namespace.id"
                        role="switch"
                        :aria-checked="required"
                        :aria-labelledby="`${namespace.id}-label`"
                        :aria-describedby="`${namespace.id}-instructions ${namespace.id}-desc`"
                        class="lightswitch has-labels"
                        :class="{'on': required}"
                        ref="button"
                    >
                        <div class="lightswitch-container"><div class="handle"></div></div>
                        <input
                            type="hidden"
                            :name="getName(namespace.name)"
                            :value="(required ? 1 : null)"
                        />
                    </button>
                    <span data-toggle="on" aria-hidden="true" v-html="onLabel"></span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// Import Pinia
import { mapStores } from 'pinia';
import { useAddressSettingsStore } from '../stores/AddressSettingsStore';

export default {
    mounted() {
        // Activate the lightswitch toggle
        $('.lightswitch', this.$refs.container).lightswitch();
        // Watch the toggle switch to see if it changes
        this.watchToggle();
    },
    props: {
        label: String,
        instructions: String,
        onLabel: String,
        namespace: Object,
        markRequired: Boolean,
    },
    computed: {
        // Load Pinia store
        ...mapStores(useAddressSettingsStore),

        /**
         * Whether coordinates are required.
         */
        required() {
            // Get the Pinia store
            const addressSettingsStore = useAddressSettingsStore();
            // Return whether coordinates are required
            return addressSettingsStore.settings.requireCoordinates;
        }
    },
    methods: {

        /**
         * Get namespaced field name.
         */
        getName(setting) {
            // Get the Pinia store
            const addressSettingsStore = useAddressSettingsStore();
            // Return the namespaced field name
            return addressSettingsStore.getName(`[${setting}]`);
        },

        /**
         * Watch the toggle switch to see if it changes.
         */
        watchToggle() {
            // Create mutation observer
            const observer = new MutationObserver(() => {
                // Get the Pinia store
                const addressSettingsStore = useAddressSettingsStore();
                // Update whether coordinates are required
                addressSettingsStore.settings.requireCoordinates = ('true' === this.$refs.button.ariaChecked);
            });
            // Listen to attribute changes on the button
            observer.observe(this.$refs.button, {attributes: true});
        }

    }
}
</script>

<style scoped>
.address-settings-fields .lightswitch-inner-container {
    padding-left: 4px;
}
</style>

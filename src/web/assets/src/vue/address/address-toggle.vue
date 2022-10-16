<template>
    <span class="map-toggle-container">
        <span
            class="map-toggle"
            v-if="'hidden' !== config.style"
            @click="addressStore.changeVisibility()"
            :style="{'margin-top': `${marginTop}px`}"
        >
            <span v-if="'icon' !== config.style">{{ config.text }}</span>
            <img
                class="map-toggle-icon"
                v-if="'text' !== config.style"
                alt="Marker icon"
                :title="'icon' === config.style ? config.text : false"
                :src="config.icon"
            />
        </span>
    </span>
</template>

<script>
// Import Pinia
import { mapStores } from 'pinia';
import { useAddressStore } from '../stores/AddressStore';

export default {
    data(){
        return {
            // Field container not mounted by default
            container: null
        }
    },
    computed: {
        // Load Pinia store
        ...mapStores(useAddressStore),

        /**
         * Get toggle formatting configuration.
         */
        config()
        {
            // Get the Pinia store
            const addressStore = useAddressStore();
            // Return configuration
            return addressStore.configToggle;
        },

        /**
         * Dynamically set the top margin.
         */
        marginTop()
        {
            // Set default toggle offset
            let toggleOffset = -25;

            // If the toggle is hidden
            if ('hidden' === this.config.style) {
                // Return the default offset
                return toggleOffset;
            }

            // If field container is not fully mounted
            if (!this.container) {
                // Return the default offset
                return toggleOffset;
            }

            // Look at parent element to get nearby items
            let copyTextBtn  = this.container.getElementsByClassName('copytextbtn');
            let instructions = this.container.getElementsByClassName('instructions');

            // If "copy" div is visible
            if (copyTextBtn.length) {
                // Compile new class
                const toggleClass = `gm-toggle-${this.config.style}`;
                // Add new class to "copy" div
                copyTextBtn[0].classList.add(toggleClass);
            }

            // If field has instructions
            if (instructions.length) {
                // Measure height of instructions div
                let height = instructions[0].clientHeight;
                // Adjust toggle offset accordingly
                toggleOffset -= height;
            }

            // Return the adjusted toggle offset
            return toggleOffset;
        },
    },
    mounted(){
        // Find the parent field container
        this.container = this.$el.closest('.field');
    }
}
</script>

<style scoped>
.map-toggle {
    float: right;
    margin-right: 8px;
    cursor: pointer;
}
.map-toggle-icon {
    display: inline;
    height: 14px;
    margin-left: 5px;
    margin-bottom: -2px;
}
</style>

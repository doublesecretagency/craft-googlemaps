<template>
    <span
        v-if="'hidden' !== toggleMode"
        @click="toggle()"
        :style="{
            'float': 'right',
            'margin-top': marginTop,
            'margin-right': '8px',
            'cursor': 'pointer',
        }"
    >
        <span v-if="'icon' !== toggleMode">{{ toggleText }}</span>
        <img
            v-if="'text' !== toggleMode"
            alt="Marker icon"
            :title="'icon' === toggleMode ? toggleText : false"
            :src="markerIcon"
            :style="{
                'height': '14px',
                'margin-left': '2px',
                'margin-bottom': '-2px'
            }"
        />
    </span>
</template>

<script>
    export default {
        data() {
            return {
                toggleOffset: -25,
            }
        },
        computed: {
            marginTop() {
                return `${this.toggleOffset}px`;
            },
            toggleMode() {
                return this.$root.$data.settings.visibilityToggle;
            },
            toggleText() {
                return (this.showMap ? 'Hide Map' : 'Show Map');
            },
            showMap() {
                return this.$root.$data.settings.showMap;
            },
            markerIcon() {
                let icons = this.$root.$data.icons;
                return (this.showMap ? icons.markerHollow : icons.marker);
            }
        },
        mounted() {
            this.adjustTogglePosition();
        },
        methods: {
            adjustTogglePosition() {

                // Find the field instructions div
                let $container = this.$el.closest('.field');
                let instructions = $container.getElementsByClassName('instructions');

                // If no field instructions, bail
                if (!instructions.length) {
                    return;
                }

                // Get height of instructions div
                let height = instructions[0].clientHeight;

                // Recalculate toggle offset
                this.toggleOffset -= height;

            },
            toggle() {
                // Show or hide the map
                let showMap = this.$root.$data.settings.showMap;
                this.$root.$data.settings.showMap = !showMap;
            }
        }
    }
</script>

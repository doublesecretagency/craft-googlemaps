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

                // Look at parent element to get nearby items
                let $container = this.$el.closest('.field');
                let copyTextBtn = $container.getElementsByClassName('copytextbtn');
                let instructions = $container.getElementsByClassName('instructions');

                // If "copy" div is visible
                if (copyTextBtn.length) {
                    // Compile new class
                    const toggleClass = `gm-toggle-${this.toggleMode}`;
                    // Add new class to "copy" div
                    copyTextBtn[0].classList.add(toggleClass);
                }

                // If field has instructions
                if (instructions.length) {
                    // Measure height of instructions div
                    let height = instructions[0].clientHeight;
                    // Adjust toggle offset accordingly
                    this.toggleOffset -= height;
                }

            },
            toggle() {
                // Show or hide the map
                let showMap = this.$root.$data.settings.showMap;
                this.$root.$data.settings.showMap = !showMap;
            }
        }
    }
</script>

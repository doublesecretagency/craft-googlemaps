<template>
    <span
        v-if="'hidden' !== toggleMode"
        @click="toggle()"
        :style="{
            'float': 'right',
            'margin-top': '-25px',
            'margin-right': marginRight,
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
            let settings = this.$root.$data.settings;
            return {
                marginRight: (settings.usingCpFieldInspect ? '18px' : '4px')
            }
        },
        methods: {
            toggle() {
                // Show or hide the map
                let showMap = this.$root.$data.settings.showMap;
                this.$root.$data.settings.showMap = !showMap;
            }
        },
        computed: {
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
            },
        }
    }
</script>

<template>
    <!--
     - DEFAULT
     - Without CP Field Inspect:
     -  'margin-right': '4px'
     -->
    <!--
     - With CP Field Inspect:
     -->
    <span
        @click="toggle()"
        :style="{
            'float': 'right',
            'margin-top': '-25px',
            'margin-right': marginRight,
            'cursor': 'pointer',
        }"
    >
        {{ showMap ? 'Hide Map' : 'Show Map' }}
        <img
            :src="markerIcon"
            alt="Marker icon"
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
            let usingCpFieldInspect = true;
            return {
                marginRight: (usingCpFieldInspect ? '18px' : '4px')
            }
        },
        computed: {
            showMap() {
                return this.$root.$data.settings.showMap;
            },
            markerIcon() {
                let icons = this.$root.$data.icons;
                return (this.showMap ? icons.markerHollow : icons.marker);
            },
        },
        methods: {
            toggle() {
                // Show or hide the map
                let showMap = this.$root.$data.settings.showMap;
                this.$root.$data.settings.showMap = !showMap;
            }
        }
    }
</script>

<template>
    <div>
        <input
            v-for="coord in coordinatesDisplay()"
            :placeholder="coord.label"
            :type="getType"
            :readonly="getReadOnly"
            :class="getInputClasses"
            v-model.number="$root.$data.data.coords[coord.key]"
            autocomplete="chrome-off"
            :name="`${namespacedName}[${coord.key}]`"
            :style="coord.styles"
        />
    </div>
</template>

<script>
    export default {
        data() {
            return {
                handle: this.$root.$data.handle,
                namespacedName: this.$root.$data.namespacedName
            }
        },
        computed: {
            getType() {
                // What type of input should the coordinate fields be?
                return ('hidden' === this.$root.$data.settings.coordinatesMode ? 'hidden' : 'number');
            },
            getReadOnly() {
                // Whether the coordinate fields should be read-only
                return !['editable','hidden'].includes(this.$root.$data.settings.coordinatesMode);
            },
            getInputClasses() {
                // Get the coordinates mode from settings
                let mode = this.$root.$data.settings.coordinatesMode;

                // If hidden, return empty array
                if ('hidden' === mode) {
                    return [];
                }

                // Return array of input classes
                return [
                    'text',
                    'code',
                    'fullwidth',
                    ('editable' !== mode ? 'disabled' : null)
                ];
            }
        },
        methods: {
            // Get the display array
            coordinatesDisplay() {
                return [
                    {
                        key: 'lat',
                        label: 'Latitude',
                        styles: {'width': '43%'}
                    },
                    {
                        key: 'lng',
                        label: 'Longitude',
                        styles: {'width': '43%'}
                    },
                    {
                        key: 'zoom',
                        label: 'Zoom',
                        styles: {'width': '11%'}
                    }
                ];
            }
        }
    }
</script>

<style scoped>
    .disabled {
        opacity: 0.60;
        background-color: #e4eaf4;
    }
</style>

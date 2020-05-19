<template>
    <div>
        <input
            v-for="coord in coordinatesDisplay()"
            :placeholder="coord.label"
            :type="getType"
            :readonly="getReadOnly"
            v-model.number="$root.$data.data.coords[coord.key]"
            :class="inputClasses"
            autocomplete="chrome-off"
            :name="`fields[${coord.key}]`"
            :style="coord.styles"
        />
    </div>
</template>

<script>
    export default {
        data() {
            // Get the coordinates mode from settings
            let mode = this.$root.$data.settings.coordinatesMode;

            // Initialize the input classes
            let inputClasses = [];

            // If the coordinates aren't hidden, add classes
            if ('hidden' !== mode) {
                inputClasses = [
                    'text',
                    'code',
                    'fullwidth',
                    ('editable' !== mode ? 'disabled' : null)
                ];
            }

            // Return data
            return {
                mode: mode,
                inputClasses: inputClasses
            }
        },
        methods: {
            // Get the display array
            coordinatesDisplay() {
                return [
                    {
                        key: 'lat',
                        label: 'Latitude',
                        styles: {
                            'float': 'left',
                            'width': '44%',
                            'margin-top': '2px'
                        }
                    },
                    {
                        key: 'lng',
                        label: 'Longitude',
                        styles: {
                            'float': 'left',
                            'width': '43%',
                            'margin-left': '1%',
                            'margin-top': '2px'
                        }
                    },
                    {
                        key: 'zoom',
                        label: 'Zoom',
                        styles: {
                            'float': 'left',
                            'width': '11%',
                            'margin-left': '1%',
                            'margin-top': '2px'
                        }
                    }
                ];
            }
        },
        computed: {
            getType() {
                // What type of input should the coordinate fields be?
                return ('hidden' === this.mode ? 'hidden' : 'number');
            },
            getReadOnly() {
                // Whether the coordinate fields should be read-only
                return !['editable','hidden'].includes(this.mode)
            }
        }
    }
</script>

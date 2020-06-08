<template>
    <table class="shadow-box editable">
        <thead>
        <tr>
            <th>Label</th>
            <th>Width</th>
            <th>Enable</th>
            <th>Require</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><input type="text" value="Street Address" placeholder="subfieldHandle" /></td>
            <td><input type="number" value="" /></td>
            <td><input type="checkbox" value="1" /></td>
            <td><input type="checkbox" value="1" /></td>
            <td class="thin action"><a class="move icon" title="Reorder"></a></td>
<!--            <td><input type="number" value="{# {{ subfield.width }} #}" /></td>-->
<!--            <td><input type="checkbox" value="1" {# {{ subfield.enabled ? 'checked="checked"'|raw }} #} /></td>-->
<!--            <td><input type="checkbox" value="1" {# {{ subfield.required ? 'checked="checked"'|raw }} #} /></td>-->
<!--            <td class="thin action"><a class="move icon" title="{{ 'Reorder'|t('google-maps') }}"></a></td>-->
        </tr>
        </tbody>
    </table>
</template>

<script>
    export default {
        props: ['settings', 'data'],
        data() {



            console.log(this.$root.$data.settings.subfieldConfig);



            // let settings = this.$root.$data.settings;
            return {
                // toggleOffset: -25,
                // marginRight: (settings.usingCpFieldInspect ? '18px' : '4px')
            }
        },
        computed: {
            // marginTop() {
            //     return `${this.toggleOffset}px`;
            // },
            // toggleMode() {
            //     return this.$root.$data.settings.visibilityToggle;
            // },
            // toggleText() {
            //     return (this.showMap ? 'Hide Map' : 'Show Map');
            // },
            // showMap() {
            //     return this.$root.$data.settings.showMap;
            // },
            // markerIcon() {
            //     let icons = this.$root.$data.icons;
            //     return (this.showMap ? icons.markerHollow : icons.marker);
            // }
        },
        methods: {
            // adjustTogglePosition() {
            //
            //     // Find the field instructions div
            //     let $container = this.$el.closest('.field');
            //     let instructions = $container.getElementsByClassName('instructions');
            //
            //     // If no field instructions, bail
            //     if (!instructions.length) {
            //         return;
            //     }
            //
            //     // Get height of instructions div
            //     let height = instructions[0].clientHeight;
            //
            //     // Recalculate toggle offset
            //     this.toggleOffset -= height;
            //
            // },
            // toggle() {
            //     // Show or hide the map
            //     let showMap = this.$root.$data.settings.showMap;
            //     this.$root.$data.settings.showMap = !showMap;
            // }
        }
    }
</script>

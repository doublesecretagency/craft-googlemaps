<template>
    <table id="fields-myTable" class="editable fullwidth">
        <thead>
            <tr>
                <th scope="col" class="singleline-cell textual">Label</th>
                <th scope="col" class="number-cell textual" style="text-align:right">Width</th>
                <th scope="col" class="checkbox-cell" style="text-align:center" title="Include a subfield in the visible layout.">Show</th>
                <th scope="col" class="checkbox-cell" style="text-align:center" title="Require a subfield when saving an address.">Req.</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="subfield in subfieldConfig()">
                <td class="singleline-cell textual">
                    <textarea name="" v-model="$root.$data.settings.subfieldConfig[subfield.key].label" rows="1" style="min-height: 34px;" :placeholder="subfield.key"></textarea>
                </td>
                <td class="textual code" style="width:15%; text-align:right;">
                    <input type="number" name="" v-model="$root.$data.settings.subfieldConfig[subfield.key].width" style="min-height: 34px; max-width:60px; text-align:right; border:none;">
                </td>
                <td class="checkbox-cell" style="width:15%; text-align:center;">
                    <div class="checkbox-wrapper">
                        <input type="hidden" name="" value="" /><input type="checkbox" v-model="$root.$data.settings.subfieldConfig[subfield.key].enabled" :id="`enabled-${subfield.key}`" class="checkbox" name="" /><label :for="`enabled-${subfield.key}`"></label>
                    </div>
                </td>
                <td class="checkbox-cell" style="width:15%; text-align:center;">
                    <div class="checkbox-wrapper">
                        <input type="hidden" name="" value="" /><input type="checkbox" v-model="$root.$data.settings.subfieldConfig[subfield.key].required" :id="`required-${subfield.key}`" class="checkbox" name="" /><label :for="`required-${subfield.key}`"></label>
                    </div>
                </td>
                <td class="thin action"><a class="move icon" title="Reorder"></a></td>
            </tr>
        </tbody>
    </table>

</template>

<script>
    import subfieldConfig from './utils/subfield-config';

    export default {
        props: ['settings', 'data'],
        data() {
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
            // Get the display array
            subfieldConfig() {
                // Get the subfield arrangement
                let arrangement = this.$root.$data.settings.subfieldConfig;
                // Return configured arrangement
                return subfieldConfig(arrangement);
            }
        }
    }
</script>

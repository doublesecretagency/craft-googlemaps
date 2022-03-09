<template>
    <table class="editable fullwidth">
        <thead>
            <tr>
                <th scope="col" class="singleline-cell textual">Label</th>
                <th scope="col" class="number-cell textual" style="text-align:right">Width</th>
                <th scope="col" class="checkbox-cell" style="text-align:center" title="Include a subfield in the visible layout.">Show</th>
                <th scope="col" class="checkbox-cell" style="text-align:center" title="Show autocomplete matches when typing.">Auto</th>
<!--                <th scope="col" class="checkbox-cell" style="text-align:center" title="Require a subfield when saving an address.">Req.</th>-->
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody ref="sortable">
            <tr v-for="subfield in subfieldConfig" :class="{'disabled': !$root.$data.settings.subfieldConfig[subfield.key].enabled}" :data-handle="subfield.key">
                <td class="singleline-cell textual">
                    <textarea :name="fieldName(subfield.key, 'label')" v-model="$root.$data.settings.subfieldConfig[subfield.key].label" rows="1" style="min-height: 34px;" :placeholder="subfield.key"></textarea>
                </td>
                <td class="textual code" style="width:15%; text-align:right;">
                    <input type="number" :name="fieldName(subfield.key, 'width')" v-model.number="$root.$data.settings.subfieldConfig[subfield.key].width" style="min-height: 34px; max-width:60px; text-align:right; border:none;">
                </td>
                <td class="checkbox-cell" style="width:15%; text-align:center;">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" :name="fieldName(subfield.key, 'enabled')" v-model="$root.$data.settings.subfieldConfig[subfield.key].enabled" :id="`enabled-${subfield.key}`" class="checkbox" value="1" /><label :for="`enabled-${subfield.key}`"></label>
                    </div>
                </td>
                <td class="checkbox-cell" style="width:15%; text-align:center;">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" :name="fieldName(subfield.key, 'autocomplete')" v-model="$root.$data.settings.subfieldConfig[subfield.key].autocomplete" :id="`autocomplete-${subfield.key}`" class="checkbox" value="1" /><label :for="`autocomplete-${subfield.key}`"></label>
                    </div>
                </td>
<!--                <td class="checkbox-cell" style="width:15%; text-align:center;">-->
<!--                    <div class="checkbox-wrapper">-->
<!--                        <input type="checkbox" :name="fieldName(subfield.key, 'required')" v-model="$root.$data.settings.subfieldConfig[subfield.key].required" :id="`required-${subfield.key}`" class="checkbox" value="1" /><label :for="`required-${subfield.key}`"></label>-->
<!--                    </div>-->
<!--                </td>-->
                <td class="thin action">
                    <a class="move icon" title="Reorder"></a>
                    <input type="hidden" :name="fieldName(subfield.key, 'position')" v-model="$root.$data.settings.subfieldConfig[subfield.key].position" />
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    import subfieldConfig from './../utils/subfield-config';

    export default {
        props: ['settings', 'namespacedName'],
        data() {
            return {
                subfieldConfig: []
            }
        },
        mounted() {

            // Activate sortable subfield manager
            new Sortable(this.$refs.sortable, {
                handle: '.move',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onUpdate: this.updatePositions
            });

            // Get the subfield arrangement
            let arrangement = this.$root.$data.settings.subfieldConfig;

            // Return configured arrangement
            this.subfieldConfig = subfieldConfig(arrangement);

        },
        methods: {
            fieldName(subfield, setting) {
                // Set the namespaced field name
                return `${this.namespacedName}[${subfield}][${setting}]`;
            },
            updatePositions() {

                // Get all subfield manager rows
                let rows = Array.from(this.$refs.sortable.children);

                // Loop through subfields as currently arranged
                rows.forEach((currentValue, i) => {

                    // Get current subfield position
                    let position = (i + 1);

                    // Get current subfield handle
                    let handle = currentValue.dataset.handle;

                    // Update subfield position
                    this.$root.$data.settings.subfieldConfig[handle].position = position;

                });

            }
        }
    }
</script>

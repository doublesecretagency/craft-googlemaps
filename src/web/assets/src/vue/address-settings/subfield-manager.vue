<template>
    <div class="field">
        <div class="heading">
            <label>Subfield Manager</label>
            <div class="instructions">
                <p>Rename, resize, rearrange, require, or hide each subfield...</p>
            </div>
        </div>
        <div>
            <table class="editable fullwidth">
                <thead>
                    <tr>
                        <th scope="col" class="singleline-cell textual">Label</th>
                        <th scope="col" class="number-cell textual" style="text-align:right">Width</th>
                        <th scope="col" class="checkbox-cell" style="text-align:center; cursor:help;" title="Selected subfields will be shown in the visible layout. (Non-selected subfields will be included as hidden inputs.)">Show</th>
                        <th scope="col" class="checkbox-cell" style="text-align:center; cursor:help;" title="When typing an address, Google Places Autocomplete will be active on the selected subfields.">Auto</th>
                        <th scope="col" class="checkbox-cell" style="text-align:center; cursor:help; white-space:nowrap;" title="If the field is marked as &quot;Required&quot;, it must include the selected subfields. See warning message below.">Req.<span class="required" aria-hidden="true"></span></th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>

                <draggable
                    tag="tbody"
                    :list="subfieldConfig"
                    item-key="handle"
                    handle=".move"
                    ghost-class="sortable-ghost"
                >
                    <template #item="{element, index}">
                        <tr
                            :data-handle="element.handle"
                            :class="{'disabled': !element.enabled}"
                        >
                            <td class="singleline-cell textual">
                                <input
                                    type="hidden"
                                    :name="getName(index, 'handle')"
                                    :value="element.handle"
                                >
                                <textarea
                                    :name="getName(index, 'label')"
                                    v-model="element.label"
                                    rows="1"
                                    style="min-height: 34px;"
                                    :placeholder="element.handle"
                                ></textarea>
                            </td>
                            <td class="textual code" style="width:15%; text-align:right;">
                                <input
                                    type="number"
                                    :name="getName(index, 'width')"
                                    v-model.number="element.width"
                                    style="min-height: 34px; max-width:60px; text-align:right; border:none;"
                                >
                            </td>
                            <td class="checkbox-cell" style="width:15%; text-align:center;">
                                <div class="checkbox-wrapper">
                                    <input type="checkbox"
                                        :name="getName(index, 'enabled')"
                                        v-model="element.enabled"
                                        :id="`enabled-${element.handle}-${rand}`"
                                        class="checkbox"
                                        value="1"
                                    ><label :for="`enabled-${element.handle}-${rand}`"></label>
                                </div>
                            </td>
                            <td class="checkbox-cell" style="width:15%; text-align:center;">
                                <div class="checkbox-wrapper">
                                    <input
                                        type="checkbox"
                                        :name="getName(index, 'autocomplete')"
                                        v-model="element.autocomplete"
                                        :id="`autocomplete-${element.handle}-${rand}`"
                                        class="checkbox"
                                        value="1"
                                    ><label :for="`autocomplete-${element.handle}-${rand}`"></label>
                                </div>
                            </td>
                            <td class="checkbox-cell" style="width:15%; text-align:center;">
                                <div class="checkbox-wrapper">
                                    <input
                                        type="checkbox"
                                        :name="getName(index, 'required')"
                                        v-model="element.required"
                                        :id="`required-${element.handle}-${rand}`"
                                        class="checkbox"
                                        value="1"
                                    ><label :for="`required-${element.handle}-${rand}`"></label>
                                </div>
                            </td>
                            <td class="thin action">
                                <a class="move icon" title="Reorder"></a>
                            </td>
                        </tr>
                    </template>
                </draggable>

            </table>
        </div>
    </div>
</template>

<script>
// Import Pinia
import { mapStores } from 'pinia';
import { useAddressSettingsStore } from '../stores/AddressSettingsStore';

// Vue Draggable (based on SortableJS)
import draggable from 'vuedraggable';

export default {
    data() {
        // Get a random string of numbers
        const rand = Math.random()
            .toString()
            .replace(/^0\./, '');

        return {
            'rand': rand,
        }
    },
    components: {
        draggable,
    },
    computed: {
        // Load Pinia store
        ...mapStores(useAddressSettingsStore),

        /**
         * Bubble the subfield configuration.
         * Necessary to prevent conflicts.
         */
        subfieldConfig() {
            // Get the Pinia store
            const addressSettingsStore = useAddressSettingsStore();
            // Return the subfield configuration
            return addressSettingsStore.settings.subfieldConfig;
        }
    },
    methods: {
        /**
         * Get namespaced field name.
         */
        getName(index, setting) {
            // Get the Pinia store
            const addressSettingsStore = useAddressSettingsStore();
            // Return the namespaced field name
            return addressSettingsStore.getName(`[subfieldConfig][${index}][${setting}]`);
        },
    }
}
</script>

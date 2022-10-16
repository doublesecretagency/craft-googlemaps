<template>
    <div class="field">
        <div class="heading">

            <div class="instructions">
                <p style="color:#000"><strong>&#8593;&nbsp;</strong> Use this preview field to <strong>set the default map starting position</strong>. <strong>&#8593;</strong></p>
                <ul>
                    <li><strong>Search</strong> for a central starting location.</li>
                    <li><strong>Drag &amp; drop</strong> the pin to set a more precise location.</li>
                    <li><strong>Zoom</strong> in or out to a comfortable default viewing level.</li>
                </ul>
            </div>

            <hr>

            <div class="field" style="margin-top:10px">
                <div class="heading settings-note">
                    <label>Set the Default Map Starting Position</label>
                    <div class="instructions">
                        <p>You can to set the <strong>default coordinates &amp; zoom level</strong> using the "Preview" field above. Whatever position you save this map to will become the default map orientation for all new instances of this field.</p>
                        <p>Only the <strong>latitude</strong>, <strong>longitude</strong>, and <strong>zoom</strong> will be used to orient the map when a new element is being created. No other data from the "Preview" field will prepopulate future instances of this field.</p>
                    </div>
                </div>

                <div class="heading settings-note warning" style="margin-top:12px">
                    <label>Required Subfields and/or Coordinates (<span class="required" aria-hidden="true"></span>)</label>
                    <div class="instructions">
                        <p>If any of your subfields or coordinates are required, you <strong>must also</strong> set the field to be "Required" when adding it to your field layout.</p>
                    </div>
                    <img class="screenshot" :src="addressSettingsStore.images.required" alt="Screenshot of field layout">
                </div>
            </div>

        </div>
    </div>
</template>

<script>
// Import Pinia
import { mapStores } from 'pinia';
import { useAddressSettingsStore } from '../stores/AddressSettingsStore';

export default {
    computed: {
        // Load Pinia store
        ...mapStores(useAddressSettingsStore)
    },
}
</script>

<style scoped>
.settings-note {
    border-radius: 5px;
    border: 1px solid #cdd8e4;
    padding: 12px 14px;
}
.settings-note .instructions {
    margin-top:10px;
}
.settings-note.warning {
    background-color: rgba(255,229,100,0.3);
    border-color: #e7c000;
    color: #6b5900;
}
.settings-note.warning .required:after {
    margin: 0 0;
}
img.screenshot {
    width: 331px;
    max-width: 100%;
    margin: 13px auto 5px;
    border-style: solid;
    border-width: 1px;
    border-color: #edf2f7;
    border-radius: 0.3rem;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 10%), 0 1px 2px 0 rgb(0 0 0 / 6%);
}
</style>

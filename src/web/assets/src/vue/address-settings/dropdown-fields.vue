<template>
    <div>

        <select-field
            label="Show map on initial load?"
            instructions="When the field is initially loaded, the map should..."
            model="mapOnStart"
            :namespace="{
                id: 'map-on-start',
                name: 'mapOnStart'
            }"
        ></select-field>

        <select-field
            label="Show map when address search is triggered?"
            instructions="When an address lookup is performed, the map should..."
            model="mapOnSearch"
            :namespace="{
                id: 'map-on-search',
                name: 'mapOnSearch'
            }"
        ></select-field>

        <select-field
            label="Map Visibility Toggle"
            instructions="How should the map visibility toggle be displayed?"
            model="visibilityToggle"
            :namespace="{
                id: 'visibility-toggle',
                name: 'visibilityToggle'
            }"
        ></select-field>

        <select-field
            label="Display Coordinates"
            instructions="How should the location coordinates be displayed?"
            model="coordinatesMode"
            :namespace="{
                id: 'coordinates-mode',
                name: 'coordinatesMode'
            }"
        ></select-field>

        <lightswitch-field
            label="Require Coordinates"
            instructions="If enabled, this Address &lt;strong&gt;must&lt;/strong&gt; include a valid set of coordinates. Only applicable when the Address field is marked as &quot;Required&quot; in the field layout."
            onLabel="Require coordinates when field is &quot;Required&quot;"
            :markRequired="true"
            :namespace="{
                id: 'require-coordinates',
                name: 'requireCoordinates'
            }"
        ></lightswitch-field>

    </div>
</template>

<script>
import LightswitchField from './lightswitch-field';
import SelectField from './select-field';

export default {
    components: {
        LightswitchField,
        SelectField
    }
}
</script>

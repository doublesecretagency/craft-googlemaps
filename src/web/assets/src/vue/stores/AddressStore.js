import { ref, computed, watch } from 'vue';
import { defineStore } from 'pinia';

// Adjust formatting for specific countries
const formatCountries = {
    // Put street number before the street name
    numberFirst: [
        'Australia',
        'Canada',
        'France',
        'Hong Kong',
        'India',
        'Ireland',
        'Malaysia',
        'New Zealand',
        'Pakistan',
        'Singapore',
        'Sri Lanka',
        'Taiwan',
        'Thailand',
        'United Kingdom',
        'United States',
    ],
    // Put comma after the street name
    commaAfterStreet: [
        'Italy',
    ]
};

export const useAddressStore = defineStore('address', () => {

    // ========================================================================= //
    // State

    const namespace = ref({
        id: null,
        name: null,
        handle: null,
    });
    const settings = ref({});
    const data = ref({});
    const images = ref({});
    const formatting = ref(formatCountries);

    // When address data is changed
    watch(data, () => {
        // Normalize the address info
        _normalizeData();
    }, {deep: true});

    // ========================================================================= //
    // Getters

    /**
     * Formatting configuration for the map visibility toggle.
     */
    const configToggle = computed(() => {
        // Abbreviate variable
        const s = settings.value;
        // Return configuration
        return {
            'style': s.visibilityToggle, // both | text | icon
            'text': (s.showMap ? 'Hide Map' : 'Show Map'),
            'icon': (s.showMap ? images.value.iconOff : images.value.iconOn),
        }
    });

    /**
     * Formatting configuration for the coordinates subfields.
     */
    const configCoords = computed(() => {
        // Get the coordinates mode
        const mode = settings.value.coordinatesMode;
        // Return configuration
        return {
            'type': ('hidden' === mode ? 'hidden' : 'number'),
            'readOnly': !['editable','hidden'].includes(mode),
        }
    });

    /**
     * Get the complete set of fully configured subfields.
     */
    const subfields = computed(() => {

        // Get subfield configuration from the field settings
        let subfields = settings.value.subfieldConfig;

        // Loop through all subfields
        subfields.forEach(subfield => {

            // Initialize input styles and width
            let styles = {};

            // If the subfield is disabled
            if (!subfield.enabled) {
                // Render it, but keep it hidden
                styles['display'] = 'none';
            } else {
                // Get subfield width
                let width = subfield.width;
                // Never go over 100%
                if (100 < width) {
                    width = 100;
                }
                // Give up 1% width to the right margin
                styles['width'] = `${--width}%`;
            }

            // Append styles to subfield config
            subfield.styles = styles;
        });

        // Return fully configured subfields
        return subfields;
    });

    // ========================================================================= //
    // Actions

    /**
     * Toggle visibility of the map.
     */
    function changeVisibility()
    {
        settings.value.showMap = !settings.value.showMap;
    }

    /**
     * Check whether coordinates are valid.
     */
    function validateCoords(coords)
    {
        // Loop through coordinates
        for (let key in coords) {
            // Skip the zoom value
            if ('zoom' === key) {
                continue;
            }
            // Get individual coordinate
            let coord = coords[key];
            // If coordinate is not a number or string, return false
            if (!['number','string'].includes(typeof coord)) {
                return false;
            }
            // If coordinate is not numeric, return false
            if (isNaN(coord)) {
                return false;
            }
            // If coordinate is an empty string, return false
            if ('' === coord) {
                return false;
            }
        }

        // Success, coordinates are valid!
        return true;
    }

    /**
     * Populate address data when Autocomplete selected.
     */
    function updateData(place)
    {
        // Set address subfield data
        _setAddressData(place.address_components);

        // Get address data
        const address = data.value.address;

        // Whether the `name` value matches the `street1` value
        let boringName = (place.name === address.street1);

        // Append additional data
        address.name      = (!boringName ? place.name : null);
        address.placeId   = place.place_id;
        address.formatted = place.formatted_address;
        address.raw       = JSON.stringify(place);

        // Set coordinates
        let coords = place.geometry.location;
        data.value.coords.lat = parseFloat(coords.lat().toFixed(7));
        data.value.coords.lng = parseFloat(coords.lng().toFixed(7));

        // If coords are invalid, clear meta subfields
        if (!data.value.coords.lat || !data.value.coords.lng) {
            address.placeId   = null;
            address.formatted = null;
            address.raw       = null;
        }

        // If not changing the map visibility, bail
        if ('noChange' === settings.value.mapOnSearch) {
            return;
        }

        // Change map visibility based on settings
        settings.value.showMap = ('open' === settings.value.mapOnSearch);
    }

    /**
     * Set the individual subfield values.
     */
    function _setAddressData(components)
    {
        // Initialize address data
        let apiData = {};

        // Loop through components from API results
        components.forEach(c => {
            // Get component type
            let type = c['types'][0];
            // Set value from component
            switch (type) {
                case 'locality':
                case 'neighborhood':
                    apiData[type] = c['long_name'];
                    break;
                case 'country':
                    apiData[type] = c['long_name'];
                    apiData['countryCode'] = c['short_name'];
                    break;
                default:
                    apiData[type] = c['short_name'];
                    break;
            }
        });

        // Get address data
        const address = data.value.address;

        // Set address data to Vue
        address.street1      = _formatStreet(apiData);
        address.street2      = null;
        address.city         = apiData['locality'];
        address.state        = apiData['administrative_area_level_1'];
        address.zip          = apiData['postal_code'];
        address.neighborhood = apiData['neighborhood'];
        address.county       = apiData['administrative_area_level_2'];
        address.country      = apiData['country'];
        address.countryCode  = apiData['countryCode'];

        // Country-specific adjustments
        switch (apiData['country']) {
            case 'United Kingdom':
                address.city  = apiData['postal_town'];
                address.state = apiData['administrative_area_level_2'];
                break;
        }
    }

    /**
     * Format the main street address.
     */
    function _formatStreet(apiData)
    {
        // Abbreviate variables
        let streetNumber = apiData.street_number || '';
        let streetName   = apiData.route         || '';
        let country      = apiData.country       || '';

        // Default street format
        let street = `${streetName} ${streetNumber}`;

        // If needed, put street number before the street name
        if (formatting.value.numberFirst.includes(country)) {
            street = `${streetNumber} ${streetName}`;
        }

        // If needed, put comma after the street name
        if (formatting.value.commaAfterStreet.includes(country)) {
            street = `${streetName}, ${streetNumber}`;
        }

        // Return formatted street address
        return street.trim().replace(/,*$/,'');
    }

    /**
     * Normalize the address data when anything changes.
     */
    function _normalizeData()
    {
        // If coordinates are invalid
        if (!validateCoords(data.value.coords)) {
            // Reset the meta fields
            data.value.address['formatted'] = null;
            data.value.address['raw'] = null;
        }
    }

    // ========================================================================= //

    // Return reactive values
    return {
        // State
        namespace,
        settings,
        data,
        images,
        formatting,

        // Getters
        configToggle,
        configCoords,
        subfields,

        // Actions
        changeVisibility,
        validateCoords,
        updateData,
    }

})

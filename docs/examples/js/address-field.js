/**
 * Front-End Form
 * https://plugins.doublesecretagency.com/google-maps/address-field/front-end-form/
 *
 * If you are building a front-end form with an Address field,
 * copy & paste this whole file to the front-end of your site.
 * This JavaScript file helps your front-end Address field
 * behave more like a native back-end Address field.
 *
 * Once you have copied this script over to the front-end,
 * you are free to adjust it as necessary to meet the needs
 * of your unique site.
 */

// Create dynamic Address fields on the front-end
window.addressField = window.addressField || {

    // Countries with a comma after the street name
    _commaAfterStreet: [
        'Italy',
    ],

    // Countries where the street number precedes the street name
    _numberFirst: [
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

    // ========================================================================= //

    /*
     * NOTE: Only activate a single subfield!
     * Specify the input ID of the primary subfield.
     *
     *   addressField.activateSubfield('myAddressField-street1')
     */

    // Activate the Google Places Autocomplete on a specified input
    activateSubfield: function (id) {

        // Specify which subfield to use for autocompletion
        const subfield = document.getElementById(id);

        // Create an Autocomplete object
        const autocomplete = new google.maps.places.Autocomplete(subfield, {
            types: ['geocode'],
            fields: [
                'address_components',
                'formatted_address',
                'geometry.location',
                'name',
                'place_id'
            ]
        });

        // Listen for autocomplete trigger
        autocomplete.addListener('place_changed', () => {

            // Get newly selected place
            let place = autocomplete.getPlace();

            // Get new address info
            let components = place.address_components;
            let coords = place.geometry.location;

            // Set all subfield data
            let address = this._addressComponents(components);

            // Set all input values
            document.getElementById('address-name').value         = place.name           || null;
            document.getElementById('address-street1').value      = address.street1      || null;
            document.getElementById('address-street2').value      = address.street2      || null;
            document.getElementById('address-city').value         = address.city         || null;
            document.getElementById('address-state').value        = address.state        || null;
            document.getElementById('address-zip').value          = address.zip          || null;
            document.getElementById('address-neighborhood').value = address.neighborhood || null;
            document.getElementById('address-county').value       = address.county       || null;
            document.getElementById('address-country').value      = address.country      || null;
            document.getElementById('address-countryCode').value  = address.countryCode  || null;
            document.getElementById('address-placeId').value      = place.placeId        || null;
            document.getElementById('address-lat').value          = parseFloat(coords.lat().toFixed(7)) || null;
            document.getElementById('address-lng').value          = parseFloat(coords.lng().toFixed(7)) || null;
            document.getElementById('address-formatted').value    = place.formatted_address || null;
            document.getElementById('address-raw').value          = JSON.stringify(place) || null;
        });

        // Prevent address selection from attempting to submit the form
        google.maps.event.addDomListener(subfield, 'keydown', (event) => {
            if (event.keyCode === 13) {
                event.preventDefault();
            }
        });

    },

    // ========================================================================= //

    // Format the main street address
    _formatStreetAddress: function (a) {

        // Abbreviate variables
        let streetNumber = a.street_number || '';
        let streetName   = a.route         || '';
        let country      = a.country       || '';

        // Default street format
        let street = `${streetName} ${streetNumber}`;

        // If country with different format, use that format
        if (this._numberFirst.includes(country)) {
            street = `${streetNumber} ${streetName}`;
        } else if (this._commaAfterStreet.includes(country)) {
            street = `${streetName}, ${streetNumber}`;
        }

        // Return formatted street address
        return street.trim().replace(/,*$/,'');
    },

    // Set the formatted address data
    _addressComponents: function (components) {

        // Initialize formatted address data
        let formatted = {};

        // Initialize outgoing data
        let data = {}

        // Loop through address components
        components.forEach(c => {

            // Get component type
            let type = c['types'][0];

            // Format component
            switch (type) {
                case 'locality':
                case 'neighborhood':
                    formatted[type] = c['long_name'];
                    break;
                case 'country':
                    formatted[type] = c['long_name'];
                    formatted['countryCode'] = c['short_name'];
                    break;
                default:
                    formatted[type] = c['short_name'];
                    break;
            }

        });

        // Set address data to Vue
        data.street1      = this._formatStreetAddress(formatted);
        data.street2      = null;
        data.city         = formatted['locality'];
        data.state        = formatted['administrative_area_level_1'];
        data.zip          = formatted['postal_code'];
        data.neighborhood = formatted['neighborhood'];
        data.county       = formatted['county'];
        data.country      = formatted['country'];
        data.countryCode  = formatted['countryCode'];

        // Country-specific adjustments
        switch (formatted['country']) {
            case 'United Kingdom':
                data.city  = formatted['postal_town'];
                data.state = formatted['administrative_area_level_2'];
                break;
        }

        // Send back reformatted data
        return data;
    }

};

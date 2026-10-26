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

        // Get the shared ID prefix of all subfields (ie: "myAddressField")
        const prefix = id.substring(0, id.lastIndexOf('-'));

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

            // Collect all subfield values
            const values = {
                name:         place.name,
                street1:      address.street1,
                street2:      address.street2,
                city:         address.city,
                state:        address.state,
                zip:          address.zip,
                neighborhood: address.neighborhood,
                county:       address.county,
                country:      address.country,
                countryCode:  address.countryCode,
                placeId:      place.place_id,
                lat:          parseFloat(coords.lat().toFixed(7)),
                lng:          parseFloat(coords.lng().toFixed(7)),
                formatted:    place.formatted_address,
                raw:          JSON.stringify(place),
            };

            // Set each input which exists in the form
            for (const [handle, value] of Object.entries(values)) {
                const input = document.getElementById(`${prefix}-${handle}`);
                if (input) {
                    input.value = value ?? '';
                }
            }
        });

        // Prevent address selection from attempting to submit the form
        subfield.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
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
        data.county       = formatted['administrative_area_level_2'];
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

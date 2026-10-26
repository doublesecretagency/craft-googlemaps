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
     *
     * Requires "Places API (New)" to be enabled in your Google Cloud project.
     */

    // Activate Google Places Autocomplete on a specified input
    activateSubfield: async function (id) {

        // Specify which subfield to use for autocompletion
        const subfield = document.getElementById(id);

        // Get the shared ID prefix of all subfields (ie: "myAddressField")
        const prefix = id.substring(0, id.lastIndexOf('-'));

        // Load the Places library
        const places = await google.maps.importLibrary('places');

        // Create a list of suggestions below the input
        const list = document.createElement('ul');
        list.id = `${id}-suggestions`;
        list.setAttribute('role', 'listbox');
        list.style.cssText = 'position:absolute; z-index:1000; margin:0; padding:0; list-style:none; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.3); display:none;';
        subfield.insertAdjacentElement('afterend', list);

        // Turn the input into a combobox
        subfield.setAttribute('role', 'combobox');
        subfield.setAttribute('aria-autocomplete', 'list');
        subfield.setAttribute('aria-controls', list.id);
        subfield.setAttribute('aria-expanded', 'false');
        subfield.setAttribute('autocomplete', 'off');

        // Current suggestions, highlighted suggestion, session token, and typing timer
        let suggestions = [];
        let active = -1;
        let token = null;
        let timer = null;

        // Close the list of suggestions
        const close = () => {
            list.style.display = 'none';
            subfield.setAttribute('aria-expanded', 'false');
            subfield.removeAttribute('aria-activedescendant');
            active = -1;
        };

        // Highlight a suggestion
        const highlight = (index) => {
            active = index;
            [...list.querySelectorAll('[role="option"]')].forEach((li, i) => {
                li.setAttribute('aria-selected', (i === index) ? 'true' : 'false');
                li.style.background = (i === index) ? '#ebf2fe' : '';
            });
            subfield.setAttribute('aria-activedescendant', `${list.id}-${index}`);
        };

        // Select a suggestion, and fill in each subfield
        const select = async (index) => {

            // Get the selected suggestion
            const prediction = suggestions[index]?.placePrediction;
            if (!prediction) {
                return;
            }
            close();

            // Get the place details (this ends the session)
            const place = prediction.toPlace();
            await place.fetchFields({fields: ['addressComponents', 'formattedAddress', 'location', 'displayName', 'id']});
            token = null;

            // Convert the address components
            const components = (place.addressComponents || []).map(c => ({
                long_name: c.longText,
                short_name: c.shortText,
                types: c.types,
            }));

            // Set all subfield data
            let address = this._addressComponents(components);

            // Collect all subfield values
            const values = {
                name:         place.displayName,
                street1:      address.street1,
                street2:      address.street2,
                city:         address.city,
                state:        address.state,
                zip:          address.zip,
                neighborhood: address.neighborhood,
                county:       address.county,
                country:      address.country,
                countryCode:  address.countryCode,
                placeId:      place.id,
                lat:          parseFloat(place.location.lat().toFixed(7)),
                lng:          parseFloat(place.location.lng().toFixed(7)),
                formatted:    place.formattedAddress,
                raw:          JSON.stringify(place),
            };

            // Set each input which exists in the form
            for (const [handle, value] of Object.entries(values)) {
                const input = document.getElementById(`${prefix}-${handle}`);
                if (input) {
                    input.value = value ?? '';
                }
            }
        };

        // Fetch suggestions as the user types
        subfield.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(async () => {

                // If nothing to search, close the list
                const text = subfield.value.trim();
                if (!text) {
                    close();
                    return;
                }

                // Request suggestions (one session per address)
                token = token || new places.AutocompleteSessionToken();
                const response = await places.AutocompleteSuggestion.fetchAutocompleteSuggestions({
                    input: text,
                    sessionToken: token,
                });
                suggestions = (response.suggestions || []).filter(s => s.placePrediction);

                // Display the suggestions
                list.textContent = '';
                suggestions.forEach((s, i) => {
                    const li = document.createElement('li');
                    li.id = `${list.id}-${i}`;
                    li.setAttribute('role', 'option');
                    li.style.cssText = 'padding:6px 10px; cursor:pointer;';
                    li.textContent = s.placePrediction.text.text;
                    li.addEventListener('mousedown', (event) => {
                        event.preventDefault();
                        select(i);
                    });
                    list.appendChild(li);
                });

                // Show the Google Maps attribution (required by Google)
                const attribution = document.createElement('li');
                attribution.setAttribute('translate', 'no');
                attribution.style.cssText = 'padding:4px 10px; text-align:right; font:12px Roboto, sans-serif; color:#5e5e5e;';
                attribution.textContent = 'Google Maps';
                list.appendChild(attribution);

                // Open the list, as wide as the input
                list.style.width = `${subfield.offsetWidth}px`;
                list.style.display = suggestions.length ? '' : 'none';
                subfield.setAttribute('aria-expanded', suggestions.length ? 'true' : 'false');
                active = -1;
            }, 150);
        });

        // Navigate the suggestions with the keyboard
        subfield.addEventListener('keydown', (event) => {

            // Never let Enter submit the form from this input
            if (event.key === 'Enter') {
                event.preventDefault();
            }

            // If the list is closed, bail
            if ('none' === list.style.display) {
                return;
            }

            // Handle navigation keys
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                highlight((active + 1) % suggestions.length);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                highlight((active <= 0 ? suggestions.length : active) - 1);
            } else if (event.key === 'Enter') {
                select(active);
            } else if (event.key === 'Escape') {
                close();
            }
        });

        // Close the suggestions when leaving the input
        subfield.addEventListener('blur', close);

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

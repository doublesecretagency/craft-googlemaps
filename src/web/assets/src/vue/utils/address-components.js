// Countries where the street number precedes the street name
const numberFirst = [
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
];

// Countries with a comma after the street name
const commaAfterStreet = [
    'Italy',
];

// Format the main street address
function formatStreetAddress(a) {

    // Abbreviate variables
    let streetNumber = a.street_number || '';
    let streetName   = a.route         || '';
    let country      = a.country       || '';

    // Default street format
    let street = `${streetName} ${streetNumber}`;

    // If country with different format, use that format
    if (numberFirst.includes(country)) {
        street = `${streetNumber} ${streetName}`;
    } else if (commaAfterStreet.includes(country)) {
        street = `${streetName}, ${streetNumber}`;
    }

    // Return formatted street address
    return street.trim().replace(/,*$/,'');
}

// Set the formatted address data
export default function addressComponents(components, data) {

    // Initialize formatted address data
    let formatted = {};

    // Loop through address components
    components.forEach(c => {

        // Get component type
        let type = c['types'][0];

        // Format component
        switch (type) {
            case 'locality':
            case 'country':
                formatted[type] = c['long_name'];
                break;
            default:
                formatted[type] = c['short_name'];
                break;
        }

    });

    // Set address data to Vue
    data.street1 = formatStreetAddress(formatted);
    data.street2 = null;
    data.city    = formatted['locality'];
    data.state   = formatted['administrative_area_level_1'];
    data.zip     = formatted['postal_code'];
    data.county  = formatted['administrative_area_level_2'];
    data.country = formatted['country'];

    // Country-specific adjustments
    switch (formatted['country']) {
        case 'United Kingdom':
            data.city  = formatted['postal_town'];
            data.state = formatted['administrative_area_level_2'];
            break;
    }

}

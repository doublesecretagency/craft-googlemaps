/**
 * DEFAULT
 * Determine the orientation of a map.
 *
 * @param $data
 * @returns object
 */
export default function getMapCenter($data) {

    // If available, get coords from the existing Address data
    let dataCoords = getDataCoords($data.data);
    if (dataCoords) {
        return dataCoords;
    }

    // If available, get default coords from the field settings
    let settingsCoords = getSettingsCoords($data.settings);
    if (settingsCoords) {
        return settingsCoords;
    }

    // If available, get user's current location
    let geolocationCoords = getGeolocationCoords();
    if (geolocationCoords) {
        return geolocationCoords;
    }

    // Nothing else worked, use the fallback
    return useFallbackCoords();

}

// ========================================================================= //

// Check whether single coordinate is valid
function validCoord(coord) {

    // If coordinate is not a number or string, return false
    if (!['number','string'].includes(typeof coord)) {
        return false;
    }

    // If coordinate is not numeric, return false
    if (isNaN(coord)) {
        return false;
    }

    // Coordinate is valid, return true
    return true;
}

/**
 * Get the coordinates from the existing field data.
 *
 * @returns object
 */
function getDataCoords(data) {

    console.log('- getDataCoords -');
    // console.log(data.coords.lat);
    // console.log(typeof data.coords.lat);
    // console.log('');

    // Get coordinates from field data
    const coords = data.coords;

    // If invalid coordinates, return false
    if (!validCoord(coords.lat) || !validCoord(coords.lng)) {
        return false;
    }

    // Return coordinates
    return coords;
}

/**
 * Get the coordinates from the field's default settings.
 *
 * @param settings
 * @returns object
 */
function getSettingsCoords(settings) {

    console.log('- getSettingsCoords - SKIPPED BECAUSE IT\'S PERFECT');
    return false;

    // Get coordinates from field settings
    const coords = settings.coordinatesDefault;

    // If invalid coordinates, return false
    if (!validCoord(coords.lat) || !validCoord(coords.lng)) {
        return false;
    }

    // Return coordinates
    return coords;
}

/**
 * Determine the coordinates via user geolocation.
 *
 * @returns object
 */
function getGeolocationCoords() {

    console.log('- getGeolocationCoords -');
    // console.log('TBD');
    // console.log('');

    return false;
    // return {
    //     lat: 0,
    //     lng: 0,
    //     zoom: 0
    // }
}

/**
 * Use the generic fallback coordinates.
 * https://plugins.doublesecretagency.com/google-maps/guides/bermuda-triangle/
 *
 * @returns object
 */
function useFallbackCoords() {

    console.log('- useFallbackCoords - WORKING AS INTENDED');


    // Return fallback coordinates
    return {
        lat: 32.3113966,
        lng: -64.7527469,
        zoom: 6
    }
}

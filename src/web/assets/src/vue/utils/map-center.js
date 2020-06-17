/**
 * Attempt to get map center coordinates based on the field data or settings.
 *
 * @param field
 * @returns object
 */
export function fromField(field) {

    // If available, get coords from the existing field data
    let dataCoords = _getDataCoords(field.data);
    if (dataCoords) {
        return dataCoords;
    }

    // If available, get default coords from the field settings
    let settingsCoords = _getSettingsCoords(field.settings);
    if (settingsCoords) {
        return settingsCoords;
    }

    // Unable to get any coordinates from the field
    return false;
}

/**
 * Use the generic fallback coordinates.
 * https://plugins.doublesecretagency.com/google-maps/guides/bermuda-triangle/
 *
 * @returns object
 */
export function fromFallback() {
    // Bermuda Triangle
    return {
        lat: 32.3113966,
        lng: -64.7527469,
        zoom: 6
    }
}

// ========================================================================= //

/**
 * Get the coordinates from the field's existing data.
 *
 * @param data
 * @returns object
 */
function _getDataCoords(data) {

    // TODO: Test this function again when SAVING the field data
    // console.log('_getDataCoords');

    // Get coordinates from field data
    const coords = data.coords;

    // If invalid coordinates, return false
    if (!_validCoord(coords.lat) || !_validCoord(coords.lng)) {
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
function _getSettingsCoords(settings) {

    // Get coordinates from field settings
    const coords = settings.coordinatesDefault;

    // If invalid coordinates, return false
    if (!_validCoord(coords.lat) || !_validCoord(coords.lng)) {
        return false;
    }

    // Return coordinates
    return coords;
}

// ========================================================================= //

/**
 * Check whether a single coordinate is valid.
 *
 * @param coord
 * @returns bool
 */
function _validCoord(coord) {

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

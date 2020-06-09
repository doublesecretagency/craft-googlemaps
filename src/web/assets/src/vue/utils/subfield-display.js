/**
 * DEFAULT
 * Configure the subfield display.
 *
 * @param subfields
 * @returns array
 */
export default function subfieldDisplay(subfields) {
    // Rearrange subfields according to their position
    subfields = rearrange(subfields);
    // Return subfield display data
    return displayConfig(subfields);
}

/**
 * Rearrange the subfield data.
 *
 * @param oldOrder
 * @returns array
 */
function rearrange(oldOrder) {

    // Backup position counter
    let p = 100;

    // Initialize new order
    let newOrder = [];

    // Loop through old arrangement
    for (let key in oldOrder) {
        // Get the subfield data
        let subfield = oldOrder[key];
        // Move key to within object
        subfield.key = key;
        // Set the new subfield position
        let position = parseInt(subfield.position || p++);
        // Move subfield
        newOrder[position] = subfield;
    }

    // Return new arrangement
    return Object.values(newOrder);
}

/**
 * Configure the data to be displayed.
 *
 * @param arrangement
 * @returns array
 */
function displayConfig(arrangement) {

    // Initialize display array
    let display = [];

    // Loop through subfield arrangement
    for (let key in arrangement) {

        // Get the subfield
        let subfield = arrangement[key];

        // Initialize input styles
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

        // Append subfield configuration
        display.push({
            key: subfield.key,
            label: subfield.label,
            enabled: subfield.enabled,
            // required: subfield.required,
            styles: styles
        });

    }

    // Return the subfield display array
    return display;
}

/**
 * DEFAULT
 * Configure the subfield display.
 *
 * @param subfields
 * @returns array
 */
export default function subfieldConfig(subfields) {
    // Rearrange subfields according to their position
    return rearrange(subfields);
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

/* ORIGINAL SOURCE:
 * https://markus.oberlehner.net/blog/using-the-google-maps-api-with-vue/
 */

let initialized = !!window.google;
let resolveInitPromise;
let rejectInitPromise;

// Promise to handle the initialization status
const initPromise = new Promise((resolve, reject) => {
    resolveInitPromise = resolve;
    rejectInitPromise = reject;
});

// Export init
export default function init() {

    // If already initialized, return the promise
    if (initialized) {
        return initPromise;
    }

    // Callback triggered by Google Maps script if successfully loaded
    window['initGoogleMaps'] = () => resolveInitPromise(window.google);

    // Mark as initialized
    initialized = true;

    // Return the promise
    return initPromise;
}

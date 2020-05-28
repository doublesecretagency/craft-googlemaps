/* ORIGINAL SOURCE:
 * https://markus.oberlehner.net/blog/using-the-google-maps-api-with-vue/
 */

const CALLBACK_NAME = 'initGoogleMaps';

let initialized = !!window.google;
let resolveInitPromise;
let rejectInitPromise;

// This promise handles the initialization
// status of the google maps script.
const initPromise = new Promise((resolve, reject) => {
    resolveInitPromise = resolve;
    rejectInitPromise = reject;
});

export default function init() {

    // If Google Maps already is initialized
    // the `initPromise` should get resolved
    // eventually.
    if (initialized) return initPromise;

    initialized = true;
    // The callback function is called by
    // the Google Maps script if it is
    // successfully loaded.
    window[CALLBACK_NAME] = () => resolveInitPromise(window.google);

    return initPromise;
}

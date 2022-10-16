// Import Vue components
import { createApp } from 'vue';
import { createPinia } from 'pinia';

import AddressField from '../vue/address/address';

// Initialize Vue instances
window.initAddressField = () => {

    // If configs aren't loaded yet, bail
    if ('undefined' === typeof addressFieldConfigs) {
        return;
    }

    // Loop through all Address field configurations
    for (let i in addressFieldConfigs) {

        // Get configuration of a single Address field
        let config = addressFieldConfigs[i];

        // Get DOM element
        let element = document.getElementById(config.namespace.id);

        // If element does not exist, skip this one
        if (!element) {
            console.warn(`[GM] The following Address field cannot be found: ${config.namespace.id}`);
            continue;
        }

        // Initialize Vue instance for a single Address field
        const app = createApp(AddressField, config);

        // Initialize Pinia
        app.use(createPinia());

        // Mount to DOM
        app.mount(element);

    }

}

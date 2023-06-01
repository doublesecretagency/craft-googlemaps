// Import Vue components
import { createApp } from 'vue';
import { createPinia } from 'pinia';

import AddressFieldSettings from '../vue/address-settings/address-settings';

// Initialize Vue instance
window.initAddressFieldSettings = () => {

    // Get all matching DOM elements
    const elements = document.querySelectorAll('.address-settings-container');

    // If no matching elements found, bail
    if (!elements.length) {
        console.warn('[GM] Unable to load Vue. Cannot find an `address-settings-container`.')
        return;
    }

    // Loop through each element
    elements.forEach(container => {

        // Get the config data
        const config = JSON.parse(container.dataset.config);

        // Initialize new Vue instance
        const app = createApp(AddressFieldSettings, config);

        // Initialize Pinia
        app.use(createPinia());

        // Mount to the container
        app.mount(container);
    });

}

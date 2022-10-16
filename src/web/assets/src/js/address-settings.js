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
        console.warn('[GM] Unable to load Vue. Cannot find the `address-settings-container`.')
        return;
    }

    // Initialize new Vue instance
    const app = createApp(AddressFieldSettings);
    // const app = createApp(AddressFieldSettings, addressFieldSettingsConfig);

    // Initialize Pinia
    app.use(createPinia());

    // Mount to the first matching container
    app.mount(elements[0]);
}

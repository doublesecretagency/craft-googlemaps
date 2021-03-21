import AddressField from './../vue/address/address.vue';

// Disable silly message
Vue.config.productionTip = false;

// Initialize Vue instances
window.initAddressField = () => {

    // If configs aren't loaded yet, bail
    if ('undefined' === typeof addressFieldConfigs) {
        return;
    }

    // Set class which marks element as loaded
    const alreadyLoaded = 'vue-mounted';

    // Loop through all Address field configurations
    for (let i in addressFieldConfigs) {

        // Get configuration of a single Address field
        let config = addressFieldConfigs[i];

        // Get DOM element
        let element = document.getElementById(config.namespacedId);

        // If element does not exist, skip this one
        if (!element) {
            console.warn(`[GM] The following Address field cannot be found: ${config.namespacedId}`);
            continue;
        }

        // If already mounted, skip this one
        if (element.classList.contains(alreadyLoaded)) {
            continue;
        }

        // Initialize Vue instance for a single Address field
        new Vue({
            el: `#${config.namespacedId}`,
            components: {
                'address-field': AddressField
            },
            mounted() {
                // Mark element as mounted
                const element = document.getElementById(config.namespacedId);
                element.classList.add(alreadyLoaded);
            },
            data: {
                handle: config.handle,
                namespacedName: config.namespacedName,
                settings: config.settings,
                data: config.data,
                icons: config.icons
            }
        });

    }

}

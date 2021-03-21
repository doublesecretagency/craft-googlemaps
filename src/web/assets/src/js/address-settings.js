import AddressField from './../vue/address/address.vue';
import SubfieldManager from './../vue/address-settings/subfield-manager.vue';
import DefaultCoords from './../vue/address-settings/default-coords.vue';

// Disable silly message
Vue.config.productionTip = false;

// Initialize Vue instance
window.initAddressFieldSettings = () => {

    // Get all matching DOM elements
    const elements = document.querySelectorAll('.address-settings');

    // Set class which marks element as loaded
    const alreadyLoaded = 'vue-mounted';

    // Initialize Vue for each element
    elements.forEach(el => {

        // If already mounted, skip this one
        if (el.classList.contains(alreadyLoaded)) {
            return;
        }

        // Initialize new Vue instance
        new Vue({
            el: el,
            components: {
                'address-field': AddressField,
                'subfield-manager': SubfieldManager,
                'default-coords': DefaultCoords
            },
            mounted() {
                // Mark element as mounted
                const element = document.getElementById(el.id);
                element.classList.add(alreadyLoaded);
            },
            data: {
                settings: settings,
                data: data,
                icons: icons
            }
        });

    })

}

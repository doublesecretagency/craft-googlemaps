import AddressField from './../vue/address/address.vue';
import SubfieldManager from './../vue/address-settings/subfield-manager.vue';
import DefaultCoords from './../vue/address-settings/default-coords.vue';

// Disable silly message
Vue.config.productionTip = false;

// Initialize Vue instance
window.initAddressFieldSettings = () => {

    // Get all matching DOM elements
    var elements = document.querySelectorAll('.address-settings');

    // Initialize Vue for each element
    elements.forEach(el => {
        new Vue({
            el: el,
            components: {
                'address-field': AddressField,
                'subfield-manager': SubfieldManager,
                'default-coords': DefaultCoords
            },
            data: {
                settings: settings,
                data: data,
                icons: icons
            }
        });
    })

}

import AddressField from './../vue/address/address.vue';
import SubfieldManager from './../vue/address-settings/subfield-manager.vue';
import DefaultCoords from './../vue/address-settings/default-coords.vue';

// Disable silly message
Vue.config.productionTip = false;

// Create Vue instance
window.addressFieldSettings = new Vue({
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

// Initialize Vue instance
window.initAddressFieldSettings = () => {
    window.addressFieldSettings.$mount('#types-doublesecretagency-googlemaps-fields-AddressField-address-settings');
}

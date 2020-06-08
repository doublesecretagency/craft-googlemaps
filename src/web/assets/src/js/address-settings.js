import AddressField from './../vue/address';
import SubfieldManager from './../vue/subfield-manager';

// Disable silly message
Vue.config.productionTip = false;

// Initialize Vue instance
new Vue({
    el: '#types-doublesecretagency-googlemaps-fields-AddressField-address-settings',
    components: {
        'address-field': AddressField,
        'subfield-manager': SubfieldManager
    },
    data: {
        settings: settings,
        data: data,
        icons: icons
    }
});

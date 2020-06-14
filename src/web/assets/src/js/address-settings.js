import AddressField from './../vue/address';
import SubfieldManager from './../vue/subfield-manager';
import DefaultCoords from './../vue/default-coords';

// Disable silly message
Vue.config.productionTip = false;

// Initialize Vue instance
new Vue({
    el: '#types-doublesecretagency-googlemaps-fields-AddressField-address-settings',
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

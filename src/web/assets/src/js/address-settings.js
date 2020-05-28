import AddressField from './../vue/address';

// Disable silly message
Vue.config.productionTip = false;

// Initialize Vue instance
new Vue({
    el: '#types-doublesecretagency-googlemaps-fields-AddressField-address-settings',
    components: {
        'address-field': AddressField
    },
    data: {
        settings: settings,
        data: data
    }
});

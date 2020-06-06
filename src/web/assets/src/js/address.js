import AddressField from './../vue/address';

// Disable silly message
Vue.config.productionTip = false;

// Initialize Vue instance
new Vue({
    el: '#fields-address-'+handle,
    components: {
        'address-field': AddressField
    },
    data: {
        settings: settings,
        data: data,
        icons: icons
    }
});

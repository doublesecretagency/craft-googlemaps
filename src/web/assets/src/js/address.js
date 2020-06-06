import AddressField from './../vue/address';

// Disable silly message
Vue.config.productionTip = false;

// Loop through all Address field configurations
for (let i in addressFieldConfigs) {

    // Get configuration of a single Address field
    let config = addressFieldConfigs[i];

    // Initialize Vue instance for a single Address field
    new Vue({
        el: '#fields-address-'+config.handle,
        components: {
            'address-field': AddressField
        },
        data: {
            settings: config.settings,
            data: config.data,
            icons: config.icons
        }
    });

}

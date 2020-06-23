import AddressField from './../vue/address';

// Disable silly message
Vue.config.productionTip = false;

// Initialize collection of Vue instances
let vueAddressFields = [];

// Loop through all Address field configurations
for (let i in addressFieldConfigs) {

    // Get configuration of a single Address field
    let config = addressFieldConfigs[i];

    // Initialize Vue instance for a single Address field
    vueAddressFields[i] = new Vue({
        el: '#fields-address-'+config.handle,
        components: {
            'address-field': AddressField
        },
        data: {
            settings: config.settings,
            data: config.data,
            icons: config.icons,
            google: false,
            // initialized: false,
        }
    });

}

window.initAddressField = () => {
    console.log('callback triggered when an Address field is loaded');

    // Loop through all Address field configurations
    for (let i in addressFieldConfigs) {

        // Share Google API with each Vue instance
        vueAddressFields[i].$data.google = window.google;

        // OR SHOULD WE JUST BE SETTING `initialized` TO `true`
        // AND THEN REFER BACK TO THE GLOBAL `google` OBJECT?

    }

}

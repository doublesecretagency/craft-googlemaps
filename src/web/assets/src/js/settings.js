// import AddressField from './../vue/address/address';
// import SubfieldManager from './../vue/address-settings/subfield-manager';
// import DefaultCoords from './../vue/address-settings/default-coords';

// Disable silly message
Vue.config.productionTip = false;

// Create Vue instance
window.settings = new Vue({
    el: '#main-content',
    components: {
        // 'address-field': AddressField,
        // 'subfield-manager': SubfieldManager,
        // 'default-coords': DefaultCoords
    },
    data: {
        // settings: settings,
        // data: data,
        // icons: icons
    }
});

// // Initialize Vue instance
// window.initAddressFieldSettings = () => {
//     window.settings.$mount('#types-doublesecretagency-googlemaps-fields-AddressField-address-settings');
// }

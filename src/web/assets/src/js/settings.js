// List of all valid geolocation services
var validServices = ['','ipstack','maxmind'];

// Dropdown menu of geolocation services
var dropdown = document.getElementById('settings-geolocation-service');

// Display settings of the currently selected service
window.updateGeolocationService = function () {

    // Get specified service
    var newService = dropdown.value;

    // If invalid service, bail
    if (-1 === validServices.indexOf(newService)) {
        console.log('Invalid service specified.');
        return;
    }

    // Get DOM elements
    var settings = {
        'ipstack': document.getElementById('settings-ipstack'),
        'maxmind': document.getElementById('settings-maxmind'),
    }

    // Hide all optional settings
    settings['ipstack'].style.display = 'none';
    settings['maxmind'].style.display = 'none';

    // If a service was specified (not null), show it
    if (newService) {
        settings[newService].style.display = 'block';
    }

}

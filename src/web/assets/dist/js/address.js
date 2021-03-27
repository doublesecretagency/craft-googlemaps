/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-coords.vue":
/*!**************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-coords.vue ***!
  \**************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  data() {
    return {
      handle: this.$root.$data.handle,
      namespacedName: this.$root.$data.namespacedName
    };
  },

  computed: {
    getType() {
      // What type of input should the coordinate fields be?
      return 'hidden' === this.$root.$data.settings.coordinatesMode ? 'hidden' : 'number';
    },

    getReadOnly() {
      // Whether the coordinate fields should be read-only
      return !['editable', 'hidden'].includes(this.$root.$data.settings.coordinatesMode);
    },

    getInputClasses() {
      // Get the coordinates mode from settings
      let mode = this.$root.$data.settings.coordinatesMode; // If hidden, return empty array

      if ('hidden' === mode) {
        return [];
      } // Return array of input classes


      return ['text', 'code', 'fullwidth', 'editable' !== mode ? 'disabled' : null];
    }

  },
  methods: {
    // Get the display array
    coordinatesDisplay() {
      return [{
        key: 'lat',
        label: 'Latitude',
        styles: {
          'width': '43%'
        }
      }, {
        key: 'lng',
        label: 'Longitude',
        styles: {
          'width': '43%'
        }
      }, {
        key: 'zoom',
        label: 'Zoom',
        styles: {
          'width': '11%'
        }
      }];
    }

  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-map.vue":
/*!***********************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-map.vue ***!
  \***********************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
//
//
//
//
//
//
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  data() {
    // Make map & marker universally available
    return {
      map: null,
      marker: null,
      settings: this.$root.$data.settings
    };
  },

  computed: {
    // Compute locally for watching
    zoom() {
      return this.$root.$data.data.coords['zoom'];
    }

  },
  watch: {
    // When coordinates are changed, update the marker
    '$parent.lat': function () {
      this.updateMarkerPosition();
      this.$root.$data.data.coords['zoom'] = this.map.getZoom();
    },
    '$parent.lng': function () {
      this.updateMarkerPosition();
      this.$root.$data.data.coords['zoom'] = this.map.getZoom();
    },

    zoom() {
      this.updateZoomLevel();
    }

  },

  async mounted() {
    // Attempt to get map center from field
    let fieldPreference = this.fromField(this.$root.$data); // Initialize map using coordinates from field data or settings

    if (fieldPreference) {
      this.initMap(fieldPreference);
      return;
    } // Attempt to get map center from user's current location


    let promise = await new Promise(function (resolve, reject) {
      // Output console notification
      console.log('Attempting geolocation...'); // Attempt geolocation

      navigator.geolocation.getCurrentPosition(resolve, reject, {
        timeout: 5000
      });
    }).then(result => {
      // Output console notification
      console.log('Success!'); // If coordinates are invalid, bail

      if (!result.coords) {
        return;
      } // Initialize map based on user's current location


      this.initMap({
        lat: result.coords.latitude,
        lng: result.coords.longitude,
        zoom: 10
      });
    }, error => {
      // Output error message in console
      console.log('Unable to perform HTML5 geolocation.'); // Nothing else worked, use the fallback

      this.initMap(this.fromFallback());
    });
  },

  methods: {
    // Update the marker position
    updateMarkerPosition() {
      // If coordinates are invalid, bail
      if (!this.$parent.validCoords()) {
        return;
      } // Get coordinates


      let coords = this.$root.$data.data.coords; // Set marker position

      this.marker.setPosition({
        lat: parseFloat(coords.lat.toFixed(7)),
        lng: parseFloat(coords.lng.toFixed(7))
      }); // Center map

      this.centerMap();
    },

    // Attempt to get coordinates
    _getCoords(coords) {
      // If invalid coordinates, return false
      if (!this.$parent.validCoords(coords)) {
        return false;
      } // Return coordinates


      return coords;
    },

    // Attempt to get map center coordinates based on the field data or settings
    fromField(field) {
      // If available, get coords from the existing field data
      let dataCoords = this._getCoords(field.data.coords);

      if (dataCoords) {
        return dataCoords;
      } // If available, get default coords from the field settings


      let settingsCoords = this._getCoords(field.settings.coordinatesDefault);

      if (settingsCoords) {
        return settingsCoords;
      } // Unable to get any coordinates from the field


      return false;
    },

    // Use the generic fallback coordinates
    // https://plugins.doublesecretagency.com/google-maps/guides/bermuda-triangle/
    fromFallback() {
      // Bermuda Triangle
      return {
        lat: 32.3113966,
        lng: -64.7527469,
        zoom: 6
      };
    },

    // Update the zoom level
    updateZoomLevel() {
      // Get zoom level from field data
      let zoom = parseInt(this.$root.$data.data.coords['zoom']); // Corrections for incorrect zoom value

      if (0 === zoom || zoom < 0) {
        // Fallback when zoom is too low
        zoom = 0;
      } else if (!zoom || isNaN(zoom)) {
        // Fallback when zoom is invalid
        zoom = 11;
      } // Set map zoom level


      this.map.setZoom(zoom);
    },

    // Center map based on current marker position
    centerMap() {
      // Get coordinates
      let coords = JSON.parse(JSON.stringify(this.$root.$data.data.coords)); // If missing coordinates, bail

      if (!coords['lat'] || !coords['lng']) {
        return;
      } // Center map on marker coordinates


      this.map.panTo(coords);
    },

    // Initialize map
    initMap(startingPosition) {
      try {
        const google = window.google; // If google object doesn't exist yet, log message and bail

        if (!google) {
          console.error('The `google` object has not yet been loaded.');
          return;
        } // Determine map center


        let mapCenter = {
          lat: parseFloat(startingPosition.lat),
          lng: parseFloat(startingPosition.lng)
        }; // Create the map

        this.map = new google.maps.Map(this.$el, {
          streetViewControl: false,
          fullscreenControl: false,
          center: mapCenter,
          zoom: parseInt(startingPosition.zoom),
          controlSize: this.settings.controlSize
        }); // Create a draggable marker

        this.marker = new google.maps.Marker({
          position: mapCenter,
          map: this.map,
          draggable: true
        }); // When marker is dropped, re-center the map

        google.maps.event.addListener(this.marker, 'dragend', () => {
          let position = this.marker.getPosition();
          this.$root.$data.data.coords = {
            'lat': parseFloat(position.lat().toFixed(7)),
            'lng': parseFloat(position.lng().toFixed(7)),
            'zoom': this.map.getZoom()
          };
          this.centerMap();
        }); // When map is zoomed, update zoom value

        google.maps.event.addListener(this.map, 'zoom_changed', () => {
          this.$root.$data.data.coords['zoom'] = this.map.getZoom();
        });
      } catch (error) {
        // Unable to initialize the map
        console.error(error);
      }
    }

  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-meta.vue":
/*!************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-meta.vue ***!
  \************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
//
//
//
//
//
//
//
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  data() {
    return {
      handle: this.$root.$data.handle
    };
  },

  watch: {
    '$parent.lat': function () {
      this.validateMeta();
    },
    '$parent.lng': function () {
      this.validateMeta();
    }
  },
  methods: {
    validateMeta: function () {
      // If coordinates are invalid
      if (!this.$parent.validCoords()) {
        // Reset meta fields
        this.$root.$data.data.address['formatted'] = null;
        this.$root.$data.data.address['raw'] = null;
      }
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-subfields.vue":
/*!*****************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-subfields.vue ***!
  \*****************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _utils_address_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./../utils/address-components */ "./web/assets/src/vue/utils/address-components.js");
/* harmony import */ var _utils_subfield_display__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./../utils/subfield-display */ "./web/assets/src/vue/utils/subfield-display.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  data() {
    return {
      handle: this.$root.$data.handle,
      namespacedName: this.$root.$data.namespacedName,
      autocomplete: false,
      inputClasses: ['text', 'fullwidth']
    };
  },

  mounted() {
    try {
      const google = window.google;
      const options = {
        types: ['geocode'],
        fields: ['formatted_address', 'address_components', 'geometry.location', 'place_id']
      }; // If google object doesn't exist yet, log message and bail

      if (!google) {
        console.error('The `google` object has not yet been loaded.');
        return;
      } // If no subfields exist, bail


      if (!this.$refs.autocomplete) {
        return;
      } // Get first subfield


      const $first = this.$refs.autocomplete[0]; // Create an Autocomplete object

      this.autocomplete = new google.maps.places.Autocomplete($first, options); // Listen for autocomplete trigger

      this.autocomplete.addListener('place_changed', () => {
        // Get newly selected place
        let place = this.autocomplete.getPlace(); // Configure address data based on place

        this.setAddressData(place); // Get settings

        let settings = this.$root.$data.settings; // If not changing the map visibility, bail

        if ('noChange' === settings.mapOnSearch) {
          return;
        } // Change map visibility based on settings


        this.$root.$data.settings.showMap = 'open' === settings.mapOnSearch;
      }); // Prevent address selection from attempting to submit the form

      google.maps.event.addDomListener($first, 'keydown', event => {
        if (event.keyCode === 13) {
          event.preventDefault();
        }
      });
    } catch (error) {
      // Something went wrong
      console.error(error);
    }
  },

  methods: {
    // Populate address data when Autocomplete selected
    setAddressData(place) {
      // Get data object
      let data = this.$root.$data.data; // Get new address info

      let components = place.address_components;
      let coords = place.geometry.location; // Set all subfield data

      (0,_utils_address_components__WEBPACK_IMPORTED_MODULE_0__.default)(components, data.address);
      /**
       * If/when the coordinates are incomplete, erase the "formatted" and "raw" values.
       */
      // Append address meta data

      data.address['formatted'] = place.formatted_address;
      data.address['raw'] = JSON.stringify(place); // Set coordinates

      data.coords.lat = parseFloat(coords.lat().toFixed(7));
      data.coords.lng = parseFloat(coords.lng().toFixed(7));
    },

    // Get the display array
    subfieldDisplay() {
      // Get the subfield arrangement
      let arrangement = this.$root.$data.settings.subfieldConfig; // Return configured arrangement

      return (0,_utils_subfield_display__WEBPACK_IMPORTED_MODULE_1__.default)(arrangement);
    }

  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-toggle.vue":
/*!**************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-toggle.vue ***!
  \**************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  data() {
    return {
      toggleOffset: -25
    };
  },

  computed: {
    marginTop() {
      return `${this.toggleOffset}px`;
    },

    toggleMode() {
      return this.$root.$data.settings.visibilityToggle;
    },

    toggleText() {
      return this.showMap ? 'Hide Map' : 'Show Map';
    },

    showMap() {
      return this.$root.$data.settings.showMap;
    },

    markerIcon() {
      let icons = this.$root.$data.icons;
      return this.showMap ? icons.markerHollow : icons.marker;
    }

  },

  mounted() {
    this.adjustTogglePosition();
  },

  methods: {
    adjustTogglePosition() {
      // Look at parent element to get nearby items
      let $container = this.$el.closest('.field');
      let copyTextBtn = $container.getElementsByClassName('copytextbtn');
      let instructions = $container.getElementsByClassName('instructions'); // If "copy" div is visible

      if (copyTextBtn.length) {
        // Compile new class
        const toggleClass = `gm-toggle-${this.toggleMode}`; // Add new class to "copy" div

        copyTextBtn[0].classList.add(toggleClass);
      } // If field has instructions


      if (instructions.length) {
        // Measure height of instructions div
        let height = instructions[0].clientHeight; // Adjust toggle offset accordingly

        this.toggleOffset -= height;
      }
    },

    toggle() {
      // Show or hide the map
      let showMap = this.$root.$data.settings.showMap;
      this.$root.$data.settings.showMap = !showMap;
    }

  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address.vue":
/*!*******************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address.vue ***!
  \*******************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _address_toggle_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./address-toggle.vue */ "./web/assets/src/vue/address/address-toggle.vue");
/* harmony import */ var _address_subfields_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./address-subfields.vue */ "./web/assets/src/vue/address/address-subfields.vue");
/* harmony import */ var _address_coords_vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./address-coords.vue */ "./web/assets/src/vue/address/address-coords.vue");
/* harmony import */ var _address_meta_vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./address-meta.vue */ "./web/assets/src/vue/address/address-meta.vue");
/* harmony import */ var _address_map_vue__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./address-map.vue */ "./web/assets/src/vue/address/address-map.vue");
//
//
//
//
//
//
//
//
//
//
//





/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'AddressField',
  components: {
    'address-toggle': _address_toggle_vue__WEBPACK_IMPORTED_MODULE_0__.default,
    'address-subfields': _address_subfields_vue__WEBPACK_IMPORTED_MODULE_1__.default,
    'address-coords': _address_coords_vue__WEBPACK_IMPORTED_MODULE_2__.default,
    'address-meta': _address_meta_vue__WEBPACK_IMPORTED_MODULE_3__.default,
    'address-map': _address_map_vue__WEBPACK_IMPORTED_MODULE_4__.default
  },
  props: ['settings', 'data'],
  // data() {
  //     return {
  //         // google: false,
  //         initialized: false,
  //     }
  // },
  computed: {
    // Compute locally for watching
    lat() {
      return this.data.coords['lat'];
    },

    lng() {
      return this.data.coords['lng'];
    }

  },
  methods: {
    // Check whether coordinates are valid
    validCoords(coords) {
      // If no coordinates specified
      if (!coords) {
        // Use internal coordinates
        coords = {
          'lat': this.data.coords['lat'],
          'lng': this.data.coords['lng']
        };
      } // Loop through coordinates


      for (let key in coords) {
        // Ignore the zoom value
        if ('zoom' === key) {
          continue;
        } // Get individual coordinate


        let coord = coords[key]; // If coordinate is not a number or string, return false

        if (!['number', 'string'].includes(typeof coord)) {
          return false;
        } // If coordinate is not numeric, return false


        if (isNaN(coord)) {
          return false;
        } // If coordinate is an empty string, return false


        if ('' === coord) {
          return false;
        }
      } // Coordinates are valid!


      return true;
    }

  }
});

/***/ }),

/***/ "./web/assets/src/js/address.js":
/*!**************************************!*\
  !*** ./web/assets/src/js/address.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _vue_address_address_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./../vue/address/address.vue */ "./web/assets/src/vue/address/address.vue");
 // Disable silly message

Vue.config.productionTip = false; // Initialize Vue instances

window.initAddressField = function () {
  // If configs aren't loaded yet, bail
  if ('undefined' === typeof addressFieldConfigs) {
    return;
  } // Set class which marks element as loaded


  var alreadyLoaded = 'vue-mounted'; // Loop through all Address field configurations

  var _loop = function _loop(i) {
    // Get configuration of a single Address field
    var config = addressFieldConfigs[i]; // Get DOM element

    var element = document.getElementById(config.namespacedId); // If element does not exist, skip this one

    if (!element) {
      console.warn("[GM] The following Address field cannot be found: ".concat(config.namespacedId));
      return "continue";
    } // If already mounted, skip this one


    if (element.classList.contains(alreadyLoaded)) {
      return "continue";
    } // Initialize Vue instance for a single Address field


    new Vue({
      el: "#".concat(config.namespacedId),
      components: {
        'address-field': _vue_address_address_vue__WEBPACK_IMPORTED_MODULE_0__.default
      },
      mounted: function mounted() {
        // Mark element as mounted
        var element = document.getElementById(config.namespacedId);
        element.classList.add(alreadyLoaded);
      },
      data: {
        handle: config.handle,
        namespacedName: config.namespacedName,
        settings: config.settings,
        data: config.data,
        icons: config.icons
      }
    });
  };

  for (var i in addressFieldConfigs) {
    var _ret = _loop(i);

    if (_ret === "continue") continue;
  }
};

/***/ }),

/***/ "./web/assets/src/vue/utils/address-components.js":
/*!********************************************************!*\
  !*** ./web/assets/src/vue/utils/address-components.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ addressComponents)
/* harmony export */ });
// Countries where the street number precedes the street name
var numberFirst = ['Australia', 'Canada', 'France', 'Hong Kong', 'India', 'Ireland', 'Malaysia', 'New Zealand', 'Pakistan', 'Singapore', 'Sri Lanka', 'Taiwan', 'Thailand', 'United Kingdom', 'United States']; // Countries with a comma after the street name

var commaAfterStreet = ['Italy']; // Format the main street address

function formatStreetAddress(a) {
  // Abbreviate variables
  var streetNumber = a.street_number || '';
  var streetName = a.route || '';
  var country = a.country || ''; // Default street format

  var street = "".concat(streetName, " ").concat(streetNumber); // If country with different format, use that format

  if (numberFirst.includes(country)) {
    street = "".concat(streetNumber, " ").concat(streetName);
  } else if (commaAfterStreet.includes(country)) {
    street = "".concat(streetName, ", ").concat(streetNumber);
  } // Return formatted street address


  return street.trim().replace(/,*$/, '');
} // Set the formatted address data


function addressComponents(components, data) {
  // Initialize formatted address data
  var formatted = {}; // Loop through address components

  components.forEach(function (c) {
    // Get component type
    var type = c['types'][0]; // Format component

    switch (type) {
      case 'locality':
      case 'country':
        formatted[type] = c['long_name'];
        break;

      default:
        formatted[type] = c['short_name'];
        break;
    }
  }); // Set address data to Vue

  data.street1 = formatStreetAddress(formatted);
  data.street2 = null;
  data.city = formatted['locality'];
  data.state = formatted['administrative_area_level_1'];
  data.zip = formatted['postal_code'];
  data.country = formatted['country']; // Country-specific adjustments

  switch (formatted['country']) {
    case 'United Kingdom':
      data.city = formatted['postal_town'];
      data.state = formatted['administrative_area_level_2'];
      break;
  }
}

/***/ }),

/***/ "./web/assets/src/vue/utils/subfield-display.js":
/*!******************************************************!*\
  !*** ./web/assets/src/vue/utils/subfield-display.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ subfieldDisplay)
/* harmony export */ });
/**
 * DEFAULT
 * Configure the subfield display.
 *
 * @param subfields
 * @returns array
 */
function subfieldDisplay(subfields) {
  // Rearrange subfields according to their position
  subfields = rearrange(subfields); // Return subfield display data

  return displayConfig(subfields);
}
/**
 * Rearrange the subfield data.
 *
 * @param oldOrder
 * @returns array
 */

function rearrange(oldOrder) {
  // Backup position counter
  var p = 100; // Initialize new order

  var newOrder = []; // Loop through old arrangement

  for (var key in oldOrder) {
    // Get the subfield data
    var subfield = oldOrder[key]; // Move key to within object

    subfield.key = key; // Set the new subfield position

    var position = parseInt(subfield.position || p++); // Move subfield

    newOrder[position] = subfield;
  } // Return new arrangement


  return Object.values(newOrder);
}
/**
 * Configure the data to be displayed.
 *
 * @param arrangement
 * @returns array
 */


function displayConfig(arrangement) {
  // Initialize display array
  var display = []; // Loop through subfield arrangement

  for (var key in arrangement) {
    // Get the subfield
    var subfield = arrangement[key]; // Initialize input styles

    var styles = {}; // If the subfield is disabled

    if (!subfield.enabled) {
      // Render it, but keep it hidden
      styles['display'] = 'none';
    } else {
      // Get subfield width
      var width = subfield.width; // Never go over 100%

      if (100 < width) {
        width = 100;
      } // Give up 1% width to the right margin


      styles['width'] = "".concat(--width, "%");
    } // Append subfield configuration


    display.push({
      key: subfield.key,
      label: subfield.label,
      enabled: subfield.enabled,
      // required: subfield.required,
      styles: styles
    });
  } // Return the subfield display array


  return display;
}

/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"optionsId\":\"0\",\"vue\":true,\"id\":\"data-v-457209b9\",\"scoped\":true,\"sourceMap\":false}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./web/assets/src/vue/address/address-coords.vue":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/style-compiler/index.js?{"optionsId":"0","vue":true,"id":"data-v-457209b9","scoped":true,"sourceMap":false}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./web/assets/src/vue/address/address-coords.vue ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
// Imports

var ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n.disabled[data-v-457209b9] {\n    opacity: 0.60;\n    background-color: #e4eaf4;\n}\n", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/css-loader/dist/runtime/api.js":
/*!*****************************************************!*\
  !*** ./node_modules/css-loader/dist/runtime/api.js ***!
  \*****************************************************/
/***/ ((module) => {

"use strict";


/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
// eslint-disable-next-line func-names
module.exports = function (cssWithMappingToString) {
  var list = []; // return the list of modules as css string

  list.toString = function toString() {
    return this.map(function (item) {
      var content = cssWithMappingToString(item);

      if (item[2]) {
        return "@media ".concat(item[2], " {").concat(content, "}");
      }

      return content;
    }).join("");
  }; // import a list of modules into the list
  // eslint-disable-next-line func-names


  list.i = function (modules, mediaQuery, dedupe) {
    if (typeof modules === "string") {
      // eslint-disable-next-line no-param-reassign
      modules = [[null, modules, ""]];
    }

    var alreadyImportedModules = {};

    if (dedupe) {
      for (var i = 0; i < this.length; i++) {
        // eslint-disable-next-line prefer-destructuring
        var id = this[i][0];

        if (id != null) {
          alreadyImportedModules[id] = true;
        }
      }
    }

    for (var _i = 0; _i < modules.length; _i++) {
      var item = [].concat(modules[_i]);

      if (dedupe && alreadyImportedModules[item[0]]) {
        // eslint-disable-next-line no-continue
        continue;
      }

      if (mediaQuery) {
        if (!item[2]) {
          item[2] = mediaQuery;
        } else {
          item[2] = "".concat(mediaQuery, " and ").concat(item[2]);
        }
      }

      list.push(item);
    }
  };

  return list;
};

/***/ }),

/***/ "./web/assets/src/sass/address.scss":
/*!******************************************!*\
  !*** ./web/assets/src/sass/address.scss ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./web/assets/src/vue/address/address-coords.vue":
/*!*******************************************************!*\
  !*** ./web/assets/src/vue/address/address-coords.vue ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_address_coords_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !!babel-loader!../../../../../node_modules/vue-loader/lib/selector?type=script&index=0!./address-coords.vue */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-coords.vue");
/* harmony import */ var _node_modules_vue_loader_lib_template_compiler_index_id_data_v_457209b9_hasScoped_true_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_coords_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../../node_modules/vue-loader/lib/template-compiler/index?{"id":"data-v-457209b9","hasScoped":true,"optionsId":"0","buble":{"transforms":{}}}!../../../../../node_modules/vue-loader/lib/selector?type=template&index=0!./address-coords.vue */ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-457209b9\",\"hasScoped\":true,\"optionsId\":\"0\",\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-coords.vue");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_component_normalizer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/component-normalizer */ "./node_modules/vue-loader/lib/runtime/component-normalizer.js");
var disposed = false
function injectStyle (context) {
  if (disposed) return
  __webpack_require__(/*! !!vue-style-loader!css-loader!../../../../../node_modules/vue-loader/lib/style-compiler/index?{"optionsId":"0","vue":true,"id":"data-v-457209b9","scoped":true,"sourceMap":false}!../../../../../node_modules/vue-loader/lib/selector?type=styles&index=0!./address-coords.vue */ "./node_modules/vue-style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"optionsId\":\"0\",\"vue\":true,\"id\":\"data-v-457209b9\",\"scoped\":true,\"sourceMap\":false}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./web/assets/src/vue/address/address-coords.vue")
}
/* script */

;
/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-457209b9"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
;
var Component = (0,_node_modules_vue_loader_lib_runtime_component_normalizer__WEBPACK_IMPORTED_MODULE_2__.default)(
  _babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_address_coords_vue__WEBPACK_IMPORTED_MODULE_0__.default,
  _node_modules_vue_loader_lib_template_compiler_index_id_data_v_457209b9_hasScoped_true_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_coords_vue__WEBPACK_IMPORTED_MODULE_1__.render,
  _node_modules_vue_loader_lib_template_compiler_index_id_data_v_457209b9_hasScoped_true_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_coords_vue__WEBPACK_IMPORTED_MODULE_1__.staticRenderFns,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "web/assets/src/vue/address/address-coords.vue"

/* hot reload */
if (false) {}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Component.exports);


/***/ }),

/***/ "./web/assets/src/vue/address/address-map.vue":
/*!****************************************************!*\
  !*** ./web/assets/src/vue/address/address-map.vue ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_address_map_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !!babel-loader!../../../../../node_modules/vue-loader/lib/selector?type=script&index=0!./address-map.vue */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-map.vue");
/* harmony import */ var _node_modules_vue_loader_lib_template_compiler_index_id_data_v_54adced1_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_map_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../../node_modules/vue-loader/lib/template-compiler/index?{"id":"data-v-54adced1","hasScoped":false,"optionsId":"0","buble":{"transforms":{}}}!../../../../../node_modules/vue-loader/lib/selector?type=template&index=0!./address-map.vue */ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-54adced1\",\"hasScoped\":false,\"optionsId\":\"0\",\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-map.vue");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_component_normalizer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/component-normalizer */ "./node_modules/vue-loader/lib/runtime/component-normalizer.js");
var disposed = false
/* script */

;
/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
;
var Component = (0,_node_modules_vue_loader_lib_runtime_component_normalizer__WEBPACK_IMPORTED_MODULE_2__.default)(
  _babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_address_map_vue__WEBPACK_IMPORTED_MODULE_0__.default,
  _node_modules_vue_loader_lib_template_compiler_index_id_data_v_54adced1_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_map_vue__WEBPACK_IMPORTED_MODULE_1__.render,
  _node_modules_vue_loader_lib_template_compiler_index_id_data_v_54adced1_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_map_vue__WEBPACK_IMPORTED_MODULE_1__.staticRenderFns,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "web/assets/src/vue/address/address-map.vue"

/* hot reload */
if (false) {}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Component.exports);


/***/ }),

/***/ "./web/assets/src/vue/address/address-meta.vue":
/*!*****************************************************!*\
  !*** ./web/assets/src/vue/address/address-meta.vue ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_address_meta_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !!babel-loader!../../../../../node_modules/vue-loader/lib/selector?type=script&index=0!./address-meta.vue */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-meta.vue");
/* harmony import */ var _node_modules_vue_loader_lib_template_compiler_index_id_data_v_1e2654e0_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_meta_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../../node_modules/vue-loader/lib/template-compiler/index?{"id":"data-v-1e2654e0","hasScoped":false,"optionsId":"0","buble":{"transforms":{}}}!../../../../../node_modules/vue-loader/lib/selector?type=template&index=0!./address-meta.vue */ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-1e2654e0\",\"hasScoped\":false,\"optionsId\":\"0\",\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-meta.vue");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_component_normalizer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/component-normalizer */ "./node_modules/vue-loader/lib/runtime/component-normalizer.js");
var disposed = false
/* script */

;
/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
;
var Component = (0,_node_modules_vue_loader_lib_runtime_component_normalizer__WEBPACK_IMPORTED_MODULE_2__.default)(
  _babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_address_meta_vue__WEBPACK_IMPORTED_MODULE_0__.default,
  _node_modules_vue_loader_lib_template_compiler_index_id_data_v_1e2654e0_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_meta_vue__WEBPACK_IMPORTED_MODULE_1__.render,
  _node_modules_vue_loader_lib_template_compiler_index_id_data_v_1e2654e0_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_meta_vue__WEBPACK_IMPORTED_MODULE_1__.staticRenderFns,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "web/assets/src/vue/address/address-meta.vue"

/* hot reload */
if (false) {}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Component.exports);


/***/ }),

/***/ "./web/assets/src/vue/address/address-subfields.vue":
/*!**********************************************************!*\
  !*** ./web/assets/src/vue/address/address-subfields.vue ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_address_subfields_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !!babel-loader!../../../../../node_modules/vue-loader/lib/selector?type=script&index=0!./address-subfields.vue */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-subfields.vue");
/* harmony import */ var _node_modules_vue_loader_lib_template_compiler_index_id_data_v_6ce2c62e_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_subfields_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../../node_modules/vue-loader/lib/template-compiler/index?{"id":"data-v-6ce2c62e","hasScoped":false,"optionsId":"0","buble":{"transforms":{}}}!../../../../../node_modules/vue-loader/lib/selector?type=template&index=0!./address-subfields.vue */ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-6ce2c62e\",\"hasScoped\":false,\"optionsId\":\"0\",\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-subfields.vue");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_component_normalizer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/component-normalizer */ "./node_modules/vue-loader/lib/runtime/component-normalizer.js");
var disposed = false
/* script */

;
/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
;
var Component = (0,_node_modules_vue_loader_lib_runtime_component_normalizer__WEBPACK_IMPORTED_MODULE_2__.default)(
  _babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_address_subfields_vue__WEBPACK_IMPORTED_MODULE_0__.default,
  _node_modules_vue_loader_lib_template_compiler_index_id_data_v_6ce2c62e_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_subfields_vue__WEBPACK_IMPORTED_MODULE_1__.render,
  _node_modules_vue_loader_lib_template_compiler_index_id_data_v_6ce2c62e_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_subfields_vue__WEBPACK_IMPORTED_MODULE_1__.staticRenderFns,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "web/assets/src/vue/address/address-subfields.vue"

/* hot reload */
if (false) {}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Component.exports);


/***/ }),

/***/ "./web/assets/src/vue/address/address-toggle.vue":
/*!*******************************************************!*\
  !*** ./web/assets/src/vue/address/address-toggle.vue ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_address_toggle_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !!babel-loader!../../../../../node_modules/vue-loader/lib/selector?type=script&index=0!./address-toggle.vue */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address-toggle.vue");
/* harmony import */ var _node_modules_vue_loader_lib_template_compiler_index_id_data_v_064615ef_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_toggle_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../../node_modules/vue-loader/lib/template-compiler/index?{"id":"data-v-064615ef","hasScoped":false,"optionsId":"0","buble":{"transforms":{}}}!../../../../../node_modules/vue-loader/lib/selector?type=template&index=0!./address-toggle.vue */ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-064615ef\",\"hasScoped\":false,\"optionsId\":\"0\",\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-toggle.vue");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_component_normalizer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/component-normalizer */ "./node_modules/vue-loader/lib/runtime/component-normalizer.js");
var disposed = false
/* script */

;
/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
;
var Component = (0,_node_modules_vue_loader_lib_runtime_component_normalizer__WEBPACK_IMPORTED_MODULE_2__.default)(
  _babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_address_toggle_vue__WEBPACK_IMPORTED_MODULE_0__.default,
  _node_modules_vue_loader_lib_template_compiler_index_id_data_v_064615ef_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_toggle_vue__WEBPACK_IMPORTED_MODULE_1__.render,
  _node_modules_vue_loader_lib_template_compiler_index_id_data_v_064615ef_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_toggle_vue__WEBPACK_IMPORTED_MODULE_1__.staticRenderFns,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "web/assets/src/vue/address/address-toggle.vue"

/* hot reload */
if (false) {}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Component.exports);


/***/ }),

/***/ "./web/assets/src/vue/address/address.vue":
/*!************************************************!*\
  !*** ./web/assets/src/vue/address/address.vue ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_address_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !!babel-loader!../../../../../node_modules/vue-loader/lib/selector?type=script&index=0!./address.vue */ "./node_modules/babel-loader/lib/index.js!./node_modules/vue-loader/lib/selector.js?type=script&index=0!./web/assets/src/vue/address/address.vue");
/* harmony import */ var _node_modules_vue_loader_lib_template_compiler_index_id_data_v_0378b942_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../../node_modules/vue-loader/lib/template-compiler/index?{"id":"data-v-0378b942","hasScoped":false,"optionsId":"0","buble":{"transforms":{}}}!../../../../../node_modules/vue-loader/lib/selector?type=template&index=0!./address.vue */ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-0378b942\",\"hasScoped\":false,\"optionsId\":\"0\",\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address.vue");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_component_normalizer__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/component-normalizer */ "./node_modules/vue-loader/lib/runtime/component-normalizer.js");
var disposed = false
/* script */

;
/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
;
var Component = (0,_node_modules_vue_loader_lib_runtime_component_normalizer__WEBPACK_IMPORTED_MODULE_2__.default)(
  _babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_address_vue__WEBPACK_IMPORTED_MODULE_0__.default,
  _node_modules_vue_loader_lib_template_compiler_index_id_data_v_0378b942_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_vue__WEBPACK_IMPORTED_MODULE_1__.render,
  _node_modules_vue_loader_lib_template_compiler_index_id_data_v_0378b942_hasScoped_false_optionsId_0_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_address_vue__WEBPACK_IMPORTED_MODULE_1__.staticRenderFns,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "web/assets/src/vue/address/address.vue"

/* hot reload */
if (false) {}

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Component.exports);


/***/ }),

/***/ "./node_modules/vue-loader/lib/runtime/component-normalizer.js":
/*!*********************************************************************!*\
  !*** ./node_modules/vue-loader/lib/runtime/component-normalizer.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ normalizeComponent)
/* harmony export */ });
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent (
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier, /* server only */
  shadowMode /* vue-cli only */
) {
  scriptExports = scriptExports || {}

  // ES6 modules interop
  var type = typeof scriptExports.default
  if (type === 'object' || type === 'function') {
    scriptExports = scriptExports.default
  }

  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (render) {
    options.render = render
    options.staticRenderFns = staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = shadowMode
      ? function () { injectStyles.call(this, this.$root.$options.shadowRoot) }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}


/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-0378b942\",\"hasScoped\":false,\"optionsId\":\"0\",\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address.vue":
/*!**************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/template-compiler/index.js?{"id":"data-v-0378b942","hasScoped":false,"optionsId":"0","buble":{"transforms":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address.vue ***!
  \**************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "address-field" },
    [
      _c("address-toggle"),
      _vm._v(" "),
      _c("address-subfields"),
      _vm._v(" "),
      _c("address-coords"),
      _vm._v(" "),
      _c("address-meta"),
      _vm._v(" "),
      _c("div", { staticStyle: { clear: "both" } }),
      _vm._v(" "),
      _c("address-map")
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true

if (false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-064615ef\",\"hasScoped\":false,\"optionsId\":\"0\",\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-toggle.vue":
/*!*********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/template-compiler/index.js?{"id":"data-v-064615ef","hasScoped":false,"optionsId":"0","buble":{"transforms":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-toggle.vue ***!
  \*********************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return "hidden" !== _vm.toggleMode
    ? _c(
        "span",
        {
          style: {
            float: "right",
            "margin-top": _vm.marginTop,
            "margin-right": "8px",
            cursor: "pointer"
          },
          on: {
            click: function($event) {
              return _vm.toggle()
            }
          }
        },
        [
          "icon" !== _vm.toggleMode
            ? _c("span", [_vm._v(_vm._s(_vm.toggleText))])
            : _vm._e(),
          _vm._v(" "),
          "text" !== _vm.toggleMode
            ? _c("img", {
                style: {
                  height: "14px",
                  "margin-left": "2px",
                  "margin-bottom": "-2px"
                },
                attrs: {
                  alt: "Marker icon",
                  title: "icon" === _vm.toggleMode ? _vm.toggleText : false,
                  src: _vm.markerIcon
                }
              })
            : _vm._e()
        ]
      )
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true

if (false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-1e2654e0\",\"hasScoped\":false,\"optionsId\":\"0\",\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-meta.vue":
/*!*******************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/template-compiler/index.js?{"id":"data-v-1e2654e0","hasScoped":false,"optionsId":"0","buble":{"transforms":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-meta.vue ***!
  \*******************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("input", {
      directives: [
        {
          name: "model",
          rawName: "v-model",
          value: _vm.$root.$data.data.address["formatted"],
          expression: "$root.$data.data.address['formatted']"
        }
      ],
      attrs: { type: "hidden", name: "fields[" + _vm.handle + "][formatted]" },
      domProps: { value: _vm.$root.$data.data.address["formatted"] },
      on: {
        input: function($event) {
          if ($event.target.composing) {
            return
          }
          _vm.$set(
            _vm.$root.$data.data.address,
            "formatted",
            $event.target.value
          )
        }
      }
    }),
    _vm._v(" "),
    _c("input", {
      directives: [
        {
          name: "model",
          rawName: "v-model",
          value: _vm.$root.$data.data.address["raw"],
          expression: "$root.$data.data.address['raw']"
        }
      ],
      attrs: { type: "hidden", name: "fields[" + _vm.handle + "][raw]" },
      domProps: { value: _vm.$root.$data.data.address["raw"] },
      on: {
        input: function($event) {
          if ($event.target.composing) {
            return
          }
          _vm.$set(_vm.$root.$data.data.address, "raw", $event.target.value)
        }
      }
    })
  ])
}
var staticRenderFns = []
render._withStripped = true

if (false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-457209b9\",\"hasScoped\":true,\"optionsId\":\"0\",\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-coords.vue":
/*!********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/template-compiler/index.js?{"id":"data-v-457209b9","hasScoped":true,"optionsId":"0","buble":{"transforms":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-coords.vue ***!
  \********************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    _vm._l(_vm.coordinatesDisplay(), function(coord) {
      return _c("input", {
        directives: [
          {
            name: "model",
            rawName: "v-model.number",
            value: _vm.$root.$data.data.coords[coord.key],
            expression: "$root.$data.data.coords[coord.key]",
            modifiers: { number: true }
          }
        ],
        class: _vm.getInputClasses,
        style: coord.styles,
        attrs: {
          placeholder: coord.label,
          type: _vm.getType,
          readonly: _vm.getReadOnly,
          autocomplete: "chrome-off",
          name: _vm.namespacedName + "[" + coord.key + "]"
        },
        domProps: { value: _vm.$root.$data.data.coords[coord.key] },
        on: {
          input: function($event) {
            if ($event.target.composing) {
              return
            }
            _vm.$set(
              _vm.$root.$data.data.coords,
              coord.key,
              _vm._n($event.target.value)
            )
          },
          blur: function($event) {
            return _vm.$forceUpdate()
          }
        }
      })
    }),
    0
  )
}
var staticRenderFns = []
render._withStripped = true

if (false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-54adced1\",\"hasScoped\":false,\"optionsId\":\"0\",\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-map.vue":
/*!******************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/template-compiler/index.js?{"id":"data-v-54adced1","hasScoped":false,"optionsId":"0","buble":{"transforms":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-map.vue ***!
  \******************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      directives: [
        {
          name: "show",
          rawName: "v-show",
          value: _vm.settings.showMap,
          expression: "settings.showMap"
        }
      ],
      staticClass: "gm-map"
    },
    [_c("div", [_vm._v("Loading map...")])]
  )
}
var staticRenderFns = []
render._withStripped = true

if (false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/template-compiler/index.js?{\"id\":\"data-v-6ce2c62e\",\"hasScoped\":false,\"optionsId\":\"0\",\"buble\":{\"transforms\":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-subfields.vue":
/*!************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/template-compiler/index.js?{"id":"data-v-6ce2c62e","hasScoped":false,"optionsId":"0","buble":{"transforms":{}}}!./node_modules/vue-loader/lib/selector.js?type=template&index=0!./web/assets/src/vue/address/address-subfields.vue ***!
  \************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    _vm._l(_vm.subfieldDisplay(), function(subfield) {
      return _c("input", {
        directives: [
          {
            name: "model",
            rawName: "v-model",
            value: _vm.$root.$data.data.address[subfield.key],
            expression: "$root.$data.data.address[subfield.key]"
          }
        ],
        ref: subfield.enabled ? "autocomplete" : "",
        refInFor: true,
        class: _vm.inputClasses,
        style: subfield.styles,
        attrs: {
          placeholder: subfield.label,
          autocomplete: "chrome-off",
          name: _vm.namespacedName + "[" + subfield.key + "]"
        },
        domProps: { value: _vm.$root.$data.data.address[subfield.key] },
        on: {
          input: function($event) {
            if ($event.target.composing) {
              return
            }
            _vm.$set(
              _vm.$root.$data.data.address,
              subfield.key,
              $event.target.value
            )
          }
        }
      })
    }),
    0
  )
}
var staticRenderFns = []
render._withStripped = true

if (false) {}

/***/ }),

/***/ "./node_modules/vue-style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"optionsId\":\"0\",\"vue\":true,\"id\":\"data-v-457209b9\",\"scoped\":true,\"sourceMap\":false}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./web/assets/src/vue/address/address-coords.vue":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/style-compiler/index.js?{"optionsId":"0","vue":true,"id":"data-v-457209b9","scoped":true,"sourceMap":false}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./web/assets/src/vue/address/address-coords.vue ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(/*! !!../../../../../node_modules/css-loader/dist/cjs.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{"optionsId":"0","vue":true,"id":"data-v-457209b9","scoped":true,"sourceMap":false}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./address-coords.vue */ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/style-compiler/index.js?{\"optionsId\":\"0\",\"vue\":true,\"id\":\"data-v-457209b9\",\"scoped\":true,\"sourceMap\":false}!./node_modules/vue-loader/lib/selector.js?type=styles&index=0!./web/assets/src/vue/address/address-coords.vue");
if(content.__esModule) content = content.default;
if(typeof content === 'string') content = [[module.id, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var add = __webpack_require__(/*! !../../../../../node_modules/vue-style-loader/lib/addStylesClient.js */ "./node_modules/vue-style-loader/lib/addStylesClient.js").default
var update = add("090382fe", content, false, {});
// Hot Module Replacement
if(false) {}

/***/ }),

/***/ "./node_modules/vue-style-loader/lib/addStylesClient.js":
/*!**************************************************************!*\
  !*** ./node_modules/vue-style-loader/lib/addStylesClient.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ addStylesClient)
/* harmony export */ });
/* harmony import */ var _listToStyles__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./listToStyles */ "./node_modules/vue-style-loader/lib/listToStyles.js");
/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
  Modified by Evan You @yyx990803
*/



var hasDocument = typeof document !== 'undefined'

if (typeof DEBUG !== 'undefined' && DEBUG) {
  if (!hasDocument) {
    throw new Error(
    'vue-style-loader cannot be used in a non-browser environment. ' +
    "Use { target: 'node' } in your Webpack config to indicate a server-rendering environment."
  ) }
}

/*
type StyleObject = {
  id: number;
  parts: Array<StyleObjectPart>
}

type StyleObjectPart = {
  css: string;
  media: string;
  sourceMap: ?string
}
*/

var stylesInDom = {/*
  [id: number]: {
    id: number,
    refs: number,
    parts: Array<(obj?: StyleObjectPart) => void>
  }
*/}

var head = hasDocument && (document.head || document.getElementsByTagName('head')[0])
var singletonElement = null
var singletonCounter = 0
var isProduction = false
var noop = function () {}
var options = null
var ssrIdKey = 'data-vue-ssr-id'

// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
// tags it will allow on a page
var isOldIE = typeof navigator !== 'undefined' && /msie [6-9]\b/.test(navigator.userAgent.toLowerCase())

function addStylesClient (parentId, list, _isProduction, _options) {
  isProduction = _isProduction

  options = _options || {}

  var styles = (0,_listToStyles__WEBPACK_IMPORTED_MODULE_0__.default)(parentId, list)
  addStylesToDom(styles)

  return function update (newList) {
    var mayRemove = []
    for (var i = 0; i < styles.length; i++) {
      var item = styles[i]
      var domStyle = stylesInDom[item.id]
      domStyle.refs--
      mayRemove.push(domStyle)
    }
    if (newList) {
      styles = (0,_listToStyles__WEBPACK_IMPORTED_MODULE_0__.default)(parentId, newList)
      addStylesToDom(styles)
    } else {
      styles = []
    }
    for (var i = 0; i < mayRemove.length; i++) {
      var domStyle = mayRemove[i]
      if (domStyle.refs === 0) {
        for (var j = 0; j < domStyle.parts.length; j++) {
          domStyle.parts[j]()
        }
        delete stylesInDom[domStyle.id]
      }
    }
  }
}

function addStylesToDom (styles /* Array<StyleObject> */) {
  for (var i = 0; i < styles.length; i++) {
    var item = styles[i]
    var domStyle = stylesInDom[item.id]
    if (domStyle) {
      domStyle.refs++
      for (var j = 0; j < domStyle.parts.length; j++) {
        domStyle.parts[j](item.parts[j])
      }
      for (; j < item.parts.length; j++) {
        domStyle.parts.push(addStyle(item.parts[j]))
      }
      if (domStyle.parts.length > item.parts.length) {
        domStyle.parts.length = item.parts.length
      }
    } else {
      var parts = []
      for (var j = 0; j < item.parts.length; j++) {
        parts.push(addStyle(item.parts[j]))
      }
      stylesInDom[item.id] = { id: item.id, refs: 1, parts: parts }
    }
  }
}

function createStyleElement () {
  var styleElement = document.createElement('style')
  styleElement.type = 'text/css'
  head.appendChild(styleElement)
  return styleElement
}

function addStyle (obj /* StyleObjectPart */) {
  var update, remove
  var styleElement = document.querySelector('style[' + ssrIdKey + '~="' + obj.id + '"]')

  if (styleElement) {
    if (isProduction) {
      // has SSR styles and in production mode.
      // simply do nothing.
      return noop
    } else {
      // has SSR styles but in dev mode.
      // for some reason Chrome can't handle source map in server-rendered
      // style tags - source maps in <style> only works if the style tag is
      // created and inserted dynamically. So we remove the server rendered
      // styles and inject new ones.
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  if (isOldIE) {
    // use singleton mode for IE9.
    var styleIndex = singletonCounter++
    styleElement = singletonElement || (singletonElement = createStyleElement())
    update = applyToSingletonTag.bind(null, styleElement, styleIndex, false)
    remove = applyToSingletonTag.bind(null, styleElement, styleIndex, true)
  } else {
    // use multi-style-tag mode in all other cases
    styleElement = createStyleElement()
    update = applyToTag.bind(null, styleElement)
    remove = function () {
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  update(obj)

  return function updateStyle (newObj /* StyleObjectPart */) {
    if (newObj) {
      if (newObj.css === obj.css &&
          newObj.media === obj.media &&
          newObj.sourceMap === obj.sourceMap) {
        return
      }
      update(obj = newObj)
    } else {
      remove()
    }
  }
}

var replaceText = (function () {
  var textStore = []

  return function (index, replacement) {
    textStore[index] = replacement
    return textStore.filter(Boolean).join('\n')
  }
})()

function applyToSingletonTag (styleElement, index, remove, obj) {
  var css = remove ? '' : obj.css

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = replaceText(index, css)
  } else {
    var cssNode = document.createTextNode(css)
    var childNodes = styleElement.childNodes
    if (childNodes[index]) styleElement.removeChild(childNodes[index])
    if (childNodes.length) {
      styleElement.insertBefore(cssNode, childNodes[index])
    } else {
      styleElement.appendChild(cssNode)
    }
  }
}

function applyToTag (styleElement, obj) {
  var css = obj.css
  var media = obj.media
  var sourceMap = obj.sourceMap

  if (media) {
    styleElement.setAttribute('media', media)
  }
  if (options.ssrId) {
    styleElement.setAttribute(ssrIdKey, obj.id)
  }

  if (sourceMap) {
    // https://developer.chrome.com/devtools/docs/javascript-debugging
    // this makes source maps inside style tags work properly in Chrome
    css += '\n/*# sourceURL=' + sourceMap.sources[0] + ' */'
    // http://stackoverflow.com/a/26603875
    css += '\n/*# sourceMappingURL=data:application/json;base64,' + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + ' */'
  }

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = css
  } else {
    while (styleElement.firstChild) {
      styleElement.removeChild(styleElement.firstChild)
    }
    styleElement.appendChild(document.createTextNode(css))
  }
}


/***/ }),

/***/ "./node_modules/vue-style-loader/lib/listToStyles.js":
/*!***********************************************************!*\
  !*** ./node_modules/vue-style-loader/lib/listToStyles.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ listToStyles)
/* harmony export */ });
/**
 * Translates the list format produced by css-loader into something
 * easier to manipulate.
 */
function listToStyles (parentId, list) {
  var styles = []
  var newStyles = {}
  for (var i = 0; i < list.length; i++) {
    var item = list[i]
    var id = item[0]
    var css = item[1]
    var media = item[2]
    var sourceMap = item[3]
    var part = {
      id: parentId + ':' + i,
      css: css,
      media: media,
      sourceMap: sourceMap
    }
    if (!newStyles[id]) {
      styles.push(newStyles[id] = { id: id, parts: [part] })
    } else {
      newStyles[id].parts.push(part)
    }
  }
  return styles
}


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			id: moduleId,
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					result = fn();
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/web/assets/dist/js/address": 0,
/******/ 			"web/assets/dist/css/address": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			for(moduleId in moreModules) {
/******/ 				if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 					__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 				}
/******/ 			}
/******/ 			if(runtime) runtime(__webpack_require__);
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkIds[i]] = 0;
/******/ 			}
/******/ 			__webpack_require__.O();
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunk"] = self["webpackChunk"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["web/assets/dist/css/address"], () => (__webpack_require__("./web/assets/src/js/address.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["web/assets/dist/css/address"], () => (__webpack_require__("./web/assets/src/sass/address.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
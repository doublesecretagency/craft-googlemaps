<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by the Google Maps API.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2021 Double Secret Agency
 */

namespace doublesecretagency\smartmap\fields;

// Wrapped in false to prevent real execution
if (false) {
    // Define class to be aliased
    class Address {}
}

// Aliased for new field class
class_exists(\doublesecretagency\googlemaps\fields\AddressField::class);

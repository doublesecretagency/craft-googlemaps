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

namespace doublesecretagency\googlemaps\helpers;

use doublesecretagency\googlemaps\models\Lookup;

/**
 * Class GeocodingHelper
 * @since 4.0.0
 */
class GeocodingHelper
{

    /**
     * Initialize a geocoding lookup by configuring a Lookup Model.
     *
     * @param array|string $target
     * @return Lookup|false
     */
    public static function lookup($target = [])
    {
        // If a string target was specified, convert to array
        if (is_string($target)) {
            $target = ['address' => $target];
        }

        // If target is not an array, bail
        if (!is_array($target)) {
            return false;
        }

        // If no target specified, bail
        if (!isset($target['address']) || !$target['address']) {
            return false;
        }

        // Create a fresh lookup
        return new Lookup($target);
    }

}

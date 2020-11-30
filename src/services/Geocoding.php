<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2021 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\services;

use craft\base\Component;
use doublesecretagency\googlemaps\models\Lookup;

/**
 * Class Geocoding
 * @since 4.0.0
 */
class Geocoding extends Component
{

    /**
     * Initialize a geocoding lookup by configuring a Lookup Model.
     *
     * @param array|string $parameters
     * @return Lookup|false
     */
    public function lookup($parameters = [])
    {
        // If a string target was specified, convert to array
        if (is_string($parameters)) {
            $parameters = ['address' => $parameters];
        }

        // If parameters are not an array, bail
        if (!is_array($parameters)) {
            return false;
        }

        // If no target specified, bail
        if (!array_key_exists('address', $parameters) || !$parameters['address']) {
            return false;
        }

        // Create a fresh lookup
        return new Lookup($parameters);
    }

}

<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\helpers;

use doublesecretagency\googlemaps\models\Geolocation as GeolocationModel;
use doublesecretagency\googlemaps\services\Geocoding;

/**
 * Class GoogleMaps
 * @since 4.0.0
 */
class GoogleMaps
{

    /**
     * @var string Current schema version of the plugin.
     */
    public $property = 'value';

    /**
     * @inheritDoc
     */
    public function init()
    {
        // Not needed?
    }

    // Generate Maps

    /**
     */
    public static function dynamic($locations, $options = [])
    {
        return true;
    }

    /**
     */
    public static function static($locations, $options = [])
    {
        return true;
    }

    // Change a Marker Icon

    /**
     */
    public static function setMarkerIcon($mapId, $markerId, $icon)
    {
        return true;
    }

    // Apply a KML file

    /**
     */
    public static function loadKml($mapId, $kml, $options = [])
    {
        return true;
    }

    // Perform Visitor Geolocation

    /**
     */
    public static function getGeolocation()
    {
        return new GeolocationModel;
    }

    // Geocoding (Address Lookups)

    /**
     */
    public static function lookup($target = null)
    {
        return Geocoding::lookup($target);
    }

    // Override Google API keys

    /**
     */
    public static function setServerKey($serverKey)
    {
        return true;
    }

    /**
     */
    public static function setBrowserKey($browserKey)
    {
        return true;
    }

}

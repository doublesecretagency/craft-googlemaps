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

use Craft;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\models\Geolocation as GeolocationModel;
use doublesecretagency\googlemaps\services\Geocoding;

/**
 * Class GoogleMaps
 * @since 4.0.0
 */
class GoogleMaps
{

    /**
     * @var string Description of variable.
     */
    public $property = 'value';




    /**
     */
    public static function test()
    {
        return GoogleMapsPlugin::$plugin->mapsJavascript->testing();
//        return Craft::$app->getRequest()->getUserIP();
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
    public static function getServerKey()
    {
        return GoogleMapsPlugin::$plugin->api->getServerKey();
    }

    /**
     */
    public static function getBrowserKey()
    {
        return GoogleMapsPlugin::$plugin->api->getBrowserKey();
    }

    /**
     */
    public static function setServerKey($key)
    {
        return GoogleMapsPlugin::$plugin->api->setServerKey($key);
    }

    /**
     */
    public static function setBrowserKey($key)
    {
        return GoogleMapsPlugin::$plugin->api->setBrowserKey($key);
    }

}

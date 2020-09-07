<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2020 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\helpers;

use Craft;
use craft\helpers\Json;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\models\DynamicMap;
use doublesecretagency\googlemaps\models\Visitor;
use doublesecretagency\googlemaps\models\_OLD_DynamicMap;
use doublesecretagency\googlemaps\services\Geocoding;
use doublesecretagency\googlemaps\services\Geolocation;

/**
 * Class GoogleMaps
 * @since 4.0.0
 */
class GoogleMaps
{




    /**
     */
    public static function test()
    {

        return static::map([
            'id' => 'pancake',
            'height' => 400,
        ]);

//        $visitor = GoogleMapsPlugin::$plugin->geolocation->getVisitor();
////        $results = Geocoding::lookup('western blvd')->all();
//
//        return $visitor;
    }






    // Generate Maps

    /**
     * @param string|string[]|Section|null $value The property value
     * @return static self reference
     * @uses $sectionId
     */
    public static function map($options = [])
    {
//        return GoogleMapsPlugin::$plugin->mapsJavascript->getMap($options);
        return new DynamicMap();
    }

//    /**
//     */
//    public static function dynamic($locations, $options = [])
//    {
//        return true;
//    }

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
    public static function getVisitor($config = [])
    {
        return GoogleMapsPlugin::$plugin->geolocation->getVisitor($config);
    }

    // Geocoding (Address Lookups)

    /**
     */
    public static function lookup($target = null)
    {
        return GoogleMapsPlugin::$plugin->geocoding->lookup($target);
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

    /**
     */
    public static function getApiUrl(array $params = []): string
    {
        return GoogleMapsPlugin::$plugin->api->getApiUrl($params);
    }

}

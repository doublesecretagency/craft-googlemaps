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
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\models\DynamicMap;
use doublesecretagency\googlemaps\web\assets\JsApiAsset;

/**
 * Class GoogleMaps
 * @since 4.0.0
 */
class GoogleMaps
{

    // ========================================================================= //

    // Generate Maps

    /**
     */
    public static function map($locations = [], $options = [])
    {
//        return GoogleMapsPlugin::$plugin->mapsJavascript->getMap($locations, $options);
        return new DynamicMap($locations, $options);
    }

    /**
     */
    public static function img($locations = [], $options = [])
    {
        return true;
    }

    // ========================================================================= //

    // Load necessary JS libraries

    /**
     */
    public static function loadAssets()
    {
        Craft::$app->getView()->registerAssetBundle(JsApiAsset::class);
    }

    // ========================================================================= //

    // Perform Visitor Geolocation

    /**
     */
    public static function getVisitor($config = [])
    {
        return GoogleMapsPlugin::$plugin->geolocation->getVisitor($config);
    }

    // ========================================================================= //

    // Geocoding (Address Lookups)

    /**
     */
    public static function lookup($target = null)
    {
        return GoogleMapsPlugin::$plugin->geocoding->lookup($target);
    }

    // ========================================================================= //

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

    // ========================================================================= //

    // Get Google API URL

    /**
     */
    public static function getApiUrl(array $params = []): string
    {
        return GoogleMapsPlugin::$plugin->api->getApiUrl($params);
    }

    // ========================================================================= //

}

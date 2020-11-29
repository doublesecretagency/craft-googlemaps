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

namespace doublesecretagency\googlemaps\helpers;

use Craft;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\models\DynamicMap;
use doublesecretagency\googlemaps\models\StaticMap;
use doublesecretagency\googlemaps\web\assets\JsApiAsset;
use yii\base\Exception;

/**
 * Class GoogleMaps
 * @since 4.0.0
 */
class GoogleMaps
{

    // Initialize collection of maps
    private static $_maps = [];

    // ========================================================================= //

    // Dynamic Maps

    /**
     */
    public static function map($locations = [], $options = [])
    {
        // Create a new map object
        $map = new DynamicMap($locations, $options);

        // Store map object for future reference
        static::$_maps[$map->id] = $map;

        // Return the map object
        return $map;
    }

    /**
     * Get an existing dynamic map.
     */
    public static function getMap($mapId)
    {
        // Get existing map object
        $map = (static::$_maps[$mapId] ?? false);

        // If no map object exists, throw an error
        if (!$map) {
            throw new Exception("Encountered an error using the `getMap` method. The map \"{$mapId}\" does not exist.");
        }

        // Return the map object
        return $map;
    }

    // ========================================================================= //

    // Static Maps

    /**
     */
    public static function img($locations = [], $options = [])
    {
        return new StaticMap($locations, $options);
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

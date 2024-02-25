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

use Craft;
use craft\base\Element;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\models\DynamicMap;
use doublesecretagency\googlemaps\models\Location;
use doublesecretagency\googlemaps\models\Lookup;
use doublesecretagency\googlemaps\models\StaticMap;
use doublesecretagency\googlemaps\models\Visitor;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * Class GoogleMaps
 * @since 4.0.0
 */
class GoogleMaps
{

    /**
     * @var array An internally managed collection of Dynamic Maps.
     */
    private static array $_maps = [];

    // ========================================================================= //
    // Dynamic Maps
    // https://plugins.doublesecretagency.com/google-maps/dynamic-maps/
    // ========================================================================= //

    /**
     * Get a list of the JavaScript assets necessary for displaying Dynamic Maps.
     *
     * @param array $params Optional parameters for the Google Maps API.
     * @return string[] Collection of JS files required to display maps.
     */
    public static function getAssets(array $params = []): array
    {
        // Get asset manager
        $manager = Craft::$app->getAssetManager();
        $assets = '@doublesecretagency/googlemaps/resources';

        // Whether to use minified JavaScript files
        $minifyJsFiles = (GoogleMapsPlugin::$plugin->getSettings()->minifyJsFiles ?? false);

        // Optionally use minified files
        $min = ($minifyJsFiles ? 'min.' : '');

        // Pin to v3.55 of the API
        // https://github.com/doublesecretagency/craft-googlemaps/issues/106
        $params = array_merge($params, ['v' => '3.55']);

        // Link to Google Maps JavaScript API URL
        $files = [self::getApiUrl($params)];

        // CDN for MarkerClusterer library
        $files[] = 'https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js';

        // Append both JS files required by plugin
        $files[] = $manager->getPublishedUrl($assets, true, "js/googlemaps.{$min}js");
        $files[] = $manager->getPublishedUrl($assets, true, "js/dynamicmap.{$min}js");

        // Return list of files
        return $files;
    }

    /**
     * Load the JavaScript assets necessary for displaying Dynamic Maps.
     *
     * @param array $params Optional parameters for the Google Maps API.
     * @throws InvalidConfigException
     */
    public static function loadAssets(array $params = []): void
    {
        // Get view service
        $view = Craft::$app->getView();

        // Get all required assets
        $assets = static::getAssets($params);

        // Load each JS file
        foreach ($assets as $file) {
            $view->registerJsFile($file, ['defer' => true]);
        }
    }

    // ========================================================================= //

    /**
     * Create a new Dynamic Map object.
     *
     * @param array|Collection|Element|Location $locations
     * @param array $options
     * @return DynamicMap
     */
    public static function map(array|Collection|Element|Location $locations = [], array $options = []): DynamicMap
    {
        // Create a new map object
        $map = new DynamicMap($locations, $options);

        // Store map object for future reference
        static::$_maps[$map->id] = $map;

        // Return the map object
        return $map;
    }

    /**
     * Get an existing Dynamic Map object.
     *
     * @param string $mapId
     * @return DynamicMap|null
     * @throws Exception
     */
    public static function getMap(string $mapId): ?DynamicMap
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
    // https://plugins.doublesecretagency.com/google-maps/static-maps/
    // ========================================================================= //

    /**
     * Create a new Static Map object.
     *
     * @param array|Collection|Element|Location $locations
     * @param array $options
     * @return StaticMap
     */
    public static function img(array|Collection|Element|Location $locations = [], array $options = []): StaticMap
    {
        return new StaticMap($locations, $options);
    }

    // ========================================================================= //
    // Geocoding (Address Lookups)
    // https://plugins.doublesecretagency.com/google-maps/geocoding/
    // ========================================================================= //

    /**
     * Perform a geocoding lookup.
     *
     * @param array|string|null $target
     * @return Lookup
     */
    public static function lookup(array|string|null $target = null): Lookup
    {
        return GeocodingHelper::lookup($target);
    }

    // ========================================================================= //
    // Visitor Geolocation
    // https://plugins.doublesecretagency.com/google-maps/geolocation/
    // ========================================================================= //

    /**
     * Perform a visitor geolocation.
     *
     * @param array $config
     * @return Visitor
     * @throws GuzzleException
     */
    public static function getVisitor(array $config = []): Visitor
    {
        return GeolocationHelper::getVisitor($config);
    }

    // ========================================================================= //
    // API Service
    // https://plugins.doublesecretagency.com/google-maps/helper/api/
    // ========================================================================= //

    /**
     * Get the Google API URL.
     *
     * @param array $params
     * @return string
     */
    public static function getApiUrl(array $params = []): string
    {
        return ApiHelper::getApiUrl($params);
    }

    // ========================================================================= //

    /**
     * Get the Google API browser key.
     *
     * @return string
     */
    public static function getBrowserKey(): string
    {
        return ApiHelper::getBrowserKey();
    }

    /**
     * Get the Google API server key.
     *
     * @return string
     */
    public static function getServerKey(): string
    {
        return ApiHelper::getServerKey();
    }

    /**
     * Set the Google API browser key.
     *
     * @param string $key
     * @return string
     */
    public static function setBrowserKey(string $key): string
    {
        return ApiHelper::setBrowserKey($key);
    }

    /**
     * Set the Google API server key.
     *
     * @param string $key
     * @return string
     */
    public static function setServerKey(string $key): string
    {
        return ApiHelper::setServerKey($key);
    }

}

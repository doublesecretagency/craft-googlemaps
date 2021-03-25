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
use craft\helpers\UrlHelper;
use doublesecretagency\googlemaps\GoogleMapsPlugin;

/**
 * Class ApiHelper
 * @since 4.0.0
 */
class ApiHelper
{

    /**
     * @var string|null Google API browser key.
     */
    private static $_browserKey;

    /**
     * @var string|null Google API server key.
     */
    private static $_serverKey;

    // ========================================================================= //

    /**
     * Get the Google API browser key.
     *
     * @return string Google API browser key.
     */
    public static function getBrowserKey(): string
    {
        // Only load once
        if (null === static::$_browserKey) {
            static::$_browserKey = Craft::parseEnv(GoogleMapsPlugin::$plugin->getSettings()->browserKey);
        }
        // Return key
        return trim(static::$_browserKey);
    }

    /**
     * Get the Google API server key.
     *
     * @return string Google API server key.
     */
    public static function getServerKey(): string
    {
        // Only load once
        if (null === static::$_serverKey) {
            static::$_serverKey = Craft::parseEnv(GoogleMapsPlugin::$plugin->getSettings()->serverKey);
        }
        // Return key
        return trim(static::$_serverKey);
    }

    /**
     * Set the Google API browser key.
     *
     * @param string $key
     * @return string Google API browser key.
     */
    public static function setBrowserKey(string $key): string
    {
        return static::$_browserKey = $key;
    }

    /**
     * Set the Google API server key.
     *
     * @param string $key
     * @return string Google API server key.
     */
    public static function setServerKey(string $key): string
    {
        return static::$_serverKey = $key;
    }

    // ========================================================================= //

    /**
     * Compile a URL for pinging the Google Maps API.
     *
     * @param array $params
     * @return string The fully compiled URL.
     */
    public static function getApiUrl(array $params = []): string
    {
        // Set the base API URL
        $apiUrl = 'https://maps.googleapis.com/maps/api/js';

        // Add the browser key
        $params['key'] = static::getBrowserKey();

        // Return the fully compiled URL
        return UrlHelper::urlWithParams($apiUrl, $params);
    }

}

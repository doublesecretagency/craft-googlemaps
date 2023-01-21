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

use craft\helpers\App;
use craft\helpers\UrlHelper;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\models\Settings;

/**
 * Class ApiHelper
 * @since 4.0.0
 */
class ApiHelper
{

    /**
     * @var string|null Google API browser key.
     */
    private static ?string $_browserKey = null;

    /**
     * @var string|null Google API server key.
     */
    private static ?string $_serverKey = null;

    // ========================================================================= //

    /**
     * Get the Google API browser key.
     *
     * @return string Google API browser key.
     */
    public static function getBrowserKey(): string
    {
        /** @var Settings $settings */
        $settings = GoogleMapsPlugin::$plugin->getSettings();
        // Only load once
        if (null === static::$_browserKey) {
            static::$_browserKey = App::parseEnv($settings->browserKey);
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
        /** @var Settings $settings */
        $settings = GoogleMapsPlugin::$plugin->getSettings();
        // Only load once
        if (null === static::$_serverKey) {
            static::$_serverKey = App::parseEnv($settings->serverKey);
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

        // If callback wasn't specified
        if (!($params['callback'] ?? false)) {
            // Use native JS noop as a fallback
            $params['callback'] = 'Function.prototype';
        }

        // Return the fully compiled URL
        return UrlHelper::urlWithParams($apiUrl, $params);
    }

}

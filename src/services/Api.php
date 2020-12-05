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

use Craft;
use craft\base\Component;
use doublesecretagency\googlemaps\GoogleMapsPlugin;

/**
 * Class Api
 * @since 4.0.0
 */
class Api extends Component
{

    /**
     * @var string|null Google API browser key.
     */
    private $_browserKey;

    /**
     * @var string|null Google API server key.
     */
    private $_serverKey;

    // ========================================================================= //

    /**
     * Get the Google API browser key.
     *
     * @return string Google API browser key.
     */
    public function getBrowserKey(): string
    {
        // Only load once
        if (null === $this->_browserKey) {
            $this->_browserKey = Craft::parseEnv(GoogleMapsPlugin::$plugin->getSettings()->browserKey);
        }
        // Return key
        return trim($this->_browserKey);
    }

    /**
     * Get the Google API server key.
     *
     * @return string Google API server key.
     */
    public function getServerKey(): string
    {
        // Only load once
        if (null === $this->_serverKey) {
            $this->_serverKey = Craft::parseEnv(GoogleMapsPlugin::$plugin->getSettings()->serverKey);
        }
        // Return key
        return trim($this->_serverKey);
    }

    /**
     * Set the Google API browser key.
     *
     * @param string $key
     * @return string Google API browser key.
     */
    public function setBrowserKey(string $key): string
    {
        return $this->_browserKey = $key;
    }

    /**
     * Set the Google API server key.
     *
     * @param string $key
     * @return string Google API server key.
     */
    public function setServerKey(string $key): string
    {
        return $this->_serverKey = $key;
    }

    // ========================================================================= //

    /**
     * Compile a URL for pinging the Google Maps API.
     *
     * @param array $params
     * @return string The fully compiled URL.
     */
    public function getApiUrl(array $params = []): string
    {
        // Get browser key
        $key = $this->getBrowserKey();

        // Set base URL of Google Maps API
        $googleMapsApi = 'https://maps.googleapis.com/maps/api/js';
        $googleMapsApi .= "?key={$key}";

        // Optionally append additional parameters
        if ($params) {
            foreach ($params as $param => $value) {
                $googleMapsApi .= "&{$param}={$value}";
            }
        }

        // Return complete API URL
        return $googleMapsApi;
    }

}

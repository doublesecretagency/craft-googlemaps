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

namespace doublesecretagency\googlemaps\models;

use Craft;
use craft\base\Model;
use craft\helpers\Json;
use doublesecretagency\googlemaps\events\GeocodingEvent;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\helpers\GeocodingHelper;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use GuzzleHttp\Exception\RequestException;

/**
 * Class Lookup
 * @since 4.0.0
 */
class Lookup extends Model
{

    /**
     * @var string|null Error message, set when an error occurs.
     */
    public ?string $error = null;

    /**
     * @var array|string Internal target, converted to array prior to Lookup.
     */
    private array|string $_target;

    /**
     * @var string Google Geocoding API endpoint.
     */
    private string $_endpoint = 'https://maps.googleapis.com/maps/api/geocode/json';

    // ========================================================================= //

    /**
     * Construct the Lookup object.
     *
     * @param array|string $target
     * @param array $config
     */
    public function __construct(array|string $target = [], array $config = [])
    {
        // Set target internally
        $this->_target = $target;

        // Pass config to parent
        parent::__construct($config);
    }

    // ========================================================================= //

    /**
     * Returns all results from geocoding lookup.
     *
     * @return array|null
     */
    public function all(): ?array
    {
        // If no geocoding results, return null
        if (!$results = $this->_runLookup()) {
            return null;
        }

        // Return all results
        return $results;
    }

    /**
     * Returns first result from geocoding lookup.
     *
     * @return Address|null
     */
    public function one(): ?Address
    {
        // If no geocoding results, return null
        if (!$results = $this->_runLookup()) {
            return null;
        }

        /** @var Address $address */
        $address = $results[0];

        // Return one single result
        return $address;
    }

    /**
     * Returns coordinates of first result from geocoding lookup.
     *
     * @return array|null
     */
    public function coords(): ?array
    {
        // If no geocoding results, return null
        if (!$results = $this->_runLookup()) {
            return null;
        }

        /** @var Address $address */
        $address = $results[0];

        // Return one single result
        return $address->getCoords();
    }

    // ========================================================================= //

    /**
     * Perform the geocoding lookup.
     *
     * @return array|null An array of Address models, or
     *                    null if an error was received.
     */
    private function _runLookup(): ?array
    {
        // Get cache service
        $cache = Craft::$app->getCache();

        // Set cache duration
        $cacheDuration = (30 * 24 * 60 * 60); // 30 days

        // Cache results
        $results = $cache->getOrSet(
            $this->_target,
            function() {
                // Get geocoding response
                $response = $this->_pingEndpoint($this->_target);
                // Convert API response into address data
                return $this->_parseResponse($response);
            },
            $cacheDuration
        );

        // If error message was returned
        if (is_string($results)) {
            // Bust cache
            $cache->delete($this->_target);
            // Get error message from results
            $this->error = $results;
            $results = null;
        }

        // Trigger geocoding event
        GoogleMapsPlugin::$plugin->trigger(
            GoogleMapsPlugin::EVENT_AFTER_GEOCODING,
            new GeocodingEvent([
                'target' => $this->_target,
                'results' => $results,
            ])
        );

        // Return lookup results
        return $results;
    }

    /**
     * Ping the Google Geocoding API endpoint.
     *
     * @param array $target
     * @return array Response from geocoding endpoint.
     */
    private function _pingEndpoint(array $target): array
    {
        // Append server key
        $target['key'] = GoogleMaps::getServerKey();

        // Ensure components are properly formatted
        if (isset($target['components'])) {
            $target['components'] = $this->_formatComponents($target['components']);
        }

        // Compile endpoint URL
        $queryString = http_build_query($target);
        $url = "{$this->_endpoint}?{$queryString}";

        // Attempt to ping URL
        try {
            $client = Craft::createGuzzleClient();
            $response = $client->request('GET', $url);
        } catch (RequestException $e) {
            if (($response = $e->getResponse()) === null || $response->getStatusCode() === 500) {
                throw $e;
            }
        }

        // Return raw geocoding results
        return Json::decode((string) $response->getBody());
    }

    /**
     * Properly format a string of components.
     *
     * @param array|string $components
     * @return string Formatted string of components.
     */
    private function _formatComponents(array|string $components): string
    {
        // Already formatted properly, return as-is
        if (is_string($components)) {
            return $components;
        }

        // Can't be formatted properly, return as empty string
        if (!is_array($components)) {
            return '';
        }

        // Initialize array of component strings
        $c = [];

        // Compile each component
        foreach ($components as $component => $value) {
            $c[] = "{$component}:{$value}";
        }

        // Return compiled string of components
        return implode('|', $c);
    }

    /**
     * Interpret the response returned by the Google Geocoding API.
     *
     * @param array $response
     * @return array|string An array of Address models, or
     *                      a string w/ an error message.
     */
    private function _parseResponse(array $response): array|string
    {
        // No error by default
        $error = null;

        // Switch according to response status
        switch ($response['status'] ?? false) {
            case 'ZERO_RESULTS':
                $error = 'The geocode was successful but returned no results.';
                break;
            case 'OVER_QUERY_LIMIT':
                $error = 'You are over your quota.';
                break;
            case 'INVALID_REQUEST':
                $error = 'Invalid request. Please provide more address information.';
                break;
            case 'REQUEST_DENIED':
                if ($response['error_message'] ?? false) {
                    $error = "Google API error: {$response['error_message']}";
                } else {
                    $error = 'Your request was denied for some reason.';
                }
                break;
            default:
                if ('OK' !== ($response['status'] ?? false)) {
                    $error = 'An unknown API error occurred.';
                }
                break;
        }

        // If an error was generated, bail
        if ($error) {
            return Craft::t('google-maps', $error);
        }

        // Initialize results
        $addressResults = [];
        foreach ($response['results'] as $point) {
            $addressData = GeocodingHelper::restructureComponents($point);
            $addressResults[] = new Address($addressData);
        }

        // Return API results as Address Models
        return $addressResults;
    }

}

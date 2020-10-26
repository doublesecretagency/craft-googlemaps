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

namespace doublesecretagency\googlemaps\models;

use Craft;
use craft\base\Model;
use craft\helpers\Json;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use GuzzleHttp\Exception\RequestException;

/**
 * Class Lookup
 * @since 4.0.0
 */
class Lookup extends Model
{

    private $_parameters;
    private $_error;

    /**
     * @var string Google Geocoding API endpoint.
     */
    private $_endpoint = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * @var array Countries whose street number precedes the street name.
     */
    private $_countriesWithNumberFirst = [
        'Australia',
        'Canada',
        'France',
        'Hong Kong',
        'India',
        'Ireland',
        'Malaysia',
        'New Zealand',
        'Pakistan',
        'Singapore',
        'Sri Lanka',
        'Taiwan',
        'Thailand',
        'United Kingdom',
        'United States',
    ];

    /**
     * @var array Countries with a comma after the street name.
     */
    private $_companiesWithCommaAfterStreet = [
        'Italy',
    ];

    public function __construct($parameters = [], array $config = [])
    {
        // Set parameters internally
        $this->_parameters = $parameters;

        parent::__construct($config);
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns all results from geocoding lookup.
     *
     * @return array|false
     */
    public function all()
    {
        // If no geocoding results, return false
        if (!$results = $this->_runLookup()) {
            return false;
        }

        // Return all of the results
        return $results;
    }

    /**
     * Returns first result from geocoding lookup.
     *
     * @return Address|false
     */
    public function one()
    {
        // If no geocoding results, return false
        if (!$results = $this->_runLookup()) {
            return false;
        }

        /** @var Address $address */
        $address = $results[0];

        // Return one single result
        return $address;
    }

    /**
     * Returns coordinates of first result from geocoding lookup.
     *
     * @return array|false
     */
    public function coords()
    {
        // If no geocoding results, return false
        if (!$results = $this->_runLookup()) {
            return false;
        }

        /** @var Address $address */
        $address = $results[0];

        // Return one single result
        return $address->getCoords();
    }

    // Private Methods
    // =========================================================================

    /**
     * Perform lookup.
     *
     * @return bool|mixed
     */
    private function _runLookup()
    {
        // Get cache service
        $cache = Craft::$app->getCache();

        // Set cache duration
        $cacheDuration = 4; // 4 seconds (TEMP) // TODO: Switch to correct cache duration
//        $cacheDuration = (30 * 24 * 60 * 60); // 30 days

        // Cache results
        $results = $cache->getOrSet(
            $this->_parameters,
            function() {
                // Get geocoding response
                $response = $this->_pingEndpoint($this->_parameters);
                // Convert API response into address data
                return $this->_parseResponse($response);
            },
            $cacheDuration
        );

        // If error message was returned
        if (is_string($results)) {
            // Bust cache
            $cache->delete($this->_parameters);
            // Get error message from results
            $this->_error = $results;
            $results = false;
        }

        // Return lookup results
        return $results;
    }

    /**
     * Ping the Google Geocoding API endpoint.
     *
     * @param $parameters
     * @return mixed
     */
    private function _pingEndpoint($parameters)
    {
        // Append server key
        $parameters['key'] = GoogleMapsPlugin::$plugin->api->getServerKey();

        // Ensure components are properly formatted
        if (isset($parameters['components'])) {
            $parameters['components'] = $this->_formatComponents($parameters['components']);
        }

        // Compile endpoint URL
        $queryString = http_build_query($parameters);
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
     * @param $components
     * @return string Formatted string of components.
     */
    private function _formatComponents($components): string
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
        return (string) implode('|', $c);
    }

    /**
     * Interpret the response returned by the Google Geocoding API.
     *
     * @param $response
     * @return array|string
     */
    private function _parseResponse($response)
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
                if ('OK' != ($response['status'] ?? false)) {
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
            $addressData = $this->_formatAddressData($point);
            $addressResults[] = new Address($addressData);
        }

        // Return API results as Address Models
        return $addressResults;
    }

    /**
     * Clean and format raw address data.
     *
     * @param $unformatted
     * @return array
     */
    private function _formatAddressData($unformatted)
    {
        // Initialize formatted address data
        $formatted = [];

        // Loop through address components
        foreach ($unformatted['address_components'] as $component) {
            // If types aren't specified, skip
            if (!array_key_exists('types', $component) || !$component['types']) {
                continue;
            }
            // Generate formatted array of address data
            $c = $component['types'][0];
            switch ($c) {
                case 'locality':
                case 'country':
                    $formatted[$c] = $component['long_name'];
                    break;
                default:
                    $formatted[$c] = $component['short_name'];
                    break;
            }
        }

        // Get components
        $streetNumber = ($formatted['street_number'] ?? null);
        $streetName   = ($formatted['route'] ?? null);
        $city         = ($formatted['locality'] ?? null);
        $state        = ($formatted['administrative_area_level_1'] ?? null);
        $zip          = ($formatted['postal_code'] ?? null);
        $country      = ($formatted['country'] ?? null);

        // Country-specific adjustments
        switch ($country) {
            case 'United Kingdom':
                $city  = ($formatted['postal_town'] ?? null);
                $state = ($formatted['administrative_area_level_2'] ?? null);
                break;
        }

        // Get coordinates
        $lat = ($unformatted['geometry']['location']['lat'] ?? null);
        $lng = ($unformatted['geometry']['location']['lng'] ?? null);

        // Default street format
        $street1 = "{$streetName} {$streetNumber}";

        // If country uses a different street format, apply that format instead
        if (in_array($country, $this->_countriesWithNumberFirst, true)) {
            $street1 = "{$streetNumber} {$streetName}";
        } else if (in_array($country, $this->_companiesWithCommaAfterStreet, true)) {
            $street1 = "{$streetName}, {$streetNumber}";
        }

        // Trim whitespace from street
        $street1 = (trim($street1) ? trim($street1) : null);

        // Return formatted address data
        return [
            'street1' => $street1,
            'street2' => null,
            'city'    => $city,
            'state'   => $state,
            'zip'     => $zip,
            'country' => $country,
            'lat'     => $lat,
            'lng'     => $lng,
            'raw'     => $unformatted,
        ];
    }

}

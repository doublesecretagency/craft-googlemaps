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

namespace doublesecretagency\googlemaps\services;

use Craft;
use craft\helpers\Json;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use doublesecretagency\googlemaps\models\Address;
use doublesecretagency\googlemaps\models\Lookup;
use GuzzleHttp\Exception\RequestException;

/**
 * Class Geocoding
 * @since 4.0.0
 */
class Geocoding extends Api
{

    /**
     * @var string Google Geocoding API endpoint.
     */
    private static $_endpoint = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * @var array Countries whose street number precedes the street name.
     */
    private static $_countriesWithNumberFirst = [
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
    private static $_companiesWithCommaAfterStreet = [
        'Italy',
    ];

    public static function lookup($parameters = [])
    {
        // If a string target was specified, convert to array
        if (is_string($parameters)) {
            $parameters = ['address' => $parameters];
        }

        // If parameters are not an array, bail
        if (!is_array($parameters)) {
            return false;
        }

        // If no target specified, bail
        if (!array_key_exists('address', $parameters) || !$parameters['address']) {
            return false;
        }

//        // Cache results // TODO: Uncomment
//        $cacheDuration = (30 * 24 * 60 * 60); // 30 days
//        return Craft::$app->getCache()->getOrSet(
//            $parameters,
//            function() use ($parameters) {


                // Create a fresh lookup
                return new Lookup($parameters);



//            },
//            $cacheDuration
//        );
    }

    /**
     */
    public static function pingEndpoint($parameters)
    {
        // Append server key
        $parameters['key'] = GoogleMaps::getServerKey();

        // Compile endpoint URL
        $endpoint = static::$_endpoint;
        $queryString = http_build_query($parameters);
        $url = "{$endpoint}?{$queryString}";

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
     */
    public static function parseResponse($response)
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
            $addressData = static::formatAddressData($point);
            $addressResults[] = new Address($addressData);
        }

        // Return API results as Address Models
        return $addressResults;
    }

    /**
     */
    public static function formatAddressData($unformatted)
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

        // Get coordinates
        $lat = ($unformatted['geometry']['location']['lat'] ?? null);
        $lng = ($unformatted['geometry']['location']['lng'] ?? null);

        // Format street address according to country
        if (in_array($country, static::$_countriesWithNumberFirst)) {
            // If country puts the street number first
            $street1 = "{$streetNumber} {$streetName}";
        } else if (in_array($country, static::$_companiesWithCommaAfterStreet)) {
            // If country puts a comma after the street name
            $street1 = "{$streetName}, {$streetNumber}";
        } else {
            // Default
            $street1 = "{$streetName} {$streetNumber}";
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
//            'data'    => $unformatted, // TODO: Uncomment
        ];
    }

}

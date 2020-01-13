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

namespace doublesecretagency\googlemaps\models;

use Craft;
use craft\base\Model;
use craft\helpers\Json;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use GuzzleHttp\Exception\RequestException;

/**
 * Class Ipstack
 * @since 4.0.0
 */
class Ipstack extends Model
{

    private static $_service = 'ipstack';

    private static $_error;

    /**
     * @var string ipstack API endpoint.
     */
    private static $_endpoint = 'http://api.ipstack.com/';

    /**
     * Get a Geolocation Model representing the user's location.
     */
    public static function geolocate($ip, array $parameters = []): Geolocation
    {
        // Get cache service
        $cache = Craft::$app->getCache();

        // Set unique cache key
        $cacheKey = array_merge($parameters, [
            'ip' => $ip,
            'service' => static::$_service,
        ]);

        // Set cache duration
        $cacheDuration = 4; // 4 seconds (TEMP) // TODO: Switch to correct cache duration
//        $cacheDuration = (30 * 24 * 60 * 60); // 30 days

        // Cache results
        $results = $cache->getOrSet(
            $cacheKey,
            static function() use ($ip, $parameters) {
                // Get geolocation response
                $response = static::_pingEndpoint($ip, $parameters);
                // Convert API response into geolocation data
                return static::_parseResponse($response);
            },
            $cacheDuration
        );

        // If error message was returned
        if (is_string($results)) {
            // Bust cache
            $cache->delete($cacheKey);
            // Get error message from results
            static::$_error = $results;
            $results = new Geolocation([
                'ip' => $ip,
                'service' => static::$_service,
            ]);
        }

        // Return lookup results
        return $results;
    }

    /**
     * Ping the ipstack API endpoint.
     */
    private static function _pingEndpoint($ip, $parameters)
    {
        // Append ipstack access key
        $parameters['access_key'] = GoogleMapsPlugin::$plugin->getSettings()->ipstackApiAccessKey;

        // If the IP is missing or invalid, use "check" to autodetect
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = 'check';
        }

        // Compile endpoint URL
        $endpoint = static::$_endpoint;
        $queryString = http_build_query($parameters);
        $url = "{$endpoint}{$ip}?{$queryString}";

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
     * Interpret the response returned by the ipstack API.
     *
     * @param $response
     * @return Geolocation|string
     */
    private static function _parseResponse($response)
    {
        // Determine whether API call was successful
        if (array_key_exists('ip', $response) && array_key_exists('type', $response)) {
            $success = true;
        } else if (array_key_exists('success', $response) && array_key_exists('error', $response)) {
            $success = false;
        } else {
            return Craft::t('google-maps', 'Unable to parse ipstack API response.');
        }

        // If unsuccessful, return why
        if (!$success) {
            return Craft::t('google-maps', $response['error']['info']);
        }

        // Return results as a Geolocation Model
        return new Geolocation([
            'ip' => $response['ip'],
            'service' => static::$_service,
            'country' => $response['country_name'],
            'lat' => $response['latitude'],
            'lng' => $response['longitude'],
            'data' => $response,
        ]);
    }

}

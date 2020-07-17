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
 * Class Maxmind
 * @since 4.0.0
 */
class Maxmind extends Model
{

    private static $_service = 'maxmind';

    private static $_error;

    /**
     * @var string MaxMind API endpoint.
     */
    private static $_endpoint = 'https://geoip.maxmind.com/geoip/v2.0/';

    /**
     * Get a Visitor Model containing the visitor's location.
     */
    public static function geolocate($ip, array $parameters = []): Visitor
    {
        // Get cache service
        $cache = Craft::$app->getCache();

        // Set unique cache key
        $cacheKey = array_merge($parameters, [
            'service' => static::$_service,
            'ip' => $ip,
        ]);

        // Set cache duration
        $cacheDuration = 4; // 4 seconds (TEMP) // TODO: Switch to correct cache duration
//        $cacheDuration = (30 * 24 * 60 * 60); // 30 days

        // Cache results
        $results = $cache->getOrSet(
            $cacheKey,
            static function() use ($ip) {
                // Get geolocation response
                $response = static::_pingEndpoint($ip);
                // Convert API response into geolocation data
                return static::_parseResponse($response);
            },
            $cacheDuration
        );

        // If an error message has been set
        if (static::$_error) {
            // Bust cache
            $cache->delete($cacheKey);
            // Create basic Visitor Model
            $results = new Visitor([
                'service' => static::$_service,
                'ip' => $ip,
            ]);
        }

        // Return lookup results
        return $results;
    }

    /**
     * Ping the MaxMind API endpoint.
     */
    private static function _pingEndpoint($ip)
    {
        // Get plugin settings
        $settings = GoogleMapsPlugin::$plugin->getSettings();

        // Get MaxMind access credentials
        $userId = $settings->maxmindUserId;
        $licenseKey = $settings->maxmindLicenseKey;
        $service = $settings->maxmindService;

        // If service is disabled
        if (!$service) {
            static::$_error = 'Please select a MaxMind Web Service.';
            return false;
        }

        // If the IP is missing or invalid, use "me" to autodetect
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = 'me';
        }

        // Compile endpoint URL
        $endpoint = static::$_endpoint;
        $url = "{$endpoint}{$service}/{$ip}";

        // Set authorization token
        $authorization = 'Basic '.base64_encode("{$userId}:{$licenseKey}");

        // Attempt to ping URL
        try {
            $client = Craft::createGuzzleClient(['timeout' => 4, 'connect_timeout' => 4]);
            $options = ['headers' => ['Authorization' => $authorization]];
            $response = $client->request('GET', $url, $options);
        } catch (RequestException $e) {
            if (($response = $e->getResponse()) === null || $response->getStatusCode() === 500) {
                throw $e;
            }
        }

        // Return raw geolocation results
        return Json::decode((string) $response->getBody());
    }

    /**
     * Interpret the response returned by the MaxMind API.
     *
     * @param $response
     * @return Visitor|false
     */
    private static function _parseResponse($response)
    {
        // Determine whether API call was successful
        if (array_key_exists('location', $response) && array_key_exists('maxmind', $response)) {
            $success = true;
        } else if (array_key_exists('code', $response) && array_key_exists('error', $response)) {
            $success = false;
        } else {
            static::$_error = Craft::t('google-maps', 'Unable to parse MaxMind API response.');
            return false;
        }

        // If unsuccessful, return why
        if (!$success) {
            // https://dev.maxmind.com/minfraud/#Errors
            static::$_error = Craft::t('google-maps', $response['error']);
            return false;
        }

        // Return results as a Visitor Model
        return new Visitor([
            'service' => static::$_service,
            'ip' => $response['traits']['ip_address'],
            'city' => $response['city']['names']['en'],
            'state' => $response['subdivisions'][0]['names']['en'],
            'country' => $response['country']['names']['en'],
            'lat' => $response['location']['latitude'],
            'lng' => $response['location']['longitude'],
            'data' => $response,
        ]);
    }

}

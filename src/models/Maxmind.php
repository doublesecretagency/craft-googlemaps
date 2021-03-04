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
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use GuzzleHttp\Exception\RequestException;

/**
 * Class Maxmind
 * @since 4.0.0
 */
class Maxmind extends Model
{

    /**
     * @const Geocoding service definition.
     */
    const SERVICE = 'maxmind';

    /**
     * @var string MaxMind API endpoint.
     */
    private static $_endpoint = 'https://geoip.maxmind.com/geoip/v2.0/';

    /**
     * @var string Internal error message.
     */
    private static $_error;

    /**
     * Get a Visitor Model containing the visitor's location.
     */
    public static function geolocate($ip, array $parameters = []): Visitor
    {
        // Get cache service
        $cache = Craft::$app->getCache();

        // Set unique cache key
        $cacheKey = array_merge($parameters, [
            'service' => static::SERVICE,
            'ip' => $ip,
        ]);

        // Get existing lookup results from cache
        $results = $cache->get($cacheKey);

        // If cached results exist, return them
        if ($results !== false) {
            return $results;
        }

        // Get geolocation response
        $response = static::_pingEndpoint($ip);

        // Convert API response into geolocation data
        $results = static::_parseResponse($response);

        // If an error occurred
        if (static::$_error) {
            // Return a basic Visitor Model
            return new Visitor([
                'service' => static::SERVICE,
                'ip' => $ip,
            ]);
        }

        // If no IP was specified, return without caching
        if (!$ip) {
            return $results;
        }

        // Set cache duration
        $cacheDuration = (30 * 24 * 60 * 60); // 30 days

        // Cache results
        $cache->set($cacheKey, $results, $cacheDuration);

        // Return visitor geolocation results
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
        $userId = Craft::parseEnv($settings->maxmindUserId);
        $licenseKey = Craft::parseEnv($settings->maxmindLicenseKey);
        $service = $settings->maxmindService;

        // If service is disabled
        if (!$service) {
            static::$_error = 'Please select a MaxMind Web Service.';
            return false;
        }

        // If no IP, let MaxMind autodetect
        $ip = ($ip ?? 'me');

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
        if (isset($response['location']) && isset($response['maxmind'])) {
            $success = true;
        } else if (isset($response['code']) && isset($response['error'])) {
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
            'service' => static::SERVICE,
            'ip'      => ($response['traits']['ip_address'] ?? null),
            'city'    => ($response['city']['names']['en'] ?? null),
            'state'   => ($response['subdivisions'][0]['names']['en'] ?? null),
            'country' => ($response['country']['names']['en'] ?? null),
            'lat'     => ($response['location']['latitude'] ?? null),
            'lng'     => ($response['location']['longitude'] ?? null),
            'raw'     => $response,
        ]);
    }

}

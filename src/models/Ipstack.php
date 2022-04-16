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
use craft\helpers\App;
use craft\helpers\Json;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

/**
 * Class Ipstack
 * @since 4.0.0
 */
class Ipstack extends Model
{

    /**
     * @const Geocoding service definition.
     */
    public const SERVICE = 'ipstack';

    /**
     * @var string ipstack API endpoint.
     */
    private static string $_endpoint = 'http://api.ipstack.com/';

    /**
     * @var string|null Internal error message.
     */
    private static ?string $_error = null;

    /**
     * Get a Visitor Model containing the visitor's location.
     *
     * @param string|null $ip
     * @param array $parameters
     * @return Visitor
     * @throws GuzzleException
     */
    public static function geolocate(?string $ip, array $parameters = []): Visitor
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
        $response = static::_pingEndpoint($ip, $parameters);

        // Convert API response into geolocation data
        $results = static::_parseResponse($response);

        // If an error occurred
        if (static::$_error) {
            // Return a basic Visitor Model
            return new Visitor([
                'service' => static::SERVICE,
                'ip' => $ip,
                'error' => static::$_error,
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
     * Ping the ipstack API endpoint.
     *
     * @param string|null $ip
     * @param array $parameters
     * @return array|null
     * @throws GuzzleException
     */
    private static function _pingEndpoint(?string $ip, array $parameters): ?array
    {
        /** @var Settings $settings */
        $settings = GoogleMapsPlugin::$plugin->getSettings();

        // Get ipstack access credentials
        $parameters['access_key'] = App::parseEnv($settings->ipstackApiAccessKey);

        // If no IP, let ipstack autodetect
        $ip = ($ip ?? 'check');

        // Compile endpoint URL
        $endpoint = static::$_endpoint;
        $queryString = http_build_query($parameters);
        $url = "{$endpoint}{$ip}?{$queryString}";

        // Attempt to ping URL
        try {
            $client = Craft::createGuzzleClient(['timeout' => 4, 'connect_timeout' => 4]);
            $response = $client->request('GET', $url);
        } catch (RequestException $e) {
            if (($response = $e->getResponse()) === null || $response->getStatusCode() === 500) {
                throw $e;
            }
        }

        // Return raw geolocation results
        return Json::decode((string) $response->getBody());
    }

    /**
     * Interpret the response returned by the ipstack API.
     *
     * @param array|null $response
     * @return Visitor|null
     */
    private static function _parseResponse(?array $response): ?Visitor
    {
        // Determine whether API call was successful
        if (array_key_exists('ip', $response) && array_key_exists('type', $response)) {
            $success = true;
        } else if (array_key_exists('success', $response) && array_key_exists('error', $response)) {
            $success = false;
        } else {
            static::$_error = Craft::t('google-maps', 'Unable to parse ipstack API response.');
            return null;
        }

        // If unsuccessful, return why
        if (!$success) {
            // https://ipstack.com/documentation#errors
            static::$_error = Craft::t('google-maps', $response['error']['info']);
            return null;
        }

        // Return results as a Visitor Model
        return new Visitor([
            'service' => static::SERVICE,
            'ip'      => ($response['ip'] ?? null),
            'city'    => ($response['city'] ?? null),
            'state'   => ($response['region_name'] ?? null),
            'country' => ($response['country_name'] ?? null),
            'lat'     => ($response['latitude'] ?? null),
            'lng'     => ($response['longitude'] ?? null),
            'raw'     => $response,
        ]);
    }

}

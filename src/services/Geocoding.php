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
use doublesecretagency\googlemaps\models\Lookup as LookupModel;
use GuzzleHttp\Exception\RequestException;

/**
 * Class Geocoding
 * @since 4.0.0
 */
class Geocoding extends Api
{

    private $_endpoint = 'https://maps.googleapis.com/maps/api/geocode/json';

    public static function lookup($target = null)
    {
        // If no target specified, bail
        if (!$target) {
            return false;
        }

        // Create a fresh lookup
        return new LookupModel($target);
    }

    /**
     * Ping the Google Geocoding API.
     *
     * @param $parameters
     * @return mixed
     */
    public function getApiResults($parameters)
    {
        // Append server key
        $parameters['key'] = $this->getServerKey();

        // Compile endpoint URL
        $queryString = http_build_query($parameters);
        $url = "{$this->_endpoint}?{$queryString}";

        // Attempt to ping URL
        try {
            $client = Craft::createGuzzleClient();
            $response = $client->request('GET', $url, $parameters);
        } catch (RequestException $e) {
            if (($response = $e->getResponse()) === null || $response->getStatusCode() === 500) {
                throw $e;
            }
        }

        // Return raw geocoding results
        return Json::decode((string) $response->getBody());
    }

}

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
use doublesecretagency\googlemaps\services\Geocoding;

/**
 * Class Lookup
 * @since 4.0.0
 */
class Lookup extends Model
{

    private $_parameters;
    private $_results;
    private $_error;

    public function __construct($parameters = [], array $config = [])
    {
        $this->_parameters = $parameters;
        parent::__construct($config);
    }

    /**
     * Perform lookup.
     */
    private function _runLookup()
    {
        // If no results stored, perform lookup
        if (!$this->_results) {

//            // Cache results // TODO: Uncomment
//            $cacheDuration = (30 * 24 * 60 * 60); // 30 days
//            $this->_results = Craft::$app->getCache()->getOrSet(
//                $this->_parameters,
//                function() {

                    // Get geocoding response
                    $response = Geocoding::pingEndpoint($this->_parameters);

                    // Convert API response into address data
//                    return Geocoding::parseResponse($response);
                    $this->_results = Geocoding::parseResponse($response);

//                },
//                $cacheDuration
//            );

            // If string was returned, set it as an error message
            if (is_string($this->_results)) {
                $this->_error = $this->_results;
                $this->_results = false;
            }

        }

        // Return lookup results
        return $this->_results;
    }

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

}

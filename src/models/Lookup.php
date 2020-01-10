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

use craft\base\Model;
use doublesecretagency\googlemaps\GoogleMapsPlugin;

/**
 * Class Lookup
 * @since 4.0.0
 */
class Lookup extends Model
{

    private $_results;

    public function __construct($parameters = [], array $config = [])
    {
        // If a string target was specified, convert to array
        if (is_string($parameters)) {
            $parameters = ['address' => $parameters];
        }

        // If parameters are not an array, bail
        if (!is_array($parameters)) {
            return;
        }

        // Get raw geocoding results
        $rawResults = GoogleMapsPlugin::$plugin->geocoding->getApiResults($parameters);


        // CONVERT RAW RESULTS INTO ADDRESS DATA


        \Craft::dd($rawResults);



//        if (is_array($attributes)) {
//            foreach ($attributes as $key => $value) {
//                if (property_exists($this, $key)) {
//                    $this[$key] = $value;
//                }
//            }
//        }
//
//        if (null !== $this->lat && null !== $this->lng) {
//            $this->coords = [$this->lat, $this->lng];
//        }

        $this->_results = [
            new Address,
            new Address,
            new Address,
            new Address,
            new Address,
            new Address,
        ];

        parent::__construct($config);
    }

    /**
     */
    private function _pingApi()
    {
    }

    /**
     * Returns all results from geocoding lookup.
     *
     * @return array|false
     */
    public function all()
    {
        // If no results, return false
        if (empty($this->_results)) {
            return false;
        }

        // Return all of the results
        return $this->_results;
    }

    /**
     * Returns first result from geocoding lookup.
     *
     * @return Address|false
     */
    public function one()
    {
        // If no results, return false
        if (empty($this->_results)) {
            return false;
        }

        /** @var Address $address */
        $address = $this->_results[0];

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
        // If no results, return false
        if (empty($this->_results)) {
            return false;
        }

        /** @var Address $address */
        $address = $this->_results[0];

        // Return one single result
        return $address->getCoords();
    }

}

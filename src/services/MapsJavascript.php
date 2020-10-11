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

namespace doublesecretagency\googlemaps\services;

//use doublesecretagency\googlemaps\models\_OLD_DynamicMap;

/**
 * Class MapsJavascript
 * @since 4.0.0
 */
class MapsJavascript extends Api
{

//    /**
//     * @var int Counter for generating map IDs.
//     */
//    private $_mapCounter = 0;
//
//    /**
//     * @var array Internal collection of maps.
//     */
//    private $_maps = [];
//
//    /**
//     * Get a Dynamic Map model (new or existing).
//     *
//     * @param array|string $options
//     * @return _OLD_DynamicMap|false
//     */
//    public function getMap($options = [])
//    {
//        // If the options are actually a string
//        if (is_string($options)) {
//            // Return existing map
//            return ($this->_maps[$options] ?? false);
//        }
//
//        // Generate a new dynamic map
//        $map = new _OLD_DynamicMap($options);
//
//        // Alias for map ID
//        $id =& $map->dna['id'];
//
//        // If no map ID, set one
//        if (!$id) {
//            $id = 'gm-map-'.++$this->_mapCounter;
//        }
//
//        // Add new map to internal collection
//        $this->_maps[$id] = $map;
//
//        // Return new map
//        return $map;
//    }

}

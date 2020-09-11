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

use craft\base\Element;
use craft\base\Model;
use craft\helpers\Html;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use craft\helpers\Template;
use Twig\Markup;
use yii\base\Exception;

/**
 * Class DynamicMap
 * @since 4.0.0
 */
class DynamicMap extends Model
{

    /**
     * @var array Collection of internal data representing a map to be rendered.
     */
    private $_dna = [];

    /*
     * TODO: Can these 3 properties be combined?
     *
     * $_default = [
     *     'markerOptions' => ...,
     *     'infoWindowOptions' => ...,
     *     'infoWindowTemplate' => ...,
     * ]
     */

    /**
     * @var array|null A set of marker options to be used as the fallback.
     */
    private $_defaultMarkerOptions;

    /**
     * @var array|null A set of info window options to be used as the fallback.
     */
    private $_defaultInfoWindowOptions;

    /**
     * @var string|null A Twig template to be used as the fallback for the info window.
     */
    private $_defaultInfoWindowTemplate;

    // ========================================================================= //

    /**
     * Can't output directly as a string (unfortunately)
     * because `__toString` isn't compatible with
     * the `raw` filter (necessary to show an HTML tag).
     *
     * @return string
     */
    public function __toString()
    {
        return 'To display a map, append `.html()` to the map object.';
    }

    /**
     * Initialize a Dynamic Map object.
     *
     * @param array|Element|Address $locations
     * @param array $options
     * @param array $config
     */
    public function __construct($locations = [], array $options = [], array $config = [])
    {
        // Initialize map
        $this->_addMap($locations, $options);

        // Call parent constructor
        parent::__construct($config);
    }

    // ========================================================================= //

    /**
     * Add one or more markers to the map.
     *
     * @param array|Element|Address $locations
     * @param array $options
     */
    public function markers($locations, array $options = [])
    {
        $this->_addMarkers($locations, $options);
    }

    // ========================================================================= //

    public function html(): Markup
    {
        // If no DNA, throw an error
        if (!$this->_dna) {
            throw new Exception('Model misconfigured. The map DNA is empty.');
        }

        // Alias map from DNA
        $map =& $this->_dna[0];

        // If the first item is not a map, throw an error
        if ('map' != $map['type']) {
            throw new Exception('Map model misconfigured. The chain must begin with a `.map()` segment.');
        }

        // Compile map container
        $html = Html::modifyTagAttributes('<div>Loading map...</div>', [
            'id' => $map['options']['id'],
            'class' => 'gm-map',
            'data-dna' => Json::encode($this->_dna),
        ]);

        // Return Markup
        return Template::raw($html);
    }

    // ========================================================================= //

    /**
     * Return the immutable DNA array.
     *
     * @return array
     */
    public function getDna(): array
    {
        return $this->_dna;
    }

    // ========================================================================= //

//    private function _setMapDna($options)
//    {
//        $mapDna = [];
//
////        // TEMP
////        $options = [
////            'id' => 'gm-map-1',
////            'width' => null,
////            'height' => null,
////            'zoom' => null, // (uses fitBounds by default)
////            'center' => null, // (uses fitBounds by default)
////            'styles' => null,
////            'mapOptions' => null,
////            'markerOptions' => null,
////            'infoWindowOptions' => null,
////            'infoWindowTemplate' => null,
////            'fields' => null,
////        ];
////        // ENDTEMP
//
//
//        // Set the map ID
//        if (isset($options['id'])) {
//            $mapDna['id'] = $options['id'];
//        } else {
//            $mapCounter = 1; // TEMP
//            $mapDna['id'] = "gm-map-{$mapCounter}";
//        }
//
//        // If the width is specified
//        if (isset($options['width'])) {
//            $mapDna['width'] = $options['width'];
//        }
//
//        // If the height is specified
//        if (isset($options['height'])) {
//            $mapDna['height'] = $options['height'];
//        }
//
//        // If the zoom is specified
//        if (isset($options['zoom'])) {
//            $mapDna['zoom'] = $options['zoom'];
//        }
//
//        // If the center is specified
//        if (isset($options['center'])) {
//            // Apply center
//        }
//
//        // If the styles are specified
//        if (isset($options['styles'])) {
//            // Apply styles
//        }
//
//        // If map options are specified
//        if (isset($options['mapOptions'])) {
//            // Apply map options
//        }
//
//        // If marker options are specified, set as internal fallback
//        if (isset($options['markerOptions'])) {
//            $this->_defaultMarkerOptions = $options['markerOptions'];
//        }
//
//        // If info window options are specified, set as internal fallback
//        if (isset($options['infoWindowOptions'])) {
//            $this->_defaultInfoWindowOptions = $options['infoWindowOptions'];
//        }
//
//        // If the info window template is specified
//        if (isset($options['infoWindowTemplate'])) {
//            $this->_defaultInfoWindowTemplate = $options['infoWindowTemplate'];
//        }
//
//        // If the fields are specified
//        if (isset($options['fields'])) {
//            // Apply fields
//        }
//
////        \Craft::dd($mapDna);
//
//        $this->_dna['map'] = $mapDna;
//
//    }

//    private function _setMarkersDna($locations, $options = [])
//    {
//        $markersDna = [];
//
//        // If location was specified as coordinates, nest them in an array
//        if (isset($locations['lat']) && isset($locations['lng'])) {
//            $locations = [$locations];
//        }
//
//        // Force an array structure
//        if (!is_array($locations)) {
//            $locations = [$locations];
//        }
//
//        // Loop through all locations
//        foreach ($locations as $location) {
//
//            // Get coordinates (or collection of coordinates) from location
//            $coordinates = $this->_setLocationCoords($location, $options);
//
//            // If invalid coordinates, skip location
//            if (!$coordinates) {
//                continue;
//            }
//
//            // Add each marker to collection
//            foreach ($coordinates as $coords) {
//                $this->_dna['markers'][] = $this->_markerDna($coords, $options);
//            }
//
//        }
//
//    }

//    /**
//     * Configure the DNA of a single marker.
//     *
//     * @param $coords
//     * @param array $options
//     * @return array
//     */
//    private function _markerDna($coords, array $options = []): array
//    {
////        // TEMP
////        $options = [
////            'icon' => null,
////            'markerOptions' => null,
////            'infoWindowOptions' => null,
////            'infoWindowTemplate' => null,
////            'fields' => null,
////        ];
////        // ENDTEMP
//
//        // Set values
//        $markerOptions      = ($options['markerOptions']      ?? $this->_defaultMarkerOptions      ?? null);
//        $infoWindowOptions  = ($options['infoWindowOptions']  ?? $this->_defaultInfoWindowOptions  ?? null);
//        $infoWindowTemplate = ($options['infoWindowTemplate'] ?? $this->_defaultInfoWindowTemplate ?? null);
//
//
//        // Return compiled marker DNA
//        return [
//            'coords' => $coords,
//            'markerOptions' => $markerOptions,
//        ];
//    }

    // ========================================================================= //

    private function _addMap($locations, $options)
    {
        // Ensure options are a valid array
        if (!$options || !is_array($options)) {
            $options = [];
        }

        // If no ID, automatically generate a random one
        if (!isset($options['id'])) {
            $hash = StringHelper::randomString(6);
            $options['id'] = "gm-map-{$hash}";
        }

        // Add map to DNA
        $this->_dna[] = [
            'type' => 'map',
            'locations' => $this->_convertToCoords($locations),
            'options' => $options,
        ];
    }

    private function _addMarkers($locations, $options)
    {
        // If no locations were specified, bail
        if (!$locations) {
            return $this;
        }

        // Add markers to DNA
        $this->_dna[] = [
            'type' => 'markers',
            'locations' => $this->_convertToCoords($locations),
            'options' => $options,
        ];
    }


    // Always return coordinates within a parent array,
    // to compensate for Elements with multiple Addresses.
    private function _convertToCoords($locations)
    {

        // If location was specified as coordinates, return them as-is
        if (isset($locations['lat']) && isset($locations['lng'])) {
            return [$locations];
        }

        return $locations;


        // CHECK TO SEE WHAT KIND OF THING $location IS

        // GET THE COORDS OF WHATEVER IT IS


        \Craft::dd($locations);


    }

}

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
use craft\helpers\Template;
use Twig\Markup;

/**
 * Class StaticMap
 * @since 4.0.0
 */
class StaticMap extends Model
{
//
//    /*
//     * Individual settings to be compiled into DNA
//     */
//    private $_id = null;
//    private $_styles = [];
//    private $_mapOptions = [];
//    private $_markerOptions = [];
//    private $_infoWindowOptions = [];
//    private $_markers = [];
//
//    /**
//     * @var array Collection of internal data representing a map to be rendered.
//     */
//    private $_dna;
//
//    /**
//     * Initialize a Map object.
//     *
//     * @param array $mapOptions
//     * @param array|Element|Address $locations
//     * @param array $config
//     */
//    public function __construct(array $mapOptions = [], $locations = [], array $markerOptions = [], array $config = [])
//    {
//        // TEMP
//        $mapOptions = [
//            // string - Set the id attribute of the map container.
//            'id' => 'gm-map-1',
//            // int - Set the width of the map (in px).
//            'width' => null,
//            // int - Set the height of the map (in px).
//            'height' => null,
//            // int - Set the default zoom level of the map. (1 - 16)
//            'zoom' => null, // (uses fitBounds by default)
//            // coords - Set the center position of the map.
//            'center' => null, // (uses fitBounds by default)
//            // array - An array of map styles.
//            'styles' => null,
//            // object - Accepts any google.maps.MapOptions properties.
//            'mapOptions' => null,
//            // object - Accepts any google.maps.MarkerOptions properties.
//            'markerOptions' => null,
//            // object - Accepts any google.maps.InfoWindowOptions properties.
//            'infoWindowOptions' => null,
//            // string - Template path to use for creating info windows.
//            'infoWindowTemplate' => null,
//            // string or array - Which field(s) of the element(s) should be included on the map. (null will include all Address fields)
//            'fields' => null,
//        ];
//        // ENDTEMP
//
//        $this->_setConfigOptions($mapOptions, $locations);
//        parent::__construct($config);
//    }
//
//    /**
//     * Can't output directly as a string (unfortunately)
//     * because `__toString` isn't compatible with
//     * the `raw` filter (necessary to show an HTML tag).
//     *
//     * @return string
//     */
//    public function __toString()
//    {
//        return 'To display a map, append `.html()` to the map object.';
//    }
//
//    /**
//     * Render a fully-functional map container.
//     *
//     * @return Markup
//     */
//    public function html(): Markup
//    {
//
//
//        // COMPILE DNA AT THE LAST MINUTE
//
//
//        // Compile map container
//        $html = Html::modifyTagAttributes('<div>Loading map...</div>', [
//            'id' => $this->_dna['id'],
//            'class' => 'gm-map',
//            'data-dna' => Json::encode($this->_dna),
//        ]);
//
//        // Return Markup
//        return Template::raw($html);
//    }
//
//    // Public Methods
//    // =========================================================================
//
//    /**
//     * Add one or more markers to the map.
//     *
//     * @param array|Element|Address $locations
//     * @param array $markerOptions
//     */
//    public function markers($locations, array $markerOptions = [])
//    {
//        $this->_dna['markers'][] = [
//            // Configuration for marker
//        ];
//    }
//
//    // Private Methods
//    // =========================================================================
//
//    /**
//     * Create the DNA based on options specified
//     * by the user during initialization.
//     *
//     * @param array $options
//     * @param array|Element|Address $locations
//     */
//    private function _setConfigOptions(array $options = [], $locations = [])
//    {
//        // Set the map ID
//        if (isset($options['id'])) {
//            $this->_id = $options['id'];
//        } else {
//            $mapCounter = 1; // TEMP
//            $this->_id = "gm-map-{$mapCounter}";
//        }
//
//        // Inherit user-specified config options
//        $this->_styles = $options['styles'];
//        $this->_mapOptions = $options['mapOptions'];
//        $this->_markerOptions = $options['markerOptions'];
//        $this->_infoWindowOptions = $options['infoWindowOptions'];
//
//        // Parse $locations into an array of Marker objects
//        if ($locations) {
//            $this->_markers[] = new Marker;
//        }
//    }

}

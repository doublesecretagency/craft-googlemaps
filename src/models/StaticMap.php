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
use craft\base\Element;
use craft\base\Model;
use craft\helpers\Html;
use craft\helpers\StringHelper;
use craft\helpers\Template;
use doublesecretagency\googlemaps\fields\AddressField;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use Twig\Markup;
use yii\base\Exception;

/**
 * Class StaticMap
 * @since 4.0.0
 */
class StaticMap extends Model
{

    /**
     * @var string The ID of this map model.
     */
    public $id;

    /**
     * @var array Collection of internal data representing a map to be rendered.
     */
    private $_dna = [
        ['size' => '640x640'],
        ['maptype' => 'roadmap'],
        ['scale' => '4'],
    ];

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
        return 'To display a map, append `.tag()` to the map object.';
    }

    /**
     * Initialize a Static Map object.
     *
     * @param array|Element|Address $locations
     * @param array $options
     * @param array $config
     */
    public function __construct($locations = [], array $options = [], array $config = [])
    {
        // Ensure options are a valid array
        if (!$options || !is_array($options)) {
            $options = [];
        }

        // If no ID, automatically generate a random one
        if (!isset($options['id'])) {
            $hash = StringHelper::randomString(6);
            $options['id'] = "map-{$hash}";
        }

        // Set internal map ID
        $this->id = $options['id'];







//        // Initialize map DNA
//        $this->_dna[] = [
//            'type' => 'map',
//            'locations' => $this->_convertToCoords($locations),
//            'options' => $options,
//        ];

        // Call parent constructor
        parent::__construct($config);
    }

    // ========================================================================= //

    /**
     * Add one or more markers to the map.
     *
     * @param array|Element|Address $locations
     * @param array $options
     * @return $this
     */
    public function markers($locations, array $options = []): StaticMap
    {
//        // If no locations were specified, bail
//        if (!$locations) {
//            return $this;
//        }

        $this->_dna[] = ['markers' => 'color:blue%7Clabel:S%7C40.702147,-74.015794'];
        $this->_dna[] = ['markers' => 'color:green%7Clabel:G%7C40.711614,-74.012318'];
        $this->_dna[] = ['markers' => 'color:red%7Clabel:C%7C40.718217,-73.998284'];

//        // Add to map DNA
//        $this->_dna[] = [
//            'type' => 'markers',
//            'locations' => $this->_convertToCoords($locations),
//            'options' => $options,
//        ];

        // Keep the party going
        return $this;
    }

    /**
     * Style the map.
     *
     * @param array $styleSet
     * @return $this
     */
    public function styles(array $styleSet): StaticMap
    {
        // If not a valid style set, bail
        if (!$styleSet || !is_array($styleSet)) {
            return $this;
        }

        // Add to map DNA
        $this->_dna[] = ['styles' => $styleSet];

        // Keep the party going
        return $this;
    }

    /**
     * Change zoom level of the map.
     *
     * @param int $level
     * @return $this
     */
    public function zoom(int $level): StaticMap
    {
        // Add to map DNA
        $this->_dna[] = ['zoom' => $level];

        // Keep the party going
        return $this;
    }

    /**
     * Re-center the map.
     *
     * @param array|string $coords
     * @return $this
     */
    public function center($coords): StaticMap
    {
        // If not a valid style set, bail
        if (!$coords) {
            return $this;
        }

        // Add to map DNA
        $this->_dna[] = ['center' => $coords];

        // Keep the party going
        return $this;
    }

    /**
     * Set the icon of an existing marker.
     *
     * @param string $markerId
     * @return $this
     */
    public function setMarkerIcon($markerId, $icon): StaticMap
    {
        // Add to map DNA
//        $this->_dna[] = [
//            'type' => 'setMarkerIcon',
//            'markerId' => $markerId,
//            'icon' => $icon,
//        ];

        // Keep the party going
        return $this;
    }

    // ========================================================================= //

    public function tag(): Markup
    {

        /*
         * $options should include `alt` and `title`
         */

        // Compile map container
        $html = Html::modifyTagAttributes('<img>', [
            'id' => $this->id,
            'class' => 'gm-img',
            'alt' => 'this-map',
            'src' => $this->src(),
        ]);

        // Return Markup
        return Template::raw($html);
    }

    public function src(): string
    {


//        Craft::dd($this->_dna);



        // If no DNA, throw an error
        if (!$this->_dna) {
            throw new Exception('Model misconfigured. The map DNA is empty.');
        }

        // Get browser key
        $key = trim(GoogleMaps::getBrowserKey());

        // Set base URL of Google Maps API
        $url = 'https://maps.googleapis.com/maps/api/staticmap';
        $url .= "?key={$key}";

        // Loop through DNA sequence
        foreach ($this->_dna as $block) {
            foreach ($block as $param => $value) {
                $url .= "&{$param}={$value}";
            }
        }

        // Return compiled URL
        return $url;

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


    /**
     * Unpack and initialize map DNA.
     *
     * @return array
     */
    public function _unpack(): array
    {
//        // Loop through DNA sequence
//        foreach ($this->_dna as $block) {
//
//            switch ($block->type) {
//
//                // Create a new map
//                case 'map':
////                    map = new StaticMap(block.locations, block.options);
//                    break;
//
//                // Add markers to the map
//                case 'markers':
////                    map.markers(block.locations, block.options);
//                    break;
//
////                // Add KML layer to the map
////                case 'kml':
//////                    map.kml(block.url, block.options);
////                    break;
//
//                // Style the map
//                case 'styles':
////                    map.styles(block.styleSet);
//                    break;
//
//                // Zoom the map
//                case 'zoom':
////                    map.zoom(block.level);
//                    break;
//
//                // Center the map
//                case 'center':
////                    map.center(block.coords);
//                    break;
//
////                // Fit the map bounds
////                case 'fit':
//////                    map.fit();
////                    break;
////
////                // Refresh the map
////                case 'refresh':
//////                    map.refresh();
////                    break;
////
////                // Pan to a specific marker
////                case 'panToMarker':
//////                    map.panToMarker(block.markerId);
////                    break;
//
//                // Set icon of an existing marker
//                case 'setMarkerIcon':
////                    map.setMarkerIcon(block.markerId, block.icon);
//                    break;
//
////                // Hide a marker
////                case 'hideMarker':
//////                    map.hideMarker(block.markerId);
////                    break;
////
////                // Show a marker
////                case 'showMarker':
//////                    map.showMarker(block.markerId);
////                    break;
//
//            }

//        }


//        // Switch according to DNA block type
//        switch (block.type) {

//        }
//
//    }
//
//        // Store map object for future reference
//        this._maps[map.id] = map;
//
//        // Return the map object
//        return map;
    }

    // ========================================================================= //


    // Always return coordinates within a parent array,
    // to compensate for Elements with multiple Addresses.
    private function _convertToCoords($locations): array
    {
        // If it's a Location Model, return the coordinates
        if (is_a($locations, Location::class)) {
            return [$locations->getCoords()];
        }

        // If it's a natural set of coordinates, return as-is
        if (is_array($locations) && isset($locations['lat']) && isset($locations['lng'])) {
            return [$locations];
        }

        // Force array syntax
        if (!is_array($locations)) {
            $locations = [$locations];
        }

        // Initialize results array
        $results = [];

        // Loop through all locations
        foreach ($locations as $location) {

            // If it's a Location Model, add the coordinates to results
            if (is_a($location, Location::class)) {
                $results[] = $location->getCoords();
            }

            // If it's a natural set of coordinates, add them to results as-is
            if (is_array($location) && isset($location['lat']) && isset($location['lng'])) {
                $results[] = $location;
            }

            // If not an Element, skip it
            if (!is_a($location, Element::class)) {
                continue;
            }

            // Get all fields associated with Element
            $fields = $location->getFieldLayout()->getFields();

            // Loop through all relevant fields
            foreach ($fields as $field) {
                // If not an Address Field, skip it
                if (!is_a($field, AddressField::class)) {
                    continue;
                }
                // Get value of Address Field
                $address = $location->{$field->handle};
                // If no Address, skip
                if (!$address) {
                    continue;
                }
                // Add coordinates to results
                if ($address->hasCoords()) {
                    $results[] = array_merge(
                        $address->getCoords(),
                        ['id' => "{$location->id}-{$field->handle}"]
                    );
                }
            }

        }

        // Return final results
        return $results;
    }

}

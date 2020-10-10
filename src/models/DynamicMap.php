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
use craft\helpers\Json;
use craft\helpers\StringHelper;
use craft\helpers\Template;
use doublesecretagency\googlemaps\fields\AddressField;
use doublesecretagency\googlemaps\web\assets\JsApiAsset;
use Twig\Markup;
use yii\base\Exception;

/**
 * Class DynamicMap
 * @since 4.0.0
 */
class DynamicMap extends Model
{

    /**
     * @var string The ID of this map model.
     */
    public $id;

    /**
     * @var array Collection of internal data representing a map to be rendered.
     */
    private $_dna = [];

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
     * Initialize a Dynamic Map object.
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

        // Unless otherwise specified, preload the necessary JavaScript
        if (!isset($options['js']) || !is_bool($options['js'])) {
            $options['js'] = true;
        }

        // Get view service
        $view = Craft::$app->getView();

        // Load assets
        if ($options['js']) {
            $view->registerAssetBundle(JsApiAsset::class);
        }

        // If in devMode, enable JS logging
        if (Craft::$app->getConfig()->general->devMode) {
            $view->registerJs('googleMaps.log = true;', $view::POS_END);
        }

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
     * @return $this
     */
    public function markers($locations, array $options = []): DynamicMap
    {
        $this->_addMarkers($locations, $options);
        return $this;
    }

    /**
     * Add a KML layer to the map.
     *
     * @param string $url
     * @param array $options
     * @return $this
     */
    public function kml($url, array $options = []): DynamicMap
    {
        $this->_addKml($url, $options);
        return $this;
    }

    /**
     * Style the map.
     *
     * @param array $styleSet
     * @return $this
     */
    public function styles(array $styleSet): DynamicMap
    {
        $this->_addStyles($styleSet);
        return $this;
    }

    /**
     * Change zoom level of the map.
     *
     * @param int $level
     * @return $this
     */
    public function zoom(int $level): DynamicMap
    {
        $this->_addZoom($level);
        return $this;
    }

    /**
     * Re-center the map.
     *
     * @param array $coords
     * @return $this
     */
    public function center(array $coords): DynamicMap
    {
        $this->_addCenter($coords);
        return $this;
    }

    /**
     * Fit map to existing marker bounds.
     *
     * @return $this
     */
    public function fit(): DynamicMap
    {
        $this->_addFit();
        return $this;
    }

    /**
     * Refresh the map.
     * Generally useless, only exists for parity.
     *
     * @return $this
     */
    public function refresh(): DynamicMap
    {
        $this->_addRefresh();
        return $this;
    }

    // ========================================================================= //

    public function tag($init = true): Markup
    {
        // If no DNA, throw an error
        if (!$this->_dna) {
            throw new Exception('Model misconfigured. The map DNA is empty.');
        }

        // Alias map from DNA
        $map =& $this->_dna[0];

        // If the first item is not a map, throw an error
        if ('map' != $map['type']) {
            throw new Exception('Map model misconfigured. The chain must begin with a `map()` segment.');
        }

        // Compile map container
        $html = Html::modifyTagAttributes('<div>Loading map...</div>', [
            'id' => $this->id,
            'class' => 'gm-map',
            'data-dna' => Json::encode($this->_dna),
        ]);

        // Initialize map (unless suppressed)
        if ($init) {
            $view = Craft::$app->getView();
            $js = "addEventListener('load', function(){googleMaps.init('{$this->id}')});";
            $view->registerJs($js, $view::POS_END);
        }

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


    /*
     *
     * COMPILE DNA!!
     *
     */


    private function _addMap($locations, $options)
    {
        // Add map to the DNA
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
            return;
        }

        // Add markers to the DNA
        $this->_dna[] = [
            'type' => 'markers',
            'locations' => $this->_convertToCoords($locations),
            'options' => $options,
        ];
    }

    private function _addKml($url, $options)
    {
        // If no url was specified, bail
        if (!$url) {
            return;
        }

        // Add a KML layer to the DNA
        $this->_dna[] = [
            'type' => 'kml',
            'url' => $url,
            'options' => $options,
        ];
    }

    private function _addStyles($styleSet)
    {
        // If not a valid style set, bail
        if (!$styleSet || !is_array($styleSet)) {
            return;
        }

        // Add map styles to the DNA
        $this->_dna[] = [
            'type' => 'styles',
            'styleSet' => $styleSet,
        ];
    }

    private function _addZoom($level)
    {
        // Add map styles to the DNA
        $this->_dna[] = [
            'type' => 'zoom',
            'level' => $level,
        ];
    }

    private function _addCenter($coords)
    {
        // If not a valid style set, bail
        if (!$coords) {
            return;
        }

        // Add map styles to the DNA
        $this->_dna[] = [
            'type' => 'center',
            'coords' => $coords,
        ];
    }

    private function _addFit()
    {
        // Add fitBounds call to the DNA
        $this->_dna[] = [
            'type' => 'fit',
        ];
    }

    private function _addRefresh()
    {
        // Add refresh call to the DNA
        $this->_dna[] = [
            'type' => 'refresh',
        ];
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

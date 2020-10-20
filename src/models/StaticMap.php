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
use craft\helpers\Template;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use doublesecretagency\googlemaps\helpers\MapHelper;
use Twig\Markup;

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

        // Set internal map ID
        $this->id = ($options['id'] ?? MapHelper::generateId('map'));

        // Internalize options, fallback to defaults
        $this->_dna['scale']   = ($options['scale']   ?? 4);
        $this->_dna['size']    = ($options['size']    ?? '640x640');
        $this->_dna['maptype'] = ($options['maptype'] ?? 'roadmap');

        // Get marker options
        $markerOptions = ($options['markerOptions'] ?? []);

        // Load first batch of markers
        $this->markers($locations, $markerOptions);

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
        // If no locations were specified, bail
        if (!$locations) {
            return $this;
        }

        // Initialize marker parts
        $parts = [];

        // Loop through marker options
        foreach ($options as $k => $v) {
            $parts[] = "{$k}:{$v}";
        }

        // Get a collection of coordinate sets
        $collection = MapHelper::extractCoords($locations);

        // Append each set of marker coordinates
        foreach ($collection as $coords) {
            $parts[] = MapHelper::stringCoords($coords);
        }

        // Add to map DNA
        $this->_dna['markers'][] = implode('|', $parts);

        // Keep the party going
        return $this;
    }

    /**
     * Add a defined path to the map.
     *
     * @param array|Element|Address $points
     * @param array $options
     * @return $this
     */
    public function path($points, array $options = []): StaticMap
    {
        // If no points were specified, bail
        if (!$points) {
            return $this;
        }

        // Initialize path parts
        $parts = [];

        // Loop through path options
        foreach ($options as $k => $v) {
            $parts[] = "{$k}:{$v}";
        }

        // Get a collection of coordinate sets
        $collection = MapHelper::extractCoords($points);

        // Append each set of coordinates to path
        foreach ($collection as $coords) {
            $parts[] = MapHelper::stringCoords($coords);
        }

        // Add to map DNA
        $this->_dna['path'][] = implode('|', $parts);

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
        if (!$styleSet) {
            return $this;
        }

        // Loop through style set
        foreach ($styleSet as $subset) {

            // Initialize path parts
            $parts = [];

            // Compile each subset of styles
            foreach ($subset as $k => $v) {
                $parts[] = "{$k}:{$v}";
            }

            // Add to map DNA
            $this->_dna['style'][] = implode('|', $parts);

        }

        // Keep the party going
        return $this;
    }

    /**
     * Set zoom level of the map.
     *
     * @param int $level
     * @return $this
     */
    public function zoom(int $level): StaticMap
    {
        // Add to map DNA
        $this->_dna['zoom'] = $level;

        // Keep the party going
        return $this;
    }

    /**
     * Center the map.
     *
     * @param array|string $coords
     * @return $this
     */
    public function center($coords): StaticMap
    {
        // If not a valid set of coordinates, bail
        $validFormat = (is_string($coords) || is_array($coords));
        if (!$coords || !$validFormat) {
            return $this;
        }

        // Add to map DNA
        if (is_string($coords)) {
            $this->_dna['center'] = $coords;
        } else {
            $this->_dna['center'] = MapHelper::stringCoords($coords);
        }

        // Keep the party going
        return $this;
    }

    // ========================================================================= //

    public function tag(): Markup
    {

        /*
         * TODO:
         * $options should include:
         * `alt`, `title`, and `classes`
         */

        // Additional classes
        $classes = '';

        // Compile map container
        $html = Html::modifyTagAttributes('<img>', [
            'id' => $this->id,
            'class' => trim("gm-img {$classes}"),
            'alt' => 'this-map',
            'src' => $this->src(),
        ]);

        // Return Markup
        return Template::raw($html);
    }

    public function src(): string
    {
        // Get browser key
        $key = trim(GoogleMaps::getBrowserKey());

        // Set base URL of Google Maps API
        $url = 'https://maps.googleapis.com/maps/api/staticmap';
        $url .= "?key={$key}";

        // Loop through DNA sequence
        foreach ($this->_dna as $param => $value) {

            // If value is an string
            if (is_string($value) || is_numeric($value)) {
                // Append parameter value
                $url .= "&{$param}={$value}";
                // Skip to next block
                continue;
            }

            // If value is an array
            if (is_array($value)) {
                // Loop through each value
                foreach ($value as $v) {
                    // Append each individually
                    $url .= "&{$param}={$v}";
                }
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

}

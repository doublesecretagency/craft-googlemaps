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
    private $_dna = [
        'markers' => [],
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

        // Set internal map ID
        $this->id = ($options['id'] ?? MapHelper::generateId('map'));

        // Internalize options, fallback to defaults
        $this->_dna['scale']   = ($options['scale']   ?? '4');
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

        // Get a collection of coordinate sets
        $collection = MapHelper::extractCoords($locations);

        // Loop through all coordinates
        foreach ($collection as $coords) {

            // Determine the marker ID
            $markerId = ($coords['id'] ?? MapHelper::generateId('marker'));


            /*
             * Get rid of the marker ID?
             * Is it useless in a static map?
             *
             * Seems like it creates more
             * problems than it solves.
             *
             * Could then merge marker commands.
             */







            // Initialize marker parts
            $parts = [];

            // Loop through marker options
            foreach ($options as $k => $v) {
                $parts[] = "{$k}:{$v}";
            }

            // Append marker coordinates
            $parts[] = "{$coords['lat']},{$coords['lng']}";



            // Add to map DNA
            $this->_dna['markers'][$markerId] = implode('%7C', $parts);

        }

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
     * Set zoom level of the map.
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
     * Center the map.
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



        // Get browser key
        $key = trim(GoogleMaps::getBrowserKey());

        // Set base URL of Google Maps API
        $url = 'https://maps.googleapis.com/maps/api/staticmap';
        $url .= "?key={$key}";

        // Loop through DNA sequence
        foreach ($this->_dna as $param => $value) {

            // If not the markers parameter
            if ('markers' != $param) {
                // Append parameter value
                $url .= "&{$param}={$value}";
                // Skip to next block
                continue;
            }

            // Loop through markers
            foreach ($value as $marker) {
                // Append each marker
                $url .= "&markers={$marker}";
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

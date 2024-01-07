<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by the Google Maps API.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2021 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\models;

use craft\base\Element;
use craft\base\Model;
use craft\helpers\Html;
use craft\helpers\Template;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use doublesecretagency\googlemaps\helpers\MapHelper;
use Exception;
use Illuminate\Support\Collection;
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
    public string $id;

    /**
     * @var array Collection of internal data representing a map to be rendered.
     */
    private array $_dna = [];

    /**
     * @var array Internalized set of options.
     */
    private array $_mapOptions;

    /**
     * @var int Map image width.
     */
    private int $_w = 640;

    /**
     * @var int Map image height.
     */
    private int $_h = 320;

    // ========================================================================= //

    /**
     * Can't output directly as a string (unfortunately)
     * because `__toString` isn't compatible with
     * the `raw` filter (necessary to show an HTML tag).
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'To display a map, append `.tag()` to the map object.';
    }

    /**
     * Initialize a Static Map object.
     *
     * @param array|Collection|Element|Location $locations
     * @param array $options
     * @param array $config
     */
    public function __construct(array|Collection|Element|Location $locations = [], array $options = [], array $config = [])
    {
        // Ensure options are a valid array
        if (!$options || !is_array($options)) {
            $options = [];
        }

        // Internalize original map options
        $this->_mapOptions = $options;

        // Set internal map ID
        $this->id = ($options['id'] ?? MapHelper::generateId('map'));

        // Get marker options
        $markerOptions = ($options['markerOptions'] ?? []);

        // Load initial markers
        $this->markers($locations, $markerOptions);

        // Configure DNA based on specified options
        $this->_setDimensions($options);
        $this->_setFormatting($options);
        $this->_setPositioning($options);
        $this->_setVisibility($options);

        // Call parent constructor
        parent::__construct($config);
    }

    // ========================================================================= //

    /**
     * Add one or more markers to the map.
     *
     * @param array|Collection|Element|Location $locations
     * @param array $options
     * @return $this
     */
    public function markers(array|Collection|Element|Location $locations, array $options = []): StaticMap
    {
        // If no locations were specified, bail
        if (!$locations) {
            return $this;
        }

        // If locations are a Collection
        if ($locations instanceof Collection) {
            // Convert to an array
            $locations = $locations->all();
        }

        // Initialize marker parts
        $parts = [];

        // Whitelist of valid options
        $validOptions = ['color','label','icon','anchor','size','scale'];

        // Apply whitelisted marker options
        foreach ($options as $k => $v) {
            // If option isn't valid, skip
            if (!in_array($k, $validOptions, true)) {
                continue;
            }
            // If value is an array, throw an error
            if (is_array($v)) {
                if ('icon' === $k) {
                    $error = "Set `{$k}` to the associated image URL.";
                } else {
                    $error = "Set `{$k}` to a string instead of an array.";
                }
                throw new Exception("Each static map marker property must be a string. {$error}");
            }
            // If value is not a string or numeric, skip
            if (!is_string($v) && !is_numeric($v)) {
                continue;
            }
            // Append URL parts to generate static map
            $parts[] = "{$k}:{$v}";
        }

        // Get a collection of coordinate sets
        $collection = MapHelper::extractCoords($locations, $options);

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
     * @param array|Collection|Element|Location $points
     * @param array $options
     * @return $this
     */
    public function path(array|Collection|Element|Location $points, array $options = []): StaticMap
    {
        // If no points were specified, bail
        if (!$points) {
            return $this;
        }

        // Initialize path parts
        $parts = [];

        // If provided but invalid, convert geodesic value to a string
        if (isset($options['geodesic']) && !is_string($options['geodesic'])) {
            $options['geodesic'] = ($options['geodesic'] ? 'true' : 'false');
        }

        // Whitelist of valid options
        $validOptions = ['weight','color','fillcolor','geodesic'];

        // Apply whitelisted path options
        foreach ($options as $k => $v) {
            if (in_array($k, $validOptions, true)) {
                $parts[] = "{$k}:{$v}";
            }
        }

        // Get a collection of coordinate sets
        $collection = MapHelper::extractCoords($points, $options);

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

            // Get optional feature and element values
            $feature = ($subset['featureType'] ?? false);
            $element = ($subset['elementType'] ?? false);

            // Get required style rules
            $styleRules = ($subset['stylers'] ?? []);

            // If a feature was specified, prepend it
            if ($feature) {
                $parts[] = "feature:{$feature}";
            }

            // If an element was specified, prepend it
            if ($element) {
                $parts[] = "element:{$element}";
            }

            // Compile each subset of styles
            foreach ($styleRules as $rule) {
                // Loop through each rule
                foreach ($rule as $k => $v) {
                    // Replace symbol for URL
                    $v = str_replace('#', '0x', $v);
                    // Append part
                    $parts[] = "{$k}:{$v}";
                }
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
    public function center(array|string $coords): StaticMap
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

    /**
     * Outputs a fully-rendered `<img>` tag for use in a Twig template.
     *
     * @return Markup
     */
    public function tag(): Markup
    {
        // Get custom attributes
        $attr = ($this->_mapOptions['attr'] ?? []);

        // Use internalized ID as fallback
        $attr['id'] = ($attr['id'] ?? $this->id);

        // Get the final map source
        $attr['src'] = $this->src();

        // If no class is specified, fallback to the default
        $attr['class'] = ($attr['class'] ?? 'gm-img');

        // Set image dimensions
        $attr['width']  = $this->_w;
        $attr['height'] = $this->_h;

        // Compile map container
        $html = Html::modifyTagAttributes('<img>', $attr);

        // Return Markup
        return Template::raw($html);
    }

    /**
     * Returns the fully-compiled `src` value of the static map.
     *
     * @return string
     */
    public function src(): string
    {
        // Get browser key
        $key = GoogleMaps::getBrowserKey();

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

    // ========================================================================= //

    /**
     * Configure map dimensions based on relevant options.
     *
     * @param array $options
     */
    private function _setDimensions(array $options): void
    {
        // Internalize official dimensions
        $this->_w = ($options['width']  ?? $this->_w);
        $this->_h = ($options['height'] ?? $this->_h);

        // Compile size parameter
        $this->_dna['size'] = ($options['size'] ?? "{$this->_w}x{$this->_h}");

        // Compile scale parameter
        $this->_dna['scale'] = ($options['scale'] ?? 2);
    }

    /**
     * Configure map formatting based on relevant options.
     *
     * @param array $options
     */
    private function _setFormatting(array $options): void
    {
        // If a image format was specified, apply it
        if (isset($options['format']) && is_string($options['format'])) {
            $this->_dna['format'] = trim($options['format']);
        }

        // If a map type was specified, apply it
        if (isset($options['maptype']) && is_string($options['maptype'])) {
            $this->_dna['maptype'] = trim($options['maptype']);
        }

        // If a language was specified, apply it
        if (isset($options['language']) && is_string($options['language'])) {
            $this->_dna['language'] = trim($options['language']);
        }

        // If a region was specified, apply it
        if (isset($options['region']) && is_string($options['region'])) {
            $this->_dna['region'] = trim($options['region']);
        }
    }

    /**
     * Configure map positioning (and styles) based on relevant options.
     *
     * @param array $options
     */
    private function _setPositioning(array $options): void
    {
        // If valid zoom specified, apply it
        if (isset($options['zoom']) && is_int($options['zoom'])) {
            $this->zoom($options['zoom']);
        }

        // If center specified, apply it
        if (isset($options['center'])) {
            $this->center($options['center']);
        }

        // If valid map styles specified, apply them
        if (isset($options['styles']) && is_array($options['styles'])) {
            $this->styles($options['styles']);
        }
    }

    /**
     * Configure map positioning based on `visible` points.
     *
     * @param array $options
     */
    private function _setVisibility(array $options): void
    {
        // If no visibility point specified
        if (!isset($options['visible'])) {
            return;
        }

        // Get value of visible
        $visible = $options['visible'];

        // If string, set parameter value and bail
        if (is_string($visible)) {
            $this->_dna['visible'] = urlencode(trim($visible));
            return;
        }

        // If not an array, bail
        if (!is_array($visible)) {
            return;
        }

        // If coordinates syntax, force nested array
        if (isset($visible['lat'],$visible['lng'])) {
            $visible = [$visible];
        }

        // Initialize collection of all points
        $points = [];

        // Loop through all visible points
        foreach ($visible as $v) {
            // If string, set parameter value and skip
            if (is_string($v)) {
                $points[] = trim($v);
                continue;
            }
            // If not an array, skip
            if (!is_array($visible)) {
                continue;
            }
            // Add point from specified coordinates
            $points[] = MapHelper::stringCoords($v);
        }

        // Set visible points
        $this->_dna['visible'] = urlencode(implode('|', $points));
    }

}

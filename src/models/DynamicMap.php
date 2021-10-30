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

use Craft;
use craft\base\Element;
use craft\base\Model;
use craft\helpers\Html;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use craft\helpers\Template;
use craft\models\FieldLayout;
use craft\web\View;
use doublesecretagency\googlemaps\fields\AddressField;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use doublesecretagency\googlemaps\helpers\MapHelper;
use Throwable;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
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

    /**
     * @var array Collection of JS callback functions tied to markers.
     */
    private $_markerCallbacks = [];

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
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __construct($locations = [], array $options = [], array $config = [])
    {
        // Call parent constructor
        parent::__construct($config);

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

        // Initialize map DNA without markers
        $this->_dna[] = [
            'type' => 'map',
            'locations' => [],
            'options' => $options,
        ];

        // Prevent conflict between map ID and marker IDs
        unset($options['id']);

        // Load all map markers
        $this->markers($locations, $options);

        // Fit map to marker boundaries
        $this->fit();

        // Optionally zoom the map
        if ($options['zoom'] ?? false) {
            $this->zoom($options['zoom']);
        }

        // Optionally center the map
        if ($options['center'] ?? false) {
            $this->center($options['center']);
        }
    }

    // ========================================================================= //

    /**
     * Add one or more markers to the map.
     *
     * @param array|Element|Address $locations
     * @param array $options
     * @return $this
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function markers($locations, array $options = []): DynamicMap
    {
        // If no locations were specified, bail
        if (!$locations) {
            return $this;
        }

        // Whether markers should be placed individually
        $uniqueMarkers = (
            ($options['infoWindowTemplate'] ?? false) ||
            ($options['markerLink'] ?? false) ||
            ($options['markerClick'] ?? false)
        );

        // If adding markers individually
        if ($uniqueMarkers) {
            // Create individual markers one at a time
            $this->_individualMarkers($locations, $options);
        } else {
            // Add markers to DNA as a group
            $this->_dna[] = [
                'type' => 'markers',
                'locations' => MapHelper::extractCoords($locations, $options),
                'options' => $options,
            ];
        }

        // Keep the party going
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
        // If no url was specified, bail
        if (!$url) {
            return $this;
        }

        // Add KML layer to DNA
        $this->_dna[] = [
            'type' => 'kml',
            'url' => $url,
            'options' => $options,
        ];

        // Keep the party going
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
        // Add map styles to DNA
        $this->_dna[] = [
            'type' => 'styles',
            'styleSet' => $styleSet,
        ];

        // Keep the party going
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
        // Add zoom level to DNA
        $this->_dna[] = [
            'type' => 'zoom',
            'level' => $level,
        ];

        // Keep the party going
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
        // If not a valid style set, bail
        if (!$coords) {
            return $this;
        }

        // Add map center to DNA
        $this->_dna[] = [
            'type' => 'center',
            'coords' => $coords,
        ];

        // Keep the party going
        return $this;
    }

    /**
     * Fit map to existing marker bounds.
     *
     * @return $this
     */
    public function fit(): DynamicMap
    {
        // Add fitBounds to DNA
        $this->_dna[] = [
            'type' => 'fit',
        ];

        // Keep the party going
        return $this;
    }

    /**
     * Refresh the map.
     *
     * Generally useless in Twig/PHP,
     * only exists for parity with JS.
     *
     * @return $this
     */
    public function refresh(): DynamicMap
    {
        // Add map refresh to DNA
        $this->_dna[] = [
            'type' => 'refresh',
        ];

        // Keep the party going
        return $this;
    }

    /**
     * Pan map to center on a specific marker.
     *
     * @param string $markerId
     * @return $this
     */
    public function panToMarker($markerId): DynamicMap
    {
        // Add pan to marker to DNA
        $this->_dna[] = [
            'type' => 'panToMarker',
            'markerId' => $markerId,
        ];

        // Keep the party going
        return $this;
    }

    /**
     * Open the info window of a specific marker.
     *
     * @param string $markerId
     * @return $this
     */
    public function openInfoWindow($markerId): DynamicMap
    {
        // Add open info window to DNA
        $this->_dna[] = [
            'type' => 'openInfoWindow',
            'markerId' => $markerId,
        ];

        // Keep the party going
        return $this;
    }

    /**
     * Set the icon of an existing marker.
     *
     * @param string $markerId
     * @return $this
     */
    public function setMarkerIcon($markerId, $icon): DynamicMap
    {
        // Add marker icon to DNA
        $this->_dna[] = [
            'type' => 'setMarkerIcon',
            'markerId' => $markerId,
            'icon' => $icon,
        ];

        // Keep the party going
        return $this;
    }

    /**
     * Hide a marker.
     *
     * @param string $markerId
     * @return $this
     */
    public function hideMarker($markerId): DynamicMap
    {
        // Add call to hide marker
        $this->_dna[] = [
            'type' => 'hideMarker',
            'markerId' => $markerId,
        ];

        // Keep the party going
        return $this;
    }

    /**
     * Show a marker.
     *
     * @param string $markerId
     * @return $this
     */
    public function showMarker($markerId): DynamicMap
    {
        // Add call to show marker
        $this->_dna[] = [
            'type' => 'showMarker',
            'markerId' => $markerId,
        ];

        // Keep the party going
        return $this;
    }

    // ========================================================================= //

    /**
     * Hide a KML layer.
     *
     * @param string $kmlId
     * @return $this
     */
    public function hideKml($kmlId): DynamicMap
    {
        // Add call to hide KML layer
        $this->_dna[] = [
            'type' => 'hideKml',
            'kmlId' => $kmlId,
        ];

        // Keep the party going
        return $this;
    }

    /**
     * Show a KML layer.
     *
     * @param string $kmlId
     * @return $this
     */
    public function showKml($kmlId): DynamicMap
    {
        // Add call to show KML layer
        $this->_dna[] = [
            'type' => 'showKml',
            'kmlId' => $kmlId,
        ];

        // Keep the party going
        return $this;
    }

    // ========================================================================= //

    /**
     * Outputs a dynamic map in a `<div>` tag for use in a Twig template.
     *
     * @param array $options Set of options to configure the rendered tag.
     * @return Markup
     * @throws Exception
     */
    public function tag(array $options = []): Markup
    {
        // If no DNA, throw an error
        if (!$this->_dna) {
            throw new Exception('Model misconfigured. The map DNA is empty.');
        }

        // If the first DNA item is not a map, throw an error
        if ('map' !== ($this->_dna[0]['type'] ?? false)) {
            throw new Exception('Map model misconfigured. The chain must begin with a `map()` segment.');
        }

        // Prevent `markerClick` callback string from being exported to JS
        array_walk($this->_dna, function (&$item) {
            // If `markerClick` was specified, convert to boolean `true`
            if ($item['options']['markerClick'] ?? false) {
                $item['options']['markerClick'] = true;
            }
        });

        // Get view service
        $view = Craft::$app->getView();

        // Unless otherwise specified, preload the necessary JavaScript assets
        if (!isset($options['assets']) || !is_bool($options['assets'])) {
            $options['assets'] = true;
        }

        // If no additional API parameters were specified, default to empty array
        if (!isset($options['api']) || !is_array($options['api'])) {
            $options['api'] = [];
        }

        // If we're permitted to load JS assets
        if ($options['assets']) {
            // Load assets with optional API parameters
            GoogleMaps::loadAssets($options['api']);
        }

        // If marker callbacks were specified
        if ($this->_markerCallbacks) {
            // Initialize list of callbacks
            $jsCallbacks = '';
            // Loop through callbacks
            foreach ($this->_markerCallbacks as $markerId => $callback) {
                // Append each callback to list
                $jsCallbacks .= "    '{$markerId}': {$callback},\n";
            }
            // Associate callbacks with this map
            $cb = "googleMaps._markerCallbacks['{$this->id}'] = {\n{$jsCallbacks}};";
            // Register callbacks at the end of the page
            $view->registerJs($cb, $view::POS_END);
        }

        // Initialize the map (unless intentionally suppressed)
        if ($options['init'] ?? true) {
            // Get optional callback
            $callback = ($options['callback'] ?? 'null');
            // Initialize Google Maps after page has loaded
            $googleMapsInit = "googleMaps.init('{$this->id}', {$callback})";
            $js = "addEventListener('load', function () {{$googleMapsInit}});";
            // Register JS at the end of the page
            $view->registerJs($js, $view::POS_END);
        }

        // Compile map container
        $html = Html::modifyTagAttributes('<div>Loading map...</div>', [
            'id' => $this->id,
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

    /**
     * Create individual markers one at a time.
     *
     * @param $locations
     * @param $options
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function _individualMarkers($locations, $options)
    {
        // Initialize infoWindowOptions
        $options = $options ?? [];
        $options['infoWindowOptions'] = $options['infoWindowOptions'] ?? [];

        // Whether value is a simple set of coordinates
        $isCoords = (is_array($locations) && isset($locations['lat'], $locations['lng']));

        // If it's an array, but not a coordinates set
        if (is_array($locations) && !$isCoords) {
            // Loop through each location
            foreach ($locations as $l) {
                // Call method recursively
                $this->_individualMarkers($l, $options);
            }
            // Our work here is done
            return;
        }

        /**
         * Due to recursion, locations are now
         * guaranteed to be a singular item
         */
        $location =& $locations;

        // Extract location coordinates
        $coords = MapHelper::extractCoords($location, $options);

        // If info window template specified
        if ($options['infoWindowTemplate'] ?? false) {
            // Add an info window for the marker
            $this->_markerInfoWindow($location, $options, $isCoords);
        }

        // If marker click callback specified and is a string
        if (($options['markerClick'] ?? false) && is_string($options['markerClick'])) {
            // Parse marker click callback
            $this->_parseLocationString($location, $options['markerClick']);
            // Get the marker ID
            $markerId = ($coords[0]['id'] ?? 'err');
            // Create marker click listener in JavaScript
            $this->_markerClick($markerId, $options);
        }

        // If marker link specified and is a string
        if (($options['markerLink'] ?? false) && is_string($options['markerLink'])) {
            // Parse marker link
            $this->_parseLocationString($location, $options['markerLink']);
        }

        // Add individual marker to DNA
        $this->_dna[] = [
            'type' => 'markers',
            'locations' => $coords,
            'options' => $options,
        ];
    }

    // ========================================================================= //

    /**
     * Create marker click listener in JavaScript.
     */
    private function _markerClick($markerId, $options)
    {
        // Get optional callback
        $callback = ($options['markerClick'] ?? false);

        // If no callback was specified, bail
        if (!$callback) {
            return;
        }

        // Add marker callback to collection
        $this->_markerCallbacks[$markerId] = $callback;
    }

    // ========================================================================= //

    /**
     * Parse a dynamic marker string.
     *
     * @param $location
     * @param $string
     * @throws Exception
     * @throws LoaderError
     * @throws SyntaxError
     * @throws Throwable
     */
    private function _parseLocationString($location, &$string)
    {
        // Get view services
        $view = Craft::$app->getView();

        // If location is an object, parse string with object data
        if (is_object($location)) {
            $string = $view->renderObjectTemplate($string, $location);
        }

        // If location is an array, parse string with array data
        if (is_array($location)) {
            $string = $view->renderString($string, $location);
        }
    }

    // ========================================================================= //

    /**
     * Creates a single marker with a corresponding info window.
     *
     * @param $location
     * @param $options
     * @param $isCoords
     */
    private function _markerInfoWindow($location, &$options, $isCoords)
    {
        // Initialize marker data
        $infoWindow = [
            'mapId' => $this->id,
        ];

        // If location is a set of coordinates
        if ($isCoords) {

            // Set only the coordinates
            $infoWindow['coords'] = $location;

            // Create info window
            $this->_createInfoWindow($options, $infoWindow);

            // Our work here is done
            return;
        }

        // If location is an Address Model
        if ($location instanceof Address) {

            // Set address and coordinates
            $infoWindow['address'] = $location;
            $infoWindow['coords'] = $location->getCoords();

            // Create info window
            $this->_createInfoWindow($options, $infoWindow);

            // Our work here is done
            return;
        }

        // If location is a Visitor Model
        if ($location instanceof Visitor) {

            // Set address and coordinates
            $infoWindow['visitor'] = $location;
            $infoWindow['coords'] = $location->getCoords();

            // Create info window
            $this->_createInfoWindow($options, $infoWindow);

            // Our work here is done
            return;
        }

        // If location is a Location Model
        if ($location instanceof Location) {

            // Set address and coordinates
            $infoWindow['coords'] = $location->getCoords();

            // Create info window
            $this->_createInfoWindow($options, $infoWindow);

            // Our work here is done
            return;
        }

        // If location is an Element
        if ($location instanceof Element) {

            // Set both `element` and `entry` (or comparable)
            $elementType = $location::refHandle();
            $infoWindow['element'] = $location;
            $infoWindow[$elementType] = $location;

            // Ensure field option exists
            $options['field'] = ($options['field'] ?? false);

            // Optionally filter by specified field(s)
            if (is_array($options['field'])) {
                $filter = $options['field'];
            } else if (is_string($options['field'])) {
                $filter = [$options['field']];
            } else {
                $filter = false;
            }

            // Get all fields associated with Element
            /** @var FieldLayout $layout */
            $layout = $location->getFieldLayout();
            $fields = $layout->getFields();

            // Loop through all relevant fields
            foreach ($fields as $f) {
                // If filter field was specified but doesn't match, skip it
                if ($filter && !in_array($f->handle, $filter, true)) {
                    continue;
                }
                // If not an Address Field, skip it
                if (!($f instanceof AddressField)) {
                    continue;
                }
                // Get value of Address Field
                $address = $location->{$f->handle};
                // If no Address, skip
                if (!$address) {
                    continue;
                }

                // If address doesn't have valid coordinates, skip
                if (!$address->hasCoords()) {
                    continue;
                }

                // Set address, coordinates, and marker ID
                $infoWindow['address'] = $address;
                $infoWindow['coords'] = $address->getCoords();
                $infoWindow['markerId'] = "{$location->id}-{$f->handle}";

                // Create info window
                $this->_createInfoWindow($options, $infoWindow);

            }

            // Our work here is done
            return;
        }
    }

    /**
     * Creates the info window of a single marker.
     *
     * @param mixed $location
     * @param array $options
     * @param array $infoWindow
     */
    private function _createInfoWindow(&$options, $infoWindow)
    {
        // Get view services
        $view = Craft::$app->getView();

        // Attempt to render the Twig template
        try {

            // Render specified info window template
            $template = $view->renderTemplate($options['infoWindowTemplate'], $infoWindow);

        } catch (\Exception $e) {

            // Get the template root directory
            $root = Craft::$app->getPath()->getSiteTemplatesPath();
            $filepath = $view->resolveTemplate($options['infoWindowTemplate']);
            $filename = str_replace($root, '', $filepath);

            // Render error message template
            $template = $view->renderTemplate('google-maps/maps/info-window-error', [
                'filename' => $filename,
                'message' => $e->getMessage(),
            ], View::TEMPLATE_MODE_CP);

        }

        // Set rendered template as infoWindowOptions content
        $options['infoWindowOptions']['content'] = $template;

    }

}

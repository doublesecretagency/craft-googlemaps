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

use craft\base\Model;
use craft\helpers\Html;
use craft\helpers\Json;
use craft\helpers\Template;

use Twig\Markup;

/*
 * KILLING IN FAVOR OF NEW CHAINING METHOD
 */

/**
 * Class DynamicMap
 * @since 4.0.0
 */
class _OLD_DynamicMap extends Model
{

    /**
     * @var array Collection of data representing a map to be rendered.
     */
    public $dna = [];

    public function __construct($options = [], array $config = [])
    {
        // Set internal DNA based on specified options
        $this->dna = $options;

        parent::__construct($config);
    }

    public function __toString()
    {
        return 'To display a map, append `.html()` to the map object.';
    }

    /**
     * Render a fully-functional map container.
     *
     * @return Markup
     */
    public function html(): Markup
    {
        // Compile map container
        $html = Html::modifyTagAttributes('<div>Loading map...</div>', [
            'id' => $this->dna['id'],
            'class' => 'gm-map',
            'data-dna' => Json::encode($this->dna),
        ]);

        // Return Markup
        return Template::raw($html);
    }

    // Public Methods
    // =========================================================================

    /**
     * Add a marker to the map.
     */
    public function addMarker($options)
    {
        $this->dna['markers'][] = [
            // Configuration for marker
        ];
    }

    /**
     * Add an info window to the map.
     */
    public function addInfoWindow($options)
    {
        $this->dna['infoWindow'][] = [
            // Configuration for info window
        ];
    }

    // Private Methods
    // =========================================================================

}

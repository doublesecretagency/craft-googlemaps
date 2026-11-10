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

namespace doublesecretagency\googlemaps\web\assets;

use craft\web\AssetBundle;

/**
 * Loads the marker clustering library for maps which use it.
 *
 * @since 5.5.1
 */
class MarkerClustererAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        // Publish the whole resources folder
        $this->sourcePath = '@doublesecretagency/googlemaps/resources';

        // Load the marker clustering library
        $this->js = [
            'js/lib/markerclusterer-2.6.2.min.js',
        ];

        // Defer the file, matching the other map scripts
        $this->jsOptions = [
            'defer' => true,
        ];
    }

}

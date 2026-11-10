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
use doublesecretagency\googlemaps\GoogleMapsPlugin;

/**
 * Loads the plugin's own JavaScript for dynamic maps.
 *
 * @since 5.5.1
 */
class DynamicMapAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        // Publish the whole resources folder, including the libraries loaded on demand
        $this->sourcePath = '@doublesecretagency/googlemaps/resources';

        // Whether to use minified JavaScript files (settings may not exist during a Cloud build)
        $minifyJsFiles = (GoogleMapsPlugin::getInstance()?->getSettings()->minifyJsFiles ?? false);

        // Optionally use minified files
        $min = ($minifyJsFiles ? 'min.' : '');

        // Load JS files
        $this->js = [
            "js/googlemaps.{$min}js",
            "js/dynamicmap.{$min}js",
        ];

        // Defer each file, matching the Google Maps API
        $this->jsOptions = [
            'defer' => true,
        ];
    }

}

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
 * Class JsApiAsset
 * @since 4.0.0
 */
class JsApiAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->sourcePath = '@doublesecretagency/googlemaps/resources';
        $this->depends = [
            GoogleMapsAsset::class,
        ];

        // Whether to use minified JavaScript files
        $minifyJsFiles = (GoogleMapsPlugin::$plugin->getSettings()->minifyJsFiles ?? false);

        // Optionally use minified files
        $min = ($minifyJsFiles ? 'min.' : '');

        // Load JS files
        $this->js = [
            "js/googlemaps.{$min}js",
            "js/dynamicmap.{$min}js",
        ];
    }

}

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

        // Load the Google Maps API, then the plugin's own JavaScript
        $this->depends = [
            GoogleMapsAsset::class,
            DynamicMapAsset::class,
        ];
    }

}

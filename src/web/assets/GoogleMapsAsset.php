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
use doublesecretagency\googlemaps\helpers\GoogleMaps;

/**
 * Class GoogleMapsAsset
 * @since 4.0.0
 */
class GoogleMapsAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        // Load Google Maps API
        $this->js = [
            GoogleMaps::getApiUrl()
        ];

        $this->jsOptions = [
            'defer' => true,
        ];
    }

}

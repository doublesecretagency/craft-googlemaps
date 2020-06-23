<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014 Double Secret Agency
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
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        // Load Google Maps API
        $this->js = [
            GoogleMaps::getApiUrl()
        ];

        $this->jsOptions = [
//            'async' => true, // Possibly causing a race condition
            'defer' => true,
        ];
    }

}

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

namespace doublesecretagency\googlemaps\web\assets;

use craft\web\AssetBundle;
use doublesecretagency\googlemaps\helpers\GoogleMaps;

/**
 * Class JsApiAsset
 * @since 4.0.0
 */
class JsApiAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->sourcePath = '@doublesecretagency/googlemaps/resources';
        $this->depends = [
            GoogleMapsAsset::class,
        ];

        $this->js = [
            'js/googlemaps.js',
        ];
    }

}

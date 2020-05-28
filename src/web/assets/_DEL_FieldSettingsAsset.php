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
use craft\web\assets\cp\CpAsset;

/**
 * Class FieldSettingsAsset
 * @since 4.0.0
 */
class DELFieldSettingsAsset extends AssetBundle
{

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->sourcePath = '@doublesecretagency/googlemaps/resources';
        $this->depends = [CpAsset::class, GoogleMapsAsset::class];

//        $this->css = [
//            'css/fieldtype-settings.css',
//        ];
//
//        $this->js = [
//            'js/Sortable.min.js',
//            'js/fieldtype-settings.js',
//        ];
    }

}

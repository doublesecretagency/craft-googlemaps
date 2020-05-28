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
use craft\web\assets\vue\VueAsset;

/**
 * Class AddressFieldSettingsAsset
 * @since 4.0.0
 */
class AddressFieldSettingsAsset extends AssetBundle
{

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->sourcePath = '@doublesecretagency/googlemaps/web/assets/dist';
        $this->depends = [
            CpAsset::class,
//            VueAsset::class,
            GoogleMapsAsset::class
        ];

        $this->js = [
            'https://cdn.jsdelivr.net/npm/vue/dist/vue.js', // TEMP for development
            'js/address-settings.js',
        ];

    }

}

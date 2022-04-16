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
use craft\web\assets\cp\CpAsset;
use craft\web\assets\vue\VueAsset;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\helpers\GoogleMaps;

/**
 * Class AddressFieldAsset
 * @since 4.0.0
 */
class AddressFieldAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->sourcePath = '@doublesecretagency/googlemaps/web/assets/dist';
        $this->depends = [
            CpAsset::class,
            VueAsset::class,
        ];

        $this->css = [
            'css/address.css',
        ];

        $this->js = [
            'js/address.js',
            $this->_getApiUrl(),
        ];
    }

    /**
     * Generate a fully compiled URL for the Google API.
     *
     * @return string
     */
    private function _getApiUrl(): string
    {
        // Required API URL configuration options
        $params = [
            'libraries' => 'places',
            'callback' => 'initAddressField',
        ];

        // Get optional field parameters
        $fieldParams = GoogleMapsPlugin::$plugin->getSettings()->fieldParams;

        // If field parameters are specified, append them
        if ($fieldParams && is_array($fieldParams)) {
            $params = array_merge($params, $fieldParams);
        }

        // Return the fully compiled API URL
        return GoogleMaps::getApiUrl($params);
    }

}

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

namespace doublesecretagency\googlemaps\services;

use craft\base\Component;
use doublesecretagency\googlemaps\GoogleMapsPlugin;

/**
 * Class Api
 * @since 4.0.0
 */
class Api extends Component
{

    public static function getServerKey()
    {
        return GoogleMapsPlugin::$plugin->getSettings()->serverKey;
    }

    public static function getBrowserKey()
    {
        return GoogleMapsPlugin::$plugin->getSettings()->browserKey;
    }

    public static function setServerKey($serverKey)
    {
        $settings = GoogleMapsPlugin::$plugin->getSettings();
        $settings->setAttributes(['serverKey' => $serverKey], false);
    }

    public static function setBrowserKey($browserKey)
    {
        $settings = GoogleMapsPlugin::$plugin->getSettings();
        $settings->setAttributes(['browserKey' => $browserKey], false);
    }

}

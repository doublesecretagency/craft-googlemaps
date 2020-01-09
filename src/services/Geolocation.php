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

use Craft;
use craft\base\Component;

/**
 * Class Geolocation
 * @since 4.0.0
 */
class Geolocation extends Component
{

    public function getUserIP()
    {
        return Craft::$app->getRequest()->getUserIP();
    }

    public function geolocateIp($ip)
    {
        return true;
    }

}

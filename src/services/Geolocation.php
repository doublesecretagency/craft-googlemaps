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
use doublesecretagency\googlemaps\models\Ipstack;

/**
 * Class Geolocation
 * @since 4.0.0
 */
class Geolocation extends Component
{

    /**
     * @var string|null IP address of current user.
     */
    public $ip;

    /**
     * @inheritDoc
     */
    public function init()
    {
        // Get user's IP address
        $this->ip = Craft::$app->getRequest()->getUserIP();
    }

    public function geolocateUser()
    {
        $service = $this->_getServiceClass();
        return $service::geolocate($this->ip);
    }

    private function _getServiceClass()
    {
        $selectedService = 'ipstack';

        // Return selected geolocation service
        switch ($selectedService) {
            case 'ipstack':
                return Ipstack::class;
//            case 'maxmind':
//                return MaxMind::class;
            default:
                return false;
        }
    }

}

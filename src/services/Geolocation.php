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
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\models\Ipstack;
use doublesecretagency\googlemaps\models\Maxmind;

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
        // Get user's IP address // TODO: Uncomment original
//        $this->ip = Craft::$app->getRequest()->getUserIP();

        $this->ip = '1.1.1.1'; // TEMP
//        $this->ip = '8.8.8.8'; // TEMP
//        $this->ip = '138.197.201.111'; // TEMP
    }

    public function getVisitor($config = [])
    {
        // Set geolocation service
        $selected = GoogleMapsPlugin::$plugin->getSettings()->geolocationService;
        $service = ($config['service'] ?? $selected);

        // Get API model of the specified geolocation service
        $model = $this->_getApiModel($service);

        // If a valid service model is not available, bail
        if (!$model) {
            return false;
        }

        // Set IP address
        $ip = ($config['ip'] ?? $this->ip);

        // Return the geolocation results
        return $model::geolocate($ip);
    }

    private function _getApiModel($selected)
    {
        // Supported geolocation services
        $supported = [
            'ipstack' => Ipstack::class,
            'maxmind' => Maxmind::class,
        ];

        // Return selected geolocation service
        return ($supported[$selected] ?? false);
    }

}

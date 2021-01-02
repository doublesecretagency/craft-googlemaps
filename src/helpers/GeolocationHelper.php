<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2021 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\helpers;

use Craft;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\models\Ipstack;
use doublesecretagency\googlemaps\models\Maxmind;
use doublesecretagency\googlemaps\models\Visitor;

/**
 * Class GeolocationHelper
 * @since 4.0.0
 */
class GeolocationHelper
{

    /**
     * @var string|null IP address of current user.
     */
    public static $ip;

    /**
     * Set the IP address internally.
     */
    public function init()
    {
        // Get user's IP address
        static::$ip = Craft::$app->getRequest()->getUserIP();
    }

    /**
     * Conduct a geolocation lookup to determine the user's approximate location.
     *
     * @param array $config
     * @return Visitor|false
     */
    public static function getVisitor($config = [])
    {
        // Set geolocation service
        $selected = GoogleMapsPlugin::$plugin->getSettings()->geolocationService;
        $service = ($config['service'] ?? $selected);

        // Get API model of the specified geolocation service
        $model = static::_getApiModel($service);

        // If a valid service model is not available, bail
        if (!$model) {
            return false;
        }

        // Set IP address
        $ip = ($config['ip'] ?? static::$ip);

        // Return the geolocation results
        return $model::geolocate($ip);
    }

    /**
     * Load the selected API model.
     *
     * @param $selected
     * @return string|null
     */
    private static function _getApiModel($selected)
    {
        // Supported geolocation services
        $supported = [
            'ipstack' => Ipstack::class,
            'maxmind' => Maxmind::class,
        ];

        // Return selected geolocation service
        return ($supported[$selected] ?? null);
    }

}

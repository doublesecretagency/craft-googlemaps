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

namespace doublesecretagency\googlemaps\helpers;

use Craft;
use doublesecretagency\googlemaps\events\GeolocationEvent;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\models\Ipstack;
use doublesecretagency\googlemaps\models\Maxmind;
use doublesecretagency\googlemaps\models\Settings;
use doublesecretagency\googlemaps\models\Visitor;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class GeolocationHelper
 * @since 4.0.0
 */
class GeolocationHelper
{

    /**
     * Conduct a geolocation lookup to determine the user's approximate location.
     *
     * @param array $config
     * @return Visitor
     * @throws GuzzleException
     */
    public static function getVisitor(array $config = []): Visitor
    {
        /** @var Settings $settings */
        $settings = GoogleMapsPlugin::$plugin->getSettings();

        // Set geolocation service
        $service = ($config['service'] ?? $settings->geolocationService);

        // Get API model of the specified geolocation service
        /** @var Ipstack|Maxmind $model */
        $model = static::_getApiModel($service);

        // If a valid service model is not available, bail
        if (!$model) {
            // Set error message
            $error = Craft::t('google-maps', 'No geolocation service enabled.');
            // Return a basic Visitor Model
            return new Visitor([
                'service' => $service,
                'error' => $error,
            ]);
        }

        // Get IP address from config, or let Craft autodetect
        $ip = ($config['ip'] ?? Craft::$app->getRequest()->getUserIP());

        // If...
        if (
            !$ip ||                              // No IP, or
            ('127.0.0.1' === $ip) ||             // Local IP, or
            !filter_var($ip, FILTER_VALIDATE_IP) // Invalid IP
        ) {
            // Explicitly set to null
            $ip = null;
        }

        // Perform the geolocation
        $visitor = $model::geolocate($ip);

        // Trigger geolocation event
        GoogleMapsPlugin::$plugin->trigger(
            GoogleMapsPlugin::EVENT_AFTER_GEOLOCATION,
            new GeolocationEvent([
                'service' => $service,
                'ip' => $ip,
                'visitor' => $visitor,
            ])
        );

        // Return a Visitor Model
        return $visitor;
    }

    /**
     * Load the selected API model.
     *
     * @param string $selected
     * @return string|null
     */
    private static function _getApiModel(string $selected): ?string
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

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

namespace doublesecretagency\googlemaps\models;

/**
 * Class Ipstack
 * @since 4.0.0
 */
class Ipstack extends Location
{

    /**
     * @var string ipstack API endpoint.
     */
    private static $_endpoint = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * Get a Geolocation Model representing the user's location.
     *
     * @return Geolocation
     */
    public static function geolocate($ip)
    {
        $options = [
            'ip' => $ip,
//            'service' => '',
//            'country' => '',
//            'lat' => '',
//            'lng' => '',
//            'data' => '',
        ];

        return new Geolocation($options);
    }

}

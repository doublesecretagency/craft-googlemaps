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

namespace doublesecretagency\googlemaps\events;

use doublesecretagency\googlemaps\models\Visitor;
use yii\base\Event;

/**
 * Class GeolocationEvent
 * @since 4.0.0
 */
class GeolocationEvent extends Event
{

    /**
     * @var string Which service was used to detect the location.
     */
    public string $service;

    /**
     * @var string The visitor's IP address.
     */
    public string $ip;

    /**
     * @var Visitor The resulting Visitor location data.
     */
    public Visitor $visitor;

}

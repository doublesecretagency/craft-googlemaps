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
     * Which service was used to detect the location.
     *
     * @var string
     */
    public $service;

    /**
     * The visitor's IP address.
     *
     * @var string
     */
    public $ip;

    /**
     * The resulting Visitor location data.
     *
     * @var Visitor
     */
    public $visitor;

}

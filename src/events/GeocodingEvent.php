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

use yii\base\Event;

/**
 * Class GeocodingEvent
 * @since 4.0.0
 */
class GeocodingEvent extends Event
{

    /**
     * @var array Target used to generate geocoding query.
     */
    public array $target;

    /**
     * @var array Results of geocoding address lookup.
     */
    public array $results;

}

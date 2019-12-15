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

use craft\base\Model;

/**
 * Class Location
 * @since 4.0.0
 */
class Location extends Model
{

    public function __toString(): string
    {
        return "{$this->lat}, {$this->lng}";
    }

    /**
     * @var float|null Latitude of location.
     */
    public $lat;

    /**
     * @var float|null Longitude of location.
     */
    public $lng;

    /**
     * @var mixed|null The raw original data returned by the service which generated the model.
     */
    public $data;

    /**
     * Returns a formatted set of coordinates for this location.
     *
     * @return array
     */
    public function getCoords()
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }

}

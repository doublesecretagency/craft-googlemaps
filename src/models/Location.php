<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2020 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\models;

use craft\base\Model;

/**
 * Class Location
 * @since 4.0.0
 */
class Location extends Model
{

    /**
     * @var float|null Latitude of location.
     */
    public $lat;

    /**
     * @var float|null Longitude of location.
     */
    public $lng;

    /**
     * Automatically display coordinates as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->lat}, {$this->lng}";
    }

    /**
     * Whether this location contains a valid set of coordinates.
     *
     * @return bool
     */
    public function hasCoords(): bool
    {
        return (
            is_numeric($this->lat) &&
            is_numeric($this->lng)
        );
    }

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

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

    // ========================================================================= //

    /**
     * Automatically display coordinates as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->lat}, {$this->lng}";
    }

    // ========================================================================= //

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

    // ========================================================================= //

    public function linkToDirections($options = []): string
    {

        // @TODO: Reassess both of these functions

        // THESE SHOULD REALLY JUST BE BOTH USING THE GOOGLE URL API

        return 'https://www.google.com/maps/dir/14.9704507,102.1049483/@14.9702797,102.1036072,17z';


// linkToDirections($destinationAddress, $destinationTitle = null, $originTitle = false, $originAddress = false)
//
//        // If no destination address, bail
//        if (!$destinationAddress) {
//            return '#missing-address-field';
//        }
//        // If destination address isn't an Address model, bail
//        if (!is_a($destinationAddress, AddressModel::class)) {
//            return '#invalid-address-field';
//        }
//        // If starting address isn't an Address model, set it to false
//        if (!is_a($originAddress, AddressModel::class)) {
//            $originAddress = false;
//        }
//        // Compile URL
//        $url = 'https://www.google.com/maps/dir/?api=1&';
//        if ($originAddress) {
//            $url .= 'origin='.$this->_formatForDirections($originAddress, $originTitle).'&';
//        }
//        $url .= 'destination='.$this->_formatForDirections($destinationAddress, $destinationTitle);
//        // Return link
//        return $url;
    }

    public function linkToGoogleMap(): string
    {
        // If invalid coordinates, bail
        if (!$this->hasCoords()) {
            return '#invalid-address-coordinates';
        }

        // Get coordinates
        $coords = implode(',', $this->coords);

        $zoom = 17;

        $url = "https://www.google.com/maps/{$coords}/@{$coords},{$zoom}z";

        return $url;

//        // Get location name
//        if ($title) {
//            $place = urlencode((string) $title);
//        } else if ($address->isEmpty()) {
//            $place = $coords;
//        } else {
//            $place = urlencode((string) $address->format(true, true));
//        }


        $place = 'TEMPTITLE';

        // Return link
        return 'https://www.google.com/maps/place/'.$place.'/@'.$coords;
        // http://maps.google.com/maps?z=12&t=m&q=loc:38.9419+-78.3020


//        "https://www.google.com/maps/place/14°58'13.0"N+102°06'13.0"E/@14.9702849,102.1014185,17z"
    }

}

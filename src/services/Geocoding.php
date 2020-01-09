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

use doublesecretagency\googlemaps\models\Lookup as LookupModel;

/**
 * Class Geocoding
 * @since 4.0.0
 */
class Geocoding extends Api
{

    public static function lookup($target = null) {

        // If null,


        return new LookupModel;
    }






    public function testing() {
        // Server & browser keys are built in!
        return $this->serverKey;
//        return $this->browserKey;
    }

}

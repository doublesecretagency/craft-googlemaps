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

/**
 * Class MapsJavascript
 * @since 4.0.0
 */
class MapsJavascript extends Api
{

    public function testing() {
        // Server & browser keys are built in!
        return $this->serverKey;
//        return $this->browserKey;
    }

}

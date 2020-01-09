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
 * Class Settings
 * @since 4.0.0
 */
class Settings extends Model
{

    /**
     * @var string|null Google API Server Key.
     */
    public $serverKey = 'lorem';

    /**
     * @var string|null Google API Browser Key.
     */
    public $browserKey = 'ipsum';

    /**
     * @var string|null Currently selected geolocation service.
     */
    public $geolocation;

    /**
//     * @var string|null ipstack API Access Key.
//     */
//    public $ipstackAccessKey;
//
//    /**
//     * @var string|null MaxMind Service.
//     */
//    public $maxmindService;
//
//    /**
//     * @var string|null MaxMind User ID.
//     */
//    public $maxmindUserId;
//
//    /**
//     * @var string|null MaxMind License Key.
//     */
//    public $maxmindLicenseKey;

//    /**
//     * @var string|null Route to debug page.
//     */
//    public $debugRoute = 'smart-map/debug';

}

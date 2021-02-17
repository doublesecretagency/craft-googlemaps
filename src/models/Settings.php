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

namespace doublesecretagency\googlemaps\models;

use craft\base\Model;

/**
 * Class Settings
 * @since 4.0.0
 */
class Settings extends Model
{

    /**
     * @var string|null Google API Browser Key.
     */
    public $browserKey;

    /**
     * @var string|null Google API Server Key.
     */
    public $serverKey;

    /**
     * @var string|null Optional geolocation service. Can be 'ipstack', 'maxmind', or null.
     */
    public $geolocationService;

    /**
     * @var string|null ipstack API Access Key.
     */
    public $ipstackApiAccessKey;

    /**
     * @var string|null MaxMind User ID.
     */
    public $maxmindUserId;

    /**
     * @var string|null MaxMind License Key.
     */
    public $maxmindLicenseKey;

    /**
     * @var string|null MaxMind Service.
     */
    public $maxmindService;

    /**
     * @var bool Whether to log JS progress to console. Only relevant when rendering a dynamic map.
     */
    public $enableJsLogging = true;

}

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
    public ?string $browserKey = null;

    /**
     * @var string|null Google API Server Key.
     */
    public ?string $serverKey = null;

    /**
     * @var string|null Optional geolocation service. Can be 'ipstack', 'maxmind', or null.
     */
    public ?string $geolocationService = null;

    /**
     * @var string|null ipstack API Access Key.
     */
    public ?string $ipstackApiAccessKey = null;

    /**
     * @var string|null MaxMind User ID.
     */
    public ?string $maxmindUserId = null;

    /**
     * @var string|null MaxMind License Key.
     */
    public ?string $maxmindLicenseKey = null;

    /**
     * @var string|null MaxMind Service.
     */
    public ?string $maxmindService = null;

    /**
     * @var bool Whether to log JS progress to console. Only relevant when rendering a dynamic map.
     */
    public bool $enableJsLogging = true;

    /**
     * @var bool Whether to use minified front-end JavaScript files. Only relevant when rendering a dynamic map.
     */
    public bool $minifyJsFiles = false;

    /**
     * @var int Control the size of map UI elements in Address fields.
     */
    public int $fieldControlSize = 27;

    /**
     * @var array Additional optional parameters for configuring Address fields.
     */
    public array $fieldParams = [];

}

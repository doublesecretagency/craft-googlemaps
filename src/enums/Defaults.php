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

namespace doublesecretagency\googlemaps\enums;

/**
 * Class Defaults
 * @since 4.3.0
 */
abstract class Defaults
{

    /**
     * Default coordinates. (Bermuda Triangle)
     */
    public const COORDINATES = [
        'lat' => 32.3113966,
        'lng' => -64.7527469,
        'zoom' => 6
    ];

    /**
     * Default subfield configuration.
     */
    public const SUBFIELDCONFIG = [
        [
            'handle'       => 'name',
            'label'        => 'Name',
            'width'        => 100,
            'enabled'      => false,
            'autocomplete' => false,
            'required'     => false
        ],
        [
            'handle'       => 'street1',
            'label'        => 'Street Address',
            'width'        => 100,
            'enabled'      => true,
            'autocomplete' => true,
            'required'     => false
        ],
        [
            'handle'       => 'street2',
            'label'        => 'Apartment or Suite',
            'width'        => 100,
            'enabled'      => true,
            'autocomplete' => false,
            'required'     => false
        ],
        [
            'handle'       => 'city',
            'label'        => 'City',
            'width'        => 50,
            'enabled'      => true,
            'autocomplete' => false,
            'required'     => false
        ],
        [
            'handle'       => 'state',
            'label'        => 'State',
            'width'        => 15,
            'enabled'      => true,
            'autocomplete' => false,
            'required'     => false
        ],
        [
            'handle'       => 'zip',
            'label'        => 'Zip Code',
            'width'        => 35,
            'enabled'      => true,
            'autocomplete' => false,
            'required'     => false
        ],
        [
            'handle'       => 'neighborhood',
            'label'        => 'Neighborhood',
            'width'        => 100,
            'enabled'      => false,
            'autocomplete' => false,
            'required'     => false
        ],
        [
            'handle'       => 'county',
            'label'        => 'County or District',
            'width'        => 100,
            'enabled'      => false,
            'autocomplete' => false,
            'required'     => false
        ],
        [
            'handle'       => 'country',
            'label'        => 'Country',
            'width'        => 100,
            'enabled'      => true,
            'autocomplete' => false,
            'required'     => false
        ],
        [
            'handle'       => 'countryCode',
            'label'        => 'Country Code',
            'width'        => 100,
            'enabled'      => false,
            'autocomplete' => false,
            'required'     => false
        ],
        [
            'handle'       => 'placeId',
            'label'        => 'Place ID',
            'width'        => 100,
            'enabled'      => false,
            'autocomplete' => false,
            'required'     => false
        ],
    ];

}

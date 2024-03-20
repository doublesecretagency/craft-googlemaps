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

namespace doublesecretagency\googlemaps\gql\arguments;

use craft\gql\base\Arguments;
use doublesecretagency\googlemaps\gql\types\Raw;
use GraphQL\Type\Definition\Type;

/**
 * Class AddressFields
 * @since 4.3.0
 */
class AddressFields extends Arguments
{

    /**
     * @inheritdoc
     */
    public static function getArguments(): array
    {
        return [
            'id' => [
                'type' => Type::id(),
                'description' => "ID of the Address.",
            ],
            'elementId' => [
                'type' => Type::int(),
                'description' => "Element ID of the element containing the Address.",
            ],
            'fieldId' => [
                'type' => Type::int(),
                'description' => "Field ID of the Address field.",
            ],
            'formatted' => [
                'type' => Type::string(),
                'description' => "A nicely-formatted single-line interpretation of the address, provided by Google during the initial geocoding.",
            ],
            'raw' => [
                'type' => Raw::getType(),
                'description' => "The original data used to create this Address Model. Contains the full JSON response from the original Google API call.",
            ],
            'name' => [
                'type' => Type::string(),
                'description' => "The location's official name. Commonly used for landmarks and business names.",
            ],
            'street1' => [
                'type' => Type::string(),
                'description' => "The first line of the street address. Usually contains the street name & number of the location.",
            ],
            'street2' => [
                'type' => Type::string(),
                'description' => "The second line of the street address. Usually contains the apartment, unit, or suite number.",
            ],
            'city' => [
                'type' => Type::string(),
                'description' => "The city. (aka: town)",
            ],
            'state' => [
                'type' => Type::string(),
                'description' => "The state. (aka: province)",
            ],
            'zip' => [
                'type' => Type::string(),
                'description' => "The zip code. (aka: postal code)",
            ],
            'neighborhood' => [
                'type' => Type::string(),
                'description' => "The neighborhood.",
            ],
            'county' => [
                'type' => Type::string(),
                'description' => "The local county. (aka: district)",
            ],
            'country' => [
                'type' => Type::string(),
                'description' => "The country. (aka: nation)",
            ],
            'countryCode' => [
                'type' => Type::string(),
                'description' => "The country code.",
            ],
            'placeId' => [
                'type' => Type::string(),
                'description' => "The official `place_id` as specified by the Google API.",
            ],
            'distance' => [
                'type' => Type::float(),
                'description' => "The distance between the Address and a proximity search target. If not conducting a proximity search, value will be `null`.",
            ],
            'zoom' => [
                'type' => Type::int(),
                'description' => "Zoom level of the map as shown in the control panel.",
            ],
            'lat' => [
                'type' => Type::float(),
                'description' => "Latitude of location.",
            ],
            'lng' => [
                'type' => Type::float(),
                'description' => "Longitude of location.",
            ],
        ];
    }

}

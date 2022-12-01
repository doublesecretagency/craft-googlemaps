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

namespace doublesecretagency\googlemaps\gql\types;

use craft\gql\base\SingularTypeInterface;
use craft\gql\GqlEntityRegistry;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;

/**
 * Class Address
 * @since 4.3.0
 */
class Address extends ScalarType implements SingularTypeInterface
{

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'GoogleMaps_Address';
    }

    /**
     * Returns a singleton instance to ensure one type per schema.
     *
     * @return Type
     */
    public static function getType(): Type
    {
        // Return the Address type
        return GqlEntityRegistry::getEntity(static::getName())
            ?: GqlEntityRegistry::createEntity(self::getName(), new ObjectType([
                'name' => static::getName(),
                'description' => "An Address field as defined by the Google Maps plugin.",
                'fields' => [
                    'id' => [
                        'type' => Type::int(),
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
                        'description' => "The original data used to create this Address Model. Contains the full response from the original Google API call.",
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
                    'county' => [
                        'type' => Type::string(),
                        'description' => "The local county. (aka: district)",
                    ],
                    'country' => [
                        'type' => Type::string(),
                        'description' => "The country. (aka: nation)",
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
                ]
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function serialize($value)
    {
        if (empty($value)) {
            return null;
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function parseValue($value)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {
        return $valueNode->value;
    }

}

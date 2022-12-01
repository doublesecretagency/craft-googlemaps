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
 * Class Raw
 * @since 4.3.0
 */
class Raw extends ScalarType implements SingularTypeInterface
{

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'GoogleMaps_Raw';
    }

    /**
     * Returns a singleton instance to ensure one type per schema.
     *
     * @return Type
     */
    public static function getType(): Type
    {
        // Return the Raw type
        return GqlEntityRegistry::getEntity(static::getName())
            ?: GqlEntityRegistry::createEntity(self::getName(), new ObjectType([
                'name' => static::getName(),
                'description' => "Original data from the Google API call which generated the Address.",
                'fields' => [
                    'address_components' => [
                        'type' => Type::listOf(Component::getType()),
                        'description' => "An array containing the separate components applicable to this address."
                    ],
                    'formatted_address' => [
                        'type' => Type::string(),
                        'description' => "A string containing the human-readable address of this location."
                    ],
                    'geometry' => [
                        'type' => Geometry::getType(),
                        'description' => "The place's geometry-related information."
                    ],
                    'name' => [
                        'type' => Type::string(),
                        'description' => "The place's name."
                    ],
                    'place_id' => [
                        'type' => Type::string(),
                        'description' => "A textual identifier that uniquely identifies a place."
                    ],
                    'html_attributions' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => "An array of attributions to display when showing the search results."
                    ],
                ],
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

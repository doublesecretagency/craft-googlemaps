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
 * Class Component
 * @since 4.3.0
 */
class Component extends ScalarType implements SingularTypeInterface
{

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'GoogleMaps_Component';
    }

    /**
     * Returns a singleton instance to ensure one type per schema.
     *
     * @return Type
     */
    public static function getType(): Type
    {
        // Return the Component type
        return GqlEntityRegistry::getEntity(static::getName())
            ?: GqlEntityRegistry::createEntity(self::getName(), new ObjectType([
                'name' => static::getName(),
                'description' => "Each component of the raw address data.",
                'fields' => [
                    'long_name' => [
                        'type' => Type::string(),
                        'description' => "The full text description or name of the address component.",
                    ],
                    'short_name' => [
                        'type' => Type::string(),
                        'description' => "An abbreviated textual name for the address component, if available.",
                    ],
                    'types' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => "An array indicating the type of the address component.",
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

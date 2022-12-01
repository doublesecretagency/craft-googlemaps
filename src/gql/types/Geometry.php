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
 * Class Geometry
 * @since 4.3.0
 */
class Geometry extends ScalarType implements SingularTypeInterface
{

    /**
     * @inheritdoc
     */
    public static function getName(): string
    {
        return 'GoogleMaps_Geometry';
    }

    /**
     * Returns a singleton instance to ensure one type per schema.
     *
     * @return Type
     */
    public static function getType(): Type
    {
        // Return the Geometry type
        return GqlEntityRegistry::getEntity(static::getName())
            ?: GqlEntityRegistry::createEntity(self::getName(), new ObjectType([
                'name' => static::getName(),
                'description' => "The place's geometry-related information.",
                'fields' => [
                    'location' => [
                        'type' => Location::getType(),
                        'description' => "The latitude and longitude of the place.",
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

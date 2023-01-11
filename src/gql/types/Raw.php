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
     * @inheritdoc
     */
    public static function getType(): Type
    {
        // Return the GoogleMaps_Raw type
        return GqlEntityRegistry::getEntity(self::getName())
            ?: GqlEntityRegistry::createEntity(self::getName(), new self([
                'name' => self::getName(),
                'description' => "The original data used to create an Address Model. Contains the full JSON response from the original Google API call.",
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

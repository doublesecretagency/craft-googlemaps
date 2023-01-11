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
use doublesecretagency\googlemaps\gql\arguments\AddressFields;
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
     * @inheritdoc
     */
    public static function getType(): Type
    {
        // Return the GoogleMaps_Address type
        return GqlEntityRegistry::getEntity(self::getName())
            ?: GqlEntityRegistry::createEntity(self::getName(), new ObjectType([
                'name' => self::getName(),
                'description' => "An Address field as defined by the Google Maps plugin.",
                'fields' => function() {
                    return AddressFields::getArguments();
                },
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

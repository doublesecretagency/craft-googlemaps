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

namespace doublesecretagency\googlemaps\gql\types\input;

use craft\gql\GqlEntityRegistry;
use doublesecretagency\googlemaps\gql\arguments\AddressFields;
use GraphQL\Type\Definition\InputObjectType;

/**
 * Class AddressInput
 * @since 4.3.0
 */
class AddressInput extends InputObjectType
{

    /**
     * Return the name of the GraphQL input type.
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'GoogleMaps_AddressInput';
    }

    /**
     * Return an instance of the GraphQL input type.
     *
     * @return mixed
     */
    public static function getType(): mixed
    {
        return GqlEntityRegistry::getEntity(self::getName())
            ?: GqlEntityRegistry::createEntity(self::getName(), new InputObjectType([
                'name' => self::getName(),
                'description' => "Input for an Address field as defined by the Google Maps plugin.",
                'fields' => function() {
                    return AddressFields::getArguments();
                },
            ])
        );
    }

}

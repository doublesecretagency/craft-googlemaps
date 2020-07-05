<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2020 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\records;

use craft\db\ActiveRecord;

/**
 * Class Address
 * @since 4.0.0
 *
 * @property int $id ID of address.
 * @property int $elementId ID of element containing address.
 * @property int $fieldId ID of field containing address.
 * @property string $formatted Properly formatted address according to the Google API.
 * @property string $raw Complete raw JSON address info from Google API.
 * @property string $street1 Street name and number.
 * @property string $street2 Apartment or suite number.
 * @property string $city City.
 * @property string $state State (or province, territory, etc).
 * @property string $zip Zip code (or postal code, etc).
 * @property string $country Country.
 * @property float $distance Distance from another specified point.
 * @property float $lat Latitude of location.
 * @property float $lng Longitude of location.
 * @property int $zoom Zoom level of map.
 */
class Address extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%googlemaps_addresses}}';
    }

}

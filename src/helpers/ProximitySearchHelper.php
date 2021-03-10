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

namespace doublesecretagency\googlemaps\helpers;

use Craft;
use craft\elements\db\ElementQueryInterface;
use doublesecretagency\googlemaps\fields\AddressField;

/**
 * Class ProximitySearchHelper
 * @since 4.0.0
 */
class ProximitySearchHelper
{

    /**
     * @var ElementQueryInterface The actual element query (passed by reference) which is being called.
     */
    private static $_query;

    /**
     * @var AddressField The field used to generate a proximity search. (aka "myAddressField")
     */
    private static $_field;

    /**
     * Modify the existing elements query to perform a proximity search.
     *
     * @param $query
     * @param $options
     * @param $field
     */
    public static function modifyElementsQuery($query, $options, $field)
    {
        // Internalize objects
        static::$_query = $query;
        static::$_field = $field;

        // Join with plugin table
        static::$_query->subQuery->leftJoin(
            '{{%googlemaps_addresses}} addresses',
            '[[addresses.elementId]] = [[elements.id]] AND [[addresses.fieldId]] = :fieldId',
            [':fieldId' => $field->id]
        );

        // Apply all proximity search options
        if (isset($options['target'])) {
            static::_applyProximitySearch($options);
        }

        // Apply 'subfields' option
        if (isset($options['subfields'])) {
            static::_applySubfields($options['subfields']);
        }

        // Apply 'requireCoords' option
        if (isset($options['requireCoords'])) {
            static::_applyRequireCoords($options['requireCoords']);
        }
    }

    // ========================================================================= //

    /**
     * Conduct a target-based proximity search.
     *
     * @param array $options
     */
    private static function _applyProximitySearch(array $options)
    {
        // Set defaults
        $default = [
            'range'  => 500,
            'units'  => 'mi',
        ];

        // Get specified values
        $target = ($options['target'] ?? null);
        $range  = ($options['range']  ?? $default['range']);
        $units  = ($options['units']  ?? $default['units']);

        // If no target exists, bail
        if (!$target) {
            return;
        }

        // Ensure range is valid
        if (!is_numeric($range) || $range < 1) {
            $range = $default['range'];
        }

        // Ensure units are valid
        $validUnits = ['mi', 'km', 'miles', 'kilometers'];
        if (!in_array($units, $validUnits, true)) {
            $units = $default['units'];
        }

        // Retrieve the starting coordinates from the specified target
        $coords = static::_getTargetCoords($target);

        // If no coordinates, use default
        if (!$coords) {
            $coords = AddressField::DEFAULT_COORDINATES;
        }

        // Implement haversine formula via SQL
        $haversine = static::_haversineSql(
            $coords['lat'] ?? 0,
            $coords['lng'] ?? 0,
            $units
        );

        // Modify subquery
        static::$_query->subQuery
            ->addSelect(
                "{$haversine} AS [[distance]]"
            )
            ->andWhere(
                '[[addresses.fieldId]] = :fieldId',
                [':fieldId' => static::$_field->id]
            )
        ;

        // Briefly store the distance under the field handle
        $fieldHandle = static::$_field->handle;
        static::$_query->query
            ->addSelect(
                "[[subquery.distance]] AS [[{$fieldHandle}]]"
            )
        ;

        // Handle distance based on database type
        if (Craft::$app->getDb()->getIsMysql()) {
            // Configure for MySQL
            static::$_query->subQuery
                ->having(
                    '[[distance]] <= :range',
                    [':range' => $range]
                )
            ;
        } else {
            // Configure for Postgres
            static::$_query->query
                ->andWhere(
                    '[[distance]] <= :range',
                    [':range' => $range]
                )
            ;
        }
    }

    /**
     * Filter by contents of specific subfields.
     *
     * @param array|null $subfields
     */
    private static function _applySubfields($subfields)
    {
        // If not an array, bail
        if (!is_array($subfields)) {
            return;
        }

        // Complete list of valid subfields
        $whitelist = ['street1','street2','city','state','zip','country','lat','lng'];

        // Loop through specified subfields
        foreach ($subfields as $subfield => $value) {

            // If not a valid subfield, skip
            if (!in_array($subfield, $whitelist, true)) {
                continue;
            }

            // Force the value to be an array
            if (is_string($value) || is_float($value)) {
                $value = [$value];
            }

            // If value is still not an array, skip
            if (!is_array($value)) {
                continue;
            }

            // Initialize WHERE clause
            $where = [];

            // Loop through filter values
            foreach ($value as $filter) {
                $where[] = [$subfield => $filter];
            }

            // Re-organize WHERE filters
            if (1 == count($where)) {
                $where = $where[0];
            } else {
                array_unshift($where, 'or');
            }

            // Append WHERE clause to subquery
            static::$_query->subQuery->andWhere($where);
        }
    }

    /**
     * Filter to only include Addresses which have valid coordinates.
     *
     * @param bool $requireCoords
     */
    private static function _applyRequireCoords(bool $requireCoords)
    {
        // If coordinates are not required, bail
        if (!$requireCoords) {
            return;
        }

        // Omit Addresses with missing or incomplete coordinates
        static::$_query->subQuery->andWhere(['not', [
            'or',
            ['lat' => null],
            ['lng' => null]
        ]]);
    }

    // ========================================================================= //

    /**
     * Based on the target provided, determine a center point for the proximity search.
     *
     * @param mixed $target
     * @return array|false Set of coordinates to use as center of proximity search.
     */
    private static function _getTargetCoords($target)
    {
        // Get coordinates based on type of target specified
        switch (gettype($target)) {

            // Target specified as a string
            case 'string':

                // Perform geocoding based on target string, return coordinates
                return GoogleMaps::lookup($target)->coords();

            // Target specified as an array
            case 'array':

                // If coordinates were specified directly, return them as-is
                if (isset($target['lat']) && isset($target['lng'])) {
                    return $target;
                }
                // Perform geocoding based on target array, return coordinates
                return GoogleMaps::lookup($target)->coords();

        }

        // Something's not right, return default coordinates
        return AddressField::DEFAULT_COORDINATES;
    }

    /**
     * Apply haversine formula via SQL.
     *
     * @param float $lat
     * @param float $lng
     * @param string $units
     * @return string The haversine formula portion of an SQL query.
     */
    private static function _haversineSql(float $lat, float $lng, string $units = 'mi'): string
    {
        // Determine radius
        $radius = static::haversineRadius($units);

        // Calculate haversine formula
        return "(
            {$radius} * acos(
                cos(radians({$lat})) *
                cos(radians([[addresses.lat]])) *
                cos(radians([[addresses.lng]]) - radians({$lng})) +
                sin(radians({$lat})) *
                sin(radians([[addresses.lat]]))
            )
        )";
    }

    /**
     * Get the radius of Earth as measured in the specified units.
     *
     * @param string $units
     * @return int
     */
    public static function haversineRadius(string $units): int
    {
        switch ($units) {
            case 'km':
            case 'kilometers':
                return 6371;
            case 'mi':
            case 'miles':
            default:
                return 3959;
        }
    }

}

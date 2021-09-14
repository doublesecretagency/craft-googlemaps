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
use yii\base\ExitException;

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
     * @var array|string Optionally filter results by individual subfields.
     */
    private static $_subfieldFilter;

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
        static::$_subfieldFilter = ($options['subfields'] ?? []);

        // Join with plugin table
        static::$_query->subQuery->leftJoin(
            '{{%googlemaps_addresses}} addresses',
            '[[addresses.elementId]] = [[elements.id]] AND [[addresses.fieldId]] = :fieldId',
            [':fieldId' => $field->id]
        );

        // Filter by field ID
        static::$_query->subQuery->andWhere(
            '[[addresses.fieldId]] = :fieldId',
            [':fieldId' => $field->id]
        );

        // Apply all proximity search options
        static::_applyProximitySearch($options);

        // Apply 'subfields' option
        if (static::$_subfieldFilter) {
            static::_applySubfields(static::$_subfieldFilter);
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
        // Set proximity search defaults
        $default = [
            'range'  => 500,
            'units'  => 'mi',
        ];

        // Get specified target
        $target = ($options['target'] ?? null);

        // If no target is specified
        if (!$target) {
            // Modify subquery, append empty distance column
            static::$_query->subQuery->addSelect(
                "NULL AS [[distance]]"
            );
            // Bail
            return;
        }

        // Retrieve the starting coordinates from the specified target
        $coords = static::_getTargetCoords($target);

        // If no coordinates, use default
        if (!$coords) {
            $coords = AddressField::DEFAULT_COORDINATES;
        }

        // Get specified units
        $units = ($options['units'] ?? $default['units']);

        // Ensure units are valid
        $validUnits = ['mi', 'km', 'miles', 'kilometers'];
        if (!in_array($units, $validUnits, true)) {
            $units = $default['units'];
        }

        // Implement haversine formula via SQL
        $haversine = static::_haversineSql(
            $coords['lat'],
            $coords['lng'],
            $units
        );

        // Get specified range
        $range = ($options['range'] ?? $default['range']);

        // Ensure range is valid
        if (!is_numeric($range) || $range < 1) {
            $range = $default['range'];
        }

        // Modify subquery, sort by nearest
        static::$_query->subQuery->addSelect(
            "{$haversine} AS [[distance]]"
        );

        // Briefly store the distance under the field handle
        $fieldHandle = static::$_field->handle;
        static::$_query->query->addSelect(
            "[[subquery.distance]] AS [[{$fieldHandle}]]"
        );

        // Handle distance based on database type
        if (Craft::$app->getDb()->getIsMysql()) {
            // Configure for MySQL
            static::$_query->subQuery->having(
                '[[distance]] <= :range',
                [':range' => $range]
            );
        } else {
            // Configure for Postgres
            static::$_query->query->andWhere(
                '[[distance]] <= :range',
                [':range' => $range]
            );
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
     * Perform a geocoding address lookup to determine the coordinates of a given target.
     *
     * If necessary, this method will reconfigure the subfields filter
     * as part of the subfield filter fallback mechanism.
     *
     * @param mixed $target
     * @return array|false Set of coordinates based on specified target.
     * @throws ExitException
     */
    private static function _lookupCoords($target)
    {
        // Perform geocoding based on specified target
        $address = GoogleMaps::lookup($target)->one();

        // Get coordinates of specified target
        $coords = ($address ? $address->getCoords() : false);

        // If fallback filter is disabled, bail with coordinates
        if ('fallback' !== static::$_subfieldFilter) {
            return $coords;
        }

        // If address contains a valid street address, bail with coordinates
        if ($address->street1 ?? false) {
            return $coords;
        }

        // If no raw address components exist, bail with coordinates
        if (!($address->raw['address_components'] ?? false)) {
            return $coords;
        }

        // Determine type of address result
        $type = ($address->raw['types'][0] ?? false);

//        Craft::dd($type);

        // List of narrowly focused location types
        // will be exempt from the fallback filter
        $focusedTypes = [
            'premise',      // "123 Main Street"
            'route',        // "Western Blvd"
            'intersection', // "Western Blvd and 22nd Street"
            'locality',     // "Los Angeles"
            'neighborhood', // "Venice, California"
        ];
        /**
         * We may need to add to this list of focused types.
         * This is a list of narrowly defined areas, which
         * can be used for an ACCURATE proximity search.
         *
         * Broadly focused types lack the same level of
         * accuracy, and may be subjected to the subfield
         * fallback filter mechanism.
         *
         * More information:
         * https://plugins.doublesecretagency.com/google-maps/guides/filter-by-subfields/#subfield-filter-fallback
         */

        // If the location type is narrowly focused, bail with coordinates
        if (in_array($type, $focusedTypes)) {
            return $coords;
        }

        /**
         * Still here? It's time to configure the subfields filter.
         */

        // Restructure the raw address components
        $address = GeocodingHelper::restructureComponents($address->raw ?? []);

        // Configure subfield filter
        $filter = [
            'city'    => $address['city'],
            'state'   => $address['state'],
            'zip'     => $address['zip'],
            'county'  => $address['county'],
            'country' => $address['country'],
        ];

        // Grossly simplify the target string
        $t = (is_string($target) ? $target : ($target['address'] ?? ''));
        $t = trim(strtolower($t));

        // Prune unspecified subfields
        foreach ($filter as $subfield => $value) {

            // If no value was specified
            if (null === $value) {
                // Remove from filter
                unset($filter[$subfield]);
                // Continue to next
                continue;
            }

            // Grossly simplify the filter value
            $v = trim(strtolower($value));

            // If target and value are identical, filter by THIS PART ONLY!
            if ($t === $v) {
                $filter = [$subfield => $value];
                break;
            }

        }

        // If subfield filter was properly configured, set it
        if ($filter) {
            static::$_subfieldFilter = $filter;
        }

        // Return coordinates
        return $coords;
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

                // Return coordinates based on geocoding lookup
                return static::_lookupCoords($target);

            // Target specified as an array
            case 'array':

                // If coordinates were specified directly, return them as-is
                if (isset($target['lat']) && isset($target['lng'])) {
                    return $target;
                }
                // Return coordinates based on geocoding lookup
                return static::_lookupCoords($target);

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

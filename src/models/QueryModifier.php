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

namespace doublesecretagency\googlemaps\models;

use craft\base\Model;
use craft\elements\db\ElementQueryInterface;
use doublesecretagency\googlemaps\fields\AddressField;
use doublesecretagency\googlemaps\helpers\GoogleMaps;

/**
 * Class QueryModifier
 * @since 4.0.0
 */
class QueryModifier extends Model
{

    /**
     * @var ElementQueryInterface The actual element query (passed by reference) which is being called.
     */
    private $_query;

    /**
     * @var AddressField The field used to generate a proximity search. (aka "myAddressField")
     */
    private $_field;

    /**
     * Build a Query Modifier from scratch.
     *
     * @param $query
     * @param $options
     * @param $field
     * @param array $config
     */
    public function __construct($query, $options, $field, array $config = [])
    {
        // Internalize objects
        $this->_query = $query;
        $this->_field = $field;

        // Join with plugin table
        $this->_query->subQuery->leftJoin(
            '{{%googlemaps_addresses}} addresses',
            '[[addresses.elementId]] = [[elements.id]] AND [[addresses.fieldId]] = :fieldId',
            [':fieldId' => $field->id]
        );

        // Apply all proximity search options
        if (isset($options['target'])) {
            $this->_applyProximitySearch($options);
        }

        // Apply 'subfields' option
        if (isset($options['subfields'])) {
            $this->_applySubfields($options['subfields']);
        }

        // Apply 'requireCoords' option
        if (isset($options['requireCoords'])) {
            $this->_applyRequireCoords($options['requireCoords']);
        }

        // Close the loop
        parent::__construct($config);
    }

    // ========================================================================= //

    /**
     * Conduct a target-based proximity search.
     *
     * @param array $options
     */
    private function _applyProximitySearch($options)
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
        $coords = $this->_getTargetCoords($target);

        // Implement haversine formula via SQL
        $haversine = $this->_haversineSql(
            $coords['lat'],
            $coords['lng'],
            $units
        );

        // Modify subquery
        $this->_query->subQuery
            ->addSelect(
                "{$haversine} AS [[distance]]"
            )
            ->andWhere(
                '[[addresses.fieldId]] = :fieldId',
                [':fieldId' => $this->_field->id]
            )
            ->having(
                '[[distance]] <= :range',
                [':range' => $range]
            )
        ;

        // Briefly store the distance under the field handle
        $this->_query->query
            ->addSelect(
                "[[subquery.distance]] AS [[{$this->_field->handle}]]"
            )
        ;
    }

    /**
     * Filter by contents of specific subfields.
     *
     * @param array|null $subfields
     */
    private function _applySubfields($subfields)
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
            $this->_query->subQuery->andWhere($where);
        }
    }

    /**
     * Filter to only include Addresses which have valid coordinates.
     *
     * @param bool $requireCoords
     */
    private function _applyRequireCoords(bool $requireCoords)
    {
        // If coordinates are not required, bail
        if (!$requireCoords) {
            return;
        }

        // Omit Addresses with missing or incomplete coordinates
        $this->_query->subQuery->andWhere(['not', [
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
     * @return array Set of coordinates to use as center of proximity search.
     */
    private function _getTargetCoords($target)
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
    private function _haversineSql(float $lat, float $lng, string $units = 'mi'): string
    {
        // Determine unit of measurement
        switch ($units) {
            case 'km':
            case 'kilometers':
                $unitVal = 6371;
                break;
            case 'mi':
            case 'miles':
            default:
                $unitVal = 3959;
                break;
        }

        // Calculate haversine formula
        return "(
            {$unitVal} * acos(
                cos(
                    radians({$lat})
                ) * cos(
                    radians([[addresses.lat]])
                ) * cos(
                    radians([[addresses.lng]]) - radians({$lng})
                ) + sin(
                    radians({$lat})
                ) * sin(
                    radians([[addresses.lat]])
                )
            )
        )";
    }

}

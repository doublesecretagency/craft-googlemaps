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

use Craft;
use craft\base\Model;
use craft\elements\db\ElementQueryInterface;

/**
 * Class QueryParser
 * @since 4.0.0
 */
class QueryParser extends Model
{

    /**
     * @var ElementQueryInterface
     */
    private $_query;

    public function __construct($query, $options, array $config = [])
    {
        // Internalize query
        $this->_query = $query;

        // Join with plugin table
        $this->_query->subQuery->leftJoin('{{%googlemaps_addresses}} addresses', '[[addresses.elementId]] = [[elements.id]]');

        // Apply 'target' option
        if (isset($options['target'])) {
            $this->_applyTarget($options['target']);
        }

        // Apply 'range' option
        if (isset($options['range'])) {
            $this->_applyRange($options['range']);
        }

        // Apply 'units' option
        if (isset($options['units'])) {
            $this->_applyUnits($options['units']);
        }

        // Apply 'fields' option
        if (isset($options['fields'])) {
            $this->_applyFields($options['fields']);
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
     */
    private function _applyTarget($target)
    {
    }

    /**
     */
    private function _applyRange($range)
    {
    }

    /**
     */
    private function _applyUnits($units)
    {
    }

    /**
     */
    private function _applyFields($fields)
    {
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
            if (!in_array($subfield, $whitelist)) {
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

}

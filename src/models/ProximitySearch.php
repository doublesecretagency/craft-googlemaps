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

namespace doublesecretagency\googlemaps\models;

use Craft;
use craft\base\Model;
use craft\db\Connection;
use craft\elements\db\ElementQueryInterface;
use craft\fields\Number;
use doublesecretagency\googlemaps\enums\Defaults;
use doublesecretagency\googlemaps\fields\AddressField;
use doublesecretagency\googlemaps\helpers\GeocodingHelper;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use yii\web\HttpException;

/**
 * Class ProximitySearch
 * @since 5.0.0
 */
class ProximitySearch extends Model
{

    /**
     * @var string Default units of proximity search.
     */
    private string $_defaultUnits = 'mi';

    /**
     * @var ElementQueryInterface|null Element query being adjusted to perform the proximity search.
     */
    public ?ElementQueryInterface $query = null;

    /**
     * @var AddressField|null Address field being queried in proximity search.
     */
    public ?AddressField $field = null;

    /**
     * @var array|null Array of options for configuring the proximity search.
     */
    public ?array $options = null;

    // ========================================================================= //

    /**
     * @inerhitdoc
     * @throws HttpException
     */
    public function init(): void
    {
        // If no field or options, bail
        if (!$this->field || !$this->options) {
            return;
        }

        // Join with plugin table
        $this->query->subQuery->leftJoin(
            '{{%googlemaps_addresses}} gm_addresses',
            '[[gm_addresses.elementId]] = [[elements.id]] AND [[gm_addresses.siteId]] = [[elements_sites.siteId]] AND [[gm_addresses.fieldId]] = :fieldId',
            [':fieldId' => $this->field->id]
        );

        // Apply each option
        $this->_applyRange();
        $this->_applyUnits();
        $this->_applyTarget();
        $this->_applySubfields();
        $this->_applyRequireCoords();
        $this->_applyReverseRadius();
    }

    // ========================================================================= //

    /**
     * Apply the `range` option.
     */
    private function _applyRange(): void
    {
        // Get specified range
        $range = ($this->options['range'] ?? null);

        // If range is not valid, nullify it
        if (!is_numeric($range) || $range <= 0) {
            $range = null;
        }

        // Update options array
        $this->options['range'] = $range;
    }

    /**
     * Apply the `units` option.
     */
    private function _applyUnits(): void
    {
        // Get specified units
        $units = ($this->options['units'] ?? $this->_defaultUnits);

        // Ensure units are valid
        $validUnits = ['mi', 'km', 'miles', 'kilometers'];
        if (!in_array($units, $validUnits, true)) {
            $units = $this->_defaultUnits;
        }

        // Update options array
        $this->options['units'] = $units;
    }

    /**
     * Apply the `target` option.
     */
    private function _applyTarget(): void
    {
        // Get specified target
        $target = ($this->options['target'] ?? null);

        // If no target is specified
        if (!$target) {
            // Modify subquery, append empty distance column
            $this->query->subQuery->addSelect(
                "NULL AS [[distance]]"
            );
            // Bail
            return;
        }

        // Retrieve the starting coordinates from the specified target
        $coords = $this->_getTargetCoords($target);

        // If no coordinates, use default
        if (!$coords) {
            $coords = Defaults::COORDINATES;
        }

        // Get coordinates
        $lat = ($coords['lat'] ?? Defaults::COORDINATES['lat']);
        $lng = ($coords['lng'] ?? Defaults::COORDINATES['lng']);

        // Implement haversine formula via SQL
        $haversine = $this->_haversineSql($lat, $lng);

        // Modify subquery, sort by nearest
        $this->query->subQuery->addSelect(
            "{$haversine} AS [[distance]]"
        );

        // Briefly store the distance under the field handle
        $this->query->query->addSelect(
            "[[distance]] AS [[{$this->field->handle}]]"
        );

        // Get the reverse radius
        $reverseRadius = ($this->options['reverseRadius'] ?? null);

        // If applying a valid reverse radius, bail
        if ($reverseRadius && is_string($reverseRadius)) {
            return;
        }

        // If a range was specified
        if ($this->options['range']) {
            $condition = '[[distance]] <= :range';
            $params = [':range' => $this->options['range']];
        } else {
            $condition = '[[distance]] IS NOT NULL';
            $params = [];
        }

        // Filter based on database type
        if (Craft::$app->getDb()->getIsMysql()) {
            // MySQL
            $this->query->subQuery->having($condition, $params);
        } else {
            // Postgres
            $this->query->query->andWhere($condition, $params);
        }

    }

    /**
     * Apply the `subfields` option.
     */
    private function _applySubfields(): void
    {
        // Get subfields
        $subfields = ($this->options['subfields'] ?? []);

        // If subfields are not an array, bail
        if (!is_array($subfields)) {
            return;
        }

        // Get handles of all subfields
        $valid = array_column(Defaults::SUBFIELDCONFIG, 'handle');

        // Complete list of valid subfields
        $whitelist = array_merge($valid, ['lat','lng']);

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
            if (1 === count($where)) {
                $where = $where[0];
            } else {
                array_unshift($where, 'or');
            }

            // Append WHERE clause to subquery
            $this->query->subQuery->andWhere($where);
        }
    }

    /**
     * Apply the `requireCoords` option.
     */
    private function _applyRequireCoords(): void
    {
        // If coordinates are required
        if ($this->options['requireCoords'] ?? false) {

            // Omit Addresses with missing or incomplete coordinates
            $this->query->subQuery->andWhere(['not', [
                'or',
                ['lat' => null],
                ['lng' => null]
            ]]);

        }
    }

    /**
     * Apply the `reverseRadius` option.
     *
     * @throws HttpException
     */
    private function _applyReverseRadius(): void
    {
        // Get the reverse radius
        $reverseRadius = ($this->options['reverseRadius'] ?? null);

        // If not using a valid reverse radius, bail
        if (!$reverseRadius || !is_string($reverseRadius)) {
            return;
        }

        // Get content column in the subquery
        $this->query->subQuery->addSelect(
            "[[elements_sites.content]]"
        );

        // Get reverse radius field UID
        $layoutField = $this->field->layoutElement->getLayout()->getField($reverseRadius);

        // Get the actual reverse field
        $reverseField = $layoutField->getField();

        // If not a Number field type
        if (!is_a($reverseField, Number::class)) {
            // Get the actual field class
            $actualClass = get_class($reverseField);
            // Throw an error
            throw new HttpException(500, "The \"{$reverseRadius}\" field is a {$actualClass}. Please specify a Number field for the `reverseRadius` option.");
        }

        /** @var Connection $db */
        $db = Craft::$app->getDb();
        $qb = $db->getQueryBuilder();
        $sql = $qb->jsonExtract('elements_sites.content', [$layoutField->uid]);

        // Add reverse radius result to query
        $this->query->subQuery->addSelect("{$sql} AS [[gm_reverseRadius]]");

        // Filter by the reverse radius
        if (Craft::$app->getDb()->getIsMysql()) {
            // Configure for MySQL
            $this->query->subQuery->andHaving(
                "[[distance]] <= CAST(NULLIF(({$sql}), '') AS DECIMAL(10,4))"
            );
        } else {
            // Configure for Postgres

            // Get the target which defines the center point
            $target = ($this->options['target'] ?? null);

            // If target is defined, convert it to coordinates
            $coords = $target ? $this->_getTargetCoords($target) : null;

            // If unable to resolve coordinates, bail
            if (!$coords) {
                return;
            }

            // Implement haversine formula via SQL
            $haversine = $this->_haversineSql($coords['lat'], $coords['lng']);

            // Apply Postgres-specific reverse radius filter
            $this->query->subQuery->andWhere(
                "({$haversine}) <= CAST(NULLIF(({$sql}), '') AS double precision)"
            );
        }

    }

    // ========================================================================= //

    /**
     * Perform a geocoding address lookup to determine the coordinates of a given target.
     *
     * If necessary, this method will reconfigure the subfields filter
     * as part of the subfield filter fallback mechanism.
     *
     * @param array|string|null $target
     * @return array|null Set of coordinates based on specified target.
     */
    private function _lookupCoords(array|string|null $target): ?array
    {
        // Perform geocoding based on specified target
        $address = GoogleMaps::lookup($target)->one();

        // Get coordinates of specified target
        $coords = $address?->getCoords();

        // Get subfields
        $subfields = ($this->options['subfields'] ?? []);

        // If fallback filter is disabled, bail with coordinates
        if ('fallback' !== $subfields) {
            return $coords;
        }

        /**
         * Subfield Filter Fallback
         */

        // If address contains a valid street address, bail with coordinates
        if ($address->street1 ?? null) {
            return $coords;
        }

        // If no raw address components exist, bail with coordinates
        if (!($address->raw['address_components'] ?? null)) {
            return $coords;
        }

        // Determine type of address result
        $type = ($address->raw['types'][0] ?? null);

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
        if (in_array($type, $focusedTypes, true)) {
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
        $t = strtolower(trim($t));

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
            $v = strtolower(trim($value));

            // If target and value are identical, filter by THIS PART ONLY!
            if ($t === $v) {
                $filter = [$subfield => $value];
                break;
            }

        }

        // If subfield filter was properly configured, set it
        if ($filter) {
            $this->options['subfields'] = $filter;
        }

        // Return coordinates
        return $coords;
    }

    // ========================================================================= //

    /**
     * Based on the target provided, determine a center point for the proximity search.
     *
     * @param array|string $target
     * @return array|null Set of coordinates to use as center of proximity search.
     */
    private function _getTargetCoords(array|string $target): ?array
    {
        // Get coordinates based on type of target specified
        switch (gettype($target)) {

            // Target specified as a string
            case 'string':

                // Return coordinates based on geocoding lookup
                return $this->_lookupCoords($target);

            // Target specified as an array
            case 'array':

                // If coordinates were specified directly, return them as-is
                if (isset($target['lat'], $target['lng'])) {
                    return $target;
                }
                // Return coordinates based on geocoding lookup
                return $this->_lookupCoords($target);

        }

        // Something's not right, return default coordinates
        return Defaults::COORDINATES;
    }

    // ========================================================================= //

    /**
     * Apply haversine formula via SQL.
     *
     * @param float $lat
     * @param float $lng
     * @return string The haversine formula portion of an SQL query.
     */
    private function _haversineSql(float $lat, float $lng): string
    {
        // Determine radius
        $radius = self::haversineRadius($this->options['units'] ?? $this->_defaultUnits);

        // Calculate haversine formula
        return "(
            {$radius} * acos(
                cos(radians({$lat})) *
                cos(radians([[gm_addresses.lat]])) *
                cos(radians([[gm_addresses.lng]]) - radians({$lng})) +
                sin(radians({$lat})) *
                sin(radians([[gm_addresses.lat]]))
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
        return match ($units) {
            'km', 'kilometers' => 6371,
            default => 3959, // miles
        };
    }

}

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

namespace doublesecretagency\googlemaps\helpers;

use craft\base\Element;
use craft\helpers\StringHelper;
use doublesecretagency\googlemaps\fields\AddressField;
use doublesecretagency\googlemaps\models\Location;

/**
 * Class MapHelper
 * @since 4.0.0
 */
class MapHelper
{

    /**
     * Generate a random ID.
     *
     * @param string|null $prefix
     * @return string
     */
    public static function generateId(string $prefix = null): string
    {
        // Generate random hash
        $hash = StringHelper::randomString(6);

        // Return new ID (with optional prefix)
        return ($prefix ? "{$prefix}-{$hash}" : $hash);
    }

    // ========================================================================= //

    /**
     * Retrieve all coordinates from a specified set of locations.
     *
     * Coordinates will always be returned inside of a parent array,
     * to compensate for Elements with multiple Address Fields.
     *
     * @param mixed $locations
     * @return array Collection of coordinate sets
     */
    public static function extractCoords($locations): array
    {
        // If it's a Location Model, return the coordinates
        if (is_a($locations, Location::class)) {
            return [$locations->getCoords()];
        }

        // If it's a natural set of coordinates, return as-is
        if (is_array($locations) && isset($locations['lat'], $locations['lng'])) {
            return [$locations];
        }

        // Force array syntax
        if (!is_array($locations)) {
            $locations = [$locations];
        }

        // Initialize results array
        $results = [];

        // Loop through all locations
        foreach ($locations as $location) {

            // If it's a Location Model, add the coordinates to results
            if (is_a($location, Location::class)) {
                $results[] = $location->getCoords();
            }

            // If it's a natural set of coordinates, add them to results as-is
            if (is_array($location) && isset($location['lat'], $location['lng'])) {
                $results[] = $location;
            }

            // If not an Element, skip it
            if (!is_a($location, Element::class)) {
                continue;
            }

            // Get all fields associated with Element
            $fields = $location->getFieldLayout()->getFields();

            // Loop through all relevant fields
            foreach ($fields as $field) {
                // If not an Address Field, skip it
                if (!is_a($field, AddressField::class)) {
                    continue;
                }
                // Get value of Address Field
                $address = $location->{$field->handle};
                // If no Address, skip
                if (!$address) {
                    continue;
                }
                // Add coordinates to results
                if ($address->hasCoords()) {
                    $results[] = array_merge(
                        $address->getCoords(),
                        ['id' => "{$location->id}-{$field->handle}"]
                    );
                }
            }

        }

        // Return final results
        return $results;
    }

    // ========================================================================= //

    /**
     * Convert a set of coordinates into a string.
     *
     * @param array $coords
     * @return string
     */
    public static function stringCoords(array $coords): string
    {
        // If misconfigured coordinates, return empty string
        if (!isset($coords['lat'],$coords['lng'])) {
            return '';
        }

        // Return stringified coordinates
        return "{$coords['lat']},{$coords['lng']}";
    }

}

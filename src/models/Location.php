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

/**
 * Class Location
 * @since 4.0.0
 */
class Location extends Model
{

    /**
     * @var float|null Latitude of location.
     */
    public $lat;

    /**
     * @var float|null Longitude of location.
     */
    public $lng;

    // ========================================================================= //

    /**
     * Automatically display coordinates as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "{$this->lat}, {$this->lng}";
    }

    // ========================================================================= //

    /**
     * Whether this location contains a valid set of coordinates.
     *
     * @return bool
     */
    public function hasCoords(): bool
    {
        return (
            is_numeric($this->lat) &&
            is_numeric($this->lng)
        );
    }

    /**
     * Returns a formatted set of coordinates for this location.
     *
     * @return array
     */
    public function getCoords(): array
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }

    // ========================================================================= //

    /**
     * Puts a pin in the specified location and displays available place details.
     * Use this method to show a marker on the map (instead of `linkToMap`).
     *
     * For more info regarding the available parameters...
     * https://developers.google.com/maps/documentation/urls/get-started#search-action
     *
     * @param array $parameters
     * @return string
     */
    public function linkToSearch(array $parameters = []): string
    {
        // If invalid coordinates, bail
        if (!$this->hasCoords()) {
            return '#invalid-coordinates';
        }

        // Set query (if not already specified)
        $parameters['query'] = ($parameters['query'] ?? "{$this->lat},{$this->lng}");

        // Return compiled endpoint URL
        $url = 'https://www.google.com/maps/search/?api=1';
        return $this->_compileUrl($url, $parameters);
    }

    /**
     * Launches a map in directions mode.
     * Pre-fills the origin and/or destination.
     *
     * For more info regarding the available parameters...
     * https://developers.google.com/maps/documentation/urls/get-started#directions-action
     *
     * @param array $parameters
     * @param Location|null $origin
     * @return string
     */
    public function linkToDirections(array $parameters = [], Location $origin = null): string
    {
        // If invalid coordinates, bail
        if (!$this->hasCoords()) {
            return '#invalid-coordinates';
        }

        // Set destination (if not already specified)
        $parameters['destination'] = ($parameters['destination'] ?? "{$this->lat},{$this->lng}");

        // If an origin is specified, apply it
        if ($origin) {

            // If origin wasn't specified
            if (!isset($parameters['origin'])) {
                // Get origin address as a string
                $address = (is_a($origin, Address::class) ? (string) $origin : false);
                // Set origin to string address (or coordinates as fallback)
                $parameters['origin'] = ($address ?: "{$origin->lat},{$origin->lng}");
            }

            // If no origin place ID was specified
            if (!isset($parameters['origin_place_id'])) {
                // Extract the stored place ID (if it exists)
                $placeId = ($origin->raw['place_id'] ?? false);
                // If place ID exists, set as the origin place ID
                if ($placeId) {
                    $parameters['origin_place_id'] = $placeId;
                }
            }

        }

        // Return compiled endpoint URL
        $url = 'https://www.google.com/maps/dir/?api=1';
        return $this->_compileUrl($url, $parameters);
    }

    /**
     * Link to a map with no markers or directions.
     * If you want a marker, use `linkToSearch` instead.
     *
     * For more info regarding the available parameters...
     * https://developers.google.com/maps/documentation/urls/get-started#map-action
     *
     * @param array $parameters
     * @return string
     */
    public function linkToMap(array $parameters = []): string
    {
        // If invalid coordinates, bail
        if (!$this->hasCoords()) {
            return '#invalid-coordinates';
        }

        // Set center (if not already specified)
        $parameters['center'] = ($parameters['center'] ?? "{$this->lat},{$this->lng}");

        // Return compiled endpoint URL
        $url = 'https://www.google.com/maps/@?api=1&map_action=map';
        return $this->_compileUrl($url, $parameters);
    }

    /**
     * Launch a viewer to display Street View images as interactive panoramas.
     *
     * For more info regarding the available parameters...
     * https://developers.google.com/maps/documentation/urls/get-started#street-view-action
     *
     * @param array $parameters
     * @return string
     */
    public function linkToStreetView(array $parameters = []): string
    {
        // If invalid coordinates, bail
        if (!$this->hasCoords()) {
            return '#invalid-coordinates';
        }

        // Set viewpoint (if not already specified)
        $parameters['viewpoint'] = ($parameters['viewpoint'] ?? "{$this->lat},{$this->lng}");

        // Return compiled endpoint URL
        $url = 'https://www.google.com/maps/@?api=1&map_action=pano';
        return $this->_compileUrl($url, $parameters);
    }

    /**
     * Compile an API URL along with its parameters.
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    private function _compileUrl(string $url, array $params): string
    {
        // Loop through parameters to compile URL
        foreach ($params as $key => $val) {
            $url .= "&{$key}={$val}";
        }

        // Return fully compiled URL
        return $url;
    }

}

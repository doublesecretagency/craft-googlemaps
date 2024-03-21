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
use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\helpers\Template;
use Twig\Markup;

/**
 * Class Address
 * @since 4.0.0
 */
class Address extends Location
{

    /**
     * @var int|null ID of address.
     */
    public ?int $id = null;

    /**
     * @var int|null ID of element containing address.
     */
    public ?int $elementId = null;

    /**
     * @var int|null ID of field containing address.
     */
    public ?int $fieldId = null;

    /**
     * @var string|null Pre-formatted single-line address pulled directly from Google API results.
     */
    public ?string $formatted = null;

    /**
     * @var array|null Raw JSON response data from original Google API call.
     */
    public ?array $raw = null;

    /**
     * @var string|null Name of place or business.
     */
    public ?string $name = null;

    /**
     * @var string|null Street name and number.
     */
    public ?string $street1 = null;

    /**
     * @var string|null Apartment or suite number.
     */
    public ?string $street2 = null;

    /**
     * @var string|null City.
     */
    public ?string $city = null;

    /**
     * @var string|null State (or province, territory, etc).
     */
    public ?string $state = null;

    /**
     * @var string|null Zip code (or postal code, etc).
     */
    public ?string $zip = null;

    /**
     * @var string|null Neighborhood.
     */
    public ?string $neighborhood = null;

    /**
     * @var string|null County or district (political or administrative).
     */
    public ?string $county = null;

    /**
     * @var string|null Country.
     */
    public ?string $country = null;

    /**
     * @var string|null Country code.
     */
    public ?string $countryCode = null;

    /**
     * @var string|null Place ID as assigned by the Google API.
     */
    public ?string $placeId = null;

    /**
     * @var float|null Distance from another specified point.
     */
    public ?float $distance = null;

    /**
     * @var int|null Zoom level of map.
     */
    public ?int $zoom = null;

    /**
     * @var array|null Handles of visible subfields.
     */
    public ?array $enabledSubfields = null;

    // ========================================================================= //

    /**
     * Automatically format the address on a single line (if possible).
     *
     * @return string
     */
    public function __toString(): string
    {
        // Get Google-formatted address
        $googleFormatted = trim((string) $this->formatted);

        // If Google-formatted address exists, return it
        if ($googleFormatted) {
            return $googleFormatted;
        }

        // Get multiline-formatted address (as a single line)
        $multilineFormatted = (string) $this->multiline(1);

        // If able to format via `multiline` method, return it
        if ($multilineFormatted) {
            return $multilineFormatted;
        }

        // Return an empty string
        return '';
    }

    // ========================================================================= //

    /**
     * Get the element containing this address.
     *
     * @return ElementInterface|null
     */
    public function getElement(): ?ElementInterface
    {
        // If element ID does not exist, bail
        if (!$this->elementId) {
            return null;
        }

        // Return element containing this address
        return Craft::$app->getElements()->getElementById($this->elementId);
    }

    /**
     * Get the field containing this address.
     *
     * @return FieldInterface|null
     */
    public function getField(): ?FieldInterface
    {
        // If field ID does not exist, bail
        if (!$this->fieldId) {
            return null;
        }

        // Return field containing this address
        return Craft::$app->getFields()->getFieldById($this->fieldId);
    }

    // ========================================================================= //

    /**
     * Checks whether address is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        // Assumed empty by default
        $isEmpty = true;

        // Loop through enabled subfields
        foreach ($this->enabledSubfields as $subfield) {

            // If subfield is not empty
            if (!empty($this->{$subfield})) {
                // Mark address as not empty
                $isEmpty = false;
                // Break the loop
                break;
            }

        }

        // Return whether address is empty
        return $isEmpty;
    }

    /**
     * Format the Address as a multiline HTML string.
     *
     * @param int $maxLines The maximum number of lines to be allocated.
     * @return Markup
     */
    public function multiline(int $maxLines = 3): Markup
    {
        // Get enabled subfields
        $enabled = $this->enabledSubfields;

        // Only show enabled subfields
        $street1 = (in_array('street1', $enabled, true) ? $this->street1 : '');
        $street2 = (in_array('street2', $enabled, true) ? $this->street2 : '');
        $city    = (in_array('city',    $enabled, true) ? $this->city    : '');
        $state   = (in_array('state',   $enabled, true) ? $this->state   : '');
        $zip     = (in_array('zip',     $enabled, true) ? $this->zip     : '');
        $country = (in_array('country', $enabled, true) ? $this->country : '');

        // Determine glue for each part
        $cityGlue = (2 <= $maxLines ? '<br />' : ', ');
        $unitGlue = (3 <= $maxLines ? '<br />' : ', ');

        // Whether the address has street info and city/state info
        $hasStreet = ($street1 || $street2);
        $hasCityState = ($city || $state || $zip);

        // Manually format multi-line address
        $formatted  = '';
        $formatted .= $street1;
        $formatted .= ($street1 && $street2 ? $unitGlue : '');
        $formatted .= $street2;
        $formatted .= ($hasStreet && $hasCityState ? $cityGlue : '');
        $formatted .= $city;
        $formatted .= ($city && $state ? ', ' : '');
        $formatted .= $state.' ';
        $formatted .= $zip;

        // Optionally append country
        if (4 <= $maxLines) {
            $formatted .= ($country ? "<br />{$country}" : '');
        }

        // Merge repeated commas
        $formatted = preg_replace('/(, ){2,}/', ', ', $formatted);
        // Eliminate leading comma
        $formatted = preg_replace('/^, /', '', $formatted);
        // Eliminate trailing comma
        $formatted = preg_replace('/, $/', '', $formatted);

        // Return a formatted multiline address
        return Template::raw(trim($formatted));
    }

    // ========================================================================= //

    /**
     * @inheritdoc
     */
    public function getDistance(array|Location|null $location = null, string $units = 'miles'): ?float
    {
        // If no location specified and distance is already known, return it
        if (!$location && $this->distance) {
            return (float) $this->distance;
        }

        // Default to approach of parent method
        return parent::getDistance($location, $units);
    }

    // ========================================================================= //

    /**
     * @inheritdoc
     */
    public function linkToMap(array $parameters = []): string
    {
        // If invalid coordinates, bail
        if (!$this->hasCoords()) {
            return '#invalid-coordinates';
        }

        // If query wasn't specified
        if (!isset($parameters['query'])) {
            // Get address as a string
            $address = (string) $this;
            // Set query to string address (or coordinates as fallback)
            $parameters['query'] = ($address ?: "{$this->lat},{$this->lng}");
        }

        // If place ID exists and no query place ID was specified
        if ($this->placeId && !isset($parameters['query_place_id'])) {
            // Set as the query place ID
            $parameters['query_place_id'] = $this->placeId;
        }

        // Return compiled endpoint URL
        return parent::linkToMap($parameters);
    }

    /**
     * @inheritdoc
     */
    public function linkToDirections(array $parameters = [], ?Location $origin = null): string
    {
        // If invalid coordinates, bail
        if (!$this->hasCoords()) {
            return '#invalid-coordinates';
        }

        // If destination wasn't specified
        if (!isset($parameters['destination'])) {
            // Get destination address as a string
            $address = (string) $this;
            // Set destination to string address (or coordinates as fallback)
            $parameters['destination'] = ($address ?: "{$this->lat},{$this->lng}");
        }

        // If place ID exists and no destination place ID was specified
        if ($this->placeId && !isset($parameters['destination_place_id'])) {
            // Set as the destination place ID
            $parameters['destination_place_id'] = $this->placeId;
        }

        // Return compiled endpoint URL
        return parent::linkToDirections($parameters, $origin);
    }

}

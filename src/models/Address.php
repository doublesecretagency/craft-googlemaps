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
        return (
            empty($this->street1) &&
            empty($this->street2) &&
            empty($this->city) &&
            empty($this->state) &&
            empty($this->zip) &&
            empty($this->country)
        );
    }

    /**
     * Format the Address as a multiline HTML string.
     *
     * @param int $maxLines The maximum number of lines to be allocated.
     * @return Markup
     */
    public function multiline(int $maxLines = 3): Markup
    {
        // Determine glue for each part
        $cityGlue = (2 <= $maxLines ? '<br />' : ', ');
        $unitGlue = (3 <= $maxLines ? '<br />' : ', ');

        // Whether the address has street info and city/state info
        $hasStreet = ($this->street1 || $this->street2);
        $hasCityState = ($this->city || $this->state || $this->zip);

        // Manually format multi-line address
        $formatted  = '';
        $formatted .= ($this->street1 ?: '');
        $formatted .= ($this->street1 && $this->street2 ? $unitGlue : '');
        $formatted .= ($this->street2 ?: '');
        $formatted .= ($hasStreet && $hasCityState ? $cityGlue : '');
        $formatted .= ($this->city ?: '');
        $formatted .= (($this->city && $this->state) ? ', ' : '');
        $formatted .= ($this->state ?: '').' ';
        $formatted .= ($this->zip ?: '');

        // Optionally append country
        if (4 <= $maxLines) {
            $formatted .= ($this->country ? "<br />{$this->country}" : '');
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

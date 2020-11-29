<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
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
    public $id;

    /**
     * @var int|null ID of element containing address.
     */
    public $elementId;

    /**
     * @var int|null ID of field containing address.
     */
    public $fieldId;

    /**
     * @var string|null Pre-formatted single-line address pulled directly from Google API results.
     */
    public $formatted;

    /**
     * @var array|null Raw JSON response data from original Google API call.
     */
    public $raw;

    /**
     * @var string|null Street name and number.
     */
    public $street1;

    /**
     * @var string|null Apartment or suite number.
     */
    public $street2;

    /**
     * @var string|null City.
     */
    public $city;

    /**
     * @var string|null State (or province, territory, etc).
     */
    public $state;

    /**
     * @var string|null Zip code (or postal code, etc).
     */
    public $zip;

    /**
     * @var string|null Country.
     */
    public $country;

    /**
     * @var float|null Distance from another specified point.
     */
    public $distance;

    /**
     * @var int|null Zoom level of map.
     */
    public $zoom;

    // ========================================================================= //

    /**
     * Automatically format the address on a single line (if possible).
     *
     * @return string
     */
    public function __toString(): string
    {
        // Get Google-formatted address
        $googleFormatted = (string) trim($this->formatted);

        // If Google-formatted address exists, return it
        if ($googleFormatted) {
            return $googleFormatted;
        }

        // Get multiline-formatted address
        $multilineFormatted = (string) $this->multiline();

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
    public function getElement()
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
    public function getField()
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
    public function linkToSearch(array $parameters = []): string
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

        // If no query place ID was specified
        if (!isset($parameters['query_place_id'])) {
            // Extract the stored place ID (if it exists)
            $placeId = ($this->raw['place_id'] ?? false);
            // If place ID exists, set as the query place ID
            if ($placeId) {
                $parameters['query_place_id'] = $placeId;
            }
        }

        // Return compiled endpoint URL
        return parent::linkToSearch($parameters);
    }

    /**
     * @inheritdoc
     */
    public function linkToDirections(array $parameters = [], Location $origin = null): string
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

        // If no destination place ID was specified
        if (!isset($parameters['destination_place_id'])) {
            // Extract the stored place ID (if it exists)
            $placeId = ($this->raw['place_id'] ?? false);
            // If place ID exists, set as the destination place ID
            if ($placeId) {
                $parameters['destination_place_id'] = $placeId;
            }
        }

        // Return compiled endpoint URL
        return parent::linkToDirections($parameters, $origin);
    }

}

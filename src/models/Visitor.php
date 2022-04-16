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

/**
 * Class Visitor
 * @since 4.0.0
 */
class Visitor extends Location
{

    /**
     * @var string|null The name of the service used to perform the geolocation.
     */
    public ?string $service = null;

    /**
     * @var string|null The IP address of the geolocation lookup.
     */
    public ?string $ip = null;

    /**
     * @var string|null The city determined by the geolocation lookup.
     */
    public ?string $city = null;

    /**
     * @var string|null The state determined by the geolocation lookup.
     */
    public ?string $state = null;

    /**
     * @var string|null The country determined by the geolocation lookup.
     */
    public ?string $country = null;

    /**
     * @var array|null Raw JSON response data from original call to the geolocation service.
     */
    public ?array $raw = null;

    /**
     * @var string|null Error message, set when an error occurs.
     */
    public ?string $error = null;

    // ========================================================================= //

    /**
     * Automatically show a summary of the geolocation lookup.
     *
     * @return string
     */
    public function __toString(): string
    {
        // Get location components
        $location = [];
        if ($this->city)    {$location[] = $this->city;}
        if ($this->state)   {$location[] = $this->state;}
        if ($this->country) {$location[] = $this->country;}

        // Compile a pseudo-address string
        $pseudoAddress = implode(', ', $location);

        // Return string
        return "[{$this->service}] {$this->ip} - {$pseudoAddress}";
    }

}

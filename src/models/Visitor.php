<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\models;

/**
 * Class Visitor
 * @since 4.0.0
 */
class Visitor extends Location
{

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

    /**
     * @var string|null The name of the service used to perform the geolocation.
     */
    public $service;

    /**
     * @var string|null The IP address of the geolocation lookup.
     */
    public $ip;

    /**
     * @var string|null The city determined by the geolocation lookup.
     */
    public $city;

    /**
     * @var string|null The state determined by the geolocation lookup.
     */
    public $state;

    /**
     * @var string|null The country determined by the geolocation lookup.
     */
    public $country;

}

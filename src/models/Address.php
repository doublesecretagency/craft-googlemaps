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

use craft\helpers\Template;

/**
 * Class Address
 * @since 4.0.0
 */
class Address extends Location
{

    public function __toString(): string
    {
        return (string) $this->format(true, true);
    }

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
     * @var string|null State (or province, etc).
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
     * @var string|null Distance from another specified point.
     */
    public $distance;

    /**
     * Nicely formats the address.
     *
     * @return string
     */
    public function format($mergeUnit = false, $mergeCity = false)
    {
//        $unitGlue = ($mergeUnit ? ', ' : '<br />');
//        $cityGlue = ($mergeCity ? ', ' : '<br />');
//
//        $hasStreet = ($this->street1 || $this->street2);
//        $hasCityState = ($this->city || $this->state || $this->zip);
//
//        $formatted  = '';
//        $formatted .= ($this->street1 ? $this->street1 : '');
//        $formatted .= ($this->street1 && $this->street2 ? $unitGlue : '');
//        $formatted .= ($this->street2 ? $this->street2 : '');
//        $formatted .= ($hasStreet && $hasCityState ? $cityGlue : '');
//        $formatted .= ($this->city ? $this->city : '');
//        $formatted .= (($this->city && $this->state) ? ', ' : '');
//        $formatted .= ($this->state ? $this->state : '').' ';
//        $formatted .= ($this->zip ? $this->zip : '');
//
//        // Merge repeated commas
//        $formatted = preg_replace('/(, ){2,}/', ', ', $formatted);
//        // Eliminate leading comma
//        $formatted = preg_replace('/^, /', '', $formatted);
//        // Eliminate trailing comma
//        $formatted = preg_replace('/, $/', '', $formatted);
//
//        return Template::raw(trim($formatted));
    }

}

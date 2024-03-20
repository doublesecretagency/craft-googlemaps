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

namespace doublesecretagency\googlemaps\helpers;

use Craft;
use doublesecretagency\googlemaps\models\Lookup;

/**
 * Class GeocodingHelper
 * @since 4.0.0
 */
class GeocodingHelper
{

    /**
     * @var array Countries whose street number precedes the street name.
     */
    private static array $_countriesWithNumberFirst = [
        'Australia',
        'Canada',
        'France',
        'Hong Kong',
        'India',
        'Ireland',
        'Malaysia',
        'New Zealand',
        'Pakistan',
        'Singapore',
        'Sri Lanka',
        'Taiwan',
        'Thailand',
        'United Kingdom',
        'United States',
    ];

    /**
     * @var array Countries with a comma after the street name.
     */
    private static array $_companiesWithCommaAfterStreet = [
        'Italy',
    ];

    // ========================================================================= //

    /**
     * Clean and format raw address data.
     *
     * @param array $unformatted
     * @return array
     */
    public static function restructureComponents(array $unformatted): array
    {
        // Initialize formatted address data
        $formatted = [];

        // Get address components
        $components = ($unformatted['address_components'] ?? []);

        // Loop through address components
        foreach ($components as $component) {
            // If types aren't specified, skip
            if (!isset($component['types']) || !$component['types']) {
                continue;
            }
            // Generate formatted array of address data
            $c = $component['types'][0];
            switch ($c) {
                case 'locality':
                case 'neighborhood':
                    $formatted[$c] = $component['long_name'];
                    break;
                case 'country':
                    $formatted[$c] = $component['long_name'];
                    $formatted['countryCode'] = $component['short_name'];
                    break;
                default:
                    $formatted[$c] = $component['short_name'];
                    break;
            }
        }

        // Get components
        $streetNumber = ($formatted['street_number']               ?? null);
        $streetName   = ($formatted['route']                       ?? null);
        $city         = ($formatted['locality']                    ?? null);
        $state        = ($formatted['administrative_area_level_1'] ?? null);
        $zip          = ($formatted['postal_code']                 ?? null);
        $neighborhood = ($formatted['neighborhood']                ?? null);
        $county       = ($formatted['administrative_area_level_2'] ?? null);
        $country      = ($formatted['country']                     ?? null);
        $countryCode  = ($formatted['countryCode']                 ?? null);

        // Country-specific adjustments
        switch ($country) {
            case 'United Kingdom':
                $city  = ($formatted['postal_town'] ?? null);
                $state = ($formatted['administrative_area_level_2'] ?? null);
                break;
        }

        // Get coordinates
        $lat = ($unformatted['geometry']['location']['lat'] ?? null);
        $lng = ($unformatted['geometry']['location']['lng'] ?? null);

        // Default street format
        $street1 = "{$streetName} {$streetNumber}";

        // If country uses a different street format, apply that format instead
        if (in_array($country, static::$_countriesWithNumberFirst, true)) {
            $street1 = "{$streetNumber} {$streetName}";
        } else if (in_array($country, static::$_companiesWithCommaAfterStreet, true)) {
            $street1 = "{$streetName}, {$streetNumber}";
        }

        // Trim whitespace from street
        $street1 = (trim($street1) ?: null);

        // Return formatted address data
        return [
            'name'         => ($unformatted['name'] ?? null),
            'street1'      => $street1,
            'street2'      => null,
            'city'         => $city,
            'state'        => $state,
            'zip'          => $zip,
            'neighborhood' => $neighborhood,
            'county'       => $county,
            'country'      => $country,
            'countryCode'  => $countryCode,
            'placeId'      => ($unformatted['place_id'] ?? null),
            'lat'          => $lat,
            'lng'          => $lng,
            'raw'          => $unformatted,
        ];
    }

    // ========================================================================= //

    /**
     * Initialize a geocoding lookup by configuring a Lookup Model.
     *
     * @param array|string|null $target
     * @return Lookup
     */
    public static function lookup(array|string|null $target = []): Lookup
    {
        // If a string target was specified, convert to array
        if (is_string($target) || is_numeric($target)) {
            $target = ['address' => (string) $target];
        }

        // If target is not an array, bail with error message
        if (!is_array($target)) {
            $lookup = new Lookup();
            $lookup->error = Craft::t('google-maps', 'Invalid format for lookup target. Please use a string or array of parameters.');
            return $lookup;
        }

        // If no target specified, bail with error message
        if (!isset($target['address']) || !$target['address']) {
            $lookup = new Lookup();
            $lookup->error = Craft::t('google-maps', 'No lookup target was provided.');
            return $lookup;
        }

        // Create a fresh lookup
        return new Lookup($target);
    }

}

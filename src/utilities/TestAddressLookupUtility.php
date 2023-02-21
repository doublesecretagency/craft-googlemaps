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

namespace doublesecretagency\googlemaps\utilities;

use Craft;
use craft\base\Utility;
use doublesecretagency\googlemaps\helpers\GoogleMaps;

/**
 * Class TestAddressLookupUtility
 * @since 4.3.2
 */
class TestAddressLookupUtility extends Utility
{

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('google-maps', 'Test Address Lookup');
    }

    /**
     * @inheritdoc
     */
    public static function id(): string
    {
        return 'test-address-lookup';
    }

    /**
     * @inheritdoc
     */
    public static function iconPath(): ?string
    {
        // Set the icon mask path
        $iconPath = Craft::getAlias('@vendor/doublesecretagency/craft-googlemaps/src/icon-mask.svg');

        // If not a string, bail
        if (!is_string($iconPath)) {
            return null;
        }

        // Return the icon mask path
        return $iconPath;
    }

    /**
     * @inheritdoc
     */
    public static function contentHtml(): string
    {
        // Get the specified target
        $target = Craft::$app->getRequest()->getQueryParam('target');

        // Render the utility template
        return Craft::$app->getView()->renderTemplate('google-maps/_utility/test-address-lookup', [
            'target' => $target,
            'results' => static::_testLookup($target),
        ]);
    }

    // ========================================================================= //

    /**
     * Test a location lookup.
     *
     * @param string|null $target
     * @return array
     */
    private static function _testLookup(?string $target): array
    {
        // If no target or not a string
        if (!$target || !is_string($target)) {
            // Fail with no error message
            return [
                'success' => false,
                'error' => null,
                'results' => null
            ];
        }

        // Bust cache
        Craft::$app->getCache()->delete(['address' => $target]);

        // Configure Lookup Model based on given target
        $lookup = GoogleMaps::lookup($target);

        // Perform geocoding lookup
        $results = $lookup->one();

        // If something went wrong, return an error
        if (!$lookup) {
            return [
                'success' => false,
                'error' => "Invalid target. Unable to perform geocoding lookup.",
                'results' => null
            ];
        }

        // If something went wrong, return an error
        if (null === $results) {
            // Except the one error message that isn't actually an error
            $exception = 'The geocode was successful but returned no results.';
            // But for any other reason, return an error
            if ($exception !== $lookup->error) {
                return [
                    'success' => false,
                    'error' => $lookup->error,
                    'results' => null
                ];
            }
        }

        // Return successful results
        return [
            'success' => true,
            'error' => null,
            'results' => $results
        ];
    }

}

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
use craft\helpers\UrlHelper;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use Throwable;

/**
 * Class TestGoogleApiKeysUtility
 * @since 4.3.2
 */
class TestGoogleApiKeysUtility extends Utility
{

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('google-maps', 'Test Google API Keys');
    }

    /**
     * @inheritdoc
     */
    public static function id(): string
    {
        return 'test-google-api-keys';
    }

    /**
     * @inheritdoc
     */
    public static function icon(): ?string
    {
        // Set the icon mask path
        $icon = Craft::getAlias('@vendor/doublesecretagency/craft-googlemaps/src/icon-mask-keys.svg');

        // If not a string, bail
        if (!is_string($icon)) {
            return null;
        }

        // Return the icon mask path
        return $icon;
    }

    /**
     * @inheritdoc
     */
    public static function contentHtml(): string
    {
        // Render the utility template
        return Craft::$app->getView()->renderTemplate('google-maps/_utility/test-google-api-keys', [
            'results' => static::_testServerKey(),
            'staticMap' => static::_testStaticMaps(),
        ]);
    }

    // ========================================================================= //

    /**
     * Test the Maps Static API with the browser key.
     *
     * Static map images are loaded by the browser, so the browser key is
     * tested with the site's own URL as the referrer.
     *
     * @return array
     */
    private static function _testStaticMaps(): array
    {
        // Get the browser key
        $key = GoogleMaps::getBrowserKey();

        // If no browser key, return an error
        if (!$key) {
            return [
                'success' => false,
                'error' => 'No browser key provided.',
            ];
        }

        try {
            // Request the smallest possible static map
            $response = Craft::createGuzzleClient()->get('https://maps.googleapis.com/maps/api/staticmap', [
                'query' => ['center' => '0,0', 'zoom' => 1, 'size' => '1x1', 'key' => $key],
                'headers' => ['Referer' => UrlHelper::siteUrl()],
                'http_errors' => false,
                'timeout' => 10,
            ]);
        } catch (Throwable $e) {
            // Unable to reach Google
            return [
                'success' => false,
                'error' => 'Unable to reach the Maps Static API.',
            ];
        }

        // If the request was successful, return success
        if (200 === $response->getStatusCode()) {
            return [
                'success' => true,
                'error' => null,
            ];
        }

        // Otherwise, return an error
        return [
            'success' => false,
            'error' => 'The browser key cannot load static maps. The Maps Static API may not be enabled, or the key may be restricted.',
        ];
    }

    /**
     * Test the server key.
     *
     * @return array
     */
    private static function _testServerKey(): array
    {
        // Simple hard-coded target
        $target = 'usa';

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
                'error' => 'Invalid target. Unable to perform geocoding lookup.',
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

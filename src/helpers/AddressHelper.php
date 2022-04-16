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

/**
 * Class AddressHelper
 * @since 4.0.0
 */
class AddressHelper
{

    /**
     * Simple array of marker icons for visibility toggle.
     *
     * @return array
     */
    public static function visibilityIcons(): array
    {
        return [
            'marker'       => static::_publishSvg('marker.svg'),
            'markerHollow' => static::_publishSvg('marker-hollow.svg')
        ];
    }

    /**
     * Generate a published icon URL.
     *
     * @param string $filename
     * @return string|false
     */
    private static function _publishSvg(string $filename): string|false
    {
        $manager = Craft::$app->getAssetManager();
        $assets = '@doublesecretagency/googlemaps/web/assets/dist';
        $markerSvg = "images/{$filename}";
        return $manager->getPublishedUrl($assets, true, $markerSvg);
    }

}

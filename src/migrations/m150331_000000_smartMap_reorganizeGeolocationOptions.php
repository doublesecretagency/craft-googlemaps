<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2020 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\migrations;

use Craft;
use craft\db\Migration;
use doublesecretagency\googlemaps\GoogleMapsPlugin;

/**
 * Migration: Reorganize geolocation options.
 * @since 3.0.0
 */
class m150331_000000_smartMap_reorganizeGeolocationOptions extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Get settings
        $settings = GoogleMapsPlugin::$plugin->getSettings();

        // If no settings exist, bail
        if (!$settings) {
            return true;
        }

        // Convert model into array
        $settings = $settings->getAttributes();

        // Default geolocation service
        $service = 'none';

        // If "enableService" value exists
        if (isset($settings['enableService'])) {

            // Get currently enabled
            $enabled = $settings['enableService'];

            // If geolocation is enabled
            if (in_array('geolocation', $enabled)) {

                // If MaxMind is not enabled, default to FreeGeoIp.net
                if (in_array('maxmind', $enabled)) {
                    $service = 'maxmind';
                } else {
                    $service = 'freegeoip';
                }

            }

        }

        // Set geolocation selection
        $settings['geolocation'] = $service;

        // Remove "enableService" value
        unset($settings['enableService']);

        // Save settings
        Craft::$app->getPlugins()->savePluginSettings(GoogleMapsPlugin::$plugin, $settings);

        // Success
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $migration = (new \ReflectionClass($this))->getShortName();
        echo "{$migration} cannot be reverted.\n";
        return false;
    }

}

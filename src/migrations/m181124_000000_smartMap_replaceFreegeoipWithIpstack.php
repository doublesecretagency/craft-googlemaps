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

namespace doublesecretagency\googlemaps\migrations;

use Craft;
use craft\db\Migration;
use doublesecretagency\googlemaps\GoogleMapsPlugin;

/**
 * Migration: Replace FreeGeoIp.net with ipstack.
 * @since 3.2.0
 */
class m181124_000000_smartMap_replaceFreegeoipWithIpstack extends Migration
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

        // If setting doesn't exist, bail
        if (!isset($settings['geolocation'])) {
            return true;
        }

        // If not set to "freegeoip", bail
        if ('freegeoip' != $settings['geolocation']) {
            return true;
        }

        // Modify settings
        $settings['geolocation'] = 'ipstack';

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

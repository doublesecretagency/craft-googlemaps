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

namespace doublesecretagency\googlemaps\migrations;

use Craft;
use craft\db\Migration;
use doublesecretagency\googlemaps\GoogleMapsPlugin;

/**
 * Migration: Split Google API keys
 * @since 3.0.0
 */
class m150329_000000_smartMap_splitGoogleApiKeys extends Migration
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

        // If enableService isn't set to "google", bail
        if (!isset($settings['enableService']) || !in_array('google', $settings['enableService'])) {
            return true;
        }

        // If googleApiKey isn't set, bail
        if (!isset($settings['googleApiKey'])) {
            return true;
        }

        // Get existing API key
        $existingKey = $settings['googleApiKey'];

        // Modify settings
        $settings['googleServerKey']  = $existingKey;
        $settings['googleBrowserKey'] = $existingKey;
        unset($settings['googleApiKey']);

        // Save settings
        Craft::$app->getPlugins()->savePluginSettings(GoogleMapsPlugin::$plugin, $settings);

        // Return true
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m150329_000000_smartMap_splitGoogleApiKeys cannot be reverted.\n";

        return false;
    }

}

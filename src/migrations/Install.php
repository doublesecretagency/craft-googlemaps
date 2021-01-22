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

/**
 * Installation Migration
 * @since 4.0.0
 */
class Install extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Check whether the Smart Map plugin is installed and enabled
        $pluginInstalled = Craft::$app->plugins->isPluginEnabled('smart-map');

        // Check whether the `smartmap_addresses` table exists
        $tableExists = $this->db->tableExists('{{%smartmap_addresses}}');

        // If the plugin is installed and the table exists
        if ($pluginInstalled && $tableExists) {

            // Migrate everything from Smart Map
            FromSmartMap::update($this);

        } else {

            // Configure the plugin from scratch
            FromScratch::update($this);

        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists('{{%googlemaps_addresses}}');
    }

}

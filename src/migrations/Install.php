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

namespace doublesecretagency\googlemaps\migrations;

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
        // Configure the plugin from scratch
        FromScratch::update($this);

        // If the `smartmap_addresses` table does not exist, bail
        if (!$this->db->tableExists('{{%smartmap_addresses}}')) {
            return;
        }

        // Migrate everything from Smart Map
        FromSmartMap::update();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists('{{%googlemaps_addresses}}');
    }

}

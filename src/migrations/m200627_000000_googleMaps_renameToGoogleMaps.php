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

use craft\db\Migration;

/**
 * Migration: Rename table containing Address data.
 * @since 4.0.0
 */
class m200627_000000_googleMaps_renameToGoogleMaps extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->db->tableExists('{{%smartmap_addresses}}')) {
            $this->renameTable('{{%smartmap_addresses}}', '{{%googlemaps_addresses}}');
        }
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

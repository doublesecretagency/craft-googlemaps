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
 * m210914_223501_add_subfields Migration
 * @since 4.1.0
 */
class m210914_223501_add_subfields extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Table of all Addresses
        $table = '{{%googlemaps_addresses}}';

        // If name column doesn't exist, add it
        if (!$this->db->columnExists($table, 'name')) {
            $this->addColumn($table, 'name', $this->string()->after('raw'));
        }

        // If county column doesn't exist, add it
        if (!$this->db->columnExists($table, 'county')) {
            $this->addColumn($table, 'county', $this->string()->after('zip'));
        }

        // If placeId column doesn't exist, add it
        if (!$this->db->columnExists($table, 'placeId')) {
            $this->addColumn($table, 'placeId', $this->string()->after('country'));
        }

        // Success
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m210914_223501_add_subfields cannot be reverted.\n";
        return false;
    }

}

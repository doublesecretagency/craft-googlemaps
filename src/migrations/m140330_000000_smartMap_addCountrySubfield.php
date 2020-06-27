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

use craft\db\Migration;

/**
 * Migration: Add country subfield
 * @since 3.0.0
 */
class m140330_000000_smartMap_addCountrySubfield extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!$this->db->columnExists('{{%smartmap_addresses}}', 'country')) {
            $this->addColumn('{{%smartmap_addresses}}', 'country', $this->string()->after('zip'));
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m140330_000000_smartMap_addCountrySubfield cannot be reverted.\n";

        return false;
    }

}

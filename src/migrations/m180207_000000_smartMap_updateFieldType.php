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
 * Migration: Update field type for Craft 3 compatibility
 * @since 3.0.0
 */
class m180207_000000_smartMap_updateFieldType extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Auto-update existing Address fields
        $this->update('{{%fields}}', [
            'type' => 'doublesecretagency\smartmap\fields\Address'
        ], [
            'type' => 'SmartMap_Address'
        ], [], false);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180207_000000_smartMap_updateFieldType cannot be reverted.\n";

        return false;
    }

}

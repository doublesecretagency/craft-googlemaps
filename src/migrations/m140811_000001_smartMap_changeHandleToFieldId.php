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
use craft\db\Query;

/**
 * Migration: Change handle to field ID
 * @since 3.0.0
 */
class m140811_000001_smartMap_changeHandleToFieldId extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!$this->db->columnExists('{{%smartmap_addresses}}', 'fieldId')) {
            // Create foreign key
            $this->addColumn('{{%smartmap_addresses}}', 'fieldId', $this->integer()->after('elementId'));
            $this->createIndex(null, '{{%smartmap_addresses}}', ['fieldId']);
            $this->addForeignKey(null, '{{%smartmap_addresses}}', ['fieldId'], '{{%fields}}', ['id'], 'CASCADE');
            // Get all Address fields
            $fields = (new Query())
                ->select(['id', 'handle'])
                ->from(['{{%fields}}'])
                ->where(['or',
                    ['type' => 'SmartMap_Address'], // OLD
                    ['type' => 'doublesecretagency\smartmap\fields\Address'] // NEW
                ])
                ->all($this->db);
            // Update existing Address data
            foreach ($fields as $field) {
                $data = ['fieldId' => $field['id']];
                $this->update('{{%smartmap_addresses}}', $data, ['handle' => $field['handle']]);
            }
            // After values have been transferred, disallow null fieldId values
            $this->alterColumn('{{%smartmap_addresses}}', 'fieldId', $this->integer()->notNull());
            // Remove old column
            $this->dropColumn('{{%smartmap_addresses}}', 'handle');
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

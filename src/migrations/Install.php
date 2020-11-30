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
 * Installation Migration
 * @since 3.0.0
 */
class Install extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists('{{%googlemaps_addresses}}');
    }

    /**
     * Creates the tables.
     */
    protected function createTables()
    {
        $this->createTable('{{%googlemaps_addresses}}', [
            'id'          => $this->primaryKey(),
            'elementId'   => $this->integer()->notNull(),
            'fieldId'     => $this->integer()->notNull(),
            'formatted'   => $this->string(),
            'raw'         => $this->text(),
            'street1'     => $this->string(),
            'street2'     => $this->string(),
            'city'        => $this->string(),
            'state'       => $this->string(),
            'zip'         => $this->string(),
            'country'     => $this->string(),
            'lat'         => $this->decimal(12, 8),
            'lng'         => $this->decimal(12, 8),
            'zoom'        => $this->tinyInteger(2),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid'         => $this->uid(),
        ]);
    }

    /**
     * Creates the indexes.
     */
    protected function createIndexes()
    {
        $this->createIndex(null, '{{%googlemaps_addresses}}', ['elementId']);
        $this->createIndex(null, '{{%googlemaps_addresses}}', ['fieldId']);
    }

    /**
     * Adds the foreign keys.
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(null, '{{%googlemaps_addresses}}', ['elementId'], '{{%elements}}', ['id'], 'CASCADE');
        $this->addForeignKey(null, '{{%googlemaps_addresses}}', ['fieldId'],   '{{%fields}}',   ['id'], 'CASCADE');
    }

}

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
 * FromScratch Migration
 * @since 4.0.0
 */
class FromScratch
{

    /**
     * @var Migration Internalized migration object.
     */
    private static $_migration;

    /**
     * Install and configure tables from scratch.
     *
     * @param $migration
     */
    public static function update($migration)
    {
        // Share migration locally
        static::$_migration = $migration;

        // Install everything from scratch
        static::_createTables();
        static::_createIndexes();
        static::_addForeignKeys();
    }

    /**
     * Creates the tables.
     */
    private static function _createTables()
    {
        static::$_migration->createTable('{{%googlemaps_addresses}}', [
            'id'          => static::$_migration->primaryKey(),
            'elementId'   => static::$_migration->integer()->notNull(),
            'fieldId'     => static::$_migration->integer()->notNull(),
            'formatted'   => static::$_migration->string(),
            'raw'         => static::$_migration->text(),
            'street1'     => static::$_migration->string(),
            'street2'     => static::$_migration->string(),
            'city'        => static::$_migration->string(),
            'state'       => static::$_migration->string(),
            'zip'         => static::$_migration->string(),
            'country'     => static::$_migration->string(),
            'lat'         => static::$_migration->decimal(12, 8),
            'lng'         => static::$_migration->decimal(12, 8),
            'zoom'        => static::$_migration->tinyInteger(2),
            'dateCreated' => static::$_migration->dateTime()->notNull(),
            'dateUpdated' => static::$_migration->dateTime()->notNull(),
            'uid'         => static::$_migration->uid(),
        ]);
    }

    /**
     * Creates the indexes.
     */
    private static function _createIndexes()
    {
        static::$_migration->createIndex(null, '{{%googlemaps_addresses}}', ['elementId']);
        static::$_migration->createIndex(null, '{{%googlemaps_addresses}}', ['fieldId']);
    }

    /**
     * Adds the foreign keys.
     */
    private static function _addForeignKeys()
    {
        static::$_migration->addForeignKey(null, '{{%googlemaps_addresses}}', ['elementId'], '{{%elements}}', ['id'], 'CASCADE');
        static::$_migration->addForeignKey(null, '{{%googlemaps_addresses}}', ['fieldId'],   '{{%fields}}',   ['id'], 'CASCADE');
    }

}

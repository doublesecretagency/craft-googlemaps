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

use Craft;
use craft\db\Migration;

/**
 * FromScratch Migration
 * @since 4.0.0
 */
class FromScratch
{

    /**
     * @var Migration|null Internalized migration object.
     */
    private static ?Migration $_migration = null;

    /**
     * Install and configure tables from scratch.
     *
     * @param Migration $migration
     */
    public static function update(Migration $migration): void
    {
        // Share migration locally
        static::$_migration = $migration;

        // If the table already exists, move on
        // (gracefully recover from a previous failed migration attempt)
        if (static::$_migration->db->tableExists('{{%googlemaps_addresses}}')) {
            $message = "The `googlemaps_addresses` table already exists. We may be recovering from a previously failed migration, migrated data will be appended to any existing data.";
            Craft::warning($message, __METHOD__);
            return;
        }

        // Install everything from scratch
        static::_createTables();
        static::_createIndexes();
        static::_addForeignKeys();
    }

    /**
     * Creates the tables.
     */
    private static function _createTables(): void
    {
        static::$_migration->createTable('{{%googlemaps_addresses}}', [
            'id'           => static::$_migration->primaryKey(),
            'elementId'    => static::$_migration->integer()->notNull(),
            'fieldId'      => static::$_migration->integer()->notNull(),
            'formatted'    => static::$_migration->string(),
            'raw'          => static::$_migration->text(),
            'name'         => static::$_migration->string(),
            'street1'      => static::$_migration->string(),
            'street2'      => static::$_migration->string(),
            'city'         => static::$_migration->string(),
            'state'        => static::$_migration->string(),
            'zip'          => static::$_migration->string(),
            'neighborhood' => static::$_migration->string(),
            'county'       => static::$_migration->string(),
            'country'      => static::$_migration->string(),
            'countryCode'  => static::$_migration->string(),
            'placeId'      => static::$_migration->string(),
            'lat'          => static::$_migration->decimal(12, 8),
            'lng'          => static::$_migration->decimal(12, 8),
            'zoom'         => static::$_migration->tinyInteger(2),
            'dateCreated'  => static::$_migration->dateTime()->notNull(),
            'dateUpdated'  => static::$_migration->dateTime()->notNull(),
            'uid'          => static::$_migration->uid(),
        ]);
    }

    /**
     * Creates the indexes.
     */
    private static function _createIndexes(): void
    {
        static::$_migration->createIndex(null, '{{%googlemaps_addresses}}', ['elementId']);
        static::$_migration->createIndex(null, '{{%googlemaps_addresses}}', ['fieldId']);
    }

    /**
     * Adds the foreign keys.
     */
    private static function _addForeignKeys(): void
    {
        static::$_migration->addForeignKey(null, '{{%googlemaps_addresses}}', ['elementId'], '{{%elements}}', ['id'], 'CASCADE');
        static::$_migration->addForeignKey(null, '{{%googlemaps_addresses}}', ['fieldId'],   '{{%fields}}',   ['id'], 'CASCADE');
    }

}

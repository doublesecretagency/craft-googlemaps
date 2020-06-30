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
 * Migration: Add new columns for storing additional address data.
 * @since 4.0.0
 */
class m200627_000001_googleMaps_addGoogleMapsColumns extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // If table doesn't exist, bail
        if (!$this->db->tableExists('{{%googlemaps_addresses}}')) {
            echo "The `googlemaps_addresses` table does not yet exist.\n";
            return false;
        }

        // Add `formatted` column
        if (!$this->db->columnExists('{{%googlemaps_addresses}}', 'formatted')) {
            $this->addColumn(
                '{{%googlemaps_addresses}}',
                'formatted',
                $this->string()->after('fieldId')
            );
        }

        // Add `raw` column
        if (!$this->db->columnExists('{{%googlemaps_addresses}}', 'raw')) {
            $this->addColumn(
                '{{%googlemaps_addresses}}',
                'raw',
                $this->text()->after('formatted')
            );
        }

        // Add `zoom` column
        if (!$this->db->columnExists('{{%googlemaps_addresses}}', 'zoom')) {
            $this->addColumn(
                '{{%googlemaps_addresses}}',
                'zoom',
                $this->tinyInteger(2)->after('lng')
            );
        }

        // Set zoom for existing addresses with valid coordinates
        $this->update('{{%googlemaps_addresses}}', [
            'zoom' => 11
        ], ['and',
            ['zoom' => null],
            ['not', ['lat' => null]],
            ['not', ['lng' => null]],
        ], [], false);

        // Success
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

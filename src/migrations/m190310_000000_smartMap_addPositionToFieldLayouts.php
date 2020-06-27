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
 * Migration: Add a `position` value to Address layout subfields
 * @since 3.2.2
 */
class m190310_000000_smartMap_addPositionToFieldLayouts extends Migration
{

    /**
     * This migration caused too many problems.
     * We're going to neutralize it, and try again.
     *
     * @inheritdoc
     */
    public function safeUp()
    {
        // Successfully does nothing
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

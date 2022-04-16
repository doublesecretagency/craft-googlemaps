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
use craft\i18n\Translation;

/**
 * m211111_212501_announcements_v4_1 Migration
 * @since 4.1.0
 */
class m211111_212501_announcements_v4_1 extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Get announcements services
        $announcements = Craft::$app->getAnnouncements();

        // Three New Address Subfields
        $announcements->push(
            Translation::prep('google-maps', 'Google Maps - 3 New Address Subfields'),
            Translation::prep('google-maps', 'The new subfields [`name`]({name}), [`county`]({county}), and [`placeId`]({placeId}) have been added to Address fields and models.', [
                'name'    => 'https://plugins.doublesecretagency.com/google-maps/models/address-model/#name',
                'county'  => 'https://plugins.doublesecretagency.com/google-maps/models/address-model/#county',
                'placeId' => 'https://plugins.doublesecretagency.com/google-maps/models/address-model/#placeid',
            ]),
            'google-maps'
        );

        // Replaced Marker Clustering Library
        $announcements->push(
            Translation::prep('google-maps', 'Google Maps - New Clustering Library'),
            Translation::prep('google-maps', 'The official marker clustering library has been replaced by Google, so we have internally switched over to the new official library. [Learn more...]({url})', [
                'url' => 'https://plugins.doublesecretagency.com/google-maps/dynamic-maps/clustering-markers/',
            ]),
            'google-maps'
        );

        // Success
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m211111_212501_announcements_v4_1 cannot be reverted.\n";
        return false;
    }

}

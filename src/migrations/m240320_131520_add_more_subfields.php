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
use craft\db\Query;
use craft\helpers\Json;
use craft\i18n\Translation;
use doublesecretagency\googlemaps\helpers\GeocodingHelper;

/**
 * m240320_131520_add_more_subfields Migration
 * @since 4.5.0
 */
class m240320_131520_add_more_subfields extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Table of all Addresses
        $table = '{{%googlemaps_addresses}}';

        // If neighborhood column doesn't exist, add it
        if (!$this->db->columnExists($table, 'neighborhood')) {
            $this->addColumn($table, 'neighborhood', $this->string()->after('zip'));
        }

        // If countryCode column doesn't exist, add it
        if (!$this->db->columnExists($table, 'countryCode')) {
            $this->addColumn($table, 'countryCode', $this->string()->after('country'));
        }

        // Populate the new subfields with existing data
        $this->_populateSubfields();

        // Post an announcement
        $this->_announcement();

        // Success
        return true;
    }

    // ========================================================================= //

    /**
     * Populate the new subfields.
     */
    private function _populateSubfields(): void
    {
        // Get all existing Address data
        $rows = (new Query())
            ->select(['id','raw'])
            ->from('{{%googlemaps_addresses}}')
            ->all();

        // Loop through each row
        foreach ($rows as $row) {

            // Check if JSON is valid
            // Must use this function to validate (I know it's redundant)
            $valid = json_decode($row['raw']);

            // Get original raw data
            $raw = ($valid ? Json::decode($row['raw']) : null);

            // Reset the address data
            $address = [];

            // If raw value exists, decode it
            if ($raw) {
                $address = GeocodingHelper::restructureComponents($raw);
            }

            // Set new data
            $data = [
                'neighborhood' => ($address['neighborhood'] ?? null),
                'countryCode'  => ($address['countryCode'] ?? null),
            ];

            // Update row
            $this->update('{{%googlemaps_addresses}}', $data, ['id' => $row['id']]);
        }
    }

    /**
     * Post an announcement about the new subfields.
     */
    private function _announcement(): void
    {
        // Post announcement
        Craft::$app->getAnnouncements()->push(
            Translation::prep('google-maps', 'Google Maps - 2 New Address Subfields'),
            Translation::prep('google-maps', 'The new subfields [`neighborhood`]({neighborhood}) and [`countryCode`]({countryCode}) have been added to Address fields and models.', [
                'neighborhood' => 'https://plugins.doublesecretagency.com/google-maps/models/address-model/#neighborhood',
                'countryCode'  => 'https://plugins.doublesecretagency.com/google-maps/models/address-model/#countrycode',
            ]),
            'google-maps'
        );
    }

    // ========================================================================= //

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m240320_131520_add_more_subfields cannot be reverted.\n";
        return false;
    }

}

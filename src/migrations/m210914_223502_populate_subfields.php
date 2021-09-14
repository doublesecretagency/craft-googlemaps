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
use craft\db\Query;
use craft\helpers\Json;
use doublesecretagency\googlemaps\helpers\GeocodingHelper;

/**
 * m210914_223502_populate_subfields Migration
 * @since 4.1.0
 */
class m210914_223502_populate_subfields extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
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

            // If raw value exists, decode it
            if ($raw) {
                $address = GeocodingHelper::restructureComponents($raw);
            }

            // Set new data
            $data = [
                'name'    => ($raw['name'] ?? null),
                'county'  => ($address['county'] ?? null),
                'placeId' => ($raw['place_id'] ?? null),
            ];

            // Update row
            $this->update('{{%googlemaps_addresses}}', $data, ['id' => $row['id']]);
        }

        // Success
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m210914_223502_populate_subfields cannot be reverted.\n";
        return false;
    }

}

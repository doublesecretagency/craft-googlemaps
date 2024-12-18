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
use craft\db\Table;
use craft\helpers\DateTimeHelper;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use craft\i18n\Translation;
use doublesecretagency\googlemaps\fields\AddressField;
use Exception;
use ReflectionClass;
use Yii;
use yii\base\NotSupportedException;

/**
 * m240530_122024_multisite_support Migration
 * @since 4.6.0
 */
class m240530_122024_multisite_support extends Migration
{

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function safeUp(): bool
    {
        // Add new columns
        $this->_newColumns();

        // Populate new columns with existing data
        $this->_populateData();

        // Make new column not nullable
        //  - AFTER the column has been populated
        //  - BEFORE adding indexes and foreign keys
        $this->alterColumn(Install::GM_ADDRESSES, 'siteId', $this->integer()->notNull());

        // Add new column indexes
        $this->_newIndexes();

        // Add new foreign keys
        $this->_newForeignKeys();

        // Post an announcement
        $this->_announcement();

        // Success
        return true;
    }

    // ========================================================================= //

    /**
     * Add new columns.
     * @throws NotSupportedException
     */
    private function _newColumns(): void
    {
        // If column doesn't exist, add it
        if (!$this->db->columnExists(Install::GM_ADDRESSES, 'siteId')) {
            $this->addColumn(Install::GM_ADDRESSES, 'siteId', $this->integer()->after('elementId'));
        }
    }

    /**
     * Add new column indexes.
     */
    private function _newIndexes(): void
    {
        $this->createIndex(null, Install::GM_ADDRESSES, ['siteId']);
        $this->createIndex(null, Install::GM_ADDRESSES, ['siteId', 'fieldId']);
        $this->createIndex(null, Install::GM_ADDRESSES, ['elementId', 'siteId']);
        $this->createIndex(null, Install::GM_ADDRESSES, ['elementId', 'fieldId']);
        $this->createIndex(null, Install::GM_ADDRESSES, ['elementId', 'siteId', 'fieldId'], true);
    }

    /**
     * Add new foreign keys.
     */
    private function _newForeignKeys(): void
    {
        $this->addForeignKey(null, Install::GM_ADDRESSES, ['siteId'], Table::SITES, ['id'], 'CASCADE');
    }

    // ========================================================================= //

    /**
     * Populate new column with existing data.
     *
     * @throws Exception if critical data is missing or operations fail.
     */
    private function _populateData(): void
    {
        // Get the primary site ID
        $primarySiteId = Craft::$app->getSites()->getPrimarySite()->id;

        // Set the primary site ID for all existing Addresses
        $this->update(
            Install::GM_ADDRESSES,
            ['[[siteId]]' => $primarySiteId],
            ['[[siteId]]' => null]
        );

        // Get the UIDs of all Address fields
        $fieldUids = (new Query())
            ->select('[[uid]]')
            ->from([Table::FIELDS])
            ->where(['[[type]]' => AddressField::class])
            ->column();

        // If no Address fields exist, bail
        if (!$fieldUids) {
            return;
        }

        // Loop over each Address field
        foreach ($fieldUids as $fieldUid) {

            // Get all field layouts that contain this field
            $fieldLayoutConfigs = (new Query())
                ->select('[[config]]')
                ->from([Table::FIELDLAYOUTS])
                ->where(['like', '[[config]]', $fieldUid])
                ->column();

            // Loop over all field layout configs
            foreach ($fieldLayoutConfigs as $fieldLayoutConfig) {

                // Decode the field layout config
                $fieldLayoutConfig = Json::decode($fieldLayoutConfig) ?? [];

                // Loop over all tabs in each field layout config
                foreach (($fieldLayoutConfig['tabs'] ?? []) as $tab) {

                    // Loop over all elements in each tab
                    foreach (($tab['elements'] ?? []) as $element) {

                        // If the element is this Address field
                        if (($element['fieldUid'] ?? null) === $fieldUid) {
                            // Migrate Address data for this field layout element
                            $this->_migrate($element['uid']);
                        }

                    }

                }

            }

        }

    }

    /**
     * Migrate Address data for a specific field layout element.
     *
     * @param string $fieldLayoutElementUid
     * @throws Exception
     */
    private function _migrate(string $fieldLayoutElementUid): void
    {
        // Get all element sites with content containing this field layout element
        $elementSites = (new Query())
            ->select('[[id]], [[elementId]], [[siteId]], [[content]], [[dateCreated]], [[dateUpdated]]')
            ->from([Table::ELEMENTS_SITES])
            ->where(['like', '[[content]]', $fieldLayoutElementUid])
            ->all();

        // If no element sites found, bail
        if (!$elementSites) {
            return;
        }

        // Initialize array for batch insertion
        $rows = [];

        // Loop over each element site
        foreach ($elementSites as $elementSite) {

            // Decode the content
            $elementSiteContent = Json::decode($elementSite['content']) ?? [];

            // Get the Address data from the content
            $content = $elementSiteContent[$fieldLayoutElementUid] ?? null;

            // If no Address data found, bail
            if (!$content) {
                continue;
            }

            // Extract raw data from Address field
            $raw = ($content['raw'] ?? null);

            // Get current time as a fallback
            $now = DateTimeHelper::currentUTCDateTime()->format('Y-m-d H:i:s');

            // Configure the Address data
            $gmAddress = [
                'elementId'    => (int) $elementSite['elementId'],
                'siteId'       => (int) $elementSite['siteId'],
                'fieldId'      => (int) $content['fieldId'],
                'formatted'    => ($content['formatted'] ?? null),
                'raw'          => ($raw ? Json::encode($raw) : null),
                'name'         => ($content['name'] ?? null),
                'street1'      => ($content['street1'] ?? null),
                'street2'      => ($content['street2'] ?? null),
                'city'         => ($content['city'] ?? null),
                'state'        => ($content['state'] ?? null),
                'zip'          => ($content['zip'] ?? null),
                'neighborhood' => ($content['neighborhood'] ?? null),
                'county'       => ($content['county'] ?? null),
                'country'      => ($content['country'] ?? null),
                'countryCode'  => ($content['countryCode'] ?? null),
                'placeId'      => ($content['placeId'] ?? null),
                'lat'          => (float) $content['lat'],
                'lng'          => (float) $content['lng'],
                'zoom'         => (int) ($content['zoom'] ?? 11),
                'dateCreated'  => ($elementSite['dateCreated'] ?? $now),
                'dateUpdated'  => ($elementSite['dateUpdated'] ?? $now),
                'uid'          => StringHelper::UUID(), // Generate new UUID
            ];

            // Add the Address data to the batch
            $rows[] = $gmAddress;

            // Update the content with the new Address data
            $elementSiteContent[$fieldLayoutElementUid] = $gmAddress;

            try {
                // Update the element site with the new content
                Yii::$app->db
                    ->createCommand()
                    ->update(
                        Table::ELEMENTS_SITES,
                        ['content' => $elementSiteContent],
                        ['id' => $elementSite['id']]
                    )
                    ->execute();
            } catch (Exception $e) {
                // Log error
                $error = $e->getMessage();
                Craft::error("Error updating element site [{$elementSite['id']}]: {$error}");
            }
        }

        // If no rows to insert, bail
        if (!$rows) {
            return;
        }

        // Extract column names from the first row
        $columns = array_keys($rows[0]);

        // Prepare the base SQL using `batchInsert`
        $sql = $this->db->createCommand()
            ->batchInsert(Install::GM_ADDRESSES, $columns, $rows)
            ->getRawSql();

        // If using MySQL
        if (Craft::$app->getDb()->getIsMysql()) {
            // MySQL syntax to update conflicting rows
            $updateClause = implode(', ', array_map(function($col) {
                return "$col = VALUES($col)";
            }, $columns));
            $sql .= " ON DUPLICATE KEY UPDATE {$updateClause}";
        } else {
            // Postgres syntax to update conflicting rows
            $updateClause = implode(', ', array_map(function($col) {
                return "\"$col\" = EXCLUDED.\"$col\"";
            }, $columns));
            $sql .= " ON CONFLICT (\"elementId\", \"siteId\", \"fieldId\") DO UPDATE SET {$updateClause}";
        }

        try {
            // Batch insert these Addresses
            $this->db->createCommand($sql)->execute();
        } catch (Exception $e) {
            Craft::error("Error during batch insert: ".$e->getMessage());
        }

    }

    // ========================================================================= //

    /**
     * Post an announcement.
     */
    private function _announcement(): void
    {
        // Post announcement
        Craft::$app->getAnnouncements()->push(
            Translation::prep('google-maps', 'NEW: Translatable Address Fields'),
            Translation::prep('google-maps', 'For projects with [multiple sites]({url}), each site can now store a different Address field value.', [
                'url' => 'https://plugins.doublesecretagency.com/google-maps/address-field/multisite-support/',
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
        // Get migration name
        $migration = (new ReflectionClass($this))->getShortName();
        echo "{$migration} cannot be reverted.\n";
        return false;
    }

}

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
use craft\errors\SiteNotFoundException;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
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

        // Remove duplicate Addresses before the unique index is created
        $this->_removeDuplicates();

        // Add new column indexes
        $this->_newIndexes();

        // Populate new columns with existing data
        $this->_populateData();

        // Make new column not nullable
        //  - AFTER the indexes have been created
        //  - AFTER the column has been populated
        //  - BEFORE adding foreign keys
        $this->alterColumn(Install::GM_ADDRESSES, 'siteId', $this->integer()->notNull());

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
     *
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
        // Configure each index, and whether it is unique
        $indexes = [
            [['siteId'], false],
            [['siteId', 'fieldId'], false],
            [['elementId', 'siteId'], false],
            [['elementId', 'fieldId'], false],
            [['elementId', 'siteId', 'fieldId'], true],
        ];

        // Loop over each index
        foreach ($indexes as [$columns, $unique]) {

            // If a previous attempt already created it, skip
            if (Db::findIndex(Install::GM_ADDRESSES, $columns, $unique, $this->db)) {
                continue;
            }

            // Create the index
            $this->createIndex(null, Install::GM_ADDRESSES, $columns, $unique);

        }
    }

    /**
     * Add new foreign keys.
     */
    private function _newForeignKeys(): void
    {
        // If a previous attempt already created it, bail
        if (Db::findForeignKey(Install::GM_ADDRESSES, ['siteId'], $this->db)) {
            return;
        }

        // Create the foreign key
        $this->addForeignKey(null, Install::GM_ADDRESSES, ['siteId'], Table::SITES, ['id'], 'CASCADE');
    }

    /**
     * Remove duplicate Addresses which have not been assigned to a site.
     *
     * Before 4.6, nothing prevented two rows for the same element and field.
     */
    private function _removeDuplicates(): void
    {
        // Get each element and field with more than one unassigned Address
        $duplicates = (new Query())
            ->select(['[[elementId]]', '[[fieldId]]'])
            ->from(Install::GM_ADDRESSES)
            ->where(['[[siteId]]' => null])
            ->groupBy(['[[elementId]]', '[[fieldId]]'])
            ->having('COUNT(*) > 1')
            ->all();

        // Loop over each set of duplicates
        foreach ($duplicates as $duplicate) {

            // Get the IDs, most recently updated first
            $ids = (new Query())
                ->select(['[[id]]'])
                ->from(Install::GM_ADDRESSES)
                ->where([
                    '[[elementId]]' => $duplicate['elementId'],
                    '[[fieldId]]' => $duplicate['fieldId'],
                    '[[siteId]]' => null,
                ])
                ->orderBy(['[[dateUpdated]]' => SORT_DESC, '[[id]]' => SORT_DESC])
                ->column();

            // Keep the most recent Address
            array_shift($ids);

            // Delete the rest
            $this->delete(Install::GM_ADDRESSES, ['[[id]]' => $ids]);

        }
    }

    // ========================================================================= //

    /**
     * Populate new column with existing data.
     *
     * @throws Exception
     */
    private function _populateData(): void
    {
        // Start by attempting to get Address data from the `elements_sites` table.
        $this->_phase1();

        // For records without a `siteId`, attempt to get the `siteId` from the `elements_sites` table.
        $this->_phase2();

        // For all remaining records, set the primary site ID.
        $this->_phase3();

        // Copy each Address to every site of its element which is still missing one.
        $this->_fillMissingSites();
    }

    /**
     * Start by attempting to get Address data from the `elements_sites` table.
     *
     * @return void
     * @throws Exception
     */
    private function _phase1(): void
    {
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
                ->where(['like', $this->_searchable('[[config]]'), $fieldUid])
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
     * For records without a `siteId`, attempt to get the `siteId` from the `elements_sites` table.
     *
     * @return void
     * @throws Exception
     */
    private function _phase2(): void
    {
        // Get the primary site ID
        $primarySiteId = Craft::$app->getSites()->getPrimarySite()->id;

        // Get all rows where `siteId` is NULL
        $addresses = (new Query())
            ->select(['[[id]]', '[[elementId]]', '[[fieldId]]'])
            ->from(Install::GM_ADDRESSES)
            ->where(['[[siteId]]' => null])
            ->all();

        // Loop over each Address
        foreach ($addresses as $address) {

            // Get the sites this element exists in, primary site first
            $siteIds = $this->_elementSiteIds($address['elementId'], $primarySiteId);

            // If the element exists in no sites, skip
            if (!$siteIds) {
                continue;
            }

            // Assign it to the first site
            $this->_assignSite($address, $siteIds[0]);
        }
    }

    /**
     * For all remaining records, set the primary site ID.
     *
     * @return void
     * @throws SiteNotFoundException
     */
    private function _phase3(): void
    {
        // Get the primary site ID
        $primarySiteId = Craft::$app->getSites()->getPrimarySite()->id;

        // Get all remaining rows where `siteId` is NULL
        $addresses = (new Query())
            ->select(['[[id]]', '[[elementId]]', '[[fieldId]]'])
            ->from(Install::GM_ADDRESSES)
            ->where(['[[siteId]]' => null])
            ->all();

        // Loop over each Address
        foreach ($addresses as $address) {
            // Assign it to the primary site
            $this->_assignSite($address, $primarySiteId);
        }
    }

    /**
     * Assign an unassigned Address to a site, unless it is a stale copy.
     *
     * A copy is stale when the same element and field already have an Address on any site.
     *
     * @param array $address
     * @param int $siteId
     */
    private function _assignSite(array $address, int $siteId): void
    {
        // Whether the same element and field already have an Address on a site
        $assigned = (new Query())
            ->from(Install::GM_ADDRESSES)
            ->where([
                '[[elementId]]' => $address['elementId'],
                '[[fieldId]]' => $address['fieldId'],
            ])
            ->andWhere(['not', ['[[siteId]]' => null]])
            ->exists();

        // If so, this row is a stale copy
        if ($assigned) {
            // Delete it
            $this->delete(Install::GM_ADDRESSES, ['[[id]]' => $address['id']]);
            // Bail
            return;
        }

        // Assign the site
        $this->update(
            Install::GM_ADDRESSES,
            ['[[siteId]]' => $siteId],
            ['[[id]]' => $address['id']]
        );
    }

    /**
     * Copy each Address to every site of its element which is still missing one.
     *
     * Before 4.6, one Address was shared by every site, so each site keeps a copy.
     *
     * @throws SiteNotFoundException
     */
    private function _fillMissingSites(): void
    {
        // Get the primary site ID
        $primarySiteId = Craft::$app->getSites()->getPrimarySite()->id;

        // Get each element and field with an Address
        $pairs = (new Query())
            ->select(['[[elementId]]', '[[fieldId]]'])
            ->distinct()
            ->from(Install::GM_ADDRESSES)
            ->all();

        // Loop over each element and field
        foreach ($pairs as $pair) {

            // Get the sites this element exists in, primary site first
            $siteIds = $this->_elementSiteIds($pair['elementId'], $primarySiteId);

            // Get the existing Addresses, keyed by site
            $rows = (new Query())
                ->from(Install::GM_ADDRESSES)
                ->where([
                    '[[elementId]]' => $pair['elementId'],
                    '[[fieldId]]' => $pair['fieldId'],
                ])
                ->indexBy('siteId')
                ->all();

            // Use the primary site's Address as the source, or else the first one
            $source = ($rows[$primarySiteId] ?? reset($rows));

            // Loop over each site
            foreach ($siteIds as $siteId) {

                // If this site already has an Address, skip
                if (isset($rows[$siteId])) {
                    continue;
                }

                // Copy the source Address to this site
                $copy = $source;
                unset($copy['id']);
                $copy['siteId'] = $siteId;
                $copy['uid'] = StringHelper::UUID();
                $this->insert(Install::GM_ADDRESSES, $copy);

            }
        }
    }

    /**
     * Get the IDs of every site an element exists in, primary site first.
     *
     * @param int $elementId
     * @param int $primarySiteId
     * @return int[]
     */
    private function _elementSiteIds(int $elementId, int $primarySiteId): array
    {
        // Get the sites this element exists in
        $siteIds = array_map('intval', (new Query())
            ->select(['[[siteId]]'])
            ->from(Table::ELEMENTS_SITES)
            ->where(['[[elementId]]' => $elementId])
            ->orderBy(['[[siteId]]' => SORT_ASC])
            ->column());

        // Put the primary site first
        usort($siteIds, static fn(int $a, int $b) => ($b === $primarySiteId) <=> ($a === $primarySiteId));

        // Return the site IDs
        return $siteIds;
    }

    // ========================================================================= //
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
            ->where(['like', $this->_searchable('[[content]]'), $fieldLayoutElementUid])
            ->orderBy(['[[id]]' => SORT_ASC]);

        // Loop over each element site, in batches to keep memory flat
        foreach (Db::each($elementSites) as $elementSite) {

            // Decode the content
            $elementSiteContent = Json::decode($elementSite['content']) ?? [];

            // Get the Address data from the content
            $address = $elementSiteContent[$fieldLayoutElementUid] ?? null;

            // If no Address data found, bail
            if (!$address) {
                continue;
            }

            // Normalize raw value
            $address['raw'] = AddressField::normalizeRaw($address['raw'] ?? null);

            // Get current time as a fallback
            $now = DateTimeHelper::currentUTCDateTime()->format('Y-m-d H:i:s');

            // Configure the Address data
            $gmAddress = [
                'elementId'    => (int) $elementSite['elementId'],
                'siteId'       => (int) $elementSite['siteId'],
                'fieldId'      => (int) $address['fieldId'],
                'formatted'    => ($address['formatted'] ?? null),
                'raw'          => ($address['raw'] ? Json::encode($address['raw']) : null),
                'name'         => ($address['name'] ?? null),
                'street1'      => ($address['street1'] ?? null),
                'street2'      => ($address['street2'] ?? null),
                'city'         => ($address['city'] ?? null),
                'state'        => ($address['state'] ?? null),
                'zip'          => ($address['zip'] ?? null),
                'neighborhood' => ($address['neighborhood'] ?? null),
                'county'       => ($address['county'] ?? null),
                'country'      => ($address['country'] ?? null),
                'countryCode'  => ($address['countryCode'] ?? null),
                'placeId'      => ($address['placeId'] ?? null),
                'lat'          => (float) $address['lat'],
                'lng'          => (float) $address['lng'],
                'zoom'         => (int) ($address['zoom'] ?? 11),
                'dateCreated'  => ($elementSite['dateCreated'] ?? $now),
                'dateUpdated'  => ($elementSite['dateUpdated'] ?? $now),
                'uid'          => StringHelper::UUID(), // Generate new UUID
            ];

            // Update the content with the new Address data
            $elementSiteContent[$fieldLayoutElementUid] = $gmAddress;

            // Migrate Address data in the `elements_sites` table
            $this->_migrateElementsSitesTable($elementSiteContent, $elementSite['id']);

            // Migrate Address data in the `googlemaps_addresses` table
            $this->_migrateGoogleMapsAddressesTable($gmAddress);

        }
    }

    /**
     * Migrate Address data in the `elements_sites` table.
     *
     * @param array $elementSiteContent
     * @param int $elementSiteId
     */
    private function _migrateElementsSitesTable(array $elementSiteContent, int $elementSiteId): void
    {
        try {

            // Update the element site with the new content
            Yii::$app->db
                ->createCommand()
                ->update(
                    Table::ELEMENTS_SITES,
                    ['content' => $elementSiteContent],
                    ['id' => $elementSiteId]
                )
                ->execute();

        } catch (Exception $e) {
            // Log error
            $error = $e->getMessage();
            Craft::error("Error updating element site [{$elementSiteId}]: {$error}");
        }
    }

    /**
     * Migrate Address data in the `googlemaps_addresses` table.
     *
     * @param array $gmAddress
     */
    private function _migrateGoogleMapsAddressesTable(array $gmAddress): void
    {
        try {

            // Check for a matching Address record with a NULL `siteId`
            $existingId = (new Query())
                ->select('id')
                ->from(Install::GM_ADDRESSES)
                ->where([
                    'elementId' => $gmAddress['elementId'],
                    'siteId' => null,
                    'fieldId' => $gmAddress['fieldId'],
                ])
                ->scalar();

            // If a matching Address record exists
            if ($existingId) {

                // Update the `siteId` for the existing Address record
                $this->update(
                    Install::GM_ADDRESSES,
                    ['siteId' => $gmAddress['siteId']],
                    ['id' => $existingId]
                );

            } else {

                // Otherwise, insert a new Address record
                $this->insert(Install::GM_ADDRESSES, $gmAddress);

            }
        } catch (Exception $e) {
            // Log error
            $error = $e->getMessage();
            Craft::error("Error processing address data for {$gmAddress['elementId']}-{$gmAddress['siteId']}-{$gmAddress['fieldId']}: {$error}");
        }

    }

    /**
     * Get a JSON column in a form that `LIKE` can search.
     *
     * Postgres stores JSON as `jsonb`, which has no `LIKE` operator.
     *
     * @param string $column
     * @return string
     */
    private function _searchable(string $column): string
    {
        // If using Postgres, cast to text
        if ($this->db->getIsPgsql()) {
            return "CAST({$column} AS TEXT)";
        }

        // Return the column as is
        return $column;
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

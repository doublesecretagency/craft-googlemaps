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
use craft\helpers\ArrayHelper;
use craft\helpers\DateTimeHelper;
use craft\helpers\StringHelper;
use craft\i18n\Translation;
use Exception;
use ReflectionClass;
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

        // Clean up any existing duplicates before proceeding
        $this->_cleanupDuplicates();

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
     * Clean up any existing duplicate entries.
     */
    private function _cleanupDuplicates(): void
    {
        // Get all duplicate combinations
        $duplicates = (new Query())
            ->select(['elementId', 'siteId', 'fieldId'])
            ->from(Install::GM_ADDRESSES)
            ->groupBy(['elementId', 'siteId', 'fieldId'])
            ->having('COUNT(*) > 1')
            ->all();

        // For each duplicate combination, keep only the most recent entry
        foreach ($duplicates as $duplicate) {
            // Get all IDs for this combination
            $ids = (new Query())
                ->select('id')
                ->from(Install::GM_ADDRESSES)
                ->where([
                    'elementId' => $duplicate['elementId'],
                    'siteId' => $duplicate['siteId'],
                    'fieldId' => $duplicate['fieldId']
                ])
                ->orderBy(['dateUpdated' => SORT_DESC])
                ->all();

            // Keep the most recent entry, delete others
            if (count($ids) > 1) {
                $keepId = array_shift($ids)['id'];
                $deleteIds = array_column($ids, 'id');
                
                $this->delete(
                    Install::GM_ADDRESSES,
                    ['id' => $deleteIds]
                );
            }
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
     * Populate new columns with existing data.
     * @throws Exception
     */
    private function _populateData(): void
    {
        // Get sites service
        $sites = Craft::$app->getSites();

        // Get the primary site ID
        $primarySiteId = $sites->getPrimarySite()->id;

        // Get IDs from all sites
        $siteIds = ArrayHelper::getColumn($sites->getAllSites(), 'id');

        // Sort IDs numerically
        sort($siteIds);

        // Set date updated to right now
        $dateUpdated = DateTimeHelper::currentUTCDateTime()->format('Y-m-d H:i:s');

        // Set batch size
        $batchSize = 100;
        $offset = 0;

        // Loop over all available sites
        foreach ($siteIds as $siteId) {
            // Skip the primary site (it will be handled later via `update`)
            if ($siteId === $primarySiteId) {
                continue;
            }

            // Reset offset for each site
            $offset = 0;

            while (true) {
                // Get batch of existing Address data
                $rows = (new Query())
                    ->select('*')
                    ->from(Install::GM_ADDRESSES)
                    ->orderBy('[[id]]')
                    ->limit($batchSize)
                    ->offset($offset)
                    ->all();

                // If no more rows, break
                if (empty($rows)) {
                    break;
                }

                // Get columns from first row
                $columns = array_keys($rows[0]);

                // Initialize row data
                $data = [];

                // Process this batch of rows
                foreach ($rows as $row) {
                    // Check if an entry already exists for this combination
                    $exists = (new Query())
                        ->select('id')
                        ->from(Install::GM_ADDRESSES)
                        ->where([
                            'elementId' => $row['elementId'],
                            'siteId' => $siteId,
                            'fieldId' => $row['fieldId']
                        ])
                        ->exists();

                    // Skip if entry already exists
                    if ($exists) {
                        continue;
                    }

                    // Compile row data
                    $r = [];
                    foreach ($columns as $col) {
                        $r[$col] = ($row[$col] ?? null);
                    }

                    // Update row data
                    $r['id'] = null;                  // Allow fresh ID
                    $r['siteId'] = $siteId;           // Specify each site ID
                    $r['dateUpdated'] = $dateUpdated; // Update date updated
                    $r['uid'] = StringHelper::UUID(); // Generate new UUID

                    // Add row data to array
                    $data[] = $r;
                }

                // If we have data, upsert it
                if (!empty($data)) {
                    foreach ($data as $rowData) {
                        $this->upsert(Install::GM_ADDRESSES, $rowData, false);
                    }
                }

                // Increment offset for next batch
                $offset += $batchSize;
            }
        }

        // Set the site ID of all the original Addresses
        $this->update(
            Install::GM_ADDRESSES,
            ['siteId' => $primarySiteId],
            ['siteId' => null]
        );
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

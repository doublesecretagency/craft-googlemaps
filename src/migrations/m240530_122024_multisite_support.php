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
use craft\helpers\Db;
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

        // Remove duplicate Addresses before they are copied to each site
        $this->_removeDuplicates();

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
     * Remove duplicate Addresses.
     *
     * Before 4.6, nothing prevented two rows for the same element and field.
     * A previous failed attempt can also have left duplicates on each site.
     */
    private function _removeDuplicates(): void
    {
        // Get each element, site, and field with more than one Address
        $duplicates = (new Query())
            ->select(['[[elementId]]', '[[siteId]]', '[[fieldId]]'])
            ->from(Install::GM_ADDRESSES)
            ->groupBy(['[[elementId]]', '[[siteId]]', '[[fieldId]]'])
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
                    '[[siteId]]' => $duplicate['siteId'],
                    '[[fieldId]]' => $duplicate['fieldId'],
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
     * Populate new columns with existing data.
     * @throws Exception
     */
    private function _populateData(): void
    {
        // Give each original Address a site
        $this->_assignSites();

        // Copy each Address to every site of its element which is still missing one
        $this->_fillMissingSites();
    }

    /**
     * Give each Address without a site the primary site, or the first site of its element.
     *
     * @throws SiteNotFoundException
     */
    private function _assignSites(): void
    {
        // Get the primary site ID
        $primarySiteId = Craft::$app->getSites()->getPrimarySite()->id;

        // Get all rows where `siteId` is NULL
        $addresses = (new Query())
            ->select(['[[id]]', '[[elementId]]', '[[fieldId]]'])
            ->from(Install::GM_ADDRESSES)
            ->where(['[[siteId]]' => null])
            ->orderBy(['[[id]]' => SORT_ASC]);

        // Loop over each Address, in batches to keep memory flat
        foreach (Db::each($addresses) as $address) {

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
                // Move on to the next Address
                continue;
            }

            // Get the sites this element exists in, primary site first
            $siteIds = $this->_elementSiteIds((int) $address['elementId'], $primarySiteId);

            // Assign the first site, or the primary site if the element exists in none
            $this->update(
                Install::GM_ADDRESSES,
                ['[[siteId]]' => ($siteIds[0] ?? $primarySiteId)],
                ['[[id]]' => $address['id']]
            );

        }
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
            ->orderBy(['[[elementId]]' => SORT_ASC, '[[fieldId]]' => SORT_ASC]);

        // Loop over each element and field, in batches to keep memory flat
        foreach (Db::each($pairs) as $pair) {

            // Get the sites this element exists in, primary site first
            $siteIds = $this->_elementSiteIds((int) $pair['elementId'], $primarySiteId);

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

                // Copy the source Address to this site, with a fresh ID
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

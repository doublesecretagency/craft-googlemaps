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

namespace doublesecretagency\googlemaps\helpers;

use Craft;
use craft\db\Query;
use craft\db\Table;
use craft\events\ApplyFieldSaveEvent;
use craft\events\ConfigEvent;
use craft\helpers\DateTimeHelper;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use craft\services\Fields;
use craft\services\ProjectConfig;
use doublesecretagency\googlemaps\fields\AddressField;
use doublesecretagency\googlemaps\migrations\Install;
use Exception;
use Yii;
use yii\base\Event;

/**
 * Class FieldConversionHelper
 * @since 4.6.0
 */
class FieldConversionHelper
{

    /**
     * List of Address columns.
     *
     * @var array
     */
    private static array $addressColumns = [
        'elementId', 'siteId', 'fieldId',
        'formatted', 'raw',
        'name', 'street1', 'street2',
        'city', 'state', 'zip',
        'county', 'country','neighborhood',
        'lat', 'lng', 'zoom',
        'dateCreated', 'dateUpdated', 'uid'
    ];

    /**
     * Prefix the table name.
     *
     * @param string $table
     * @return string
     */
    private static function _prefix(string $table): string
    {
        // Get the table prefix
        $tablePrefix = Craft::$app->getConfig()->getDb()->tablePrefix;

        // If no prefix, return the table name as-is
        if (!$tablePrefix) {
            return $table;
        }

        // Remove trailing underscore
        $tablePrefix = rtrim($tablePrefix, '_');

        // Return the prefixed table name
        return "{$tablePrefix}_{$table}";
    }

    /**
     * Converts Address fields from the Mapbox (Double Secret Agency) plugin.
     *
     * @return void
     */
    public static function convertMapboxFields(): void
    {
        // If Mapbox plugin is not installed and enabled, bail
        if (!Craft::$app->getPlugins()->isPluginEnabled('mapbox')) {
            return;
        }

        // When a single project config line gets updated
        Event::on(
            ProjectConfig::class,
            ProjectConfig::EVENT_UPDATE_ITEM,
            static function (ConfigEvent $event) {

                // Get old and new types
                $oldType = $event->oldValue['type'] ?? null;
                $newType = $event->newValue['type'] ?? null;

                // If old type wasn't a Mapbox Address field, bail
                if (!($oldType === 'doublesecretagency\mapbox\fields\AddressField')) {
                    return;
                }

                // If new type is not a Google Maps Address field, bail
                if (!($newType === 'doublesecretagency\googlemaps\fields\AddressField')) {
                    return;
                }

                // Get the field's UID
                $uid = str_replace('fields.', '', $event->path);

                // Get the actual field
                $field = Craft::$app->getFields()->getFieldByUid($uid);

                // If unable to get the field, bail
                if (!$field) {
                    return;
                }

                // Define the table names
                $gm_addresses = static::_prefix('googlemaps_addresses');
                $mb_addresses = static::_prefix('mapbox_addresses');

                // Merge and escape column names
                $columns = '[['.implode(']],[[', self::$addressColumns).']]';

                // Copy field's rows from `mapbox_addresses` into `googlemaps_addresses`
                $sql = <<<SQL
INSERT INTO [[{$gm_addresses}]] ({$columns})
SELECT {$columns}
FROM [[{$mb_addresses}]]
WHERE [[fieldId]] = :fieldId
  AND NOT EXISTS (
    SELECT 1
    FROM [[{$gm_addresses}]]
    WHERE [[{$gm_addresses}]].[[uid]] = [[{$mb_addresses}]].[[uid]]
);
SQL;

                // Execute the SQL statement
                Yii::$app->db->createCommand($sql)
                    ->bindValues([':fieldId' => $field->id])
                    ->execute();

            }
        );
    }

    // ========================================================================= //

    /**
     * Converts Address fields from the Maps (Ether Creative) plugin.
     *
     * @return void
     */
    public static function convertMapsFields(): void
    {
        // If Maps plugin is not installed and enabled, bail
        if (!Craft::$app->getPlugins()->isPluginEnabled('simplemap')) {
            return;
        }

        // If unable to transfer data between field types, bail (requires Craft 5.5.3+)
        if (version_compare(Craft::$app->getVersion(), '5.5.3', '<')) {
            return;
        }

        // Before the Project Config change is applied
        Event::on(
            Fields::class,
            Fields::EVENT_BEFORE_APPLY_FIELD_SAVE,
            static function (ApplyFieldSaveEvent $event) {

                // If no field is provided, bail
                if (!$field = $event->field) {
                    return;
                }

                // If not currently an Ether "Map" field, bail
                if (!($field instanceof \ether\simplemap\fields\MapField)) {
                    return;
                }

                // If no new field config, bail
                if (!$newConfig = $event->config) {
                    return;
                }

                // If new config isn't an "Address (Google Maps)" field, bail
                if ($newConfig['type'] !== AddressField::class) {
                    return;
                }

                // Migrate the field data
                static::_migrateField($field->uid);
            }
        );

    }

    /**
     * Migrate the data of a single Address field.
     *
     * @param string $uid
     * @return void
     */
    private static function _migrateField(string $uid): void
    {
        // Get all field layouts that contain this field
        $fieldLayoutConfigs = (new Query())
            ->select('[[config]]')
            ->from([Table::FIELDLAYOUTS])
            ->where(['like', '[[config]]', $uid])
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
                    if (($element['fieldUid'] ?? null) === $uid) {

                        // Migrate Address data for this field layout element
                        static::_migrateLayoutElement($element['uid']);
                    }
                }
            }
        }

    }

    /**
     * Migrate the data of a single field layout element.
     *
     * @param string $fieldLayoutElementUid
     * @return void
     */
    private static function _migrateLayoutElement(string $fieldLayoutElementUid): void
    {
        // Get all element sites with content containing this field layout element
        $elementSites = (new Query())
            ->select('[[id]], [[elementId]], [[siteId]], [[content]], [[dateCreated]], [[dateUpdated]]')
            ->from(['elements_sites' => Table::ELEMENTS_SITES])
            ->where(['like', '[[content]]', $fieldLayoutElementUid])
            ->all();

        // If no element sites found, bail
        if (!$elementSites) {
            return;
        }

        // Initialize array for batch insertion
        $rows = [];

        // Loop over each element site
        foreach ($elementSites as &$elementSite) {

            // Decode the content
            $elementSiteContent = Json::decode($elementSite['content']) ?? [];

            // Get the Address data from the content
            $content = $elementSiteContent[$fieldLayoutElementUid] ?? null;

            // If no Address data found, bail
            if (!$content) {
                continue;
            }

            // If content is a string, throw error
            if (is_string($content)) {
                throw new Exception("Invalid content for element site [{$elementSite['id']}]: {$content}");
            }

            // Get current time as a fallback
            $now = DateTimeHelper::currentUTCDateTime()->format('Y-m-d H:i:s');

            // Convert the Address data
            $gmAddress = static::_convertData($content, [
                'elementId'   => (int) $elementSite['elementId'],
                'siteId'      => (int) $elementSite['siteId'],
                'fieldId'     => (int) $content['fieldId'],
                'dateCreated' => ($elementSite['dateCreated'] ?? $now),
                'dateUpdated' => ($elementSite['dateUpdated'] ?? $now),
            ]);

            // If Address data is invalid, skip
            if (!$gmAddress) {
                continue;
            }

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

            // Encode the raw address data
            $gmAddress['raw'] = Json::encode($gmAddress['raw']);

            // Append to the batch
            $rows[] = $gmAddress;
        }

        // Unset to prevent weird side effects
        unset($elementSite);

        // If no rows to insert, bail
        if (!$rows) {
            return;
        }

        // Extract column names from the first row
        $columns = array_keys($rows[0]);

        // Prepare the base SQL using `batchInsert`
        $sql = Yii::$app->db
            ->createCommand()
            ->batchInsert(Install::GM_ADDRESSES, $columns, $rows)
            ->getRawSql();

        // If using MySQL
        if (Craft::$app->getDb()->getIsMysql()) {
            // MySQL syntax to update conflicting rows
            $updateClause = implode(', ', array_map(static function($col) {
                return "$col = VALUES($col)";
            }, $columns));
            $sql .= " ON DUPLICATE KEY UPDATE {$updateClause}";
        } else {
            // Postgres syntax to update conflicting rows
            $updateClause = implode(', ', array_map(static function($col) {
                return "\"$col\" = EXCLUDED.\"$col\"";
            }, $columns));
            $sql .= " ON CONFLICT (\"elementId\", \"siteId\", \"fieldId\") DO UPDATE SET {$updateClause}";
        }

        try {
            // Batch insert these Addresses
            Yii::$app->db
                ->createCommand($sql)
                ->execute();
        } catch (Exception $e) {
            // Log error
            $error = $e->getMessage();
            Craft::error("Error during batch insert: {$error}");
        }

    }

    /**
     * Convert content of a single Address.
     *
     * @param array $content
     * @param array $meta
     * @return array|null
     */
    private static function _convertData(array $content, array $meta): ?array
    {
        // Extract parts
        $parts = ($content['parts'] ?? null);

        // If no parts, bail
        if (!$parts) {
            return null;
        }

        // Split the address by commas
        $address = ($parts['address'] ?? '');
        $addressParts = explode(',', $address);

        // Get the neighborhood
        if (count($addressParts) > 1) {
            // Take the last part as neighborhood
            $neighborhood = array_pop($addressParts);
            // Convert empty string to null
            $neighborhood = (trim($neighborhood) ?: null);
        } else {
            // If no commas, set neighborhood to null
            $neighborhood = null;
        }

        // Recombine what's left of the address
        $recombine = implode(',', $addressParts);

        // Compile street address
        $street1 = (trim("{$parts['number']} {$recombine}") ?: null);

        try {
            // Generate a new UUID
            $uid = StringHelper::UUID();
        } catch (Exception $e) {
            // Fallback to null
            $uid = null;
        }

        // Add the Address data to the batch
        return array_merge($meta, [
            'formatted'    => ($content['address'] ?? null),
            'raw'          => ($content ?: null),
            'name'         => null,
            'street1'      => $street1,
            'street2'      => null,
            'city'         => ($parts['city'] ?? null),
            'state'        => ($parts['state'] ?? null),
            'zip'          => ($parts['postcode'] ?? null),
            'neighborhood' => $neighborhood,
            'county'       => ($parts['county'] ?? null),
            'country'      => ($parts['country'] ?? null),
            'countryCode'  => null,
            'placeId'      => null,
            'lat'          => (float) $content['lat'],
            'lng'          => (float) $content['lng'],
            'zoom'         => (int) ($content['zoom'] ?? 11),
            'uid'          => $uid,
        ]);
    }

}

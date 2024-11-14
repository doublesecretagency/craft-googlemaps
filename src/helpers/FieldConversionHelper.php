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
use craft\events\ApplyFieldSaveEvent;
use craft\events\ConfigEvent;
use craft\helpers\ElementHelper;
use craft\services\Fields;
use craft\services\ProjectConfig;
use doublesecretagency\googlemaps\fields\AddressField;
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

        // If unable to transfer data between field types, bail (requires Craft 4.13.0+)
        if (!class_exists(ApplyFieldSaveEvent::class)) {
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

                // If not a Google Maps Address field, bail
                if (!($field instanceof AddressField)) {
                    return;
                }

                // Get the original field
                $oldField = Craft::$app->getFields()->getFieldById($field->id);

                // If no original field, bail
                if (!$oldField) {
                    return;
                }

                // If original field wasn't a Maps Map field, bail
                if (!$oldField instanceof \ether\simplemap\fields\MapField) {
                    return;
                }

                // Compile the column name
                $fieldColumn = ElementHelper::fieldColumn($field->columnPrefix, $field->handle, $field->columnSuffix);

                // If using MySQL
                if (Craft::$app->getDb()->getIsMysql()) {
                    $query = static::_queryMySqlMapsFields($fieldColumn);
                } else {
                    $query = static::_queryPostgresMapsFields($fieldColumn);
                }

                // Execute the SQL statement
                Yii::$app->db->createCommand($query)
                    ->bindValues([':fieldId' => $field->id])
                    ->execute();
            }
        );

    }

    /**
     * Returns the MySQL query for converting Maps fields to Google Maps fields.
     *
     * @param string $fieldColumn
     * @return string
     */
    private static function _queryMySqlMapsFields(string $fieldColumn): string
    {
        // Define the table names
        $content      = static::_prefix('content');
        $gm_addresses = static::_prefix('googlemaps_addresses');

        // Merge and escape column names
        $addressColumns = '[['.implode(']],[[', self::$addressColumns).']]';

        // Copy field's data from the `content` table to `googlemaps_addresses`
        return <<<MYSQL
INSERT INTO [[{$gm_addresses}]] ({$addressColumns})

SELECT
    [[{$content}]].[[elementId]] AS [[elementId]],
    [[{$content}]].[[siteId]] AS [[siteId]],
    :fieldId AS [[fieldId]],
    NULLIF(JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.address')), '') AS [[formatted]],
    [[{$content}]].[[{$fieldColumn}]] AS [[raw]],
    NULL AS [[name]],
    -- Combine street number with the first part of the address and replace empty string with NULL
    NULLIF(
        CONCAT(
            JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.parts.number')), ' ',
            SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.parts.address')), ', ', 1)
        ), 
        ''
    ) AS [[street1]],
    NULL AS [[street2]],
    -- Replace empty strings with NULL for city, state, zip, county, and country
    NULLIF(JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.parts.city')), '') AS [[city]],
    NULLIF(JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.parts.state')), '') AS [[state]],
    NULLIF(JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.parts.postcode')), '') AS [[zip]],
    NULLIF(JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.parts.county')), '') AS [[county]],
    NULLIF(JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.parts.country')), '') AS [[country]],
    -- Extract neighborhood (second part of the address, if exists)
    CASE
        WHEN JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.parts.address')) LIKE '%,%'
            THEN SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.parts.address')), ', ', -1)
        ELSE NULL
    END AS [[neighborhood]],
    -- Extract lat and lng from JSON, handling 'null' strings
    NULLIF(NULLIF(JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.lat')), ''), 'null') AS [[lat]],
    NULLIF(NULLIF(JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.lng')), ''), 'null') AS [[lng]],
    NULLIF(NULLIF(JSON_UNQUOTE(JSON_EXTRACT([[{$content}]].[[{$fieldColumn}]], '$.zoom')), ''), 'null') AS [[zoom]],
    [[{$content}]].[[dateCreated]],
    [[{$content}]].[[dateUpdated]],
    [[{$content}]].[[uid]]

FROM [[{$content}]]

WHERE {$fieldColumn} IS NOT NULL
    AND NOT EXISTS (
        SELECT 1
        FROM [[{$gm_addresses}]]
        WHERE [[{$gm_addresses}]].[[uid]] = [[{$content}]].[[uid]]
    )

ORDER BY [[{$content}]].[[elementId]] ASC, [[{$content}]].[[siteId]] ASC;
MYSQL;
    }

    /**
     * Returns the Postgres query for converting Maps fields to Google Maps fields.
     *
     * @param string $fieldColumn
     * @return string
     */
    private static function _queryPostgresMapsFields(string $fieldColumn): string
    {
        // Define the table names
        $content      = static::_prefix('content');
        $gm_addresses = static::_prefix('googlemaps_addresses');

        // Merge and escape column names
        $addressColumns = '[['.implode(']],[[', self::$addressColumns).']]';

        // Copy field's data from the `content` table to `googlemaps_addresses`
        return <<<POSTGRES
INSERT INTO [[{$gm_addresses}]] ({$addressColumns})

SELECT
    [[{$content}]].[[elementId]] AS [[elementId]],
    [[{$content}]].[[siteId]] AS [[siteId]],
    :fieldId AS [[fieldId]],
    NULLIF(([[{$content}]].[[{$fieldColumn}]]::jsonb)->>'address', '') AS [[formatted]],
    [[{$content}]].[[{$fieldColumn}]] AS [[raw]],
    NULL AS [[name]],
    -- Use COALESCE to handle NULLs in street1 concatenation
    NULLIF(
        COALESCE(([[{$content}]].[[{$fieldColumn}]]::jsonb)->'parts'->>'number', '') || ' ' || 
        COALESCE(SPLIT_PART(([[{$content}]].[[{$fieldColumn}]]::jsonb)->'parts'->>'address', ', ', 1), ''), 
        ''
    ) AS [[street1]],
    NULL AS [[street2]],
    -- Replace empty strings with NULL for city, state, zip, county, and country
    NULLIF(([[{$content}]].[[{$fieldColumn}]]::jsonb)->'parts'->>'city', '') AS [[city]],
    NULLIF(([[{$content}]].[[{$fieldColumn}]]::jsonb)->'parts'->>'state', '') AS [[state]],
    NULLIF(([[{$content}]].[[{$fieldColumn}]]::jsonb)->'parts'->>'postcode', '') AS [[zip]],
    NULLIF(([[{$content}]].[[{$fieldColumn}]]::jsonb)->'parts'->>'county', '') AS [[county]],
    NULLIF(([[{$content}]].[[{$fieldColumn}]]::jsonb)->'parts'->>'country', '') AS [[country]],
    -- Extract neighborhood (second part of the address, if exists)
    CASE
        WHEN ([[{$content}]].[[{$fieldColumn}]]::jsonb)->'parts'->>'address' LIKE '%,%'
            THEN SPLIT_PART(([[{$content}]].[[{$fieldColumn}]]::jsonb)->'parts'->>'address', ', ', 2)
        ELSE NULL
    END AS [[neighborhood]],
    -- Extract lat and lng from JSON, with regex check for numeric values
    CASE 
        WHEN ([[{$content}]].[[{$fieldColumn}]]::jsonb)->>'lat' ~ '^-?\d+(\.\d+)?$' 
        THEN (([[{$content}]].[[{$fieldColumn}]]::jsonb)->>'lat')::numeric 
        ELSE NULL 
    END AS [[lat]],
    CASE 
        WHEN ([[{$content}]].[[{$fieldColumn}]]::jsonb)->>'lng' ~ '^-?\d+(\.\d+)?$' 
        THEN (([[{$content}]].[[{$fieldColumn}]]::jsonb)->>'lng')::numeric 
        ELSE NULL 
    END AS [[lng]],
    CASE 
        WHEN ([[{$content}]].[[{$fieldColumn}]]::jsonb)->>'zoom' ~ '^\d+(\.\d+)?$' 
        THEN (([[{$content}]].[[{$fieldColumn}]]::jsonb)->>'zoom')::numeric 
        ELSE NULL 
    END AS [[zoom]],
    [[{$content}]].[[dateCreated]],
    [[{$content}]].[[dateUpdated]],
    [[{$content}]].[[uid]]
FROM [[{$content}]]
WHERE [[{$content}]].[[{$fieldColumn}]] IS NOT NULL
    AND NOT EXISTS (
        SELECT 1
        FROM [[{$gm_addresses}]]
        WHERE [[{$gm_addresses}]].[[uid]] = [[{$content}]].[[uid]]
    )
ORDER BY [[{$content}]].[[elementId]] ASC, [[{$content}]].[[siteId]] ASC;
POSTGRES;
    }

}

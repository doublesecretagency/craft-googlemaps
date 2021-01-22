<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2021 Double Secret Agency
 */

namespace doublesecretagency\googlemaps\migrations;

use Craft;
use craft\db\Migration;
use craft\fields\MissingField;
use craft\helpers\Json;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\smartmap\SmartMap;
use Throwable;
use yii\base\NotSupportedException;

/**
 * FromSmartMap Migration
 * @since 4.0.0
 */
class FromSmartMap
{

    /**
     * Class name constants for both Address fields
     */
    const SMARTMAP_ADDRESS   = 'doublesecretagency\\smartmap\\fields\\Address';
    const GOOGLEMAPS_ADDRESS = 'doublesecretagency\\googlemaps\\fields\\AddressField';

    /**
     * @var Migration Internalized migration object.
     */
    private static $_migration;

    /**
     * @var array Default Address field layout.
     */
    private static $_defaultLayout = [
        'street1' => ['enable' => 1, 'width' => 100, 'position' => 1],
        'street2' => ['enable' => 1, 'width' => 100, 'position' => 2],
        'city'    => ['enable' => 1, 'width' =>  50, 'position' => 3],
        'state'   => ['enable' => 1, 'width' =>  15, 'position' => 4],
        'zip'     => ['enable' => 1, 'width' =>  35, 'position' => 5],
        'country' => ['enable' => 1, 'width' => 100, 'position' => 6],
        'lat'     => ['enable' => 1, 'width' =>  50, 'position' => 7],
        'lng'     => ['enable' => 1, 'width' =>  50, 'position' => 8],
    ];

    // ========================================================================= //

    /**
     * Install and configure tables from scratch.
     *
     * @param $migration
     */
    public static function update($migration)
    {
        // Share migration locally
        static::$_migration = $migration;

        // Ensure Smart Map data is properly configured
        static::_smartMap_replaceFreegeoipWithIpstack();
        static::_smartMap_addPositionToFieldLayouts();

        // Convert to Google Maps data and make adjustments
        static::_googleMaps_renameAddressTable();
        static::_googleMaps_addNewColumns();
        static::_googleMaps_migrateAddressFields();
        static::_googleMaps_migratePluginSettings();

        // Uninstall Smart Map
        Craft::$app->plugins->uninstallPlugin('smart-map');
    }

    // ========================================================================= //

    /**
     * Smart Map
     * Replace FreeGeoIp.net with ipstack.
     */
    private static function _smartMap_replaceFreegeoipWithIpstack()
    {
        // Get settings
        $settings = SmartMap::$plugin->getSettings();

        // If no settings exist, bail
        if (!$settings) {
            return;
        }

        // Convert model into array
        $settings = $settings->getAttributes();

        // If setting doesn't exist, bail
        if (!isset($settings['geolocation'])) {
            return;
        }

        // If not set to "freegeoip", bail
        if ('freegeoip' != $settings['geolocation']) {
            return;
        }

        // Modify settings
        $settings['geolocation'] = 'ipstack';

        // Save settings
        Craft::$app->getPlugins()->savePluginSettings(SmartMap::$plugin, $settings);
    }

    /**
     * Smart Map
     * Add a `position` value to Address layout subfields.
     *
     * @throws Throwable
     */
    private static function _smartMap_addPositionToFieldLayouts()
    {
        // If admin changes are not allowed, bail
        if (!Craft::$app->getConfig()->getGeneral()->allowAdminChanges) {
            return;
        }

        // Get fields service
        $fieldsService = Craft::$app->getFields();

        // Get all fields
        $allFields = $fieldsService->getAllFields(false);

        // Loop through all fields
        foreach ($allFields as $field) {

            // If not an Address field, skip
            if (static::SMARTMAP_ADDRESS !== get_class($field)) {
                continue;
            }

            // Get field settings
            $settings = $field->getSettings();

            // If no layout
            if (!isset($settings['layout'])) {

                // Use the default layout
                $field->layout = static::$_defaultLayout;
                $fieldsService->saveField($field, false);

                // Skip to next field
                continue;
            }

            // Get existing field layout
            $layout = $settings['layout'];

            // Get first subfield value
            $subfield = reset($layout);

            // If subfield is misconfigured
            if (!isset($subfield['width']) || !isset($subfield['enable'])) {

                // Use the default layout
                $field->layout = static::$_defaultLayout;
                $fieldsService->saveField($field, false);

                // Skip to next field
                continue;
            }

            // If positions are already set, skip to next field
            if (isset($subfield['position'])) {
                continue;
            }

            // Add position to each subfield
            $i = 1;
            foreach($layout as $handle => $settings){
                $layout[$handle]['position'] = $i++;
            }

            // Save updated layout
            $field->layout = $layout;
            $fieldsService->saveField($field, false);

        }
    }

    /**
     * Google Maps
     * Rename table containing Address data.
     */
    private static function _googleMaps_renameAddressTable()
    {
        // If the table exists doesn't exist, bail
        if (!static::$_migration->db->tableExists('{{%smartmap_addresses}}')) {
            echo "Can't rename `smartmap_addresses` table because it does not exist.\n";
            return;
        }

        // Rename the table
        static::$_migration->renameTable('{{%smartmap_addresses}}', '{{%googlemaps_addresses}}');
    }

    /**
     * Google Maps
     * Add new columns for storing additional address data.
     *
     * @throws NotSupportedException
     */
    private static function _googleMaps_addNewColumns()
    {
        // If table doesn't exist, bail
        if (!static::$_migration->db->tableExists('{{%googlemaps_addresses}}')) {
            echo "Can't add new columns, the `googlemaps_addresses` table does not yet exist.\n";
            return;
        }

        // Add `formatted` column
        if (!static::$_migration->db->columnExists('{{%googlemaps_addresses}}', 'formatted')) {
            static::$_migration->addColumn(
                '{{%googlemaps_addresses}}',
                'formatted',
                static::$_migration->string()->after('fieldId')
            );
        }

        // Add `raw` column
        if (!static::$_migration->db->columnExists('{{%googlemaps_addresses}}', 'raw')) {
            static::$_migration->addColumn(
                '{{%googlemaps_addresses}}',
                'raw',
                static::$_migration->text()->after('formatted')
            );
        }

        // Add `zoom` column
        if (!static::$_migration->db->columnExists('{{%googlemaps_addresses}}', 'zoom')) {
            static::$_migration->addColumn(
                '{{%googlemaps_addresses}}',
                'zoom',
                static::$_migration->tinyInteger(2)->after('lng')
            );
        }

        // Set zoom for existing addresses with valid coordinates
        static::$_migration->update('{{%googlemaps_addresses}}', [
            'zoom' => 11
        ], ['and',
            ['zoom' => null],
            ['not', ['lat' => null]],
            ['not', ['lng' => null]],
        ], [], false);
    }

    /**
     * Google Maps
     * Update the settings of existing Address fields.
     */
    private static function _googleMaps_migrateAddressFields()
    {
        // If admin changes are not allowed, bail
        if (!Craft::$app->getConfig()->getGeneral()->allowAdminChanges) {
            return;
        }

        // Get fields service
        $fieldsService = Craft::$app->getFields();

        // Get all fields
        $allFields = $fieldsService->getAllFields(false);

        // Loop through all fields
        foreach ($allFields as $field) {

            // Get field class
            $fieldClass = get_class($field);

            // Compare field classes
            $activeAddress   = ($fieldClass === static::SMARTMAP_ADDRESS);
            $inactiveAddress = ($fieldClass === MissingField::class) && ($field->expectedType === static::SMARTMAP_ADDRESS);

            // If field is an Address, update it
            if ($activeAddress || $inactiveAddress) {

                // Get Address field settings
                $oldSettings = $field->getSettings();

                // If Missing field, go one level deeper
                if ($inactiveAddress) {
                    $oldSettings = Json::decode($oldSettings['settings']);
                }

                // Update field type and settings
                static::_updateFieldConfig($field->id, $oldSettings);

            }

        }
    }

    /**
     * Google Maps
     * Migrate all plugin settings from Smart Map.
     */
    private static function _googleMaps_migratePluginSettings()
    {
        // Get settings for both plugins
        $smartMap = (SmartMap::$plugin->getSettings()->getAttributes() ?? false);
        $googleMaps = (GoogleMapsPlugin::$plugin->getSettings()->getAttributes() ?? []);

        // If no Smart Map settings exist, bail
        if (!$smartMap) {
            return;
        }

        // Migrate settings
        $googleMaps['browserKey']          = ($smartMap['googleBrowserKey']  ?? $googleMaps['browserKey']);
        $googleMaps['serverKey']           = ($smartMap['googleServerKey']   ?? $googleMaps['serverKey']);
        $googleMaps['geolocationService']  = ($smartMap['geolocation']       ?? $googleMaps['geolocationService']);
        $googleMaps['ipstackApiAccessKey'] = ($smartMap['ipstackAccessKey']  ?? $googleMaps['ipstackApiAccessKey']);
        $googleMaps['maxmindLicenseKey']   = ($smartMap['maxmindLicenseKey'] ?? $googleMaps['maxmindLicenseKey']);
        $googleMaps['maxmindService']      = ($smartMap['maxmindService']    ?? $googleMaps['maxmindService']);
        $googleMaps['maxmindUserId']       = ($smartMap['maxmindUserId']     ?? $googleMaps['maxmindUserId']);

        // Save settings
        Craft::$app->getPlugins()->savePluginSettings(GoogleMapsPlugin::$plugin, $googleMaps);

        // Ensure plugin is enabled
        Craft::$app->getPlugins()->enablePlugin('google-maps');
    }

    // ========================================================================= //

    /**
     * Update existing field configurations.
     *
     * @param int $id ID of field
     * @param array $old Original field settings
     */
    private static function _updateFieldConfig(int $id, array $old)
    {
        // Whether the old settings contain valid coordinates
        $validCoords = (
            isset($old['dragPinLatitude'])  && $old['dragPinLatitude'] &&
            isset($old['dragPinLongitude']) && $old['dragPinLongitude']
        );

        // If coordinates are valid
        if ($validCoords) {
            // Port existing coordinates
            $coordinates = [
                'lat' => (float) ($old['dragPinLatitude'] ?? null),
                'lng' => (float) ($old['dragPinLongitude'] ?? null),
                'zoom' => (int) ($old['dragPinZoom'] ?? null),
            ];
        } else {
            // Otherwise, use empty coordinates
            $coordinates = [
                'lat' => null,
                'lng' => null,
                'zoom' => null,
            ];
        }

        // Update field settings
        $newSettings = [
            'showMap' => null,
            'mapOnStart' => 'close',
            'mapOnSearch' => 'open',
            'visibilityToggle' => 'both',
            'coordinatesMode' => 'readOnly',
            'coordinatesDefault' => $coordinates,
            'subfieldConfig' => [
                'street1' => [
                    'label' => 'Street Address',
                    'width' => (int) ($old['layout']['street1']['width'] ?? 100),
                    'enabled' => (int) ($old['layout']['street1']['enable'] ?? 1),
                    'position' => (int) ($old['layout']['street1']['position'] ?? 1),
                ],
                'street2' => [
                    'label' => 'Apartment or Suite',
                    'width' => (int) ($old['layout']['street2']['width'] ?? 100),
                    'enabled' => (int) ($old['layout']['street2']['enable'] ?? 1),
                    'position' => (int) ($old['layout']['street2']['position'] ?? 2),
                ],
                'city' => [
                    'label' => 'City',
                    'width' => (int) ($old['layout']['city']['width'] ?? 50),
                    'enabled' => (int) ($old['layout']['city']['enable'] ?? 1),
                    'position' => (int) ($old['layout']['city']['position'] ?? 3),
                ],
                'state' => [
                    'label' => 'State',
                    'width' => (int) ($old['layout']['state']['width'] ?? 15),
                    'enabled' => (int) ($old['layout']['state']['enable'] ?? 1),
                    'position' => (int) ($old['layout']['state']['position'] ?? 4),
                ],
                'zip' => [
                    'label' => 'Zip Code',
                    'width' => (int) ($old['layout']['zip']['width'] ?? 35),
                    'enabled' => (int) ($old['layout']['zip']['enable'] ?? 1),
                    'position' => (int) ($old['layout']['zip']['position'] ?? 5),
                ],
                'country' => [
                    'label' => 'Country',
                    'width' => (int) ($old['layout']['country']['width'] ?? 100),
                    'enabled' => (int) ($old['layout']['country']['enable'] ?? 1),
                    'position' => (int) ($old['layout']['country']['position'] ?? 6),
                ]
            ]
        ];

        // Update field configuration
        static::$_migration->update('{{%fields}}', [
            'type' => static::GOOGLEMAPS_ADDRESS,
            'settings' => Json::encode($newSettings)
        ], [
            'id' => $id
        ], [], false);
    }

}

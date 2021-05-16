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
use craft\db\Query;
use craft\fields\MissingField;
use craft\helpers\Json;
use doublesecretagency\googlemaps\fields\AddressField;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\records\Address;

/**
 * FromSmartMap Migration
 * @since 4.0.0
 */
class FromSmartMap
{

    /**
     * Install and configure tables from scratch.
     */
    public static function update()
    {
        // Migrate Smart Map data to Google Maps
        static::_migratePluginSettings();
        static::_migrateAddressFieldSettings();
        static::_migrateAddressFieldData();

        // Uninstall Smart Map
        Craft::$app->getPlugins()->uninstallPlugin('smart-map', true);
    }

    // ========================================================================= //

    /**
     * Migrate all plugin settings from Smart Map.
     */
    private static function _migratePluginSettings()
    {
        // If Smart Map class does not exist, bail
        if (!class_exists(\doublesecretagency\smartmap\SmartMap::class)) {
            $message = "Can't migrate Smart Map plugin settings, composer package has been removed.";
            Craft::warning($message, __METHOD__);
            return;
        }

        // Get plugins service
        $plugins = Craft::$app->getPlugins();

        // If Smart Map is not installed, bail
        if (!$plugins->isPluginEnabled('smart-map')) {
            $message = "Can't migrate Smart Map plugin settings, Smart Map plugin is not installed.";
            Craft::warning($message, __METHOD__);
            return;
        }

        // Get existing license key, if available
        GoogleMapsPlugin::$migrateLicenseKey = $plugins->getPluginLicenseKey('smart-map');

        // Get settings for both plugins
        $smartMap = (\doublesecretagency\smartmap\SmartMap::$plugin->getSettings()->getAttributes() ?? false);
        $googleMaps = (GoogleMapsPlugin::$plugin->getSettings()->getAttributes() ?? []);

        // If no Smart Map settings exist, bail
        if (!$smartMap) {
            $message = "Can't migrate Smart Map plugin settings, no settings data was found.";
            Craft::warning($message, __METHOD__);
            return;
        }

        // Replace `freegeoip` with `ipstack` (if necessary)
        $geolocationService = ($smartMap['geolocation'] ?? $googleMaps['geolocationService']);
        $geolocationService = str_replace('freegeoip', 'ipstack', $geolocationService);

        // Migrate settings
        $googleMaps['browserKey']          = ($smartMap['googleBrowserKey']  ?? $googleMaps['browserKey']);
        $googleMaps['serverKey']           = ($smartMap['googleServerKey']   ?? $googleMaps['serverKey']);
        $googleMaps['geolocationService']  = $geolocationService;
        $googleMaps['ipstackApiAccessKey'] = ($smartMap['ipstackAccessKey']  ?? $googleMaps['ipstackApiAccessKey']);
        $googleMaps['maxmindLicenseKey']   = ($smartMap['maxmindLicenseKey'] ?? $googleMaps['maxmindLicenseKey']);
        $googleMaps['maxmindService']      = ($smartMap['maxmindService']    ?? $googleMaps['maxmindService']);
        $googleMaps['maxmindUserId']       = ($smartMap['maxmindUserId']     ?? $googleMaps['maxmindUserId']);

        // Store collection of settings to be imported
        GoogleMapsPlugin::$migrateSettings = $googleMaps;
    }

    /**
     * Migrate the settings of existing Address fields.
     */
    private static function _migrateAddressFieldSettings()
    {
        // Class name of Smart Map Address field type
        $smAddress = 'doublesecretagency\\smartmap\\fields\\Address';

        // Get fields service
        $fieldsService = Craft::$app->getFields();

        // Get all fields
        $allFields = $fieldsService->getAllFields(false);

        // Loop through all fields
        foreach ($allFields as $smField) {

            // Get field class
            $fieldClass = get_class($smField);

            // Compare field classes
            $activeAddress   = ($fieldClass === $smAddress);
            $inactiveAddress = ($fieldClass === MissingField::class) && ($smField->expectedType === $smAddress);

            // If field is an Address, update it
            if ($activeAddress || $inactiveAddress) {

                // Get Address field settings
                $oldSettings = $smField->getSettings();

                // If Missing field, go one level deeper
                if ($inactiveAddress) {
                    $oldSettings = Json::decode($oldSettings['settings']);
                }

                // Reconfigure field settings
                $newSettings = static::_updateFieldConfig($oldSettings);

                // Create a fresh Google Maps Address Field
                $gmField = new AddressField();

                // Migrate field configuration
                $gmField->id           = $smField->id;
                $gmField->uid          = $smField->uid;
                $gmField->groupId      = $smField->groupId;
                $gmField->name         = $smField->name;
                $gmField->handle       = $smField->handle;
                $gmField->instructions = $smField->instructions;
                $gmField->searchable   = $smField->searchable;

                // Migrate field settings
                $gmField->showMap            = $newSettings['showMap'];
                $gmField->mapOnStart         = $newSettings['mapOnStart'];
                $gmField->mapOnSearch        = $newSettings['mapOnSearch'];
                $gmField->visibilityToggle   = $newSettings['visibilityToggle'];
                $gmField->coordinatesMode    = $newSettings['coordinatesMode'];
                $gmField->coordinatesDefault = $newSettings['coordinatesDefault'];
                $gmField->subfieldConfig     = $newSettings['subfieldConfig'];

                // Save reconfigured field
                $fieldsService->saveField($gmField, false);
            }

        }
    }

    /**
     * Migrate the data of existing Address fields.
     */
    private static function _migrateAddressFieldData()
    {
        // Get all existing Smart Map data
        $rows = (new Query())
            ->select('*')
            ->from('{{%smartmap_addresses}}')
            ->orderBy('[[id]]')
            ->all();

        // Loop through existing Address records
        foreach ($rows as $data) {

            // Attempt to load an existing record
            $record = Address::findOne([
                'elementId' => $data['elementId'],
                'fieldId'   => $data['fieldId'],
            ]);

            // If record already exists, skip it
            if ($record) {
                continue;
            }

            // Create a new Address for Google Maps
            $record = new Address([
                'id'          => ($data['id'] ?: null),
                'elementId'   => ($data['elementId'] ?: null),
                'fieldId'     => ($data['fieldId'] ?: null),
                'formatted'   => null,
                'raw'         => null,
                'street1'     => ($data['street1'] ?: null),
                'street2'     => ($data['street2'] ?: null),
                'city'        => ($data['city'] ?: null),
                'state'       => ($data['state'] ?: null),
                'zip'         => ($data['zip'] ?: null),
                'country'     => ($data['country'] ?: null),
                'lat'         => ($data['lat'] ?: null),
                'lng'         => ($data['lng'] ?: null),
                'zoom'        => 11,
                'dateCreated' => ($data['dateCreated'] ?: null),
                'dateUpdated' => ($data['dateUpdated'] ?: null),
                'uid'         => ($data['uid'] ?: null),
            ]);

            // Save record
            $record->save();

        }
    }

    // ========================================================================= //

    /**
     * Update existing field configurations.
     *
     * @param array $old Original field settings
     * @return array Reconfigured field settings
     */
    private static function _updateFieldConfig(array $old): array
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

        // Return updated field settings
        return [
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
    }

}

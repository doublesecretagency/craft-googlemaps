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
use doublesecretagency\googlemaps\models\Settings;
use doublesecretagency\googlemaps\records\Address;

/**
 * FromSmartMap Migration
 * @since 4.0.0
 */
class FromSmartMap
{

    /**
     * Migrate existing data from Smart Map.
     */
    public static function update(): void
    {
        // Don’t make the same project config changes twice
        $existingSchema = Craft::$app->getProjectConfig()->get('plugins.google-maps.schemaVersion', true);

        // If project config has not yet been written
        if (!$existingSchema) {
            // Migrate plugin and field settings
            static::_migratePluginSettings();
            static::_migrateAddressFieldSettings();
        }

        // Migrate all existing Address data
        static::_migrateAddressFieldData();

        // Uninstall Smart Map
        Craft::$app->getPlugins()->uninstallPlugin('smart-map', true);
    }

    // ========================================================================= //

    /**
     * Migrate all plugin settings from Smart Map.
     */
    private static function _migratePluginSettings(): void
    {
        /** @var Settings $settings */
        $settings = GoogleMapsPlugin::$plugin->getSettings();

        // Get settings for both plugins
        $smartMap = Craft::$app->getProjectConfig()->get('plugins.smart-map');
        $googleMaps = ($settings->getAttributes() ?? []);

        // If no Smart Map settings exist, log warning and bail
        if (!$smartMap) {
            $message = "Can't migrate Smart Map plugin settings, no settings data was found.";
            Craft::warning($message, __METHOD__);
            return;
        }

        // Migrate settings
        $googleMaps['browserKey']          = ($smartMap['settings']['googleBrowserKey']  ?? $googleMaps['browserKey']);
        $googleMaps['serverKey']           = ($smartMap['settings']['googleServerKey']   ?? $googleMaps['serverKey']);
        $googleMaps['geolocationService']  = ($smartMap['settings']['geolocation']       ?? $googleMaps['geolocationService']);
        $googleMaps['ipstackApiAccessKey'] = ($smartMap['settings']['ipstackAccessKey']  ?? $googleMaps['ipstackApiAccessKey']);
        $googleMaps['maxmindLicenseKey']   = ($smartMap['settings']['maxmindLicenseKey'] ?? $googleMaps['maxmindLicenseKey']);
        $googleMaps['maxmindService']      = ($smartMap['settings']['maxmindService']    ?? $googleMaps['maxmindService']);
        $googleMaps['maxmindUserId']       = ($smartMap['settings']['maxmindUserId']     ?? $googleMaps['maxmindUserId']);

        // Replace `freegeoip` with `ipstack` (if necessary)
        $googleMaps['geolocationService'] = str_replace('freegeoip', 'ipstack', $googleMaps['geolocationService']);

        // Store collection of settings to be imported
        GoogleMapsPlugin::$migrateSettings = $googleMaps;

        // Store existing license key, if available
        GoogleMapsPlugin::$migrateLicenseKey = ($smartMap['licenseKey'] ?? null);
    }

    /**
     * Migrate the settings of existing Address fields.
     */
    private static function _migrateAddressFieldSettings(): void
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
                    $oldSettings = Json::decodeIfJson($oldSettings['settings']);
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
                $gmField->context      = $smField->context;
                $gmField->instructions = $smField->instructions;
                $gmField->searchable   = $smField->searchable;
                $gmField->translationMethod    = $smField->translationMethod;
                $gmField->translationKeyFormat = $smField->translationKeyFormat;

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
    private static function _migrateAddressFieldData(): void
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
                'id'           => ($data['id'] ?: null),
                'elementId'    => ($data['elementId'] ?: null),
                'fieldId'      => ($data['fieldId'] ?: null),
                'formatted'    => null,
                'raw'          => null,
                'name'         => null,
                'street1'      => ($data['street1'] ?: null),
                'street2'      => ($data['street2'] ?: null),
                'city'         => ($data['city'] ?: null),
                'state'        => ($data['state'] ?: null),
                'zip'          => ($data['zip'] ?: null),
                'neighborhood' => null,
                'county'       => null,
                'country'      => ($data['country'] ?: null),
                'countryCode'  => null,
                'placeId'      => null,
                'lat'          => ($data['lat'] ?: null),
                'lng'          => ($data['lng'] ?: null),
                'zoom'         => 11,
                'dateCreated'  => ($data['dateCreated'] ?: null),
                'dateUpdated'  => ($data['dateUpdated'] ?: null),
                'uid'          => ($data['uid'] ?: null),
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
            'showMap' => false,
            'mapOnStart' => 'default',
            'mapOnSearch' => 'open',
            'visibilityToggle' => 'both',
            'coordinatesMode' => 'readOnly',
            'coordinatesDefault' => $coordinates,
            'subfieldConfig' => [
                [
                    'handle'       => 'name',
                    'label'        => 'Name',
                    'width'        => 100,
                    'enabled'      => false,
                    'autocomplete' => false,
                    'required'     => false
                ],
                [
                    'handle'       => 'street1',
                    'label'        => 'Street Address',
                    'width'        => (int) ($old['layout']['street1']['width'] ?? 100),
                    'enabled'      => (bool) ($old['layout']['street1']['enable'] ?? true),
                    'autocomplete' => true,
                    'required'     => false
                ],
                [
                    'handle'       => 'street2',
                    'label'        => 'Apartment or Suite',
                    'width'        => (int) ($old['layout']['street2']['width'] ?? 100),
                    'enabled'      => (bool) ($old['layout']['street2']['enable'] ?? true),
                    'autocomplete' => false,
                    'required'     => false
                ],
                [
                    'handle'       => 'city',
                    'label'        => 'City',
                    'width'        => (int) ($old['layout']['city']['width'] ?? 50),
                    'enabled'      => (bool) ($old['layout']['city']['enable'] ?? true),
                    'autocomplete' => false,
                    'required'     => false
                ],
                [
                    'handle'       => 'state',
                    'label'        => 'State',
                    'width'        => (int) ($old['layout']['state']['width'] ?? 15),
                    'enabled'      => (bool) ($old['layout']['state']['enable'] ?? true),
                    'autocomplete' => false,
                    'required'     => false
                ],
                [
                    'handle'       => 'zip',
                    'label'        => 'Zip Code',
                    'width'        => (int) ($old['layout']['zip']['width'] ?? 35),
                    'enabled'      => (bool) ($old['layout']['zip']['enable'] ?? true),
                    'autocomplete' => false,
                    'required'     => false
                ],
                [
                    'handle'       => 'neighborhood',
                    'label'        => 'Neighborhood',
                    'width'        => 100,
                    'enabled'      => false,
                    'autocomplete' => false,
                    'required'     => false
                ],
                [
                    'handle'       => 'county',
                    'label'        => 'County or District',
                    'width'        => 100,
                    'enabled'      => false,
                    'autocomplete' => false,
                    'required'     => false
                ],
                [
                    'handle'       => 'country',
                    'label'        => 'Country',
                    'width'        => (int) ($old['layout']['country']['width'] ?? 100),
                    'enabled'      => (bool) ($old['layout']['country']['enable'] ?? true),
                    'autocomplete' => false,
                    'required'     => false
                ],
                [
                    'handle'       => 'countryCode',
                    'label'        => 'Country Code',
                    'width'        => 100,
                    'enabled'      => false,
                    'autocomplete' => false,
                    'required'     => false
                ],
                [
                    'handle'       => 'placeId',
                    'label'        => 'Place ID',
                    'width'        => 100,
                    'enabled'      => false,
                    'autocomplete' => false,
                    'required'     => false
                ],
            ]
        ];
    }

}

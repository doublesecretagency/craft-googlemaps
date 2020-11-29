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

/**
 * Migration: Update the settings of existing Address fields.
 * @since 4.0.0
 */
class m200627_000002_googleMaps_updateAddressFieldSettings extends Migration
{

    const SMARTMAP_ADDRESS   = 'doublesecretagency\\smartmap\\fields\\Address';
    const GOOGLEMAPS_ADDRESS = 'doublesecretagency\\googlemaps\\fields\\AddressField';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // If admin changes are not allowed, bail
        if (!Craft::$app->getConfig()->getGeneral()->allowAdminChanges) {
            return true;
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
                $this->_updateFieldConfig($field->id, $oldSettings);

            }

        }

        // Success
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $migration = (new \ReflectionClass($this))->getShortName();
        echo "{$migration} cannot be reverted.\n";
        return false;
    }

    // ========================================================================= //

    /**
     * Update existing field configurations.
     *
     * @param int $id ID of field
     * @param array $old Original field settings
     */
    private function _updateFieldConfig(int $id, array $old)
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
        $this->update('{{%fields}}', [
            'type' => static::GOOGLEMAPS_ADDRESS,
            'settings' => Json::encode($newSettings)
        ], [
            'id' => $id
        ], [], false);
    }

}

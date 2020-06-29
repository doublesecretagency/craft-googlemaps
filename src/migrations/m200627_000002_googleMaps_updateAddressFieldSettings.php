<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014 Double Secret Agency
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

            // If field is an active Address, update it
            if ($activeAddress) {
                $this->_updateActiveAddress($field);
            }

            // If field is an inactive Address, update it
            if ($inactiveAddress) {
                $this->_updateInactiveAddress($field);
            }

        }

        // Set zoom for all existing addresses with valid coordinates
        $this->_updateZoom();

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

    /**
     * Update the configuration of an active Address field.
     *
     * @param $field
     */
    private function _updateActiveAddress($field)
    {
        // Do a bunch of stuff
    }

    /**
     * Update the configuration of an inactive Address field.
     *
     * @param $field
     */
    private function _updateInactiveAddress($field)
    {
        // Get Missing field settings
        $settings = $field->getSettings();

        // Get Address field settings
        $s = Json::decode($settings['settings']);

        // If coordinates are valid
        if (
            isset($s['dragPinLatitude'])  && $s['dragPinLatitude'] &&
            isset($s['dragPinLongitude']) && $s['dragPinLongitude']
        ) {
            // Port valid coordinates
            $coordinates = [
                'lat' => (float) ($s['dragPinLatitude'] ?? null),
                'lng' => (float) ($s['dragPinLongitude'] ?? null),
                'zoom' => (int) ($s['dragPinZoom'] ?? null),
            ];
        } else {
            // Otherwise, no coordinates by default
            $coordinates = [
                'lat' => null,
                'lng' => null,
                'zoom' => null,
            ];
        }

        // Update field settings
        $this->_updateField($field->id, [
            'showMap' => null,
            'mapOnStart' => 'close',
            'mapOnSearch' => 'open',
            'visibilityToggle' => 'both',
            'coordinatesMode' => 'readOnly',
            'coordinatesDefault' => $coordinates,
            'subfieldConfig' => [
                'street1' => [
                    'label' => 'Street Address',
                    'width' => (int) ($s['layout']['street1']['width'] ?? 100),
                    'enabled' => (int) ($s['layout']['street1']['enable'] ?? 1),
                    'position' => (int) ($s['layout']['street1']['position'] ?? 1),
                ],
                'street2' => [
                    'label' => 'Apartment or Suite',
                    'width' => (int) ($s['layout']['street2']['width'] ?? 100),
                    'enabled' => (int) ($s['layout']['street2']['enable'] ?? 1),
                    'position' => (int) ($s['layout']['street2']['position'] ?? 2),
                ],
                'city' => [
                    'label' => 'City',
                    'width' => (int) ($s['layout']['city']['width'] ?? 50),
                    'enabled' => (int) ($s['layout']['city']['enable'] ?? 1),
                    'position' => (int) ($s['layout']['city']['position'] ?? 3),
                ],
                'state' => [
                    'label' => 'State',
                    'width' => (int) ($s['layout']['state']['width'] ?? 15),
                    'enabled' => (int) ($s['layout']['state']['enable'] ?? 1),
                    'position' => (int) ($s['layout']['state']['position'] ?? 4),
                ],
                'zip' => [
                    'label' => 'Zip Code',
                    'width' => (int) ($s['layout']['zip']['width'] ?? 35),
                    'enabled' => (int) ($s['layout']['zip']['enable'] ?? 1),
                    'position' => (int) ($s['layout']['zip']['position'] ?? 5),
                ],
                'country' => [
                    'label' => 'Country',
                    'width' => (int) ($s['layout']['country']['width'] ?? 100),
                    'enabled' => (int) ($s['layout']['country']['enable'] ?? 1),
                    'position' => (int) ($s['layout']['country']['position'] ?? 6),
                ]
            ]
        ]);

    }

    /**
     * Directly update existing field configurations.
     *
     * @param $fieldId
     * @param $newSettings
     */
    private function _updateField($fieldId, $newSettings)
    {
        $this->update('{{%fields}}', [
            'type' => static::GOOGLEMAPS_ADDRESS,
            'settings' => Json::encode($newSettings)
        ], [
            'id' => $fieldId
        ], [], false);
    }

    /**
     * Set the zoom level for all existing Address values.
     */
    private function _updateZoom()
    {
        $this->update('{{%googlemaps_addresses}}', [
            'zoom' => 11
        ], ['and',
            ['zoom' => null],
            ['not', ['lat' => null]],
            ['not', ['lng' => null]],
        ], [], false);
    }

}

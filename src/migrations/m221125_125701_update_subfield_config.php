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
use craft\db\Table;
use craft\fields\MissingField;
use craft\helpers\Db;
use doublesecretagency\googlemaps\enums\Defaults;
use doublesecretagency\googlemaps\fields\AddressField;

/**
 * m221125_125701_update_subfield_config Migration
 * @since 4.3.0
 */
class m221125_125701_update_subfield_config extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Get the current schema version (as defined by the YAML file)
        $schemaVersion = Craft::$app->getProjectConfig()->get('plugins.google-maps.schemaVersion', true);

        // If the schema version is greater than or equal to 4.3.0
        if (version_compare($schemaVersion, '4.3.0', '>=')) {
            // Skip it, the migration has already been run in a different environment
            return true;
        }

        // Get services
        $fieldsService = Craft::$app->getFields();
        $matrixService = Craft::$app->getMatrix();

        // Get all fields
        $allFields = $fieldsService->getAllFields(false);

        // Filter only Address fields
        $addressFields = array_filter($allFields, function($field) {
            // Enabled Address field
            if (is_a($field, AddressField::class)) {
                return true;
            }
            // Disabled Address field
            if (is_a($field, MissingField::class) && ($field->expectedType === AddressField::class)) {
                return true;
            }
            // Not an Address field
            return false;
        });

        // Loop through Address fields
        foreach ($addressFields as $field) {

            // Normalize the subfield config
            $field->subfieldConfig = $this->_normalizeSubfieldConfig($field->subfieldConfig);

            // Save the updated Address field
            $fieldsService->saveField($field, false);

            // Split up the context string
            $context = explode(':', $field->context);

            // If Address is inside a Matrix field
            if ('matrixBlockType' === $context[0]) {
                // Get the UID of the Matrix block type
                $uid = ($context[1] ?? null);
                // Get the ID of the Matrix block type
                $blockTypeId = Db::idByUid(Table::MATRIXBLOCKTYPES, $uid);
                // If unable to fetch the ID, skip it
                if (!$blockTypeId) {
                    continue;
                }
                // Get the Matrix block type
                $blockType = $matrixService->getBlockTypeById($blockTypeId);
                // If no matching block type, skip it
                if (!$blockType) {
                    continue;
                }
                // Save the Matrix block type
                $matrixService->saveBlockType($blockType, false);
            }

        }

        // Success
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m221125_125701_update_subfield_config cannot be reverted.\n";
        return false;
    }

    // ========================================================================= //

    /**
     * Normalize the subfield configuration.
     *
     * @return array
     */
    private function _normalizeSubfieldConfig($subfieldConfig): array
    {
        // What kind of array is the subfield configuration?
        $isSequential  = (array_key_exists(0, $subfieldConfig));         // (NEW STYLE)
        $isAssociative = (array_key_exists('street1', $subfieldConfig)); // (OLD STYLE)

        // If it's a sequential array
        if ($isSequential) {
            // Strictly typecast all subfield settings
            AddressField::typecastSubfieldConfig($subfieldConfig);
            // Return the existing subfield config
            return $subfieldConfig;
        }

        // If it's NOT an associative array
        if (!$isAssociative) {
            // It's misconfigured, return the default configuration
            return Defaults::SUBFIELDCONFIG;
        }

        // Initialize new config
        $newConfig = [];

        // Loop through default subfield configuration
        foreach (Defaults::SUBFIELDCONFIG as $defaultConfig) {

            // Get the existing config
            $oldConfig = ($subfieldConfig[$defaultConfig['handle']] ?? []);

            // Append new config for each subfield
            $newConfig[] = [
                'handle'       => $defaultConfig['handle'],
                'label'        => (string) ($oldConfig['label'] ?? $defaultConfig['label']),
                'width'        => (int) ($oldConfig['width'] ?? $defaultConfig['width']),
                'enabled'      => (bool) ($oldConfig['enabled'] ?? $defaultConfig['enabled']),
                'autocomplete' => (bool) ($oldConfig['autocomplete'] ?? $defaultConfig['autocomplete']),
                'required'     => (bool) ($oldConfig['required'] ?? $defaultConfig['required'])
            ];
        }

        // Reorder the new config based on the old config's `position` value
        usort($newConfig, function ($a, $b) use ($subfieldConfig) {

            // Get original subfield configs
            $subfieldA = ($subfieldConfig[$a['handle']] ?? []);
            $subfieldB = ($subfieldConfig[$b['handle']] ?? []);

            // Get original positions
            $positionA = (int) ($subfieldA['position'] ?? 100);
            $positionB = (int) ($subfieldB['position'] ?? 101);

            // Return sorting results
            return ($positionA < $positionB) ? -1 : 1;
        });

        // Return new subfield config
        return $newConfig;
    }

}

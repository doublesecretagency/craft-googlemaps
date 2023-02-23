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

namespace doublesecretagency\googlemaps\validators;

use Craft;
use doublesecretagency\googlemaps\models\Address;
use yii\validators\Validator;

/**
 * Class AddressValidator
 * @since 4.1.4
 */
class AddressValidator extends Validator
{

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute): void
    {
        // Valid until proven otherwise
        $valid = true;

        // Remove prefix from field handle
        $fieldHandle = preg_replace('/^field:/', '', $attribute);

        // Get field configuration
        $field = Craft::$app->getFields()->getFieldByHandle($fieldHandle);

        // If invalid field, bail
        if (!$field) {
            return;
        }

        // Initialize list of required subfields
        $reqSubfields = [];

        // Get the Address model
        /** @var Address $address */
        $address = ($model->{$attribute} ?? false);

        // If invalid Address, bail
        if (!$address) {
            return;
        }

        // Get the subfield configuration
        $config = ($field->subfieldConfig ?? []);

        // Loop through subfield config settings
        foreach ($config as $settings) {

            // Extract subfield settings
            $handle   = ($settings['handle'] ?? 'err');
            $label    = ($settings['label'] ?? $handle);
            $enabled  = ($settings['enabled'] ?? false);
            $required = ($settings['required'] ?? false);

            // If subfield is not enabled, skip it
            if (!$enabled) {
                continue;
            }

            // If subfield is not required, skip it
            if (!$required) {
                continue;
            }

            // Get the subfield value
            $value = ($address[$handle] ?? false);

            // If subfield value is empty
            if (!$value) {
                // Mark entire field as invalid
                $valid = false;
                // Add label to list of required subfields
                $reqSubfields[] = "**{$label}**";
            }

        }

        // Whether coordinates are required
        $reqCoords = (bool) ($field->requireCoordinates ?? true);

        // If coordinates are required, but invalid
        if ($reqCoords && !$address->hasCoords()) {
            // Mark entire field as invalid
            $valid = false;
        }

        // If field is still valid, bail
        if ($valid) {
            return;
        }

        // Set error message
        $this->addError($model, $attribute, $this->_getMessage($reqSubfields, $reqCoords));
    }

    /**
     * Compile an appropriate error message.
     *
     * @param array $reqSubfields
     * @param bool $reqCoords
     * @return string
     */
    private function _getMessage(array $reqSubfields, bool $reqCoords): string
    {
        // If any subfields are required
        if ($reqSubfields) {
            // Implode list of required subfields
            $subfields = implode(', ', $reqSubfields);
            // Initialize message about subfields
            $message = "The following subfields are required: {$subfields}";
            // If coordinates are required
            if ($reqCoords) {
                $message .= ", **Latitude** & **Longitude**";
            }
        } else {
            // Set message about coordinates only
            $message = "A valid set of coordinates is required.";
        }

        // Return error message
        return $message;
    }

}

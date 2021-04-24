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

namespace doublesecretagency\googlemaps\exporters;

use Craft;
use craft\base\ElementExporter;
use craft\elements\db\ElementQueryInterface;
use craft\errors\InvalidFieldException;
use doublesecretagency\googlemaps\helpers\ExporterHelper;

/**
 * Class AddressesExpandedExporter
 * @since 4.0.7
 */
class AddressesExpandedExporter extends ElementExporter
{

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return 'Addresses (expanded)';
    }

    /**
     * @inheritdoc
     */
    public function export(ElementQueryInterface $query): array
    {
        // Export the Address data
        return ExporterHelper::export($query, function(&$data, $element, $addressFields) {

            // Loop through each Address field
            foreach ($addressFields as $field) {

                // Attempt to get the Address data
                try {
                    $address = $element->getFieldValue($field->handle);
                } catch (InvalidFieldException $e) {
                    // If element doesn't use this field, skip it
                    continue;
                }

                // Append config for coordinates
                $subfieldConfig = array_merge($field->subfieldConfig, [
                    'lat'  => ['label' => Craft::t('google-maps', 'Latitude')],
                    'lng'  => ['label' => Craft::t('google-maps', 'Longitude')],
                    'zoom' => ['label' => Craft::t('google-maps', 'Zoom')],
                ]);

                // Loop through each subfield
                foreach ($subfieldConfig as $handle => $subfield) {

                    // Get the user-configured subfield label
                    $label = ($subfield['label'] ?? $handle);

                    // Prefix each column with the field name
                    $column = "{$field->name} - {$label}";

                    // Append new subfield column
                    $data[] = [
                        $column => ($address->{$handle} ?? '')
                    ];

                }

            }

        });
    }

}

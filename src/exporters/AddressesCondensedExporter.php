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

use craft\base\ElementExporter;
use craft\elements\db\ElementQueryInterface;
use craft\errors\InvalidFieldException;
use doublesecretagency\googlemaps\helpers\ExporterHelper;

/**
 * Class AddressesCondensedExporter
 * @since 4.0.7
 */
class AddressesCondensedExporter extends ElementExporter
{

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return 'Addresses (condensed)';
    }

    /**
     * @inheritdoc
     */
    public function export(ElementQueryInterface $query): array
    {
        // Export the Address data
        return ExporterHelper::export($query, static function(&$data, $element, $addressFields) {

            // Loop through each Address field
            foreach ($addressFields as $field) {

                // Attempt to get the Address data
                try {
                    $address = $element->getFieldValue($field->handle);
                } catch (InvalidFieldException $e) {
                    // If element doesn't use this field, skip it
                    continue;
                }

                // Flatten the Address into a single string
                $data[] = [
                    $field->name => (string) $address,
                ];

            }

        });
    }

}

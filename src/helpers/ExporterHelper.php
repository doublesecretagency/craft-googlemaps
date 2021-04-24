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
use craft\base\Element;
use craft\base\Field;
use craft\elements\db\ElementQueryInterface;
use doublesecretagency\googlemaps\fields\AddressField;

/**
 * Class ExporterHelper
 * @since 4.0.7
 */
class ExporterHelper
{

    /**
     * Configure the export of Address data.
     *
     * @param ElementQueryInterface $query
     * @param callable $configureAddress
     * @return array
     */
    public static function export(ElementQueryInterface $query, callable $configureAddress): array
    {
        // Initialize
        $results = [];

        // Determine which are the address fields
        $addressFields = [];
        foreach (Craft::$app->getFields()->getAllFields() as $field) {
            /** @var Field $field */
            if ($field instanceof AddressField) {
                $addressFields[] = $field;
            }
        }

        // Loop through each element
        foreach ($query->each() as $element) {
            /** @var Element $element */

            // Initialize with basic element data
            $data = [[
                Craft::t('app', 'ID')    => ($element->id ?? ''),
                Craft::t('app', 'Title') => ($element->title ?? ''),
            ]];

            // Configure Address data via the callback method
            $configureAddress($data, $element, $addressFields);

            // Add new row to results
            $results[] = array_merge(...$data);
        }

        // Return results
        return $results;
    }

}

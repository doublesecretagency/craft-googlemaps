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

namespace doublesecretagency\googlemaps\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\elements\db\ElementQueryInterface;
use craft\elements\Entry;
use craft\helpers\Json;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\helpers\AddressHelper;
use doublesecretagency\googlemaps\helpers\ProximitySearchHelper;
use doublesecretagency\googlemaps\models\Address as AddressModel;
use doublesecretagency\googlemaps\records\Address as AddressRecord;
use doublesecretagency\googlemaps\web\assets\AddressFieldAsset;
use doublesecretagency\googlemaps\web\assets\AddressFieldSettingsAsset;

/**
 * Class AddressField
 * @since 4.0.0
 */
class AddressField extends Field implements PreviewableFieldInterface
{

    /**
     * Default coordinates. (Bermuda Triangle)
     */
    const DEFAULT_COORDINATES = [
        'lat' => 32.3113966,
        'lng' => -64.7527469,
        'zoom' => 6
    ];

    /**
     * Default subfield configuration.
     */
    const DEFAULT_SUBFIELD_CONFIG = [
        'street1'=> [
            'label'    => 'Street Address',
            'width'    => 100,
            'position' => 1,
            'enabled'  => 1,
            'required' => false
        ],
        'street2'=> [
            'label'    => 'Apartment or Suite',
            'width'    => 100,
            'position' => 2,
            'enabled'  => 1,
            'required' => false
        ],
        'city'=> [
            'label'    => 'City',
            'width'    => 50,
            'position' => 3,
            'enabled'  => 1,
            'required' => false
        ],
        'state'=> [
            'label'    => 'State',
            'width'    => 15,
            'position' => 4,
            'enabled'  => 1,
            'required' => false
        ],
        'zip'=> [
            'label'    => 'Zip Code',
            'width'    => 35,
            'position' => 5,
            'enabled'  => 1,
            'required' => false
        ],
        'country'=> [
            'label'    => 'Country',
            'width'    => 100,
            'position' => 6,
            'enabled'  => 1,
            'required' => false
        ]
    ];

    /**
     * Whether or not to show the map.
     *
     * @var bool
     */
    public $showMap = false;

    /**
     * What should the map be
     * when the field is initially loaded?
     *
     * @var string "default", "open" or "close"
     */
    public $mapOnStart = 'default';

    /**
     * What should the map be
     * when a geocode lookup is performed?
     *
     * @var string "open", "close" or "noChange"
     */
    public $mapOnSearch = 'open';

    /**
     * How should we display
     * the map visibility toggle?
     *
     * @var string "both", "text", "icon" or "hidden"
     */
    public $visibilityToggle = 'both';

    /**
     * How should we display
     * the coordinates fields?
     *
     * @var string "editable", "readOnly" or "hidden"
     */
    public $coordinatesMode = 'readOnly';

    /**
     * Default coordinates of a new Address field.
     *
     * @var array|null
     */
    // TODO: Should probably just be null, right? Let the fallback kick in later, in JS or PHP
    // public $coordinatesDefault;
    public $coordinatesDefault = self::DEFAULT_COORDINATES;

    /**
     * Full configuration of subfields.
     *
     * @var array|null
     */
    public $subfieldConfig = self::DEFAULT_SUBFIELD_CONFIG;

    // ========================================================================= //

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('google-maps', 'Address');
    }

    /**
     * @inheritdoc
     */
    public static function hasContentColumn(): bool
    {
        return false;
    }

    // ========================================================================= //

    /**
     * After saving element, save field to plugin table.
     *
     * @inheritdoc
     */
    public function afterElementSave(ElementInterface $element, bool $isNew)
    {
        /** @var Entry $element */

        // Get field data
        $data = $element->getFieldValue($this->handle);

        // If data doesn't exist, bail
        if (!$data) {
            return;
        }

        // Attempt to load an existing record
        $record = AddressRecord::findOne([
            'elementId' => $element->id,
            'fieldId'   => $this->id,
        ]);

        // If no record exists, create a new record
        if (!$record) {
            $record = new AddressRecord([
                'elementId' => $element->id,
                'fieldId'   => $this->id,
            ]);
        }

        // Set record attributes
        $record->setAttributes([
            'formatted' => ($data['formatted'] ?: null),
            'raw'       => ($data['raw'] ?: null),
            'street1'   => ($data['street1'] ?: null),
            'street2'   => ($data['street2'] ?: null),
            'city'      => ($data['city'] ?: null),
            'state'     => ($data['state'] ?: null),
            'zip'       => ($data['zip'] ?: null),
            'country'   => ($data['country'] ?: null),
            'lat'       => ($data['lat'] ?: null),
            'lng'       => ($data['lng'] ?: null),
            'zoom'      => ($data['zoom'] ?: null),
        ], false);

        // Save record
        $record->save();
    }

    /**
     * Prep value for use as the data leaves the database.
     *
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        /** @var Entry $element */

        // If the value is already an Address model, return it immediately
        if ($value instanceof AddressModel) {
            return $value;
        }

        // If value is an array, load it directly into an Address model
        if (is_array($value)) {
            return new AddressModel([
                'elementId' => (int) ($element->id ?? null),
                'fieldId'   => (int) ($this->id ?? null),
                'formatted' => (($value['formatted'] ?? null) ?: null),
                'raw'       => (($value['raw'] ?? null) ?: null),
                'street1'   => ($value['street1'] ?? null),
                'street2'   => ($value['street2'] ?? null),
                'city'      => ($value['city'] ?? null),
                'state'     => ($value['state'] ?? null),
                'zip'       => ($value['zip'] ?? null),
                'country'   => ($value['country'] ?? null),
                'lat'       => (float) ($value['lat'] ?? null),
                'lng'       => (float) ($value['lng'] ?? null),
                'zoom'      => (int) ($value['zoom'] ?? null),
            ]);
        }

        // If no element or no field ID, bail
        if (!$element || !$this->id) {
            return null;
        }

        // Attempt to load existing record
        $record = AddressRecord::findOne([
            'elementId' => $element->id,
            'fieldId' => $this->id,
        ]);

        // If no matching record exists, bail
        if (!$record) {
            return null;
        }

        // Get the record attributes
        $omitColumns = ['dateCreated','dateUpdated','uid'];
        $attr = $record->getAttributes(null, $omitColumns);

        // Convert coordinates to floats
        $attr['lat'] = ($attr['lat'] ? (float) $attr['lat'] : null);
        $attr['lng'] = ($attr['lng'] ? (float) $attr['lng'] : null);

        // Check if JSON is valid
        // Must use this function to validate (I know it's redundant)
        $valid = json_decode($attr['raw']);

        // Convert raw data to an array
        $attr['raw'] = ($valid ? Json::decode($attr['raw']) : null);

        // If part of a proximity search, get the distance
        if ($value && is_numeric($value)) {
            $attr['distance'] = (float) $value;
        }

        // Return an Address model
        return new AddressModel($attr);
    }

    // ========================================================================= //

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): string
    {
        // Reference assets
        $view = Craft::$app->getView();
        $view->registerAssetBundle(AddressFieldSettingsAsset::class);

        // Load fieldtype settings template
        return $view->renderTemplate('google-maps/address-settings', [
            'icons' => AddressHelper::visibilityIcons(),
            'settings' => $this->_getExtraSettings()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Reference assets
        $view = Craft::$app->getView();
        $view->registerAssetBundle(AddressFieldAsset::class);

        // Get extended settings
        $settings = $this->_getExtraSettings();

        // Whether the field has existing coordinates
        $coordsExist = ($value instanceof AddressModel && $value->hasCoords());

        // By default, show map if coordinates exist
        if ('default' === $settings['mapOnStart']) {
            $settings['showMap'] = $coordsExist;
            $settings['mapOnStart'] = ($coordsExist ? 'open' : 'close');
        }

        // Load fieldtype input template
        return $view->renderTemplate('google-maps/address', [
            'address' => $value,
            'handle' => $this->handle,
            'icons' => AddressHelper::visibilityIcons(),
            'settings' => $settings,
        ]);
    }

    // ========================================================================= //

    /**
     * Get the field settings with some extra information.
     *
     * @return array
     */
    private function _getExtraSettings(): array
    {
        // Get basic settings
        $settings = $this->getSettings();

        // Set whether to show the map on initial load
        $settings['showMap'] = ('open' === $settings['mapOnStart']);

        // Set the control size of map UI elements
        $settings['controlSize'] = GoogleMapsPlugin::$plugin->getSettings()->fieldControlSize;

        // Return settings
        return $settings;
    }

    // ========================================================================= //

    /**
     * @inheritdoc
     */
    public function modifyElementsQuery(ElementQueryInterface $query, $options = [])
    {
        // If options are not properly specified, bail
        if (!is_array($options)) {
            return;
        }

        // Modify the element query to perform a proximity search
        ProximitySearchHelper::modifyElementsQuery($query, $options, $this);
    }

    // ========================================================================= //

    // TODO: Add field validation

//    /**
//     * @inheritdoc
//     */
//    public function getElementValidationRules(): array
//    {
//        return [
//            ['validateCoords'],
//        ];
//    }
//
//    public function validateCoords(ElementInterface $element)
//    {
//        $address = $element->getFieldValue($this->handle);
//
//        $hasLat = (bool) $address->lat;
//        $hasLng = (bool) $address->lng;
//        $validLat = ($hasLat ? is_numeric($address->lat) : true);
//        $validLng = ($hasLng ? is_numeric($address->lng) : true);
//
//        if (!($validLat && $validLng)) {
//            $element->addError($this->handle, Craft::t('google-maps', 'If coordinates are specified, they must be numbers.'));
//        }
//    }

}

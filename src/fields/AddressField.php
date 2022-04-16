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
use doublesecretagency\googlemaps\validators\AddressValidator;
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
    public const DEFAULT_COORDINATES = [
        'lat' => 32.3113966,
        'lng' => -64.7527469,
        'zoom' => 6
    ];

    /**
     * Default subfield configuration.
     */
    public const DEFAULT_SUBFIELD_CONFIG = [
        'name' => [
            'label'    => 'Name',
            'width'    => 100,
            'position' => 1,
            'enabled'  => 0,
            'required' => false
        ],
        'street1' => [
            'label'    => 'Street Address',
            'width'    => 100,
            'position' => 2,
            'enabled'  => 1,
            'required' => false
        ],
        'street2' => [
            'label'    => 'Apartment or Suite',
            'width'    => 100,
            'position' => 3,
            'enabled'  => 1,
            'required' => false
        ],
        'city' => [
            'label'    => 'City',
            'width'    => 50,
            'position' => 4,
            'enabled'  => 1,
            'required' => false
        ],
        'state' => [
            'label'    => 'State',
            'width'    => 15,
            'position' => 5,
            'enabled'  => 1,
            'required' => false
        ],
        'zip' => [
            'label'    => 'Zip Code',
            'width'    => 35,
            'position' => 6,
            'enabled'  => 1,
            'required' => false
        ],
        'county' => [
            'label'    => 'County or District',
            'width'    => 100,
            'position' => 7,
            'enabled'  => 0,
            'required' => false
        ],
        'country' => [
            'label'    => 'Country',
            'width'    => 100,
            'position' => 8,
            'enabled'  => 1,
            'required' => false
        ],
        'placeId' => [
            'label'    => 'Place ID',
            'width'    => 100,
            'position' => 9,
            'enabled'  => 0,
            'required' => false
        ],
    ];

    /**
     * Whether to show the map.
     *
     * @var bool
     */
    public bool $showMap = false;

    /**
     * What should the map be
     * when the field is initially loaded?
     *
     * @var string "default", "open" or "close"
     */
    public string $mapOnStart = 'default';

    /**
     * What should the map be
     * when a geocode lookup is performed?
     *
     * @var string "open", "close" or "noChange"
     */
    public string $mapOnSearch = 'open';

    /**
     * How should we display
     * the map visibility toggle?
     *
     * @var string "both", "text", "icon" or "hidden"
     */
    public string $visibilityToggle = 'both';

    /**
     * How should we display
     * the coordinates fields?
     *
     * @var string "editable", "readOnly" or "hidden"
     */
    public string $coordinatesMode = 'readOnly';

    /**
     * Whether the coordinates subfields are required.
     *
     * @var bool
     */
    public bool $requireCoordinates = true;

    /**
     * Default coordinates of a new Address field.
     *
     * @var array|null
     */
    // TODO: Should probably just be null, right? Let the fallback kick in later, in JS or PHP
    // public $coordinatesDefault;
    public ?array $coordinatesDefault = self::DEFAULT_COORDINATES;

    /**
     * Full configuration of subfields.
     *
     * @var array|null
     */
    public ?array $subfieldConfig = self::DEFAULT_SUBFIELD_CONFIG;

    // ========================================================================= //

    /**
     * LEGACY: Properties required for Smart Map migration
     */
    public ?bool $dragPinDefault = null;
    public ?float $dragPinLatitude = null;
    public ?float $dragPinLongitude = null;
    public ?int $dragPinZoom = null;
    public ?array $layout = null;

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
    public function afterElementSave(ElementInterface $element, bool $isNew): void
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
            'name'      => ($data['name'] ?: null),
            'street1'   => ($data['street1'] ?: null),
            'street2'   => ($data['street2'] ?: null),
            'city'      => ($data['city'] ?: null),
            'state'     => ($data['state'] ?: null),
            'zip'       => ($data['zip'] ?: null),
            'county'    => ($data['county'] ?: null),
            'country'   => ($data['country'] ?: null),
            'placeId'   => ($data['placeId'] ?: null),
            'lat'       => ($data['lat'] ?: null),
            'lng'       => ($data['lng'] ?: null),
            'zoom'      => ($data['zoom'] ?: null),
        ], false);

        // Save record
        $record->save();
    }

    /**
     * As the data leaves the database, prepare the Address value for use.
     *
     * @inheritdoc
     */
    public function normalizeValue(mixed $value, ?ElementInterface $element = null): ?AddressModel
    {
        /** @var Entry $element */

        // If the value is already an Address model, return it immediately
        if ($value instanceof AddressModel) {
            return $value;
        }

        // If value is an array, load it directly into an Address model
        if (is_array($value)) {
            // Get coordinates
            $lat  = ($value['lat']  ?? null);
            $lng  = ($value['lng']  ?? null);
            $zoom = ($value['zoom'] ?? null);
            // Return Address model
            return new AddressModel([
                'elementId' => (int) ($element->id ?? null),
                'fieldId'   => (int) ($this->id ?? null),
                'formatted' => (($value['formatted'] ?? null) ?: null),
                'raw'       => (($value['raw'] ?? null) ?: null),
                'name'      => ($value['name'] ?? null),
                'street1'   => ($value['street1'] ?? null),
                'street2'   => ($value['street2'] ?? null),
                'city'      => ($value['city'] ?? null),
                'state'     => ($value['state'] ?? null),
                'zip'       => ($value['zip'] ?? null),
                'county'    => ($value['county'] ?? null),
                'country'   => ($value['country'] ?? null),
                'placeId'   => ($value['placeId'] ?? null),
                'lat'       => (is_numeric($lat) ? (float) $lat : null),
                'lng'       => (is_numeric($lng) ? (float) $lng : null),
                'zoom'      => (is_numeric($zoom) ? (int) $zoom : null),
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
    public function getElementValidationRules(): array
    {
        // If not required, skip validation
        if (!$this->required) {
            return [];
        }

        // Apply validation rule
        return [
            [AddressValidator::class]
        ];
    }

    // ========================================================================= //

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
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
    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
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

        // Get existing subfield config
        $subfieldConfig = $settings['subfieldConfig'] ?? [];

        // Start boosted `position` counter
        $boostedPosition = 101;

        // Loop through default subfields
        foreach (self::DEFAULT_SUBFIELD_CONFIG as $handle => $subfield) {

            // If subfield already exists, skip it
            if (array_key_exists($handle, $subfieldConfig)) {
                continue;
            }

            // Ensure subfield appears at the end
            $subfield['position'] = $boostedPosition++;

            // Disable subfield by default
            $subfield['enabled'] = 0;

            // Not using `required` (yet)
            unset($subfield['required']);

            // Append unused subfield to existing config
            $subfieldConfig[$handle] = $subfield;

        }

        // Update subfield config
        $settings['subfieldConfig'] = $subfieldConfig;

        // Return settings
        return $settings;
    }

    // ========================================================================= //

    /**
     * @inheritdoc
     */
    public function modifyElementsQuery(ElementQueryInterface $query, mixed $value): void
    {
        // If options are not properly specified, bail
        if (!is_array($value)) {
            return;
        }

        // Modify the element query to perform a proximity search
        ProximitySearchHelper::modifyElementsQuery($query, $value, $this);
    }

}

// Aliased from old field class
class_alias(AddressField::class, \doublesecretagency\smartmap\fields\Address::class);

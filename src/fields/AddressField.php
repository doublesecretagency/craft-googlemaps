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
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\elements\Entry;
use craft\events\CancelableEvent;
use craft\events\DefineFieldKeywordsEvent;
use craft\events\PopulateElementEvent;
use craft\helpers\Json;
use doublesecretagency\googlemaps\enums\Defaults;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\gql\types\Address as AddressType;
use doublesecretagency\googlemaps\gql\types\input\AddressInput;
use doublesecretagency\googlemaps\models\Address as AddressModel;
use doublesecretagency\googlemaps\models\ProximitySearch;
use doublesecretagency\googlemaps\records\Address as AddressRecord;
use doublesecretagency\googlemaps\validators\AddressValidator;
use doublesecretagency\googlemaps\web\assets\AddressFieldAsset;
use doublesecretagency\googlemaps\web\assets\AddressFieldSettingsAsset;
use GraphQL\Type\Definition\Type;
use yii\base\Event;

/**
 * Class AddressField
 * @since 4.0.0
 */
class AddressField extends Field implements PreviewableFieldInterface
{

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
    public ?array $coordinatesDefault = Defaults::COORDINATES;

    /**
     * Full configuration of subfields.
     *
     * @var array|null
     */
    public ?array $subfieldConfig = Defaults::SUBFIELDCONFIG;

    // ========================================================================= //

    /**
     * Dead-end recipient of Preview data
     * when field settings are saved.
     *
     * NOT USED ANYWHERE:
     * Only exists to satisfy saving the field settings.
     *
     * @var array
     */
    public array $settingsPreview = [];

    // ========================================================================= //

    /**
     * Static configuration of a proximity search, if one is being carried out.
     *
     * @var array|null
     */
    public static ?array $proximitySearch = null;

    /**
     * Non-static configuration of a proximity search, if one is being carried out.
     *
     * @var array
     */
    public array $isProximitySearch = [];

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
    public function init(): void
    {
        // Register proximity search events
        $this->_proximitySearchEvents();
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('google-maps', 'Address (Google Maps)');
    }

    /**
     * @inheritdoc
     */
    public static function icon(): string
    {
        return 'location-dot';
    }

    /**
     * @inheritdoc
     */
    public static function supportedTranslationMethods(): array
    {
        return [
            self::TRANSLATION_METHOD_NONE,
            self::TRANSLATION_METHOD_SITE,
            self::TRANSLATION_METHOD_SITE_GROUP,
            self::TRANSLATION_METHOD_LANGUAGE,
            self::TRANSLATION_METHOD_CUSTOM,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function dbType(): array|string|null
    {
        return null;
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
            'siteId'    => $element->siteId,
            'fieldId'   => $this->id,
        ]);

        // If no record exists, create a new record
        if (!$record) {
            $record = new AddressRecord([
                'elementId' => $element->id,
                'siteId'    => $element->siteId,
                'fieldId'   => $this->id,
            ]);
        }

        // Set record attributes
        $record->setAttributes([
            'formatted'    => ($data['formatted']    ?: null),
            'raw'          => ($data['raw']          ?: null),
            'name'         => ($data['name']         ?: null),
            'street1'      => ($data['street1']      ?: null),
            'street2'      => ($data['street2']      ?: null),
            'city'         => ($data['city']         ?: null),
            'state'        => ($data['state']        ?: null),
            'zip'          => ($data['zip']          ?: null),
            'neighborhood' => ($data['neighborhood'] ?: null),
            'county'       => ($data['county']       ?: null),
            'country'      => ($data['country']      ?: null),
            'countryCode'  => ($data['countryCode']  ?: null),
            'placeId'      => ($data['placeId']      ?: null),
            'lat'          => ($data['lat']          ?: null),
            'lng'          => ($data['lng']          ?: null),
            'zoom'         => ($data['zoom']         ?: null),
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
            // Get IDs
            $elementId = ($value['elementId'] ?? $element->id     ?? null);
            $siteId    = ($value['siteId']    ?? $element->siteId ?? null);
            $fieldId   = ($value['fieldId']   ?? $this->id        ?? null);
            // Get coordinates
            $lat  = ($value['lat']  ?? null);
            $lng  = ($value['lng']  ?? null);
            $zoom = ($value['zoom'] ?? null);
            // Normalize raw value
            $value['raw'] = static::normalizeRaw($value['raw'] ?? null);
            // Return Address model
            return new AddressModel([
                'elementId'    => (int) $elementId,
                'siteId'       => (int) $siteId,
                'fieldId'      => (int) $fieldId,
                'formatted'    => ($value['formatted']    ?? null),
                'raw'          => ($value['raw']          ?? null),
                'name'         => ($value['name']         ?? null),
                'street1'      => ($value['street1']      ?? null),
                'street2'      => ($value['street2']      ?? null),
                'city'         => ($value['city']         ?? null),
                'state'        => ($value['state']        ?? null),
                'zip'          => ($value['zip']          ?? null),
                'neighborhood' => ($value['neighborhood'] ?? null),
                'county'       => ($value['county']       ?? null),
                'country'      => ($value['country']      ?? null),
                'countryCode'  => ($value['countryCode']  ?? null),
                'placeId'      => ($value['placeId']      ?? null),
                'lat'          => (is_numeric($lat) ? (float) $lat : null),
                'lng'          => (is_numeric($lng) ? (float) $lng : null),
                'zoom'         => (is_numeric($zoom) ? (int) $zoom : null),
                'enabledSubfields' => $this->_getEnabledSubfields(),
            ]);
        }

        // If no element or no field ID, bail
        if (!$element || !$this->id) {
            return null;
        }

        // Attempt to load existing record
        $record = AddressRecord::findOne([
            'elementId' => $element->id,
            'siteId'    => $element->siteId,
            'fieldId'   => $this->id,
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

        // Normalize raw value
        $attr['raw'] = static::normalizeRaw($attr['raw'] ?? null);

        // Get handles of visible subfields
        $attr['enabledSubfields'] = $this->_getEnabledSubfields();

        // If part of a proximity search, get the distance
        if ($value && is_numeric($value)) {
            $attr['distance'] = (float) $value;
        }

        // Return an Address model
        return new AddressModel($attr);
    }

    /**
     * Normalize the raw value.
     *
     * @param mixed $raw
     * @return array|null
     */
    public static function normalizeRaw(mixed $raw): ?array
    {
        // If already an array, return as-is
        if (is_array($raw)) {
            return $raw;
        }

        // If not a string, return null
        if (!is_string($raw)) {
            return null;
        }

        // If string contains `[object Object]`, return null
        if (str_contains($raw, '[object Object]')) {
            return null;
        }

        // Convert string to an array
        return Json::decode($raw);
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
    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        // Whether the field has existing coordinates
        $coordsExist = ($value instanceof AddressModel && $value->hasCoords());

        // Get extended settings
        $settings = $this->_getExtraSettings();

        // By default, show map if coordinates exist
        if ('default' === $settings['mapOnStart']) {
            $settings['showMap'] = $coordsExist;
            $settings['mapOnStart'] = ($coordsExist ? 'open' : 'close');
        }

        // Load view service
        $view = Craft::$app->getView();

        // Register assets
        $view->registerAssetBundle(AddressFieldAsset::class);

        // Load fieldtype input template
        return $view->renderTemplate('google-maps/address', [
            'config' => [
                'namespace' => [
                    'id' => $view->namespaceInputId($this->handle),
                    'name' => $view->namespaceInputName($this->handle),
                    'handle' => $this->handle,
                ],
                'settings' => $settings,
                'data' => $this->_getAddressData($value),
                'images' => $this->_publishImages([
                    'iconOn' => 'marker.svg',
                    'iconOff' => 'marker-hollow.svg',
                ]),
                'isRevision' => ($element?->getIsRevision() ?? false),
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        // Load view service
        $view = Craft::$app->getView();

        // Register assets
        $view->registerAssetBundle(AddressFieldSettingsAsset::class);

        // Get namespace
        $ns = $view->getNamespace();

        // Load fieldtype settings template
        return $view->renderTemplate('google-maps/address-settings', [
            'config' => [
                'namespace' => [
                    'id' => $view->namespaceInputId('gm-settings-preview'),
                    'name' => $ns,
                    'handle' => $ns,
                ],
                'settings' => $this->_getExtraSettings(),
                'data' => $this->_getAddressData(),
                'images' => $this->_publishImages([
                    'iconOn' => 'marker.svg',
                    'iconOff' => 'marker-hollow.svg',
                    'required' => 'required.png',
                ]),
            ]
        ]);
    }

    // ========================================================================= //

    /**
     * Typecast the subfield configuration.
     *
     * @param $subfieldConfig
     */
    public static function typecastSubfieldConfig(&$subfieldConfig): void
    {
        // Strictly typecast each subfield setting
        array_walk($subfieldConfig, static function (&$value) {
            $value = [
                'handle'       => (string) ($value['handle']       ?? ''),
                'label'        => (string) ($value['label']        ?? ''),
                'width'        => (int)    ($value['width']        ?? 100),
                'enabled'      => (bool)   ($value['enabled']      ?? false),
                'autocomplete' => (bool)   ($value['autocomplete'] ?? false),
                'required'     => (bool)   ($value['required']     ?? false),
            ];
        });
    }

    // ========================================================================= //

    /**
     * Normalize the subfield configuration.
     *
     * @param array $subfieldConfig
     * @return array
     */
    private function _normalizeSubfieldConfig(array $subfieldConfig): array
    {
        // What kind of array is the subfield configuration?
        $isSequential  = (array_key_exists(0, $subfieldConfig));         // (NEW STYLE)
        $isAssociative = (array_key_exists('street1', $subfieldConfig)); // (OLD STYLE)

        // If it's a sequential array
        if ($isSequential) {
            // Get existing subfield handles
            $handles = [];
            foreach ($subfieldConfig as $subfield) {
                $handles[] = $subfield['handle'];
            }
            // Loop through default subfields
            foreach (Defaults::SUBFIELDCONFIG as $subfield) {
                // If subfield doesn't already exist
                if (!in_array($subfield['handle'], $handles, true)) {
                    // Append to subfield config
                    $subfieldConfig[] = $subfield;
                }
            }
            // Strictly typecast all subfield settings
            static::typecastSubfieldConfig($subfieldConfig);
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
                'label'        => (string) ($oldConfig['label']        ?? $defaultConfig['label']),
                'width'        => (int)    ($oldConfig['width']        ?? $defaultConfig['width']),
                'enabled'      => (bool)   ($oldConfig['enabled']      ?? false),
                'autocomplete' => (bool)   ($oldConfig['autocomplete'] ?? false),
                'required'     => (bool)   ($oldConfig['required']     ?? false),
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

        // Normalize the subfield config
        $settings['subfieldConfig'] = $this->_normalizeSubfieldConfig($settings['subfieldConfig'] ?? []);

        // Return settings
        return $settings;
    }

    /**
     * Extract data from an Address model,
     * or set everything to null if no model.
     *
     * @param AddressModel|null $address
     * @return array[]
     */
    private function _getAddressData(?AddressModel $address = null): array
    {
        return [
            'address'=> [
                'formatted'    => ($address->formatted    ?? null),
                'raw'          => ($address->raw          ?? null),
                'name'         => ($address->name         ?? null),
                'street1'      => ($address->street1      ?? null),
                'street2'      => ($address->street2      ?? null),
                'city'         => ($address->city         ?? null),
                'state'        => ($address->state        ?? null),
                'zip'          => ($address->zip          ?? null),
                'neighborhood' => ($address->neighborhood ?? null),
                'county'       => ($address->county       ?? null),
                'country'      => ($address->country      ?? null),
                'countryCode'  => ($address->countryCode  ?? null),
                'placeId'      => ($address->placeId      ?? null),
            ],
            'coords'=> [
                'lat'  => ($address->lat ?? null),
                'lng'  => ($address->lng ?? null),
                'zoom' => ($address->zoom ?? null),
            ]
        ];
    }

    /**
     * Publish a set of images, returning their published URLs.
     *
     * @param array $images
     * @return array
     */
    private function _publishImages(array $images): array
    {
        // Load asset manager
        $assetManager = Craft::$app->getAssetManager();

        // Directory of images
        $directory = '@doublesecretagency/googlemaps/web/assets/dist';

        // Publish each image, and change each value to the published URL
        array_walk($images, static function (&$value) use ($assetManager, $directory) {
            $value = $assetManager->getPublishedUrl($directory, true, "images/{$value}");
        });

        // Return published images
        return $images;
    }

    // ========================================================================= //

    /**
     * Get handles of enabled subfields.
     *
     * @return array
     */
    private function _getEnabledSubfields(): array
    {
        // Get the subfield configuration
        $subfieldConfig = ($this->subfieldConfig ?? Defaults::SUBFIELDCONFIG);

        // Initialize array of handles
        $handles = [];

        // Loop through subfield configuration
        foreach ($subfieldConfig as $subfield) {
            // Get subfield details
            $enabled = ($subfield['enabled'] ?? false);
            $handle  = ($subfield['handle']  ?? false);
            // If the subfield is enabled with a valid handle
            if ($enabled && $handle) {
                // Append to array of handles
                $handles[] = $handle;
            }
        }

        // Return handles
        return $handles;
    }

    // ========================================================================= //

    /**
     * @inheritdoc
     */
    public static function queryCondition(array $instances, mixed $value, array &$params): array
    {
        // If options are not properly specified, bail
        if (!is_array($value)) {
            return [];
        }

        // Get parameters of the proximity search
        static::$proximitySearch = [
            'field'   => ($instances[0] ?? null),
            'options' => $value,
        ];

        // Don't (yet) modify the query
        return [];
    }

    /**
     * Events which handle a proximity search.
     */
    private function _proximitySearchEvents(): void
    {
        // After an element query has been prepared
        Event::on(
            ElementQuery::class,
            ElementQuery::EVENT_AFTER_PREPARE,
            function (CancelableEvent $event) {
                /** @var ElementQueryInterface $query */
                $query = $event->sender;

                // If not a proximity search, bail
                if (!static::$proximitySearch) {
                    return;
                }

                // This is a proximity search
                $this->isProximitySearch = static::$proximitySearch;

                // Adjust query
                new ProximitySearch([
                    'query'   => $query,
                    'field'   => static::$proximitySearch['field'],
                    'options' => static::$proximitySearch['options'],
                ]);

                // Nullify search parameters
                // (prevent duplicate execution)
                static::$proximitySearch = null;
            }
        );
        // Before each element is populated
        Event::on(
            ElementQuery::class,
            ElementQuery::EVENT_BEFORE_POPULATE_ELEMENT,
            function (PopulateElementEvent $event) {

                // If not a proximity search, bail
                if (!$this->isProximitySearch) {
                    return;
                }

                // Get the field handle
                $handle = ($this->isProximitySearch['field']->handle ?? null);

                // Get distance from the SQL result
                $distance = ($event->row[$handle] ?? null);

                // Set distance to field value data
                $event->row['fieldValues'][$handle] = $distance;
            }
        );
    }

    // ========================================================================= //

    /**
     * @inheritdoc
     */
    public function getSearchKeywords(mixed $value, ElementInterface $element): string
    {
        // If no value or not an Address
        if (!$value || !is_a($value, AddressModel::class)) {
            // Bail with empty string
            return '';
        }

        // Give plugins/modules a chance to define custom keywords
        if ($this->hasEventHandlers(self::EVENT_DEFINE_KEYWORDS)) {
            $event = new DefineFieldKeywordsEvent([
                'value' => $value,
                'element' => $element,
            ]);
            $this->trigger(self::EVENT_DEFINE_KEYWORDS, $event);
            if ($event->handled) {
                return $event->keywords;
            }
        }

        // Return default keywords
        return implode(' ', [
            $value->formatted,
            $value->name,
            $value->street1,
            $value->street2,
            $value->city,
            $value->state,
            $value->zip,
            $value->neighborhood,
            $value->county,
            $value->country,
            $value->countryCode,
        ]);
    }

    // ========================================================================= //

    /**
     * @inheritdoc
     */
    public function getContentGqlType(): Type|array
    {
        return AddressType::getType();
    }

    /**
     * @inheritdoc
     */
    public function getContentGqlMutationArgumentType(): Type|array
    {
        return [
            'name' => $this->handle,
            'type' => AddressInput::getType(),
            'description' => $this->instructions,
        ];
    }

}

// Aliased from old field class
class_alias(AddressField::class, \doublesecretagency\smartmap\fields\Address::class);

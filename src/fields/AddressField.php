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

namespace doublesecretagency\googlemaps\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use doublesecretagency\googlemaps\helpers\AddressHelper;
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
     * What should the map be
     * when the field is initially loaded?
     *
     * @var string "open" or "close"
     */
    public $mapOnStart = 'close';

    public $showMap;

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
//        SmartMap::$plugin->smartMap->saveAddressField($this, $element);
    }

    /**
     * Prep value for use as the data leaves the database.
     *
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
//        return SmartMap::$plugin->smartMap->getAddressField($this, $element, $value);
    }

    // ========================================================================= //

//    /**
//     * @inheritdoc
//     */
//    public function getSettings(): array
//    {
//        $settings = parent::getSettings();
//
////        // Ensure layout is an array
////        if (!is_array($settings['layout'])) {
////            $settings['layout'] = json_decode($settings['layout'], true);
////        }
//
//        return $settings;
//    }

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

        // Load fieldtype input template
        return $view->renderTemplate('google-maps/address', [
//            'value' => $value,
            'handle' => $this->handle,
            'icons' => AddressHelper::visibilityIcons(),
            'settings' => $this->_getExtraSettings(),
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

        // Set whether we're using CP Field Inspect
        $settings['usingCpFieldInspect'] = Craft::$app->getPlugins()->isPluginEnabled('cp-field-inspect');

        // Return settings
        return $settings;
    }

    // ========================================================================= //

//    /**
//     * @inheritdoc
//     */
//    public function modifyElementsQuery(ElementQueryInterface $query, $params)
//    {
//        // If no params, bail
//        if (!$params) {
//            return null;
//        }
//        // If params are not an array, bail
//        if (!is_array($params)) {
//            return null;
//        }
//        // Modify the query
//        $params['fieldId']     = $this->id;
//        $params['fieldHandle'] = $this->handle;
//        SmartMap::$plugin->smartMap->modifyQuery($query, $params);
//    }

    // ========================================================================= //

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
//            $element->addError($this->handle, Craft::t('smart-map', 'If coordinates are specified, they must be numbers.'));
//        }
//    }

}

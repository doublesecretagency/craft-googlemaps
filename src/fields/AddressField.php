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
use doublesecretagency\googlemaps\web\assets\addressfield\AddressFieldAsset;

/**
 * Class AddressField
 * @since 4.0.0
 */
class AddressField extends Field implements PreviewableFieldInterface
{

    /**
     * What should the map be
     * when the field is initially loaded?
     *
     * @var string "open" or "close"
     */
    public $mapOnStart = 'close';

    /**
     * What should the map be
     * when a geocode lookup is performed?
     *
     * @var string "open", "close" or "noChange"
     */
    public $mapOnSearch = 'noChange';

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
    public $coordinatesDefault = [
        'lat' => 34.038136,
        'lng' => -118.243996,
        'zoom' => 11
    ];

    /**
     * Full configuration of subfields.
     *
     * @var array|null
     */
    public $subfieldConfig = [
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
//        $view->registerAssetBundle(FieldSettingsAssets::class);

        // Load fieldtype settings template
        return $view->renderTemplate('google-maps/address-settings', [
            'settings' => $this->getSettings(),
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

        // Get published icon URLs
        $icons = [
            'marker'       => $this->_publishSvg('marker.svg'),
            'markerHollow' => $this->_publishSvg('marker-hollow.svg')
        ];

        // Whether or not the CP Field Inspect is being used
        $usingCpFieldInspect = Craft::$app->getPlugins()->isPluginEnabled('cp-field-inspect');

        // Load fieldtype input template
        return $view->renderTemplate('google-maps/address', [
//            'name' => $this->handle,
//            'value' => $value,
            'field' => $this,
            'icons' => $icons,
            'usingCpFieldInspect' => $usingCpFieldInspect,
        ]);
    }

    /**
     * Generate a published icon URL.
     *
     * @param $filename
     * @return string|false
     */
    private function _publishSvg($filename)
    {
        $manager = Craft::$app->getAssetManager();
        $assets = '@doublesecretagency/googlemaps/web/assets';
        $markerSvg = "addressfield/dist/images/{$filename}";
        return $manager->getPublishedUrl($assets, true, $markerSvg);
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

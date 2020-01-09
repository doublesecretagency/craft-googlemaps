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

namespace doublesecretagency\googlemaps;

use Craft;
use craft\base\Plugin;
use doublesecretagency\googlemaps\models\Settings;
use doublesecretagency\googlemaps\services\AddressField;
use doublesecretagency\googlemaps\services\Api;
use doublesecretagency\googlemaps\services\Geocoding;
use doublesecretagency\googlemaps\services\Geolocation;
use doublesecretagency\googlemaps\services\MapsJavascript;
use doublesecretagency\googlemaps\services\MapsStatic;
use doublesecretagency\googlemaps\services\ProximitySearch;
use doublesecretagency\googlemaps\web\twig\Extension;

/**
 * Class GoogleMapsPlugin
 * @since 4.0.0
 *
 * @property AddressField $addressField
 * @property Api $api
 * @property Geocoding $geocoding
 * @property Geolocation $geolocation
 * @property MapsJavascript $mapsJavascript
 * @property MapsStatic $mapsStatic
 * @property ProximitySearch $proximitySearch
 */
class GoogleMapsPlugin extends Plugin
{

    /**
     * @var bool The plugin has a settings page.
     */
//    public $hasCpSettings = true;

    /**
     * @var string Current schema version of the plugin.
     */
    public $schemaVersion = '4.0.0-alpha.1';

    /**
     * @var Plugin $plugin Self-referential plugin property.
     */
    public static $plugin;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Load Twig extension
        Craft::$app->getView()->registerTwigExtension(new Extension());

        // Load plugin components
        $this->setComponents([
            'addressField' => AddressField::class,
            'api' => Api::class,
            'geocoding' => Geocoding::class,
            'geolocation' => Geolocation::class,
            'mapsJavascript' => MapsJavascript::class,
            'mapsStatic' => MapsStatic::class,
            'proximitySearch' => ProximitySearch::class,
        ]);
    }

    /**
     * @return Settings Plugin settings model.
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

//    /**
//     * @return string The fully rendered settings template.
//     */
//    protected function settingsHtml(): string
//    {
//        $view = Craft::$app->getView();
//        $view->registerAssetBundle(SettingsAssets::class);
//        $overrideKeys = array_keys(Craft::$app->getConfig()->getConfigFromFile('smart-map'));
//        return $view->renderTemplate('smart-map/settings', [
//            'settings' => $this->getSettings(),
//            'overrideKeys' => $overrideKeys,
//            'docsUrl' => $this->documentationUrl,
//        ]);
//    }

}

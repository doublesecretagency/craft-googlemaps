<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2020 Double Secret Agency
 */

namespace doublesecretagency\googlemaps;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use doublesecretagency\googlemaps\fields\AddressField;
use doublesecretagency\googlemaps\models\Settings;
use doublesecretagency\googlemaps\services\Address;
use doublesecretagency\googlemaps\services\Api;
use doublesecretagency\googlemaps\services\Geocoding;
use doublesecretagency\googlemaps\services\Geolocation;
use doublesecretagency\googlemaps\services\MapsJavascript;
use doublesecretagency\googlemaps\services\MapsStatic;
use doublesecretagency\googlemaps\services\ProximitySearch;
use doublesecretagency\googlemaps\web\assets\SettingsAsset;
use doublesecretagency\googlemaps\web\twig\Extension;
use yii\base\Event;

/**
 * Class GoogleMapsPlugin
 * @since 4.0.0
 *
 * @property Address $address
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
    public $hasCpSettings = true;

    /**
     * @var string Current schema version of the plugin.
     */
    public $schemaVersion = '4.0.0-alpha.2';

    /**
     * @var Plugin $plugin Self-referential plugin property.
     */
    public static $plugin;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Load Twig extension
        Craft::$app->getView()->registerTwigExtension(new Extension());

        // Load plugin components
        $this->setComponents([
            'address' => Address::class,
            'api' => Api::class,
            'geocoding' => Geocoding::class,
            'geolocation' => Geolocation::class,
            'mapsJavascript' => MapsJavascript::class,
            'mapsStatic' => MapsStatic::class,
            'proximitySearch' => ProximitySearch::class,
        ]);

        // Register field type
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = AddressField::class;
            }
        );
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        // Reference assets
        $view = Craft::$app->getView();
        $view->registerAssetBundle(SettingsAsset::class);

        // Get data from config file
        $configFile = Craft::$app->getConfig()->getConfigFromFile('google-maps');

        // Load plugin settings template
        return $view->renderTemplate('google-maps/settings', [
            'configFile' => $configFile,
            'settings' => $this->getSettings(),
        ]);
    }

}

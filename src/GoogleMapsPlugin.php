<?php
/**
 * Google Maps plugin for Craft CMS
 *
 * Maps in minutes. Powered by Google Maps.
 *
 * @author    Double Secret Agency
 * @link      https://plugins.doublesecretagency.com/
 * @copyright Copyright (c) 2014, 2021 Double Secret Agency
 */

namespace doublesecretagency\googlemaps;

use Craft;
use craft\base\Plugin;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\UrlHelper;
use craft\services\Fields;
use craft\services\Plugins;
use doublesecretagency\googlemaps\fields\AddressField;
use doublesecretagency\googlemaps\models\Settings;
use doublesecretagency\googlemaps\services\Geolocation;
use doublesecretagency\googlemaps\web\assets\SettingsAsset;
use doublesecretagency\googlemaps\web\twig\Extension;
use yii\base\Event;

/**
 * Class GoogleMapsPlugin
 * @since 4.0.0
 *
 * @property Geolocation $geolocation
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
    public $schemaVersion = '4.0.0-beta.1';

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
            'geolocation' => Geolocation::class,
        ]);

        // Register field type
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            static function (RegisterComponentTypesEvent $event) {
                $event->types[] = AddressField::class;
            }
        );

        // Redirect after install
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            static function (PluginEvent $event) {

                // If console request, bail
                if (Craft::$app->getRequest()->getIsConsoleRequest()) {
                    return;
                }

                // If not Google Maps, bail
                if ('google-maps' != $event->plugin->handle) {
                    return;
                }

                // Redirect to the plugin's settings page (with a welcome message)
                $url = UrlHelper::cpUrl('settings/plugins/google-maps', ['welcome' => 1]);
                Craft::$app->getResponse()->redirect($url)->send();
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

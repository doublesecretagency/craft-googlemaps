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

namespace doublesecretagency\googlemaps;

use Craft;
use craft\base\Element;
use craft\base\Field;
use craft\base\Model;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterElementExportersEvent;
use craft\helpers\UrlHelper;
use craft\services\Fields;
use craft\services\Plugins;
use craft\services\Utilities;
use doublesecretagency\googlemaps\exporters\AddressesCondensedExporter;
use doublesecretagency\googlemaps\exporters\AddressesExpandedExporter;
use doublesecretagency\googlemaps\fields\AddressField;
use doublesecretagency\googlemaps\models\Settings;
use doublesecretagency\googlemaps\utilities\TestGoogleApiKeysUtility;
use doublesecretagency\googlemaps\web\assets\SettingsAsset;
use doublesecretagency\googlemaps\web\twig\Extension;
use yii\base\Event;

/**
 * Class GoogleMapsPlugin
 * @since 4.0.0
 */
class GoogleMapsPlugin extends Plugin
{

    /**
     * @event GeocodingEvent The event that is triggered after a geocoding address lookup has been performed.
     */
    public const EVENT_AFTER_GEOCODING = 'afterGeocoding';

    /**
     * @event GeolocationEvent The event that is triggered after a visitor geolocation has been performed.
     */
    public const EVENT_AFTER_GEOLOCATION = 'afterGeolocation';

    /**
     * @var bool The plugin has a settings page.
     */
    public bool $hasCpSettings = true;

    /**
     * @var string Current schema version of the plugin.
     */
    public string $schemaVersion = '4.3.0';

    /**
     * @var GoogleMapsPlugin Self-referential plugin property.
     */
    public static GoogleMapsPlugin $plugin;

    /**
     * @var array Collection of settings to be migrated.
     */
    public static array $migrateSettings = [];

    /**
     * @var string|null Existing license key to be migrated.
     */
    public static ?string $migrateLicenseKey = null;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Load Twig extension
        Craft::$app->getView()->registerTwigExtension(new Extension());

        // Register enhancements for the control panel
        if (Craft::$app->getRequest()->getIsCpRequest()) {
            $this->_registerUtilities();
        }

        // Register all events
        $this->_registerFieldType();
        $this->_registerExporters();

        // Redirect after plugin install
        $this->_postInstallRedirect();

        // Normalize the subfield configuration
        $this->_normalizeSubfieldConfig();
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): ?string
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

    // ========================================================================= //

    /**
     * Register the field type.
     *
     * @return void
     */
    private function _registerFieldType(): void
    {
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            static function (RegisterComponentTypesEvent $event) {
                $event->types[] = AddressField::class;
            }
        );
    }

    /**
     * Register the exporters.
     *
     * @return void
     */
    private function _registerExporters(): void
    {
        Event::on(
            Entry::class,
            Element::EVENT_REGISTER_EXPORTERS,
            static function (RegisterElementExportersEvent $event) {
                $event->exporters[] = AddressesCondensedExporter::class;
                $event->exporters[] = AddressesExpandedExporter::class;
            }
        );
    }

    /**
     * Register utilities.
     */
    private function _registerUtilities(): void
    {
        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            static function(RegisterComponentTypesEvent $event) {
                $event->types[] = TestGoogleApiKeysUtility::class;
            }
        );
    }

    // ========================================================================= //

    /**
     * After the plugin has been installed,
     * redirect to the "Welcome" settings page.
     *
     * @return void
     */
    private function _postInstallRedirect(): void
    {
        // After the plugin has been installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            static function (PluginEvent $event) {

                // If installed plugin isn't Google Maps, bail
                if ('google-maps' !== $event->plugin->handle) {
                    return;
                }

                // Get plugins service
                $plugins = Craft::$app->getPlugins();

                // If settings are being migrated, save them
                if (static::$migrateSettings) {
                    $plugins->savePluginSettings(
                        static::$plugin,
                        static::$migrateSettings
                    );
                }

                // If plugin license key is being migrated, save it
                if (static::$migrateLicenseKey) {
                    $plugins->setPluginLicenseKey(
                        'google-maps',
                        static::$migrateLicenseKey
                    );
                }

                // If installed via console, no need for a redirect
                if (Craft::$app->getRequest()->getIsConsoleRequest()) {
                    return;
                }

                // Redirect to the plugin's settings page (with a welcome message)
                $url = UrlHelper::cpUrl('settings/plugins/google-maps', ['welcome' => 1]);
                Craft::$app->getResponse()->redirect($url)->send();
            }
        );
    }

    /**
     * When the field settings are saved,
     * normalize the subfield configuration.
     *
     * @return void
     */
    private function _normalizeSubfieldConfig(): void
    {
        // Adjust field settings when they are saved
        Event::on(
            Field::class,
            Field::EVENT_BEFORE_SAVE,
            function (ModelEvent $event) {

                // Get field settings
                $fieldSettings = $event->sender;

                // If no subfield config, bail
                if (!($fieldSettings->subfieldConfig ?? false)) {
                    return;
                }

                // Strictly typecast all subfield settings
                AddressField::typecastSubfieldConfig($fieldSettings->subfieldConfig);
            }
        );
    }

}

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
//use doublesecretagency\googlemaps\models\Settings;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use doublesecretagency\googlemaps\web\twig\Extension;

/**
 * Class GoogleMapsPlugin
 * @since 4.0.0
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
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        // Load Twig extension
        Craft::$app->getView()->registerTwigExtension(new Extension());
    }

//    /**
//     * @return Settings Plugin settings model.
//     */
//    protected function createSettingsModel(): Settings
//    {
//        return new Settings();
//    }

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

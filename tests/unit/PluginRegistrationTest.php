<?php
namespace doublesecretagency\googlemaps\tests\unit;

use craft\base\Plugin;
use doublesecretagency\googlemaps\GoogleMapsPlugin;
use doublesecretagency\googlemaps\models\Settings;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Structural tests for the plugin class and everything it registers.
 *
 * The plugin has no services and registers no components. Its `init()` is a
 * list of event wirings, so the shape of that list IS the plugin's behavior.
 * A dropped `Event::on` call is silent: the feature simply stops happening, and
 * nothing errors.
 */
class PluginRegistrationTest extends TestCase
{
    private string $pluginSource;
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        $path = dirname(__DIR__, 2) . '/src/GoogleMapsPlugin.php';
        $this->assertTrue(file_exists($path), "GoogleMapsPlugin.php should exist at: $path");
        $this->pluginSource = file_get_contents($path);
        $this->reflection = new ReflectionClass(GoogleMapsPlugin::class);
    }

    // ========================================================================= //
    // Class shape
    // ========================================================================= //

    public function testExtendsCraftPlugin(): void
    {
        $this->assertTrue($this->reflection->isSubclassOf(Plugin::class));
    }

    public function testHasASettingsPage(): void
    {
        $this->assertTrue($this->reflection->getDefaultProperties()['hasCpSettings']);
    }

    public function testUsesTheSettingsModel(): void
    {
        $this->assertStringContainsString('return new Settings();', $this->pluginSource);
    }

    public function testDeclaresASchemaVersion(): void
    {
        $version = $this->reflection->getDefaultProperties()['schemaVersion'];

        $this->assertIsString($version);
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', $version);
    }

    public function testHoldsASelfReference(): void
    {
        // `GoogleMapsPlugin::$plugin` is read all over the codebase, including
        // from the static helper facade.
        $this->assertTrue($this->reflection->hasProperty('plugin'));
        $this->assertTrue($this->reflection->getProperty('plugin')->isStatic());
    }

    public function testCarriesTheSmartMapMigrationHolders(): void
    {
        // Populated by FromSmartMap and consumed by the post-install handler.
        $this->assertTrue($this->reflection->getProperty('migrateSettings')->isStatic());
        $this->assertTrue($this->reflection->getProperty('migrateLicenseKey')->isStatic());
    }

    // ========================================================================= //
    // Events the plugin fires
    // ========================================================================= //

    public function testDeclaresTheGeocodingEvent(): void
    {
        $this->assertSame('afterGeocoding', GoogleMapsPlugin::EVENT_AFTER_GEOCODING);
    }

    public function testDeclaresTheGeolocationEvent(): void
    {
        $this->assertSame('afterGeolocation', GoogleMapsPlugin::EVENT_AFTER_GEOLOCATION);
    }

    public function testGeocodingEventIsTriggeredByTheLookupModel(): void
    {
        $lookup = file_get_contents(dirname(__DIR__, 2) . '/src/models/Lookup.php');

        $this->assertStringContainsString('GoogleMapsPlugin::EVENT_AFTER_GEOCODING', $lookup);
        $this->assertStringContainsString('new GeocodingEvent(', $lookup);
    }

    public function testGeolocationEventIsTriggeredByTheGeolocationHelper(): void
    {
        $helper = file_get_contents(dirname(__DIR__, 2) . '/src/helpers/GeolocationHelper.php');

        $this->assertStringContainsString('GoogleMapsPlugin::EVENT_AFTER_GEOLOCATION', $helper);
    }

    // ========================================================================= //
    // Events the plugin listens to
    // ========================================================================= //

    /**
     * @dataProvider registrationProvider
     */
    public function testInitRegistersListener(string $class, string $event): void
    {
        $this->assertMatchesRegularExpression(
            "/{$class}::class,\s*{$class}::{$event}/",
            $this->pluginSource,
            "Lost the {$class}::{$event} registration. The feature it powers stops "
            . 'happening with no error.'
        );
    }

    public static function registrationProvider(): array
    {
        return [
            'field type'              => ['Fields', 'EVENT_REGISTER_FIELD_TYPES'],
            'compatible field types'  => ['Fields', 'EVENT_DEFINE_COMPATIBLE_FIELD_TYPES'],
            'utilities'               => ['Utilities', 'EVENT_REGISTER_UTILITIES'],
            'post install redirect'   => ['Plugins', 'EVENT_AFTER_INSTALL_PLUGIN'],
        ];
    }

    public function testRegistersTheAddressField(): void
    {
        $this->assertStringContainsString('$event->types[] = AddressField::class;', $this->pluginSource);
    }

    public function testRegistersBothExportersOnEntries(): void
    {
        $this->assertMatchesRegularExpression(
            '/Entry::class,\s*Element::EVENT_REGISTER_EXPORTERS/',
            $this->pluginSource
        );
        $this->assertStringContainsString('AddressesCondensedExporter::class;', $this->pluginSource);
        $this->assertStringContainsString('AddressesExpandedExporter::class;', $this->pluginSource);
    }

    public function testRegistersBothUtilities(): void
    {
        $this->assertStringContainsString('TestGoogleApiKeysUtility::class;', $this->pluginSource);
        $this->assertStringContainsString('TestAddressLookupUtility::class;', $this->pluginSource);
    }

    public function testUtilitiesAreOnlyRegisteredForControlPanelRequests(): void
    {
        $this->assertMatchesRegularExpression(
            '/if \(Craft::\$app->getRequest\(\)->getIsCpRequest\(\)\) \{\s*\$this->_registerUtilities\(\);/',
            $this->pluginSource
        );
    }

    public function testRegistersTheTwigExtension(): void
    {
        $this->assertStringContainsString(
            'registerTwigExtension(new Extension())',
            $this->pluginSource
        );
    }

    public function testNormalizesSubfieldConfigBeforeAnyFieldSaves(): void
    {
        $this->assertMatchesRegularExpression(
            '/Field::class,\s*Field::EVENT_BEFORE_SAVE/',
            $this->pluginSource
        );
    }

    public function testSettingsPreviewIsEmptiedBeforeSave(): void
    {
        // Otherwise a live preview address lands in project config.
        $this->assertStringContainsString('$field->settingsPreview = [];', $this->pluginSource);
    }

    // ========================================================================= //
    // Guarded registrations
    // ========================================================================= //

    public function testCompatibleFieldTypesIsGuardedOnClassExistence(): void
    {
        // The event class only exists on Craft 4.5.7 and newer.
        $this->assertStringContainsString(
            'if (!class_exists(DefineCompatibleFieldTypesEvent::class))',
            $this->pluginSource
        );
    }

    public function testAcfAdapterIsGuardedOnWpImportBeingInstalled(): void
    {
        // wp-import is a dev dependency, absent on most installs.
        $this->assertStringContainsString('if (!class_exists(WpImportCommand::class))', $this->pluginSource);
    }

    public function testMapboxAndSimpleMapAreWhitelistedAsConversionSources(): void
    {
        $this->assertStringContainsString("'doublesecretagency\\mapbox\\fields\\AddressField'", $this->pluginSource);
        $this->assertStringContainsString("'ether\\simplemap\\fields\\MapField'", $this->pluginSource);
    }

    public function testSimpleMapConversionRequiresTheNewerCraft(): void
    {
        // Field data transfer needs Craft 5.5.3.
        $this->assertStringContainsString("version_compare(Craft::\$app->getVersion(), '5.5.3', '>=')", $this->pluginSource);
    }

    // ========================================================================= //
    // Post-install behavior
    // ========================================================================= //

    public function testPostInstallOnlyActsOnItsOwnHandle(): void
    {
        $this->assertStringContainsString("if ('google-maps' !== \$event->plugin->handle)", $this->pluginSource);
    }

    public function testPostInstallSkipsTheRedirectOnConsoleInstalls(): void
    {
        $this->assertStringContainsString(
            'if (Craft::$app->getRequest()->getIsConsoleRequest())',
            $this->pluginSource,
            'A console install must not attempt an HTTP redirect.'
        );
    }

    public function testPostInstallCarriesTheWelcomeFlag(): void
    {
        $this->assertStringContainsString(
            "UrlHelper::cpUrl('settings/plugins/google-maps', ['welcome' => 1])",
            $this->pluginSource
        );
    }

    // ========================================================================= //
    // Composer wiring
    // ========================================================================= //

    public function testComposerDeclaresTheExpectedHandleAndClass(): void
    {
        $composer = json_decode(file_get_contents(dirname(__DIR__, 2) . '/composer.json'), true);

        $this->assertSame('google-maps', $composer['extra']['handle']);
        $this->assertSame(GoogleMapsPlugin::class, $composer['extra']['class']);
        $this->assertSame('craft-plugin', $composer['type']);
    }

    public function testComposerMapsBothNamespaces(): void
    {
        $composer = json_decode(file_get_contents(dirname(__DIR__, 2) . '/composer.json'), true);
        $psr4 = $composer['autoload']['psr-4'];

        $this->assertSame('src/', $psr4['doublesecretagency\\googlemaps\\']);
        $this->assertSame('legacy/', $psr4['doublesecretagency\\smartmap\\']);
    }

    public function testComposerReplacesSmartMap(): void
    {
        // So the two plugins can never be installed side by side.
        $composer = json_decode(file_get_contents(dirname(__DIR__, 2) . '/composer.json'), true);

        $this->assertArrayHasKey('doublesecretagency/craft-smartmap', $composer['replace']);
    }

    public function testLegacySmartMapFieldClassStillResolves(): void
    {
        // Keeps a Smart Map install from showing a Missing Field before the
        // migration runs.
        $this->assertTrue(
            file_exists(dirname(__DIR__, 2) . '/legacy/fields/Address.php'),
            'The legacy Smart Map field shim is gone.'
        );
    }
}

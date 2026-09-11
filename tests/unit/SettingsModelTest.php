<?php
namespace doublesecretagency\googlemaps\tests\unit;

use craft\base\Model;
use doublesecretagency\googlemaps\models\Settings;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Tests for the plugin settings, and their parity with the config template.
 *
 * `src/config.php` is copied by users into `config/google-maps.php`, so a
 * setting present in one file and absent from the other is a documentation bug
 * that reads as a working feature. A user who copies a key the model does not
 * have gets no error and no effect, which is how the stale
 * `geocodingCacheDuration` and `geolocationCacheDuration` keys survived in at
 * least one sandbox long after the settings they named stopped existing.
 */
class SettingsModelTest extends TestCase
{
    private ReflectionClass $reflection;
    private string $configTemplate;

    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(Settings::class);

        $path = dirname(__DIR__, 2) . '/src/config.php';
        $this->assertTrue(file_exists($path), "config.php should exist at: $path");
        $this->configTemplate = file_get_contents($path);
    }

    /**
     * Every public property on the settings model.
     */
    private function _settingNames(): array
    {
        $names = [];
        foreach ($this->reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (Settings::class === $property->getDeclaringClass()->getName()) {
                $names[] = $property->getName();
            }
        }
        return $names;
    }

    /**
     * Every setting named in the config template, commented out or not.
     */
    private function _templateKeys(): array
    {
        preg_match_all("/^\s*(?:\/\/)?'([a-zA-Z]+)'\s*=>/m", $this->configTemplate, $m);
        return array_values(array_unique($m[1]));
    }

    // ========================================================================= //
    // Class shape
    // ========================================================================= //

    public function testExtendsCraftModel(): void
    {
        $this->assertTrue($this->reflection->isSubclassOf(Model::class));
    }

    public function testIsInstantiableWithoutCraft(): void
    {
        $this->assertInstanceOf(Settings::class, new Settings());
    }

    // ========================================================================= //
    // Defaults
    // ========================================================================= //

    /**
     * @dataProvider defaultProvider
     */
    public function testSettingDefault(string $setting, mixed $expected): void
    {
        $this->assertSame($expected, $this->reflection->getDefaultProperties()[$setting]);
    }

    public static function defaultProvider(): array
    {
        return [
            'browserKey'          => ['browserKey', null],
            'serverKey'           => ['serverKey', null],
            'geolocationService'  => ['geolocationService', null],
            'ipstackApiAccessKey' => ['ipstackApiAccessKey', null],
            'maxmindUserId'       => ['maxmindUserId', null],
            'maxmindLicenseKey'   => ['maxmindLicenseKey', null],
            'maxmindService'      => ['maxmindService', null],
            'enableJsLogging'     => ['enableJsLogging', true],
            'minifyJsFiles'       => ['minifyJsFiles', false],
            'fieldControlSize'    => ['fieldControlSize', 27],
            'fieldParams'         => ['fieldParams', []],
        ];
    }

    public function testNoCredentialShipsWithAValue(): void
    {
        foreach (['browserKey', 'serverKey', 'ipstackApiAccessKey', 'maxmindUserId', 'maxmindLicenseKey'] as $credential) {
            $this->assertNull(
                $this->reflection->getDefaultProperties()[$credential],
                "Credential `{$credential}` has a non-null default."
            );
        }
    }

    public function testMinifiedFilesAreOffByDefault(): void
    {
        // The minified twins are hand-maintained with no build task, so
        // defaulting to them would serve stale code on a fresh install.
        $this->assertFalse($this->reflection->getDefaultProperties()['minifyJsFiles']);
    }

    // ========================================================================= //
    // Types
    // ========================================================================= //

    /**
     * @dataProvider typeProvider
     */
    public function testSettingType(string $setting, string $expectedType, bool $nullable): void
    {
        $type = $this->reflection->getProperty($setting)->getType();

        $this->assertNotNull($type, "Setting `{$setting}` has no type declaration.");
        $this->assertSame($expectedType, $type->getName());
        $this->assertSame($nullable, $type->allowsNull());
    }

    public static function typeProvider(): array
    {
        return [
            'browserKey'          => ['browserKey', 'string', true],
            'serverKey'           => ['serverKey', 'string', true],
            'geolocationService'  => ['geolocationService', 'string', true],
            'ipstackApiAccessKey' => ['ipstackApiAccessKey', 'string', true],
            'maxmindUserId'       => ['maxmindUserId', 'string', true],
            'maxmindLicenseKey'   => ['maxmindLicenseKey', 'string', true],
            'maxmindService'      => ['maxmindService', 'string', true],
            'enableJsLogging'     => ['enableJsLogging', 'bool', false],
            'minifyJsFiles'       => ['minifyJsFiles', 'bool', false],
            'fieldControlSize'    => ['fieldControlSize', 'int', false],
            'fieldParams'         => ['fieldParams', 'array', false],
        ];
    }

    // ========================================================================= //
    // Parity with the config template
    // ========================================================================= //

    public function testBothListsWereFound(): void
    {
        $this->assertNotEmpty($this->_settingNames());
        $this->assertNotEmpty($this->_templateKeys());
    }

    public function testEverySettingAppearsInTheConfigTemplate(): void
    {
        $missing = array_diff($this->_settingNames(), $this->_templateKeys());

        $this->assertSame([], array_values($missing), sprintf(
            'Settings with no entry in config.php: %s. A user copying the template '
            . 'has no way to discover them.',
            implode(', ', $missing)
        ));
    }

    public function testTheConfigTemplateNamesNoSettingThatDoesNotExist(): void
    {
        $extra = array_diff($this->_templateKeys(), $this->_settingNames());

        $this->assertSame([], array_values($extra), sprintf(
            'Keys in config.php with no matching setting: %s. Copying one of these '
            . 'into a project config produces no error and no effect.',
            implode(', ', $extra)
        ));
    }

    public function testTheConfigTemplateIsInert(): void
    {
        // It exists to be copied, not loaded. Every key must be commented out
        // except any the plugin genuinely ships as active.
        $this->assertStringContainsString('This file exists only as a template', $this->configTemplate);
        $this->assertStringContainsString('It does nothing on its own', $this->configTemplate);
    }

    // ========================================================================= //
    // The geolocation service, which gates two credential pairs
    // ========================================================================= //

    public function testGeolocationServiceAcceptsTheTwoSupportedProviders(): void
    {
        $source = file_get_contents($this->reflection->getFileName());

        $this->assertStringContainsString("'ipstack'", $source);
        $this->assertStringContainsString("'maxmind'", $source);
    }

    public function testEachGeolocationProviderHasItsCredentials(): void
    {
        $settings = $this->_settingNames();

        // ipstack takes one key; MaxMind takes a user, a license and a service.
        $this->assertContains('ipstackApiAccessKey', $settings);
        $this->assertContains('maxmindUserId', $settings);
        $this->assertContains('maxmindLicenseKey', $settings);
        $this->assertContains('maxmindService', $settings);
    }

    // ========================================================================= //
    // What is deliberately NOT a setting
    // ========================================================================= //

    /**
     * 🛑 The geocoding and geolocation caches are hardcoded to 30 days in
     * three separate models. Any config key that looks like a cache duration
     * is stale and does nothing.
     *
     * @dataProvider nonSettingProvider
     */
    public function testIsNotASetting(string $name): void
    {
        $this->assertNotContains($name, $this->_settingNames());
        $this->assertNotContains($name, $this->_templateKeys());
    }

    public static function nonSettingProvider(): array
    {
        return [
            'geocodingCacheDuration'   => ['geocodingCacheDuration'],
            'geolocationCacheDuration' => ['geolocationCacheDuration'],
            'cacheDuration'            => ['cacheDuration'],
        ];
    }

    public function testCacheDurationIsHardcodedInEveryCachingModel(): void
    {
        $thirtyDays = '(30 * 24 * 60 * 60)';

        foreach (['Lookup', 'Ipstack', 'Maxmind'] as $model) {
            $source = file_get_contents(dirname(__DIR__, 2) . "/src/models/{$model}.php");

            $this->assertStringContainsString(
                $thirtyDays,
                $source,
                "{$model} no longer hardcodes a 30 day cache. If it became configurable, "
                . 'this test and the settings model should both change together.'
            );
        }
    }
}

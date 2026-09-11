<?php
namespace doublesecretagency\googlemaps\tests\unit;

use craft\base\Field;
use doublesecretagency\googlemaps\enums\Defaults;
use doublesecretagency\googlemaps\fields\AddressField;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Structural tests for the Address field.
 *
 * This is the plugin's only field type and the surface every install depends
 * on. Its settings are stored in project config, so a renamed property is a
 * silent settings loss on the next config apply, and its public methods are
 * the contract Craft calls into.
 */
class AddressFieldShapeTest extends TestCase
{
    private ReflectionClass $reflection;
    private string $fieldSource;

    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(AddressField::class);
        $this->fieldSource = file_get_contents($this->reflection->getFileName());
    }

    // ========================================================================= //
    // Class shape
    // ========================================================================= //

    public function testExtendsCraftField(): void
    {
        $this->assertTrue($this->reflection->isSubclassOf(Field::class));
    }

    public function testIsNotAbstract(): void
    {
        $this->assertFalse($this->reflection->isAbstract());
    }

    /**
     * @dataProvider craftContractMethodProvider
     */
    public function testImplementsCraftContractMethod(string $method): void
    {
        $this->assertTrue(
            $this->reflection->hasMethod($method),
            "Craft calls {$method}() on every field."
        );
    }

    public static function craftContractMethodProvider(): array
    {
        $methods = [
            'displayName', 'icon', 'supportedTranslationMethods', 'dbType',
            'normalizeValue', 'afterElementSave', 'getInputHtml', 'getSettingsHtml',
            'getElementValidationRules', 'getSearchKeywords', 'queryCondition',
            'getContentGqlType', 'getContentGqlMutationArgumentType',
        ];
        return array_combine($methods, array_map(static fn(string $m): array => [$m], $methods));
    }

    public function testUsesIconRatherThanTheRenamedIconPath(): void
    {
        // Craft 5 renamed `iconPath()` to `icon()` and removed it from the
        // interface, so a stale name fails silently with a letter badge.
        $this->assertTrue($this->reflection->hasMethod('icon'));
        $this->assertFalse($this->reflection->hasMethod('iconPath'));
    }

    public function testNormalizeValueReturnsAnAddressModel(): void
    {
        $type = (string) $this->reflection->getMethod('normalizeValue')->getReturnType();

        $this->assertStringContainsString('Address', $type);
    }

    // ========================================================================= //
    // Settings properties, which live in project config
    // ========================================================================= //

    /**
     * @dataProvider settingProvider
     */
    public function testSettingExistsWithExpectedDefault(string $setting, mixed $expected): void
    {
        $defaults = $this->reflection->getDefaultProperties();

        $this->assertArrayHasKey($setting, $defaults, "Field setting `{$setting}` is gone.");
        $this->assertSame($expected, $defaults[$setting]);
    }

    public static function settingProvider(): array
    {
        return [
            'showMap'            => ['showMap', false],
            'mapOnStart'         => ['mapOnStart', 'default'],
            'mapOnSearch'        => ['mapOnSearch', 'open'],
            'visibilityToggle'   => ['visibilityToggle', 'both'],
            'coordinatesMode'    => ['coordinatesMode', 'readOnly'],
            'requireCoordinates' => ['requireCoordinates', true],
        ];
    }

    public function testDefaultCoordinatesComeFromTheDefaultsEnum(): void
    {
        $this->assertSame(
            Defaults::COORDINATES,
            $this->reflection->getDefaultProperties()['coordinatesDefault']
        );
    }

    public function testDefaultSubfieldConfigComesFromTheDefaultsEnum(): void
    {
        $this->assertSame(
            Defaults::SUBFIELDCONFIG,
            $this->reflection->getDefaultProperties()['subfieldConfig']
        );
    }

    public function testSettingsPreviewDefaultsToEmpty(): void
    {
        // It is a scratch value for the settings screen and must never persist.
        $this->assertSame([], $this->reflection->getDefaultProperties()['settingsPreview']);
    }

    // ========================================================================= //
    // The save path
    // ========================================================================= //

    public function testAfterElementSaveWritesEveryColumn(): void
    {
        // The field stores into its own table rather than element content, so
        // this method is the only writer.
        foreach (array_column(Defaults::SUBFIELDCONFIG, 'handle') as $handle) {
            $this->assertMatchesRegularExpression(
                "/'{$handle}'\s*=>\s*\(\\\$data\['{$handle}'\]/",
                $this->fieldSource,
                "afterElementSave() does not persist `{$handle}`."
            );
        }
    }

    public function testAfterElementSaveWritesBothMetaColumns(): void
    {
        foreach (['formatted', 'raw'] as $meta) {
            $this->assertMatchesRegularExpression(
                "/'{$meta}'\s*=>\s*\(\\\$data\['{$meta}'\]/",
                $this->fieldSource,
                "afterElementSave() does not persist `{$meta}`."
            );
        }
    }

    public function testEmptyStringsAreStoredAsNull(): void
    {
        // `?:` rather than `??`, so a blank subfield does not become an empty
        // string that later reads as a populated value.
        $this->assertMatchesRegularExpression(
            "/'formatted'\s*=>\s*\(\\\$data\['formatted'\]\s*\?: null\)/",
            $this->fieldSource
        );
    }

    public function testNormalizeValueReadsEveryColumnBack(): void
    {
        foreach (array_merge(array_column(Defaults::SUBFIELDCONFIG, 'handle'), ['formatted', 'raw']) as $key) {
            $this->assertMatchesRegularExpression(
                "/'{$key}'\s*=>\s*\(\\\$value\['{$key}'\]\s*\?\?/",
                $this->fieldSource,
                "normalizeValue() does not read `{$key}` back out of storage."
            );
        }
    }

    public function testTheDataPassedToJavaScriptCoversEveryColumn(): void
    {
        // `_getAddressData()` is what seeds the Vue store. A key missing here
        // means the store starts with that value undefined.
        $block = $this->_extractMethodBody('_getAddressData');

        foreach (array_merge(array_column(Defaults::SUBFIELDCONFIG, 'handle'), ['formatted', 'raw']) as $key) {
            $this->assertStringContainsString(
                "'{$key}'",
                $block,
                "_getAddressData() omits `{$key}`, so the Vue store never receives it."
            );
        }
    }

    public function testTheDataPassedToJavaScriptCarriesCoordinates(): void
    {
        $block = $this->_extractMethodBody('_getAddressData');

        foreach (['lat', 'lng', 'zoom'] as $key) {
            $this->assertStringContainsString("'{$key}'", $block);
        }
    }

    private function _extractMethodBody(string $method): string
    {
        $reflection = $this->reflection->getMethod($method);
        $lines = file($this->reflection->getFileName());

        return implode('', array_slice(
            $lines,
            $reflection->getStartLine() - 1,
            $reflection->getEndLine() - $reflection->getStartLine() + 1
        ));
    }

    // ========================================================================= //
    // Proximity search wiring
    // ========================================================================= //

    public function testExposesAStaticProximitySearchHolder(): void
    {
        $this->assertTrue($this->reflection->getProperty('proximitySearch')->isStatic());
    }

    public function testQueryConditionIsStatic(): void
    {
        // Craft calls it without an instance when building an element query.
        $this->assertTrue($this->reflection->getMethod('queryCondition')->isStatic());
    }

    public function testProximitySearchEventsAreWired(): void
    {
        $this->assertTrue($this->reflection->hasMethod('_proximitySearchEvents'));
    }

    // ========================================================================= //
    // GraphQL
    // ========================================================================= //

    public function testGraphqlTypesExist(): void
    {
        foreach ([
            'gql/types/Address.php',
            'gql/types/Raw.php',
            'gql/types/input/AddressInput.php',
            'gql/arguments/AddressFields.php',
        ] as $file) {
            $this->assertTrue(
                file_exists(dirname(__DIR__, 2) . "/src/{$file}"),
                "Missing GraphQL class: {$file}"
            );
        }
    }

    public function testGraphqlTypeIsReturnedFromTheField(): void
    {
        $this->assertMatchesRegularExpression(
            '/function getContentGqlType\(\)[\s\S]{0,400}Address(Type)?::/',
            $this->fieldSource
        );
    }
}

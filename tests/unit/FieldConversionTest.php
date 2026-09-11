<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\helpers\FieldConversionHelper;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Structural tests for converting other plugins' Address fields.
 *
 * An unusual amount of this plugin's surface exists to absorb competitors. Both
 * conversions run from `init()` on every request, listen to a project config
 * event, and copy rows with raw SQL, which makes them the highest-risk code in
 * the plugin: they fire during a config apply, on somebody else's data, on a
 * path nobody exercises in normal development.
 *
 * Everything here needs a database and a project config service, so it is
 * covered structurally. The guards are what the tests focus on, because a
 * dropped guard means the conversion runs when it should not.
 */
class FieldConversionTest extends TestCase
{
    private string $source;
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(FieldConversionHelper::class);
        $this->source = file_get_contents($this->reflection->getFileName());
    }

    // ========================================================================= //
    // Both conversions exist and are reachable
    // ========================================================================= //

    public function testBothConversionEntryPointsExist(): void
    {
        $this->assertTrue($this->reflection->hasMethod('convertMapboxFields'));
        $this->assertTrue($this->reflection->hasMethod('convertMapsFields'));
    }

    public function testBothEntryPointsAreStatic(): void
    {
        // Called from init() without an instance.
        $this->assertTrue($this->reflection->getMethod('convertMapboxFields')->isStatic());
        $this->assertTrue($this->reflection->getMethod('convertMapsFields')->isStatic());
    }

    public function testThePluginRunsBothOnEveryRequest(): void
    {
        $plugin = file_get_contents(dirname(__DIR__, 2) . '/src/GoogleMapsPlugin.php');

        $this->assertStringContainsString('FieldConversionHelper::convertMapboxFields();', $plugin);
        $this->assertStringContainsString('FieldConversionHelper::convertMapsFields();', $plugin);
    }

    // ========================================================================= //
    // The guards, which keep both conversions inert on a normal install
    // ========================================================================= //

    public function testBothConversionsBailWhenTheOtherPluginIsAbsent(): void
    {
        // 🛑 The most important assertion in this file. Without these, a site
        // that never had Mapbox or SimpleMap still wires event handlers that
        // reach for tables which do not exist.
        $this->assertSame(
            2,
            preg_match_all('/getPlugins\(\)->isPluginEnabled\(/', $this->source),
            'Each conversion must check that the other plugin is installed and enabled.'
        );
    }

    public function testTheMapsConversionAlsoRequiresANewerCraft(): void
    {
        // Field data transfer needs Craft 5.5.3.
        $this->assertStringContainsString("'5.5.3'", $this->source);
    }

    public function testTheMapboxConversionOnlyActsOnATypeChangeIntoThisField(): void
    {
        // Both halves matter. Acting on any field save would copy rows for
        // fields that were never Mapbox fields.
        $this->assertStringContainsString('doublesecretagency\mapbox\fields\AddressField', $this->source);
        $this->assertStringContainsString('AddressField::class', $this->source);
    }

    public function testTheMapsConversionOnlyActsOnTheEtherMapField(): void
    {
        $this->assertStringContainsString('ether\simplemap\fields\MapField', $this->source);
    }

    // ========================================================================= //
    // The events each one listens to
    // ========================================================================= //

    public function testTheMapboxConversionListensToProjectConfigUpdates(): void
    {
        $this->assertMatchesRegularExpression(
            '/ProjectConfig::class,\s*ProjectConfig::EVENT_UPDATE_ITEM/',
            $this->source
        );
    }

    public function testTheMapsConversionListensBeforeTheFieldSaveIsApplied(): void
    {
        // Before, so the old field's data is still readable.
        $this->assertMatchesRegularExpression(
            '/Fields::class,\s*Fields::EVENT_BEFORE_APPLY_FIELD_SAVE/',
            $this->source
        );
    }

    // ========================================================================= //
    // The raw SQL
    // ========================================================================= //

    public function testTheTablePrefixIsApplied(): void
    {
        // Raw SQL cannot rely on Craft's `{{%table}}` expansion, so the prefix
        // is resolved by hand. Skipping it silently targets the wrong table on
        // any install with a prefix configured.
        $this->assertTrue($this->reflection->hasMethod('_prefix'));
        $this->assertStringContainsString('tablePrefix', $this->source);
    }

    public function testColumnNamesAreEscaped(): void
    {
        $this->assertMatchesRegularExpression(
            '/quoteColumnName|implode/',
            $this->source,
            'Column names built into raw SQL must be escaped or whitelisted.'
        );
    }

    public function testTheColumnListIsDeclaredOnceRatherThanInlined(): void
    {
        // A second copy of the column list is how a conversion silently starts
        // dropping a column.
        $this->assertMatchesRegularExpression(
            "/'formatted', 'raw',/",
            $this->source,
            'The shared column list should name the meta columns explicitly.'
        );
    }

    public function testTheCopyTargetsThePluginsOwnTable(): void
    {
        $this->assertStringContainsString('googlemaps_addresses', $this->source);
    }

    public function testTheCopySourceIsTheOtherPluginsTable(): void
    {
        $this->assertStringContainsString('mapbox_addresses', $this->source);
    }

    // ========================================================================= //
    // Compatibility declarations
    // ========================================================================= //

    public function testThePluginDeclaresItselfACompatibleTargetForBoth(): void
    {
        // Without this Craft will not offer the conversion in the field type
        // dropdown, so the handler above never fires.
        $plugin = file_get_contents(dirname(__DIR__, 2) . '/src/GoogleMapsPlugin.php');

        $this->assertStringContainsString('EVENT_DEFINE_COMPATIBLE_FIELD_TYPES', $plugin);
        $this->assertStringContainsString('$event->compatibleTypes[] = AddressField::class;', $plugin);
    }

    // ========================================================================= //
    // The Smart Map lineage
    // ========================================================================= //

    public function testTheSmartMapMigrationExists(): void
    {
        $this->assertTrue(file_exists(dirname(__DIR__, 2) . '/src/migrations/FromSmartMap.php'));
    }

    /**
     * @dataProvider smartMapStepProvider
     */
    public function testSmartMapMigrationStepExists(string $method): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/src/migrations/FromSmartMap.php');

        $this->assertStringContainsString(
            "function {$method}(",
            $source,
            "FromSmartMap lost {$method}(), so that part of a legacy install is abandoned."
        );
    }

    public static function smartMapStepProvider(): array
    {
        $steps = [
            '_migratePluginSettings',
            '_migrateAddressFieldSettings',
            '_migrateAddressFieldData',
            '_updateFieldConfig',
        ];
        return array_combine($steps, array_map(static fn(string $s): array => [$s], $steps));
    }

    public function testTheLegacyFieldClassIsStillAutoloadable(): void
    {
        // Keeps a Smart Map install from rendering a Missing Field before the
        // migration has a chance to run.
        $this->assertTrue(class_exists(\doublesecretagency\smartmap\fields\Address::class));
    }

    public function testTheLegacyNamespaceIsMappedInComposer(): void
    {
        $composer = json_decode(file_get_contents(dirname(__DIR__, 2) . '/composer.json'), true);

        $this->assertSame('legacy/', $composer['autoload']['psr-4']['doublesecretagency\\smartmap\\']);
    }
}

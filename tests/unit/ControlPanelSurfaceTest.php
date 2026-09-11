<?php
namespace doublesecretagency\googlemaps\tests\unit;

use craft\base\ElementExporter;
use craft\base\Utility;
use craft\db\ActiveRecord;
use craft\web\Controller;
use doublesecretagency\googlemaps\controllers\LookupController;
use doublesecretagency\googlemaps\exporters\AddressesCondensedExporter;
use doublesecretagency\googlemaps\exporters\AddressesExpandedExporter;
use doublesecretagency\googlemaps\records\Address as AddressRecord;
use doublesecretagency\googlemaps\utilities\TestAddressLookupUtility;
use doublesecretagency\googlemaps\utilities\TestGoogleApiKeysUtility;
use doublesecretagency\googlemaps\validators\AddressValidator;
use doublesecretagency\googlemaps\web\twig\Extension;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Twig\Extension\GlobalsInterface;
use yii\validators\Validator;

/**
 * Structural tests for the control panel and public request surface.
 *
 * Everything here is either called by Craft or exposed to the outside world, so
 * a renamed method or a changed access level is a break with no local caller to
 * catch it.
 *
 * The lookup controller gets the most attention because it is anonymously
 * accessible. It is how the Address field's front-end JavaScript geocodes, so
 * it has to stay open, and that makes any change to it a change to the plugin's
 * public attack surface.
 */
class ControlPanelSurfaceTest extends TestCase
{
    // ========================================================================= //
    // The lookup controller, which is publicly reachable
    // ========================================================================= //

    public function testLookupControllerExtendsCraftController(): void
    {
        $this->assertTrue((new ReflectionClass(LookupController::class))->isSubclassOf(Controller::class));
    }

    public function testLookupControllerAllowsAnonymousAccess(): void
    {
        // Deliberate. The Address field geocodes from the browser, so this
        // endpoint must answer an unauthenticated request.
        $property = (new ReflectionClass(LookupController::class))->getProperty('allowAnonymous');
        $property->setAccessible(true);

        $this->assertTrue($property->getDefaultValue());
    }

    /**
     * @dataProvider lookupActionProvider
     */
    public function testLookupActionExists(string $action): void
    {
        $reflection = new ReflectionClass(LookupController::class);

        $this->assertTrue($reflection->hasMethod($action), "Missing {$action}().");
        $this->assertTrue($reflection->getMethod($action)->isPublic());
    }

    public static function lookupActionProvider(): array
    {
        $actions = ['actionAll', 'actionOne', 'actionCoords'];
        return array_combine($actions, array_map(static fn(string $a): array => [$a], $actions));
    }

    public function testEveryLookupActionReturnsAResponse(): void
    {
        foreach (['actionAll', 'actionOne', 'actionCoords'] as $action) {
            $type = (string) (new ReflectionClass(LookupController::class))->getMethod($action)->getReturnType();

            $this->assertStringContainsString('Response', $type, "{$action}() does not return a Response.");
        }
    }

    public function testTheThreeActionsShareOneImplementation(): void
    {
        // Three formats, one lookup, so they cannot drift apart.
        $source = file_get_contents((new ReflectionClass(LookupController::class))->getFileName());

        $this->assertStringContainsString('_performLookup', $source);
        $this->assertSame(
            3,
            preg_match_all('/\$this->_performLookup\(/', $source),
            'Each action should delegate to the shared lookup.'
        );
    }

    public function testTheServerKeyIsUsedForGeocodingRatherThanTheBrowserKey(): void
    {
        // The browser key is domain-restricted and would fail server-side.
        $lookup = file_get_contents(dirname(__DIR__, 2) . '/src/models/Lookup.php');

        $this->assertStringContainsString('GoogleMaps::getServerKey()', $lookup);
        $this->assertStringNotContainsString('GoogleMaps::getBrowserKey()', $lookup);
    }

    public function testTheGeocodingEndpointIsTheDocumentedGoogleUrl(): void
    {
        $lookup = file_get_contents(dirname(__DIR__, 2) . '/src/models/Lookup.php');

        $this->assertStringContainsString(
            "'https://maps.googleapis.com/maps/api/geocode/json'",
            $lookup
        );
    }

    // ========================================================================= //
    // The Twig extension, which is the entire public API surface
    // ========================================================================= //

    public function testTwigExtensionProvidesGlobals(): void
    {
        $this->assertTrue((new ReflectionClass(Extension::class))->implementsInterface(GlobalsInterface::class));
    }

    public function testTwigGlobalIsNamedGoogleMaps(): void
    {
        // `craft.googleMaps.*` in every template and doc page.
        $this->assertSame(['googleMaps'], array_keys((new Extension())->getGlobals()));
    }

    public function testTwigGlobalIsTheStaticHelperFacade(): void
    {
        // There is no separate variable class. The Twig global and the PHP API
        // are the same object, which is why `GoogleMaps::map()` and
        // `craft.googleMaps.map()` behave identically.
        $this->assertInstanceOf(
            \doublesecretagency\googlemaps\helpers\GoogleMaps::class,
            (new Extension())->getGlobals()['googleMaps']
        );
    }

    /**
     * @dataProvider publicApiMethodProvider
     */
    public function testPublicApiMethodIsStaticAndPublic(string $method): void
    {
        $reflection = new \ReflectionMethod(\doublesecretagency\googlemaps\helpers\GoogleMaps::class, $method);

        $this->assertTrue($reflection->isStatic(), "{$method}() must be static.");
        $this->assertTrue($reflection->isPublic(), "{$method}() must be public.");
    }

    public static function publicApiMethodProvider(): array
    {
        $methods = [
            'map', 'getMap', 'img', 'lookup', 'getVisitor',
            'getAssets', 'loadAssets', 'getApiUrl',
            'getBrowserKey', 'getServerKey', 'setBrowserKey', 'setServerKey',
        ];
        return array_combine($methods, array_map(static fn(string $m): array => [$m], $methods));
    }

    // ========================================================================= //
    // Utilities
    // ========================================================================= //

    /**
     * @dataProvider utilityProvider
     */
    public function testUtilityExtendsCraftUtility(string $class): void
    {
        $this->assertTrue((new ReflectionClass($class))->isSubclassOf(Utility::class));
    }

    /**
     * @dataProvider utilityProvider
     */
    public function testUtilityImplementsTheRequiredStatics(string $class): void
    {
        $reflection = new ReflectionClass($class);

        foreach (['displayName', 'id', 'icon', 'contentHtml'] as $method) {
            $this->assertTrue($reflection->hasMethod($method), "{$class} is missing {$method}().");
            $this->assertTrue($reflection->getMethod($method)->isStatic());
        }
    }

    /**
     * @dataProvider utilityProvider
     */
    public function testUtilityUsesIconRatherThanTheRenamedIconPath(string $class): void
    {
        // Craft 5 renamed `iconPath()` to `icon()` and dropped it from the
        // interface, so a stale name renders a gray letter badge with nothing
        // in the logs.
        $reflection = new ReflectionClass($class);

        $this->assertTrue($reflection->hasMethod('icon'));
        $this->assertFalse($reflection->hasMethod('iconPath'), "{$class} still declares the removed iconPath().");
    }

    public static function utilityProvider(): array
    {
        return [
            'api keys'       => [TestGoogleApiKeysUtility::class],
            'address lookup' => [TestAddressLookupUtility::class],
        ];
    }

    public function testTheLookupUtilityClearsItsCachedResultBeforeTesting(): void
    {
        // Otherwise it reports a 30 day old answer and reads as a live test.
        $source = file_get_contents((new ReflectionClass(TestAddressLookupUtility::class))->getFileName());

        $this->assertStringContainsString("Craft::\$app->getCache()->delete(['address' => \$target]);", $source);
    }

    // ========================================================================= //
    // Exporters
    // ========================================================================= //

    /**
     * @dataProvider exporterProvider
     */
    public function testExporterExtendsElementExporter(string $class): void
    {
        $this->assertTrue((new ReflectionClass($class))->isSubclassOf(ElementExporter::class));
    }

    /**
     * @dataProvider exporterProvider
     */
    public function testExporterHasADisplayName(string $class, string $expected): void
    {
        $this->assertSame($expected, $class::displayName());
    }

    public static function exporterProvider(): array
    {
        return [
            'condensed' => [AddressesCondensedExporter::class, 'Addresses (condensed)'],
            'expanded'  => [AddressesExpandedExporter::class, 'Addresses (expanded)'],
        ];
    }

    public function testBothExportersShareOneHelper(): void
    {
        foreach ([AddressesCondensedExporter::class, AddressesExpandedExporter::class] as $class) {
            $source = file_get_contents((new ReflectionClass($class))->getFileName());

            $this->assertStringContainsString('ExporterHelper::', $source);
        }
    }

    // ========================================================================= //
    // The record
    // ========================================================================= //

    public function testRecordExtendsActiveRecord(): void
    {
        $this->assertTrue((new ReflectionClass(AddressRecord::class))->isSubclassOf(ActiveRecord::class));
    }

    public function testRecordPointsAtThePluginsTable(): void
    {
        $this->assertSame('{{%googlemaps_addresses}}', AddressRecord::tableName());
    }

    public function testRecordTableNameMatchesTheInstallMigration(): void
    {
        $this->assertSame(
            \doublesecretagency\googlemaps\migrations\Install::GM_ADDRESSES,
            AddressRecord::tableName(),
            'The record and the migration must name the same table.'
        );
    }

    /**
     * @dataProvider recordPropertyProvider
     */
    public function testRecordDocumentsColumn(string $column): void
    {
        // ActiveRecord resolves columns at runtime, so the docblock is the only
        // in-code record of the schema this class expects.
        $source = file_get_contents((new ReflectionClass(AddressRecord::class))->getFileName());

        $this->assertMatchesRegularExpression(
            "/@property [a-z|\\\\A-Z]+ \\\${$column}\b/",
            $source,
            "The record no longer documents `{$column}`."
        );
    }

    public static function recordPropertyProvider(): array
    {
        $columns = [
            'id', 'elementId', 'siteId', 'fieldId', 'formatted', 'raw', 'name',
            'street1', 'street2', 'city', 'state', 'zip', 'neighborhood',
            'county', 'country', 'countryCode', 'placeId', 'distance', 'lat', 'lng', 'zoom',
        ];
        return array_combine($columns, array_map(static fn(string $c): array => [$c], $columns));
    }

    // ========================================================================= //
    // The validator
    // ========================================================================= //

    public function testValidatorExtendsYiiValidator(): void
    {
        $this->assertTrue((new ReflectionClass(AddressValidator::class))->isSubclassOf(Validator::class));
    }

    public function testValidatorImplementsValidateAttribute(): void
    {
        $this->assertTrue((new ReflectionClass(AddressValidator::class))->hasMethod('validateAttribute'));
    }

    public function testTheFieldRegistersItsValidator(): void
    {
        $field = file_get_contents(dirname(__DIR__, 2) . '/src/fields/AddressField.php');

        $this->assertStringContainsString('AddressValidator', $field);
    }
}

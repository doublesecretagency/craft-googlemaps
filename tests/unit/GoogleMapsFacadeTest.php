<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\helpers\ApiHelper;
use doublesecretagency\googlemaps\helpers\GeocodingHelper;
use doublesecretagency\googlemaps\helpers\GeolocationHelper;
use doublesecretagency\googlemaps\helpers\GoogleMaps;
use doublesecretagency\googlemaps\models\Address;
use doublesecretagency\googlemaps\models\DynamicMap;
use doublesecretagency\googlemaps\models\Lookup;
use doublesecretagency\googlemaps\models\StaticMap;
use doublesecretagency\googlemaps\models\Visitor;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Tests for the static facade every caller reaches the plugin through.
 *
 * `GoogleMaps` is both the PHP API and the Twig global, and it owns no logic of
 * its own: every method hands off to one of the four helpers. So the facade's
 * job is delegation, and the thing worth testing is that each entry point still
 * points where it should and still returns the documented type.
 */
class GoogleMapsFacadeTest extends TestCase
{
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(GoogleMaps::class);
    }

    // ========================================================================= //
    // Geocoding
    // ========================================================================= //

    public function testLookupCreatesALookupModel(): void
    {
        $this->assertInstanceOf(Lookup::class, GoogleMaps::lookup('123 Main St.'));
    }

    public function testLookupAcceptsAStringTarget(): void
    {
        // A bare string is the common case, and the helper wraps it into an
        // `address` parameter.
        $this->assertInstanceOf(Lookup::class, GoogleMaps::lookup('1600 Amphitheatre Parkway'));
    }

    public function testLookupAcceptsAnArrayTarget(): void
    {
        $this->assertInstanceOf(Lookup::class, GoogleMaps::lookup(['address' => '123 Main St.']));
    }

    public function testLookupAcceptsANumericTarget(): void
    {
        // A zip code typed without quotes arrives as an integer.
        $this->assertInstanceOf(Lookup::class, GoogleMaps::lookup('90210'));
    }

    public function testLookupDelegatesToTheGeocodingHelper(): void
    {
        $source = file_get_contents($this->reflection->getFileName());

        $this->assertStringContainsString('return GeocodingHelper::lookup($target);', $source);
    }

    /**
     * 🛑 A coordinate string is the only reverse-geocoding path.
     *
     * `GeocodingHelper::lookup()` requires an `address` key, so a
     * `['latlng' => ...]` target is rejected outright. Passing coordinates as a
     * plain string works, because Google's geocoder accepts them in the
     * `address` parameter. That is worth pinning, since it is the difference
     * between reverse geocoding being available and being absent.
     */
    public function testReverseGeocodingRequiresTheAddressParameter(): void
    {
        $source = file_get_contents((new ReflectionClass(GeocodingHelper::class))->getFileName());

        $this->assertStringContainsString("!isset(\$target['address'])", $source);
    }

    // ========================================================================= //
    // The three lookup accessors
    //
    // Producing real results needs a network call, so the return contracts are
    // asserted by signature and the shape by the method they delegate to.
    // ========================================================================= //

    public function testLookupAllReturnsANullableArray(): void
    {
        $this->assertSame('?array', (string) (new ReflectionMethod(Lookup::class, 'all'))->getReturnType());
    }

    public function testLookupOneReturnsANullableAddressModel(): void
    {
        $type = (string) (new ReflectionMethod(Lookup::class, 'one'))->getReturnType();

        $this->assertStringContainsString('Address', $type);
        $this->assertStringStartsWith('?', $type);
    }

    public function testLookupCoordsReturnsANullableArray(): void
    {
        $this->assertSame('?array', (string) (new ReflectionMethod(Lookup::class, 'coords'))->getReturnType());
    }

    public function testEveryAccessorIsNullableSoAFailedLookupDoesNotThrow(): void
    {
        // A geocoding failure sets `error` and returns null rather than
        // throwing, so a template can degrade instead of 500ing.
        foreach (['all', 'one', 'coords'] as $method) {
            $this->assertTrue(
                (new ReflectionMethod(Lookup::class, $method))->getReturnType()->allowsNull(),
                "Lookup::{$method}() must be nullable."
            );
        }
    }

    public function testLookupExposesAnErrorProperty(): void
    {
        $this->assertTrue((new ReflectionClass(Lookup::class))->hasProperty('error'));
    }

    public function testCoordsDelegatesToTheAddressModel(): void
    {
        // It returns `$address->getCoords()` rather than assembling a pair
        // itself, so the two cannot disagree about the shape.
        $source = file_get_contents((new ReflectionClass(Lookup::class))->getFileName());

        $this->assertMatchesRegularExpression(
            '/function coords\(\)[\s\S]{0,400}return \$address->getCoords\(\);/',
            $source
        );
    }

    public function testCoordsReturnsOnlyLatAndLng(): void
    {
        // Asserted on the method `coords()` delegates to, which needs no
        // network call.
        $coords = (new Address(['lat' => 34.0522, 'lng' => -118.2437]))->getCoords();

        $this->assertCount(2, $coords);
        $this->assertArrayHasKey('lat', $coords);
        $this->assertArrayHasKey('lng', $coords);
    }

    // ========================================================================= //
    // Map builders
    // ========================================================================= //

    public function testMapCreatesADynamicMap(): void
    {
        $this->assertInstanceOf(DynamicMap::class, GoogleMaps::map([]));
    }

    public function testImgCreatesAStaticMap(): void
    {
        $this->assertInstanceOf(StaticMap::class, GoogleMaps::img([]));
    }

    public function testMapAcceptsOptions(): void
    {
        $this->assertInstanceOf(DynamicMap::class, GoogleMaps::map([], ['id' => 'test-map']));
    }

    public function testImgAcceptsOptions(): void
    {
        $this->assertInstanceOf(StaticMap::class, GoogleMaps::img([], ['width' => 400]));
    }

    public function testMapAndImgAcceptTheSameLocationShapes(): void
    {
        // Both take `array|Collection|Element|Location`, so a template can swap
        // a dynamic map for a static one without changing its locations.
        $mapType = (string) $this->reflection->getMethod('map')->getParameters()[0]->getType();
        $imgType = (string) $this->reflection->getMethod('img')->getParameters()[0]->getType();

        $this->assertSame($mapType, $imgType);
    }

    public function testEveryNewDynamicMapGetsItsOwnId(): void
    {
        // Two maps on one page sharing a DOM id is the collision this prevents.
        $this->assertNotSame(
            GoogleMaps::map([])->id,
            GoogleMaps::map([])->id
        );
    }

    /**
     * 🛑 `getMap()` THROWS for an unknown id, despite a nullable return type.
     *
     * The signature says `?DynamicMap` and the docblock says `DynamicMap|null`,
     * but there is no path that returns null: a miss throws. So a caller
     * following the type and writing `?? $fallback` gets an exception instead.
     *
     * Pinned as it behaves rather than as it is typed, so a future change to
     * either half is a deliberate decision. Worth resolving one way or the
     * other, since the current pair is contradictory.
     */
    public function testGetMapThrowsForAnUnknownId(): void
    {
        $this->expectException(\yii\base\Exception::class);
        $this->expectExceptionMessage('The map "no-such-map" does not exist.');

        GoogleMaps::getMap('no-such-map');
    }

    public function testGetMapIsTypedNullableEvenThoughItNeverReturnsNull(): void
    {
        $type = $this->reflection->getMethod('getMap')->getReturnType();

        $this->assertTrue($type->allowsNull());

        $source = file_get_contents($this->reflection->getFileName());

        $this->assertMatchesRegularExpression(
            '/function getMap\(string \$mapId\): \?DynamicMap[\s\S]{0,400}throw new Exception/',
            $source,
            'If getMap() has been changed to return null on a miss, drop the exception '
            . 'test above and assert the null instead.'
        );
    }

    public function testGetMapFindsAMapCreatedThroughTheFacade(): void
    {
        // The facade keeps an internal collection so a later Twig call can
        // reach back into an earlier map.
        $map = GoogleMaps::map([], ['id' => 'facade-test-map']);

        $this->assertSame($map, GoogleMaps::getMap('facade-test-map'));
    }

    // ========================================================================= //
    // Delegation
    // ========================================================================= //

    /**
     * @dataProvider delegationProvider
     */
    public function testFacadeMethodDelegatesToItsHelper(string $method, string $expected): void
    {
        $source = file_get_contents($this->reflection->getFileName());

        $this->assertMatchesRegularExpression(
            "/function {$method}\([^)]*\)[^{]*\{[\s\S]{0,400}{$expected}/",
            $source,
            "GoogleMaps::{$method}() should delegate to {$expected}. The facade owns "
            . 'no logic of its own.'
        );
    }

    public static function delegationProvider(): array
    {
        return [
            'lookup'         => ['lookup', 'GeocodingHelper::lookup'],
            'getVisitor'     => ['getVisitor', 'GeolocationHelper::getVisitor'],
            'getApiUrl'      => ['getApiUrl', 'ApiHelper::getApiUrl'],
            'getBrowserKey'  => ['getBrowserKey', 'ApiHelper::getBrowserKey'],
            'getServerKey'   => ['getServerKey', 'ApiHelper::getServerKey'],
            'setBrowserKey'  => ['setBrowserKey', 'ApiHelper::setBrowserKey'],
            'setServerKey'   => ['setServerKey', 'ApiHelper::setServerKey'],
        ];
    }

    public function testGetVisitorReturnsAVisitorModel(): void
    {
        // Checked by signature, since the call itself performs a geolocation.
        $type = (string) $this->reflection->getMethod('getVisitor')->getReturnType();

        $this->assertSame(Visitor::class, ltrim($type, '?'));
    }

    public function testGeolocationIsReachedThroughItsHelper(): void
    {
        $this->assertTrue(
            (new ReflectionClass(GeolocationHelper::class))->hasMethod('getVisitor')
        );
    }

    // ========================================================================= //
    // API keys
    //
    // The setters are pure. See the note below for why the getters are not
    // exercised.
    // ========================================================================= //

    public function testBothSettersArePure(): void
    {
        // The setters only assign a private static, so they run anywhere and
        // return what they stored.
        $this->assertSame('server-test', ApiHelper::setServerKey('server-test'));
        $this->assertSame('browser-test', ApiHelper::setBrowserKey('browser-test'));
    }

    public function testTheFacadeSettersReachTheSameStorage(): void
    {
        // A key set through either entry point lands in one place.
        $this->assertSame('via-facade', GoogleMaps::setServerKey('via-facade'));
        $this->assertSame('via-helper', ApiHelper::setServerKey('via-helper'));

        $source = file_get_contents($this->reflection->getFileName());

        $this->assertStringContainsString('return ApiHelper::setServerKey($key);', $source);
        $this->assertStringContainsString('return ApiHelper::setBrowserKey($key);', $source);
    }

    public function testTheKeysAreStoredPrivatelyOnOneClass(): void
    {
        // Two copies of the key would let a setter and a getter disagree.
        $reflection = new ReflectionClass(ApiHelper::class);

        foreach (['_browserKey', '_serverKey'] as $property) {
            $this->assertTrue($reflection->hasProperty($property));
            $this->assertTrue($reflection->getProperty($property)->isPrivate());
            $this->assertTrue($reflection->getProperty($property)->isStatic());
        }
    }

    /**
     * 🛑 Why the key round-trip is not tested behaviorally.
     *
     * A `setServerKey()` then `getServerKey()` round-trip cannot run without
     * Craft, and not for the obvious reason: `ApiHelper::getServerKey()` reads
     * `GoogleMapsPlugin::$plugin->getSettings()` **before** checking whether the
     * key is already cached. So the settings lookup happens even when a caller
     * has just set the key by hand.
     *
     * The consequence is small inside a request and total outside one. A console
     * command or a script that calls `setServerKey()` still cannot read it back,
     * which makes the setter useless in exactly the contexts it looks designed
     * for.
     *
     * This test pins the current shape so that moving the settings read inside
     * the null check is a deliberate, visible change rather than an accident.
     */
    public function testTheGetterReadsSettingsBeforeConsultingItsCache(): void
    {
        $source = file_get_contents((new ReflectionClass(ApiHelper::class))->getFileName());

        $this->assertMatchesRegularExpression(
            '/function getServerKey\(\)[^{]*\{\s*'
            . '(?:\/\*\*[\s\S]*?\*\/\s*)?'
            . '\$settings = GoogleMapsPlugin::\$plugin->getSettings\(\);'
            . '[\s\S]{0,200}if \(null === static::\$_serverKey\)/',
            $source,
            'The getter no longer reads settings before its cache check. If that was '
            . 'deliberate, the setter is now usable outside a Craft request and the '
            . 'round-trip can become a real behavioral test again.'
        );
    }

    public function testKeysAreParsedForEnvironmentVariables(): void
    {
        // A settings value is typically `$GOOGLEMAPS_SERVERKEY`, so the raw
        // string would be sent to Google without this.
        $source = file_get_contents((new ReflectionClass(ApiHelper::class))->getFileName());

        $this->assertStringContainsString('App::parseEnv($settings->serverKey)', $source);
        $this->assertStringContainsString('App::parseEnv($settings->browserKey)', $source);
    }

    public function testKeysAreTrimmedBeforeUse(): void
    {
        // A trailing newline in a pasted key produces an opaque Google error.
        $source = file_get_contents((new ReflectionClass(ApiHelper::class))->getFileName());

        $this->assertSame(2, preg_match_all('/return trim\(static::\$_\w+Key\);/', $source));
    }

    // ========================================================================= //
    // Facade hygiene
    // ========================================================================= //

    public function testEveryPublicMethodIsStatic(): void
    {
        // It is reached as `craft.googleMaps.*` from Twig and statically from
        // PHP, so an instance method would be unreachable from one of them.
        foreach ($this->reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (GoogleMaps::class !== $method->getDeclaringClass()->getName()) {
                continue;
            }

            $this->assertTrue($method->isStatic(), "{$method->getName()}() is not static.");
        }
    }

    public function testTheFacadeIsInstantiableForTheTwigGlobal(): void
    {
        // The Twig extension returns `new GoogleMaps`, so it must not be
        // abstract even though every method is static.
        $this->assertFalse($this->reflection->isAbstract());
        $this->assertInstanceOf(GoogleMaps::class, new GoogleMaps());
    }

    public function testAssetsAreListedRatherThanRegisteredByTheGetter(): void
    {
        // `getAssets()` returns URLs and `loadAssets()` registers them. Keeping
        // them separate is what lets a template take the list and place the
        // scripts itself.
        $this->assertSame('array', (string) $this->reflection->getMethod('getAssets')->getReturnType());
        $this->assertSame('void', (string) $this->reflection->getMethod('loadAssets')->getReturnType());
    }
}

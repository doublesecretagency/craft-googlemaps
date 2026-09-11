<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\models\Address;
use doublesecretagency\googlemaps\models\Location;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Tests for the Address model.
 *
 * Address extends Location and adds the postal parts plus the display helpers.
 * Most of it runs without Craft, but two methods reach for `Template::raw()`
 * and therefore need a container, so those are covered structurally instead.
 * Where that boundary sits is itself worth pinning, since it decides what a
 * console script can safely call.
 */
class AddressModelTest extends TestCase
{
    /** The subfields a typical field enables. */
    private const ENABLED = ['street1', 'street2', 'city', 'state', 'zip', 'country'];

    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(Address::class);
    }

    // ========================================================================= //
    // Class shape
    // ========================================================================= //

    public function testExtendsLocation(): void
    {
        $this->assertTrue($this->reflection->isSubclassOf(Location::class));
    }

    public function testInheritsCoordinateBehaviorFromLocation(): void
    {
        $address = new Address(['lat' => 34.0522, 'lng' => -118.2437]);

        $this->assertTrue($address->hasCoords());
        $this->assertSame(['lat' => 34.0522, 'lng' => -118.2437], $address->getCoords());
    }

    /**
     * @dataProvider persistedAttributeProvider
     */
    public function testHasPersistedAttribute(string $attribute): void
    {
        $this->assertTrue(
            $this->reflection->hasProperty($attribute),
            "Address is missing `{$attribute}`, which the database column expects."
        );
    }

    public static function persistedAttributeProvider(): array
    {
        $attributes = [
            'id', 'elementId', 'siteId', 'fieldId',
            'formatted', 'raw', 'name',
            'street1', 'street2', 'city', 'state', 'zip',
            'neighborhood', 'county', 'country', 'countryCode',
            'placeId', 'distance', 'lat', 'lng', 'zoom',
        ];
        return array_map(static fn(string $a): array => [$a], $attributes);
    }

    public function testRawIsDeclaredAsAnArray(): void
    {
        // 🛑 Load-bearing. Because `raw` is an array on the PHP side, it
        // reaches JavaScript as an object, so any DOM binding for it needs a
        // serializer. A naive string coercion writes `[object Object]`, which
        // normalizeRaw() then discards, silently wiping the stored response.
        $property = $this->reflection->getProperty('raw');
        $type = $property->getType();

        $this->assertNotNull($type);
        $this->assertSame('array', (string) $type->getName());
        $this->assertTrue($type->allowsNull());
    }

    public function testFormattedIsANullableString(): void
    {
        $type = $this->reflection->getProperty('formatted')->getType();

        $this->assertSame('string', (string) $type->getName());
        $this->assertTrue($type->allowsNull());
    }

    // ========================================================================= //
    // Construction
    // ========================================================================= //

    public function testNewAddressHasNullAttributes(): void
    {
        $address = new Address();

        $this->assertNull($address->formatted);
        $this->assertNull($address->raw);
        $this->assertNull($address->street1);
        $this->assertNull($address->city);
        $this->assertNull($address->placeId);
    }

    public function testAttributesAreAssignedFromConfig(): void
    {
        $address = new Address([
            'street1' => '1 Test St',
            'city' => 'Testville',
            'state' => 'CA',
            'zip' => '90210',
            'countryCode' => 'US',
        ]);

        $this->assertSame('1 Test St', $address->street1);
        $this->assertSame('Testville', $address->city);
        $this->assertSame('CA', $address->state);
        $this->assertSame('90210', $address->zip);
        $this->assertSame('US', $address->countryCode);
    }

    public function testEnabledSubfieldsDefaultsToEmpty(): void
    {
        $this->assertSame([], (new Address())->enabledSubfields);
    }

    // ========================================================================= //
    // Stringification
    // ========================================================================= //

    public function testStringifiesToTheGoogleFormattedAddress(): void
    {
        $address = new Address(['formatted' => '1 Test St, Testville, CA 90210, USA']);

        $this->assertSame('1 Test St, Testville, CA 90210, USA', (string) $address);
    }

    public function testFormattedWinsOverTheSubfields(): void
    {
        $address = new Address([
            'formatted' => 'Google says this',
            'street1' => 'Subfields say that',
        ]);

        $this->assertSame('Google says this', (string) $address);
    }

    public function testWhitespaceOnlyFormattedIsNotUsed(): void
    {
        // A blank `formatted` must fall through to the multiline builder
        // rather than rendering an empty address.
        $source = file_get_contents($this->reflection->getFileName());

        $this->assertStringContainsString('$googleFormatted = trim((string) $this->formatted);', $source);
    }

    // ========================================================================= //
    // isEmpty, and the trap inside it
    // ========================================================================= //

    public function testIsEmptyIsTrueForABlankAddress(): void
    {
        $address = new Address(['enabledSubfields' => self::ENABLED]);

        $this->assertTrue($address->isEmpty());
    }

    public function testIsEmptyIsFalseWhenAnyEnabledSubfieldIsPopulated(): void
    {
        $address = new Address(['city' => 'Testville', 'enabledSubfields' => self::ENABLED]);

        $this->assertFalse($address->isEmpty());
    }

    /**
     * 🛑 `isEmpty()` measures the ENABLED subfields, not the address.
     *
     * `enabledSubfields` is populated by AddressField when it normalizes a
     * value, so a bare `new Address(...)` in a console script or a migration
     * has an empty list and reports itself empty no matter what it holds.
     * That is a correct reading of "empty as far as this field is concerned",
     * and a trap for anyone constructing the model directly.
     */
    public function testIsEmptyIsTrueForAPopulatedAddressWithNoEnabledSubfields(): void
    {
        $address = new Address(['street1' => '1 Test St', 'city' => 'Testville']);

        $this->assertTrue($address->isEmpty());
    }

    public function testIsEmptyIgnoresSubfieldsThatAreNotEnabled(): void
    {
        // `name` holds a value, but the field is not showing it.
        $address = new Address(['name' => 'Test Place', 'enabledSubfields' => self::ENABLED]);

        $this->assertTrue($address->isEmpty());
    }

    public function testIsEmptyIgnoresCoordinates(): void
    {
        // Coordinates are not subfields, so an address with only a pin is
        // still "empty" by this measure.
        $address = new Address([
            'lat' => 34.0522,
            'lng' => -118.2437,
            'enabledSubfields' => self::ENABLED,
        ]);

        $this->assertTrue($address->isEmpty());
    }

    // ========================================================================= //
    // Distance
    // ========================================================================= //

    public function testDistanceWorksBetweenTwoAddresses(): void
    {
        $la = new Address(['lat' => 34.0522, 'lng' => -118.2437]);
        $ny = new Address(['lat' => 40.7128, 'lng' => -74.0060]);

        $this->assertEqualsWithDelta(2445.7101588447485, $la->getDistance($ny), 0.0001);
    }

    public function testDistanceAcceptsAPlainLocation(): void
    {
        $address = new Address(['lat' => 34.0522, 'lng' => -118.2437]);

        $this->assertNotNull($address->getDistance(new Location(['lat' => 40.7128, 'lng' => -74.0060])));
    }

    // ========================================================================= //
    // The Craft boundary
    // ========================================================================= //

    /**
     * @dataProvider craftBoundMethodProvider
     */
    public function testMethodExists(string $method): void
    {
        $this->assertTrue($this->reflection->hasMethod($method));
    }

    public static function craftBoundMethodProvider(): array
    {
        return [
            'multiline'         => ['multiline'],
            'linkToMap'         => ['linkToMap'],
            'linkToDirections'  => ['linkToDirections'],
            'getElement'        => ['getElement'],
            'getField'          => ['getField'],
        ];
    }

    /**
     * Documents which methods a console script cannot call.
     *
     * `multiline()` returns a `Markup`, which means `Template::raw()`, which
     * dereferences `Craft`. Since `__toString()` falls through to it whenever
     * `formatted` is blank, casting an unformatted Address to string outside a
     * Craft request is a fatal error rather than a degraded result.
     */
    public function testMultilineReturnsMarkupAndThereforeNeedsCraft(): void
    {
        $method = $this->reflection->getMethod('multiline');

        $this->assertSame('Twig\Markup', (string) $method->getReturnType());
    }

    public function testToStringFallsThroughToMultiline(): void
    {
        $source = file_get_contents($this->reflection->getFileName());

        $this->assertMatchesRegularExpression(
            '/public function __toString\(\)[\s\S]{0,600}\$this->multiline\(/',
            $source
        );
    }
}

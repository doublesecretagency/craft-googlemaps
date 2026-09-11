<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\fields\AddressField;
use PHPUnit\Framework\TestCase;

/**
 * Pure-unit tests for the two static normalizers on AddressField.
 *
 * Both run without a Craft container, and both sit directly on the save path,
 * so a change to either silently alters what gets written to the database.
 *
 * `normalizeRaw()` is the more interesting of the two. It carries an explicit
 * guard against the literal string `[object Object]`, which is what a
 * JavaScript object becomes when something coerces it to a string instead of
 * serializing it. That guard shipped quietly in 5.1.1, thirteen months before
 * the progressive-enhancement refactor, so it records an incident rather than
 * anticipating one. Its behavior is pinned here because the store-side
 * serializer that makes it unnecessary is only one careless edit away from
 * being removed.
 */
class AddressFieldNormalizationTest extends TestCase
{
    // ========================================================================= //
    // normalizeRaw
    // ========================================================================= //

    public function testDecodesAJsonString(): void
    {
        $result = AddressField::normalizeRaw('{"formatted_address":"1 Test St","place_id":"abc"}');

        $this->assertSame(['formatted_address' => '1 Test St', 'place_id' => 'abc'], $result);
    }

    public function testPassesAnArrayThroughUnchanged(): void
    {
        $input = ['formatted_address' => '1 Test St'];

        $this->assertSame($input, AddressField::normalizeRaw($input));
    }

    public function testDecodesNestedJson(): void
    {
        $json = '{"geometry":{"location":{"lat":34.05,"lng":-118.25}}}';

        $result = AddressField::normalizeRaw($json);

        $this->assertSame(34.05, $result['geometry']['location']['lat']);
    }

    /**
     * 🛑 The guard that records a past incident.
     *
     * Nothing in the codebase writes this string. It can only arrive from a
     * JavaScript object reaching a form input through a string coercion, which
     * means a binding without a serializer.
     */
    public function testRejectsTheObjectObjectCoercion(): void
    {
        $this->assertNull(AddressField::normalizeRaw('[object Object]'));
    }

    public function testRejectsObjectObjectAnywhereInTheString(): void
    {
        // Defensive against a partially-serialized payload rather than only
        // an exactly-equal one.
        $this->assertNull(AddressField::normalizeRaw('{"nested":"[object Object]"}'));
    }

    /**
     * @dataProvider nonStringProvider
     */
    public function testReturnsNullForANonStringNonArray(mixed $input): void
    {
        $this->assertNull(AddressField::normalizeRaw($input));
    }

    public static function nonStringProvider(): array
    {
        return [
            'null'    => [null],
            'integer' => [42],
            'float'   => [1.5],
            'boolean' => [true],
        ];
    }

    public function testEmptyArrayStaysAnEmptyArray(): void
    {
        // Distinct from null: the field saved, and Google returned nothing.
        $this->assertSame([], AddressField::normalizeRaw([]));
    }

    public function testDecodesTheJsonNullLiteral(): void
    {
        // meta.twig renders `value="{{ ...|json_encode }}"`, so an unset raw
        // arrives as the four-character string `null`.
        $this->assertNull(AddressField::normalizeRaw('null'));
    }

    // ========================================================================= //
    // typecastSubfieldConfig
    // ========================================================================= //

    public function testCastsStringWidthToInteger(): void
    {
        $config = [['handle' => 'city', 'label' => 'City', 'width' => '50', 'enabled' => '1', 'autocomplete' => '0', 'required' => '0']];

        AddressField::typecastSubfieldConfig($config);

        $this->assertSame(50, $config[0]['width']);
    }

    public function testCastsStringFlagsToBooleans(): void
    {
        $config = [['handle' => 'city', 'label' => 'City', 'width' => '50', 'enabled' => '1', 'autocomplete' => '0', 'required' => '1']];

        AddressField::typecastSubfieldConfig($config);

        $this->assertTrue($config[0]['enabled']);
        $this->assertFalse($config[0]['autocomplete']);
        $this->assertTrue($config[0]['required']);
    }

    public function testLeavesAlreadyTypedValuesAlone(): void
    {
        $config = [['handle' => 'zip', 'label' => 'Zip', 'width' => 35, 'enabled' => true, 'autocomplete' => false, 'required' => false]];

        AddressField::typecastSubfieldConfig($config);

        $this->assertSame(35, $config[0]['width']);
        $this->assertTrue($config[0]['enabled']);
        $this->assertFalse($config[0]['autocomplete']);
    }

    public function testCastsEmptyAndNullToFalseOrZero(): void
    {
        $config = [['handle' => 'x', 'label' => 'X', 'width' => '', 'enabled' => '', 'autocomplete' => null, 'required' => '0']];

        AddressField::typecastSubfieldConfig($config);

        $this->assertSame(0, $config[0]['width']);
        $this->assertFalse($config[0]['enabled']);
        $this->assertFalse($config[0]['autocomplete']);
    }

    public function testHandleAndLabelStayStrings(): void
    {
        $config = [['handle' => 'city', 'label' => 'City', 'width' => '50', 'enabled' => '1', 'autocomplete' => '0', 'required' => '0']];

        AddressField::typecastSubfieldConfig($config);

        $this->assertSame('city', $config[0]['handle']);
        $this->assertSame('City', $config[0]['label']);
    }

    public function testTypecastsEverySubfieldInTheConfig(): void
    {
        $config = [
            ['handle' => 'city',  'label' => 'City',  'width' => '50', 'enabled' => '1', 'autocomplete' => '0', 'required' => '0'],
            ['handle' => 'state', 'label' => 'State', 'width' => '15', 'enabled' => '1', 'autocomplete' => '0', 'required' => '0'],
            ['handle' => 'zip',   'label' => 'Zip',   'width' => '35', 'enabled' => '0', 'autocomplete' => '0', 'required' => '0'],
        ];

        AddressField::typecastSubfieldConfig($config);

        foreach ($config as $subfield) {
            $this->assertIsInt($subfield['width']);
            $this->assertIsBool($subfield['enabled']);
            $this->assertIsBool($subfield['autocomplete']);
            $this->assertIsBool($subfield['required']);
        }
    }

    public function testAcceptsAnEmptyConfig(): void
    {
        $config = [];

        AddressField::typecastSubfieldConfig($config);

        $this->assertSame([], $config);
    }

    public function testTakesItsArgumentByReference(): void
    {
        // The plugin calls this for its side effect, from a before-save event
        // handler, so pass-by-reference is the contract.
        $reflection = new \ReflectionMethod(AddressField::class, 'typecastSubfieldConfig');

        $this->assertTrue($reflection->getParameters()[0]->isPassedByReference());
    }

    public function testIsStaticSoTheEventHandlerCanCallIt(): void
    {
        $reflection = new \ReflectionMethod(AddressField::class, 'typecastSubfieldConfig');

        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
    }
}

<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\helpers\MapHelper;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Tests for the map helper.
 *
 * Two of its three methods are pure. `generateId()` names map and marker DOM
 * elements, and `stringCoords()` renders a coordinate pair into the comma form
 * the Google API expects. `extractCoords()` walks Craft elements, so it is
 * covered structurally rather than called.
 */
class MapHelperTest extends TestCase
{
    // ========================================================================= //
    // generateId
    // ========================================================================= //

    public function testGeneratesASixCharacterId(): void
    {
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{6}$/', MapHelper::generateId());
    }

    public function testPrefixIsJoinedWithAHyphen(): void
    {
        $this->assertMatchesRegularExpression('/^marker-[a-zA-Z0-9]{6}$/', MapHelper::generateId('marker'));
    }

    public function testNoPrefixMeansNoLeadingHyphen(): void
    {
        $this->assertStringNotContainsString('-', MapHelper::generateId());
    }

    public function testConsecutiveIdsDiffer(): void
    {
        // These become DOM ids, so a collision puts two maps on one element.
        $ids = [];
        for ($i = 0; $i < 50; $i++) {
            $ids[] = MapHelper::generateId();
        }

        $this->assertCount(50, array_unique($ids));
    }

    public function testGeneratedIdIsAValidHtmlIdWhenPrefixed(): void
    {
        // An id may not begin with a digit, which a bare random string can.
        $this->assertMatchesRegularExpression('/^[A-Za-z][-A-Za-z0-9]*$/', MapHelper::generateId('map'));
    }

    // ========================================================================= //
    // stringCoords
    // ========================================================================= //

    public function testJoinsLatAndLngWithAComma(): void
    {
        $this->assertSame('34.0522,-118.2437', MapHelper::stringCoords(['lat' => 34.0522, 'lng' => -118.2437]));
    }

    public function testUsesNoSpaceAfterTheComma(): void
    {
        // The Google API query parameter does not tolerate one.
        $this->assertStringNotContainsString(' ', MapHelper::stringCoords(['lat' => 1, 'lng' => 2]));
    }

    /**
     * @dataProvider incompleteCoordsProvider
     */
    public function testReturnsEmptyStringForIncompleteCoordinates(array $coords): void
    {
        $this->assertSame('', MapHelper::stringCoords($coords));
    }

    public static function incompleteCoordsProvider(): array
    {
        return [
            'empty array' => [[]],
            'lat only'    => [['lat' => 34.0522]],
            'lng only'    => [['lng' => -118.2437]],
            'wrong keys'  => [['latitude' => 34.0522, 'longitude' => -118.2437]],
            'null lat'    => [['lat' => null, 'lng' => -118.2437]],
        ];
    }

    public function testAcceptsIntegerCoordinates(): void
    {
        $this->assertSame('1,2', MapHelper::stringCoords(['lat' => 1, 'lng' => 2]));
    }

    public function testIgnoresExtraKeys(): void
    {
        $this->assertSame(
            '34.0522,-118.2437',
            MapHelper::stringCoords(['lat' => 34.0522, 'lng' => -118.2437, 'zoom' => 11])
        );
    }

    public function testZeroCoordinatesStillRender(): void
    {
        // 0,0 is a real coordinate. `isset` rather than a truthiness check is
        // what makes this work.
        $this->assertSame('0,0', MapHelper::stringCoords(['lat' => 0, 'lng' => 0]));
    }

    // ========================================================================= //
    // Shape of the Craft-bound method
    // ========================================================================= //

    public function testExtractCoordsExists(): void
    {
        $this->assertTrue((new ReflectionClass(MapHelper::class))->hasMethod('extractCoords'));
    }

    public function testExtractCoordsAcceptsTheThreeLocationShapes(): void
    {
        $method = (new ReflectionClass(MapHelper::class))->getMethod('extractCoords');
        $type = (string) $method->getParameters()[0]->getType();

        foreach (['array', 'Element', 'Location'] as $accepted) {
            $this->assertStringContainsString($accepted, $type);
        }
    }

    public function testEveryPublicMethodIsStatic(): void
    {
        // MapHelper is a namespace of functions, never instantiated.
        $reflection = new ReflectionClass(MapHelper::class);

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $this->assertTrue($method->isStatic(), "{$method->getName()}() is not static.");
        }
    }
}

<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\models\Location;
use PHPUnit\Framework\TestCase;

/**
 * Pure-unit tests for the Location value object.
 *
 * Location is the base of Address and holds the two things every mappable
 * thing has: a coordinate pair, and the ability to describe itself as a Google
 * Maps URL. None of it needs a Craft container, so these are real behavior
 * tests rather than shape checks.
 *
 * The distance math is worth pinning precisely. It is a hand-rolled haversine
 * in PHP that has to agree with the SQL haversine used by proximity search, so
 * a silent change to either radius constant or to the formula would split the
 * two apart in a way nothing else would notice.
 */
class LocationModelTest extends TestCase
{
    /** Los Angeles City Hall, roughly. */
    private const LA = ['lat' => 34.0522, 'lng' => -118.2437];

    /** Lower Manhattan, roughly. */
    private const NY = ['lat' => 40.7128, 'lng' => -74.0060];

    /** The real distance between them, to within a mile. */
    private const LA_TO_NY_MILES = 2445.7101588447485;
    private const LA_TO_NY_KM = 3935.746254609723;

    // ========================================================================= //
    // Coordinates
    // ========================================================================= //

    public function testNewLocationHasNoCoordinates(): void
    {
        $location = new Location();

        $this->assertNull($location->lat);
        $this->assertNull($location->lng);
    }

    public function testHasCoordsIsFalseWithoutBoth(): void
    {
        $this->assertFalse((new Location())->hasCoords());
        $this->assertFalse((new Location(['lat' => 34.0522]))->hasCoords());
        $this->assertFalse((new Location(['lng' => -118.2437]))->hasCoords());
    }

    public function testHasCoordsIsTrueWithBoth(): void
    {
        $this->assertTrue((new Location(self::LA))->hasCoords());
    }

    public function testGetCoordsReturnsLatAndLng(): void
    {
        $this->assertSame(
            ['lat' => 34.0522, 'lng' => -118.2437],
            (new Location(self::LA))->getCoords()
        );
    }

    public function testStringifiesToItsCoordinates(): void
    {
        $this->assertSame('34.0522, -118.2437', (string) (new Location(self::LA)));
    }

    // ========================================================================= //
    // Distance
    // ========================================================================= //

    public function testDistanceDefaultsToMiles(): void
    {
        $distance = (new Location(self::LA))->getDistance(new Location(self::NY));

        $this->assertEqualsWithDelta(self::LA_TO_NY_MILES, $distance, 0.0001);
    }

    /**
     * @dataProvider unitAliasProvider
     */
    public function testDistanceUnitAlias(string $unit, float $expected): void
    {
        $distance = (new Location(self::LA))->getDistance(new Location(self::NY), $unit);

        $this->assertEqualsWithDelta($expected, $distance, 0.0001);
    }

    public static function unitAliasProvider(): array
    {
        return [
            'miles'      => ['miles', self::LA_TO_NY_MILES],
            'mi'         => ['mi', self::LA_TO_NY_MILES],
            'km'         => ['km', self::LA_TO_NY_KM],
            'kilometers' => ['kilometers', self::LA_TO_NY_KM],
        ];
    }

    public function testUnrecognizedUnitFallsBackToMiles(): void
    {
        // Rather than throwing or returning null, which would break a template
        // mid-render over a typo.
        $distance = (new Location(self::LA))->getDistance(new Location(self::NY), 'furlongs');

        $this->assertEqualsWithDelta(self::LA_TO_NY_MILES, $distance, 0.0001);
    }

    public function testDistanceToSelfIsZero(): void
    {
        $la = new Location(self::LA);

        $this->assertSame(0.0, $la->getDistance($la));
    }

    public function testDistanceIsSymmetric(): void
    {
        $la = new Location(self::LA);
        $ny = new Location(self::NY);

        $this->assertEqualsWithDelta($la->getDistance($ny), $ny->getDistance($la), 0.0001);
    }

    public function testDistanceIsNullWhenTheStartingPointHasNoCoordinates(): void
    {
        $this->assertNull((new Location())->getDistance(new Location(self::NY)));
    }

    public function testKilometersAreAlwaysGreaterThanMiles(): void
    {
        $la = new Location(self::LA);
        $ny = new Location(self::NY);

        $this->assertGreaterThan(
            $la->getDistance($ny, 'miles'),
            $la->getDistance($ny, 'km')
        );
    }

    public function testTheTwoUnitsAgreeOnTheConversionFactor(): void
    {
        $la = new Location(self::LA);
        $ny = new Location(self::NY);

        $ratio = $la->getDistance($ny, 'km') / $la->getDistance($ny, 'miles');

        // 6371 / 3959, the ratio of the two earth-radius constants.
        $this->assertEqualsWithDelta(1.609244, $ratio, 0.0001);
    }

    /**
     * 🛑 A target with no coordinates is treated as 0,0 rather than rejected.
     *
     * That is a real quirk rather than an oversight worth fixing blind: the
     * guard is on the starting point only. Pinned so a future change to it is
     * a deliberate decision rather than an accident.
     */
    public function testTargetWithoutCoordinatesIsTreatedAsNullIsland(): void
    {
        $distance = (new Location(self::LA))->getDistance(new Location());

        // Distance from LA to 0,0 off the coast of Africa.
        $this->assertEqualsWithDelta(7813.822689932664, $distance, 0.0001);
    }

    // ========================================================================= //
    // Link builders
    // ========================================================================= //

    public function testLinkToMapUsesTheSearchEndpoint(): void
    {
        $this->assertSame(
            'https://www.google.com/maps/search/?api=1&query=34.0522,-118.2437',
            (new Location(self::LA))->linkToMap()
        );
    }

    public function testLinkToDirectionsUsesTheDirectionsEndpoint(): void
    {
        $this->assertSame(
            'https://www.google.com/maps/dir/?api=1&destination=34.0522,-118.2437',
            (new Location(self::LA))->linkToDirections()
        );
    }

    public function testLinkToDirectionsAcceptsExtraParameters(): void
    {
        $link = (new Location(self::LA))->linkToDirections(['travelmode' => 'walking']);

        $this->assertStringContainsString('travelmode=walking', $link);
        $this->assertStringContainsString('destination=34.0522,-118.2437', $link);
    }

    public function testLinkToStreetViewUsesPanoAction(): void
    {
        $this->assertSame(
            'https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=34.0522,-118.2437',
            (new Location(self::LA))->linkToStreetView()
        );
    }

    public function testLinkToAreaCentersTheMap(): void
    {
        $this->assertSame(
            'https://www.google.com/maps/@?api=1&map_action=map&center=34.0522,-118.2437',
            (new Location(self::LA))->linkToArea()
        );
    }

    /**
     * @dataProvider linkMethodProvider
     */
    public function testEveryLinkIsAnHttpsGoogleMapsUrl(string $method): void
    {
        $link = (new Location(self::LA))->$method();

        $this->assertStringStartsWith('https://www.google.com/maps/', $link);
        $this->assertSame('1', $this->_queryValue($link, 'api'));
    }

    public static function linkMethodProvider(): array
    {
        return [
            'linkToMap'        => ['linkToMap'],
            'linkToDirections' => ['linkToDirections'],
            'linkToStreetView' => ['linkToStreetView'],
            'linkToArea'       => ['linkToArea'],
        ];
    }

    private function _queryValue(string $url, string $key): ?string
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
        return $query[$key] ?? null;
    }
}

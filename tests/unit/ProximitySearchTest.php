<?php
namespace doublesecretagency\googlemaps\tests\unit;

use craft\base\Model;
use doublesecretagency\googlemaps\models\Location;
use doublesecretagency\googlemaps\models\ProximitySearch;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Tests for proximity search.
 *
 * Proximity search modifies an element query by injecting haversine SQL, so the
 * distances it returns and the distances `Location::getDistance()` computes in
 * PHP have to agree. There are two implementations of the formula, one in SQL
 * and one in PHP, and they already share their radius constants:
 * `Location::_haversinePhp()` calls `ProximitySearch::haversineRadius()` rather
 * than declaring its own.
 *
 * That is the design worth protecting, so the tests assert the sharing rather
 * than comparing two independent copies. `haversineRadius()` is pure, so that
 * half is real behavior; the SQL generation needs a query object and is covered
 * structurally.
 */
class ProximitySearchTest extends TestCase
{
    private ReflectionClass $reflection;
    private string $source;

    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(ProximitySearch::class);
        $this->source = file_get_contents($this->reflection->getFileName());
    }

    // ========================================================================= //
    // The radius constants
    // ========================================================================= //

    /**
     * @dataProvider radiusProvider
     */
    public function testRadiusForUnit(string $unit, int $expected): void
    {
        $this->assertSame($expected, ProximitySearch::haversineRadius($unit));
    }

    public static function radiusProvider(): array
    {
        return [
            'miles'      => ['miles', 3959],
            'mi'         => ['mi', 3959],
            'km'         => ['km', 6371],
            'kilometers' => ['kilometers', 6371],
        ];
    }

    public function testUnrecognizedUnitFallsBackToMiles(): void
    {
        // A typo in a template must not produce a zero radius, which would
        // collapse every distance to zero and match everything.
        $this->assertSame(3959, ProximitySearch::haversineRadius('furlongs'));
        $this->assertSame(3959, ProximitySearch::haversineRadius(''));
    }

    public function testRadiusIsNeverZero(): void
    {
        foreach (['miles', 'mi', 'km', 'kilometers', 'nonsense', ''] as $unit) {
            $this->assertGreaterThan(0, ProximitySearch::haversineRadius($unit));
        }
    }

    public function testHaversineRadiusIsStatic(): void
    {
        // The SQL builder and any caller reach it without an instance.
        $this->assertTrue($this->reflection->getMethod('haversineRadius')->isStatic());
    }

    // ========================================================================= //
    // 🛑 The SQL and the PHP must agree
    // ========================================================================= //

    /**
     * The PHP haversine and the SQL haversine share one source of truth.
     *
     * `Location::_haversinePhp()` calls `ProximitySearch::haversineRadius()`
     * rather than declaring its own constants, which is what keeps a result
     * list's ordering and its reported distances in agreement. The test below
     * would fail the moment somebody forked the constants into Location, and
     * the one after it asserts the shared call directly.
     */
    public function testPhpDistanceUsesTheSameRadiiAsTheSql(): void
    {
        $la = new Location(['lat' => 34.0522, 'lng' => -118.2437]);
        $ny = new Location(['lat' => 40.7128, 'lng' => -74.0060]);

        $phpRatio = $la->getDistance($ny, 'km') / $la->getDistance($ny, 'miles');
        $sqlRatio = ProximitySearch::haversineRadius('km') / ProximitySearch::haversineRadius('miles');

        $this->assertEqualsWithDelta($sqlRatio, $phpRatio, 0.000001);
    }

    public function testThePhpHaversineReadsTheRadiusFromTheSharedMethod(): void
    {
        $location = file_get_contents(dirname(__DIR__, 2) . '/src/models/Location.php');

        $this->assertStringContainsString(
            'ProximitySearch::haversineRadius($units)',
            $location,
            'Location must read the radius from ProximitySearch. A local copy of the '
            . 'constants is how the reported distance drifts from the query ordering.'
        );
    }

    public function testNeitherHaversineHardcodesARadius(): void
    {
        // The literals belong in haversineRadius() and nowhere else.
        $location = file_get_contents(dirname(__DIR__, 2) . '/src/models/Location.php');

        foreach (['3959', '6371'] as $literal) {
            $this->assertStringNotContainsString(
                $literal,
                $location,
                "Location hardcodes the radius {$literal} rather than reading it from "
                . 'ProximitySearch.'
            );
        }
    }

    public function testTheSqlBuilderReadsTheRadiusFromTheSharedMethod(): void
    {
        // Rather than hardcoding a second copy of the constants.
        $this->assertStringContainsString('self::haversineRadius(', $this->source);
    }

    // ========================================================================= //
    // Accepted units
    // ========================================================================= //

    public function testTheValidUnitListMatchesWhatTheRadiusMethodHandles(): void
    {
        // Every unit the validator accepts must produce a deliberate radius
        // rather than falling through to the default.
        $this->assertStringContainsString(
            "\$validUnits = ['mi', 'km', 'miles', 'kilometers'];",
            $this->source
        );

        foreach (['mi', 'km', 'miles', 'kilometers'] as $unit) {
            $this->assertContains(
                ProximitySearch::haversineRadius($unit),
                [3959, 6371],
                "Accepted unit `{$unit}` produces an unexpected radius."
            );
        }
    }

    // ========================================================================= //
    // Class shape
    // ========================================================================= //

    public function testExtendsCraftModel(): void
    {
        $this->assertTrue($this->reflection->isSubclassOf(Model::class));
    }

    /**
     * @dataProvider optionApplierProvider
     */
    public function testOptionApplierExists(string $method): void
    {
        $this->assertTrue(
            $this->reflection->hasMethod($method),
            "Lost {$method}(). The option it handles stops being applied silently."
        );
    }

    public static function optionApplierProvider(): array
    {
        $methods = [
            '_applyRange', '_applyUnits', '_applyTarget', '_applySubfields',
            '_applyRequireCoords', '_applyReverseRadius',
        ];
        return array_combine($methods, array_map(static fn(string $m): array => [$m], $methods));
    }

    public function testInitRunsTheAppliersInOrder(): void
    {
        // Order matters: units must be resolved before the SQL that uses them.
        $init = $this->_methodBody('init');

        $unitsAt = strpos($init, '_applyUnits');
        $targetAt = strpos($init, '_applyTarget');

        $this->assertNotFalse($unitsAt, 'init() does not apply units.');
        $this->assertNotFalse($targetAt, 'init() does not apply a target.');
        $this->assertLessThan($targetAt, $unitsAt, 'Units must be resolved before the target.');
    }

    public function testTargetCoordinatesCanBeGeocoded(): void
    {
        // A string target is looked up rather than rejected.
        $this->assertTrue($this->reflection->hasMethod('_lookupCoords'));
        $this->assertTrue($this->reflection->hasMethod('_getTargetCoords'));
    }

    private function _methodBody(string $method): string
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
    // Database engine support
    // ========================================================================= //

    public function testTheSqlBranchesOnTheDatabaseDriver(): void
    {
        // 5.2.0 fixed Postgres bugs in both the forward and reverse directions,
        // so the two engines demonstrably do not share one code path.
        $this->assertMatchesRegularExpression(
            '/getIsMysql|getIsPgsql|DbConfig::DRIVER_/',
            $this->source,
            'Proximity search must account for the database driver. MySQL and '
            . 'Postgres have needed separate handling.'
        );
    }

    public function testTheFieldRoutesQueriesThroughThisModel(): void
    {
        $field = file_get_contents(dirname(__DIR__, 2) . '/src/fields/AddressField.php');

        $this->assertStringContainsString('ProximitySearch', $field);
        $this->assertTrue(
            (new ReflectionClass(\doublesecretagency\googlemaps\fields\AddressField::class))
                ->getMethod('queryCondition')->isStatic()
        );
    }
}

<?php
namespace doublesecretagency\googlemaps\tests\unit;

use craft\base\Model;
use craft\fieldlayoutelements\CustomField;
use craft\models\FieldLayout;
use doublesecretagency\googlemaps\fields\AddressField;
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

    // ========================================================================= //
    // Counting results (#115)
    // ========================================================================= //

    public function testDistanceColumnIsSkippedWhenCounting(): void
    {
        // `count()` and `{% paginate %}` select COUNT(*). Adding the distance
        // column beside it crashed MySQL's only_full_group_by mode (and Postgres).
        $this->assertMatchesRegularExpression(
            '/if \(!\$this->_isAggregate\(\)\) \{\s*\n(?:\s*\/\/[^\n]*\n)*\s*\$this->query->query->addSelect\(\s*"\[\[distance\]\] AS/',
            $this->source,
            'The outer distance column must only be added when the query is not an aggregate.'
        );
    }

    public function testAggregateDetectionCoversTheAggregateFunctions(): void
    {
        $this->assertStringContainsString(
            "preg_match('/^(COUNT|SUM|AVG|MIN|MAX)\s*\(/i', trim(\$column))",
            $this->source
        );
    }

    // ========================================================================= //
    // An address exactly at the target (#115)
    // ========================================================================= //

    public function testTheSqlHaversineClampsTheAcosArgument(): void
    {
        // For identical points rounding can land the argument a hair above 1,
        // and acos() of that is NULL in MySQL and an error in Postgres.
        $sql = $this->_haversine();

        $this->assertStringContainsString('acos(CASE', $sql);
        $this->assertMatchesRegularExpression('/WHEN \(.+\) > 1 THEN 1/s', $sql);
        $this->assertMatchesRegularExpression('/WHEN \(.+\) < -1 THEN -1/s', $sql);
    }

    public function testTheClampKeepsAMissingAddressNull(): void
    {
        // Postgres' LEAST() and GREATEST() skip NULLs, so LEAST(1, GREATEST(-1, NULL))
        // is -1 and an element without an address got a distance of 3959π miles.
        // CASE falls through to the NULL argument on both databases.
        $sql = $this->_haversine();

        $this->assertStringNotContainsString('LEAST(', $sql);
        $this->assertStringNotContainsString('GREATEST(', $sql);
        $this->assertMatchesRegularExpression('/ELSE \(.+\)\s*END\)/s', $sql);
    }

    /**
     * The real haversine SQL, in miles.
     */
    private function _haversine(): string
    {
        $search = $this->reflection->newInstanceWithoutConstructor();

        return $this->reflection->getMethod('_haversineSql')->invoke($search, 34.1, -118.3);
    }

    // ========================================================================= //
    // Postgres range filter
    // ========================================================================= //

    public function testThePostgresRangeIsFilteredInTheSubquery(): void
    {
        // Craft's count(), sum() and exists() run only the subquery unless it has
        // a HAVING, so an outer WHERE made them ignore the range on Postgres.
        $body = $this->_methodBody('_applyTarget');

        $this->assertStringContainsString('$this->query->subQuery->andWhere($condition, $params);', $body);
        $this->assertStringNotContainsString('$this->query->query->andWhere(', $body);
    }

    public function testThePostgresRangeRepeatsTheFormula(): void
    {
        // Postgres can't filter a subquery by its own column alias
        $this->assertStringContainsString(
            "\$distance = (\$isMysql ? '[[distance]]' : \"({\$haversine})\");",
            $this->_methodBody('_applyTarget')
        );
    }

    public function testTheMysqlRangeStaysAHaving(): void
    {
        $this->assertStringContainsString(
            '$this->query->subQuery->having($condition, $params);',
            $this->_methodBody('_applyTarget')
        );
    }

    // ========================================================================= //
    // A search which throws
    // ========================================================================= //

    public function testTheSearchIsClearedBeforeItRuns(): void
    {
        // Clearing it afterward meant a search which threw (a bad `reverseRadius`)
        // stayed queued, and the next query of any kind threw the same error.
        $field = file_get_contents(dirname(__DIR__, 2) . '/src/fields/AddressField.php');

        $clearedAt = strpos($field, 'static::$proximitySearch = null;');
        $runAt = strpos($field, 'new ProximitySearch([');

        $this->assertNotFalse($clearedAt);
        $this->assertNotFalse($runAt);
        $this->assertLessThan($runAt, $clearedAt, 'The search must be cleared before it runs.');
    }

    public function testThePhpHaversineClampsTheAcosArgument(): void
    {
        $location = file_get_contents(dirname(__DIR__, 2) . '/src/models/Location.php');

        $this->assertStringContainsString('acos(max(-1, min(1,', $location);
    }

    /**
     * A sandbox address whose distance to itself came back NAN before the clamp.
     */
    public function testDistanceFromAKnownOvershootingPointToItselfIsZero(): void
    {
        $point = ['lat' => 34.04572160, 'lng' => -118.58438140];

        $this->assertSame(0.0, (new Location($point))->getDistance($point));
    }

    public function testDistanceFromAPointToItselfIsNeverNan(): void
    {
        // Before the clamp roughly 4% of identical-point pairs returned NAN
        for ($lat = -89.5; $lat < 90; $lat += 0.37) {
            $point = ['lat' => $lat, 'lng' => -118.58438140];
            $distance = (new Location($point))->getDistance($point);

            $this->assertFalse(is_nan($distance), "Distance at latitude {$lat} is NAN.");
        }
    }

    public function testAZeroDistanceIsKeptOnTheAddress(): void
    {
        // A distance of 0 is falsy, so a truthiness check dropped it to null
        $field = file_get_contents(dirname(__DIR__, 2) . '/src/fields/AddressField.php');

        $this->assertStringContainsString("if (is_numeric(\$value)) {\n            \$attr['distance'] = (float) \$value;", $field);
    }

    // ========================================================================= //
    // Fields sharing a handle (Craft 5 multi-instance fields)
    // ========================================================================= //

    /**
     * An Address field instance as Craft hands it to queryCondition(): a field
     * with a handle, placed in one layout. Built without constructors so no
     * Craft container is needed.
     */
    private function _instance(int $fieldId, string $handle, int $layoutId): AddressField
    {
        $layout = (new ReflectionClass(FieldLayout::class))->newInstanceWithoutConstructor();
        $layout->id = $layoutId;

        $element = (new ReflectionClass(CustomField::class))->newInstanceWithoutConstructor();
        $element->setLayout($layout);

        $field = (new ReflectionClass(AddressField::class))->newInstanceWithoutConstructor();
        $field->id = $fieldId;
        $field->handle = $handle;
        $field->layoutElement = $element;

        return $field;
    }

    /**
     * A stand-in layout which only answers getCustomFields().
     */
    private function _layout(AddressField ...$fields): object
    {
        return new class($fields) {
            public function __construct(private array $fields) {}
            public function getCustomFields(): array { return $this->fields; }
        };
    }

    /**
     * Calls a private ProximitySearch method on a search over the given instances.
     */
    private function _call(string $method, array $instances, mixed ...$args): mixed
    {
        $search = (new ReflectionClass(ProximitySearch::class))->newInstanceWithoutConstructor();
        $search->field = $instances[0];
        $search->fields = $instances;

        return $this->reflection->getMethod($method)->invoke($search, ...$args);
    }

    public function testQueryConditionCollectsEveryFieldSharingAHandle(): void
    {
        // Craft calls queryCondition() once per field behind a handle. Before
        // the fix each call overwrote the last, so only one field was searched.
        $a = $this->_instance(1, 'address', 2);
        $b = $this->_instance(8, 'address', 6);
        $options = ['range' => 5];

        $params = [];

        try {
            AddressField::$proximitySearch = null;
            AddressField::queryCondition([$a], $options, $params);
            AddressField::queryCondition([$b], $options, $params);

            $this->assertSame([$a, $b], AddressField::$proximitySearch['fields']);
            $this->assertSame($a, AddressField::$proximitySearch['field']);
            $this->assertSame($options, AddressField::$proximitySearch['options']);
        } finally {
            AddressField::$proximitySearch = null;
        }
    }

    public function testQueryConditionStartsOverForADifferentHandle(): void
    {
        // Two proximity params in one query is a known limitation (the last
        // wins). Their fields must not merge into one search.
        $a = $this->_instance(1, 'address', 2);
        $b = $this->_instance(6, 'venue', 3);

        $params = [];

        try {
            AddressField::$proximitySearch = null;
            AddressField::queryCondition([$a], ['range' => 5], $params);
            AddressField::queryCondition([$b], ['range' => 9], $params);

            $this->assertSame([$b], AddressField::$proximitySearch['fields']);
            $this->assertSame($b, AddressField::$proximitySearch['field']);
        } finally {
            AddressField::$proximitySearch = null;
        }
    }

    public function testTheFieldHandsEveryInstanceToTheSearch(): void
    {
        $field = file_get_contents(dirname(__DIR__, 2) . '/src/fields/AddressField.php');

        $this->assertStringContainsString("'fields'  => \$search['fields'],", $field);
    }

    public function testTheJoinUsesTheFieldCondition(): void
    {
        $this->assertStringContainsString('$this->_fieldCondition(),', $this->_methodBody('init'));
        $this->assertStringNotContainsString(':fieldId', $this->_methodBody('init'));
    }

    public function testASingleFieldIsMatchedEverywhere(): void
    {
        // The common case keeps the original join: no layout restriction
        $instances = [$this->_instance(1, 'address', 2), $this->_instance(1, 'address', 13)];
        $layouts = [$this->_layout($instances[0]), $this->_layout($instances[1])];

        $this->assertSame(
            ['gm_addresses.fieldId' => 1],
            $this->_call('_fieldCondition', $instances, $layouts)
        );
    }

    public function testSeveralFieldsSharingAHandleAreAllMatched(): void
    {
        // The sandbox repro: field 1 and field 8 both answer to `address`
        $instances = [$this->_instance(1, 'address', 2), $this->_instance(8, 'address', 6)];
        $layouts = [$this->_layout($instances[0]), $this->_layout($instances[1])];

        $this->assertSame(
            ['gm_addresses.fieldId' => [1, 8]],
            $this->_call('_fieldCondition', $instances, $layouts)
        );
    }

    public function testAFieldUsedUnderAnotherHandleIsOnlyMatchedInItsOwnLayouts(): void
    {
        // Layout 16 holds field 8 as `address2` beside field 1 as `address`, so
        // matching field 8 everywhere would join a second, wrong address there.
        $instances = [$this->_instance(1, 'address', 16), $this->_instance(8, 'address', 6)];
        $layouts = [
            $this->_layout($instances[1]),
            $this->_layout($instances[0], $this->_instance(8, 'address2', 16)),
        ];

        $this->assertSame(
            ['or',
                ['gm_addresses.fieldId' => 8, 'elements.fieldLayoutId' => [6]],
                ['gm_addresses.fieldId' => 1],
            ],
            $this->_call('_fieldCondition', $instances, $layouts)
        );
    }

    public function testAnOverriddenHandleOnItsOwnIsOnlyMatchedInItsOwnLayouts(): void
    {
        // Searching `address2` must not match elements storing field 8 as `address`
        $instances = [$this->_instance(8, 'address2', 16)];
        $layouts = [
            $this->_layout($this->_instance(8, 'address', 6)),
            $this->_layout($this->_instance(1, 'address', 16), $instances[0]),
        ];

        $this->assertSame(
            ['gm_addresses.fieldId' => 8, 'elements.fieldLayoutId' => [16]],
            $this->_call('_fieldCondition', $instances, $layouts)
        );
    }

    public function testEachLayoutIsListedOnce(): void
    {
        $instances = [
            $this->_instance(1, 'address', 2),
            $this->_instance(8, 'address', 6),
            $this->_instance(1, 'address', 2),
        ];

        $layoutIds = array_map(static fn($layout) => $layout->id, $this->_call('_layouts', $instances));

        $this->assertSame([2, 6], $layoutIds);
    }

    public function testTheReverseRadiusIsReadFromEveryLayout(): void
    {
        // Each layout stores the radius under its own instance UID. Reading only
        // the first layout's UID dropped every element in the other layouts.
        $body = $this->_methodBody('_applyReverseRadius');

        $this->assertStringContainsString('foreach ($this->_layouts() as $layout) {', $body);
        $this->assertStringContainsString("'COALESCE('.implode(', ', \$radii).')'", $body);
        $this->assertStringNotContainsString('$this->field->layoutElement->getLayout()', $body);
    }

    public function testAReverseRadiusMissingFromALayoutIsSkipped(): void
    {
        // getField() throws for a missing field, which crashed the search
        // whenever the first layout lacked the Number field
        $body = $this->_methodBody('_applyReverseRadius');

        $this->assertStringContainsString('$layout->getFieldByHandle($reverseRadius)', $body);
        $this->assertStringNotContainsString('->getField($reverseRadius)', $body);
    }
}

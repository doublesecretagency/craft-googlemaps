<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\enums\GoogleConstants;
use PHPUnit\Framework\TestCase;

/**
 * Pure-unit tests for the Google API constant translation table.
 *
 * A dynamic map's options are written in Twig, where JavaScript enums like
 * `google.maps.ControlPosition.TOP_LEFT` do not exist. So a template writes the
 * token and the plugin swaps it for the value Google's API expects before the
 * DNA is exported.
 *
 * The table has two levels, which is the part worth pinning:
 *
 *   - A level 1 entry maps an option key directly to an enum name
 *     (`mapTypeId` => `MapTypeId`).
 *   - A level 2 entry maps an option key to an array of sub-keys, each with its
 *     own enum (`mapTypeControlOptions` => `position` and `style`).
 *
 * An option present in TYPES whose enum is missing from VALUES is a silent
 * no-op: the token reaches Google as an unrecognized string and is ignored.
 */
class GoogleConstantsTest extends TestCase
{
    /**
     * Flatten TYPES into a list of [option path, enum name] pairs.
     */
    private function _enumReferences(): array
    {
        $references = [];

        foreach (GoogleConstants::TYPES as $option => $mapped) {
            if (is_string($mapped)) {
                $references[$option] = $mapped;
                continue;
            }
            foreach ($mapped as $subKey => $enum) {
                $references["{$option}.{$subKey}"] = $enum;
            }
        }

        return $references;
    }

    // ========================================================================= //
    // Both tables are populated
    // ========================================================================= //

    public function testTypesIsNotEmpty(): void
    {
        $this->assertNotEmpty(GoogleConstants::TYPES);
    }

    public function testValuesIsNotEmpty(): void
    {
        $this->assertNotEmpty(GoogleConstants::VALUES);
    }

    public function testTheFlattenerFoundReferences(): void
    {
        // Guards every comparison below against passing vacuously.
        $this->assertNotEmpty($this->_enumReferences());
    }

    // ========================================================================= //
    // Referential integrity between the two tables
    // ========================================================================= //

    public function testEveryReferencedEnumExists(): void
    {
        foreach ($this->_enumReferences() as $path => $enum) {
            $this->assertArrayHasKey(
                $enum,
                GoogleConstants::VALUES,
                "`{$path}` maps to enum `{$enum}`, which has no entry in VALUES. Any "
                . 'template using it is silently ignored.'
            );
        }
    }

    public function testEveryEnumIsReachableFromAtLeastOneOption(): void
    {
        $referenced = array_unique(array_values($this->_enumReferences()));

        foreach (array_keys(GoogleConstants::VALUES) as $enum) {
            $this->assertContains(
                $enum,
                $referenced,
                "Enum `{$enum}` is defined but nothing maps to it, so it is dead."
            );
        }
    }

    // ========================================================================= //
    // The two levels of the table
    // ========================================================================= //

    public function testMapTypeIdIsTheOnlyLevelOneEntry(): void
    {
        $levelOne = array_keys(array_filter(GoogleConstants::TYPES, 'is_string'));

        $this->assertSame(['mapTypeId'], $levelOne);
    }

    /**
     * @dataProvider positionOptionProvider
     */
    public function testControlOptionTakesAPosition(string $option): void
    {
        $this->assertIsArray(GoogleConstants::TYPES[$option]);
        $this->assertSame('ControlPosition', GoogleConstants::TYPES[$option]['position']);
    }

    public static function positionOptionProvider(): array
    {
        $options = [
            'cameraControlOptions',
            'fullscreenControlOptions',
            'mapTypeControlOptions',
            'motionTrackingControlOptions',
            'panControlOptions',
            'rotateControlOptions',
            'streetViewControlOptions',
            'zoomControlOptions',
        ];
        return array_combine($options, array_map(static fn(string $o): array => [$o], $options));
    }

    public function testMapTypeControlTakesBothAPositionAndAStyle(): void
    {
        $this->assertSame(
            ['position' => 'ControlPosition', 'style' => 'MapTypeControlStyle'],
            GoogleConstants::TYPES['mapTypeControlOptions']
        );
    }

    public function testScaleControlTakesOnlyAStyle(): void
    {
        // It has no position in Google's API, unlike every other control.
        $this->assertSame(
            ['style' => 'ScaleControlStyle'],
            GoogleConstants::TYPES['scaleControlOptions']
        );
    }

    /**
     * @dataProvider optionKeyProvider
     */
    public function testOptionKeyIsCamelCase(string $option): void
    {
        // Written verbatim in a Twig map options hash.
        $this->assertMatchesRegularExpression('/^[a-z][A-Za-z0-9]*$/', $option);
    }

    public static function optionKeyProvider(): array
    {
        $keys = array_keys(GoogleConstants::TYPES);
        return array_combine($keys, array_map(static fn(string $k): array => [$k], $keys));
    }

    /**
     * @dataProvider levelTwoSubKeyProvider
     */
    public function testLevelTwoSubKeysAreOnlyPositionOrStyle(string $subKey): void
    {
        // Google's control option objects expose no other enum-valued key.
        $this->assertContains($subKey, ['position', 'style']);
    }

    public static function levelTwoSubKeyProvider(): array
    {
        $subKeys = [];
        foreach (GoogleConstants::TYPES as $mapped) {
            if (is_array($mapped)) {
                foreach (array_keys($mapped) as $subKey) {
                    $subKeys[$subKey] = [$subKey];
                }
            }
        }
        return $subKeys;
    }

    // ========================================================================= //
    // The enum members
    // ========================================================================= //

    /**
     * @dataProvider enumProvider
     */
    public function testEnumMembersAreScreamingSnakeCase(string $enum): void
    {
        foreach (array_keys(GoogleConstants::VALUES[$enum]) as $member) {
            $this->assertMatchesRegularExpression(
                '/^[A-Z][A-Z0-9_]*$/',
                (string) $member,
                "`{$enum}.{$member}` does not match Google's naming."
            );
        }
    }

    /**
     * @dataProvider enumProvider
     */
    public function testEnumIsNotEmpty(string $enum): void
    {
        $this->assertNotEmpty(GoogleConstants::VALUES[$enum]);
    }

    /**
     * @dataProvider enumProvider
     */
    public function testEnumMembersAreScalars(string $enum): void
    {
        // Positions and styles are integers; map type ids are strings. Both are
        // scalars, and a nested array here would break the replacement.
        foreach (GoogleConstants::VALUES[$enum] as $member => $value) {
            $this->assertIsScalar($value, "`{$enum}.{$member}` is not a scalar.");
        }
    }

    public static function enumProvider(): array
    {
        $keys = array_keys(GoogleConstants::VALUES);
        return array_combine($keys, array_map(static fn(string $k): array => [$k], $keys));
    }

    /**
     * @dataProvider integerEnumProvider
     */
    public function testPositionAndStyleEnumsUseIntegers(string $enum): void
    {
        foreach (GoogleConstants::VALUES[$enum] as $member => $value) {
            $this->assertIsInt($value, "`{$enum}.{$member}` should be an integer.");
        }
    }

    public static function integerEnumProvider(): array
    {
        $enums = ['ControlPosition', 'MapTypeControlStyle', 'ScaleControlStyle'];
        return array_combine($enums, array_map(static fn(string $e): array => [$e], $enums));
    }

    /**
     * 🛑 MapTypeId is the exception: its values are lowercase strings, not
     * integers, because that is what Google's API accepts.
     */
    public function testMapTypeIdUsesLowercaseStrings(): void
    {
        foreach (GoogleConstants::VALUES['MapTypeId'] as $member => $value) {
            $this->assertIsString($value, "`MapTypeId.{$member}` should be a string.");
            $this->assertSame(strtolower($value), $value, "`MapTypeId.{$member}` is not lowercase.");
        }
    }

    public function testMapTypeIdCoversTheFourBaseTypes(): void
    {
        $this->assertSame(
            ['HYBRID' => 'hybrid', 'ROADMAP' => 'roadmap', 'SATELLITE' => 'satellite', 'TERRAIN' => 'terrain'],
            GoogleConstants::VALUES['MapTypeId']
        );
    }

    public function testControlPositionCoversEveryAnchorPlusCenter(): void
    {
        // Google's documented set. A missing one silently drops a control to
        // its default corner.
        foreach ([
            'TOP_LEFT', 'TOP_CENTER', 'TOP_RIGHT',
            'LEFT_TOP', 'LEFT_CENTER', 'LEFT_BOTTOM',
            'RIGHT_TOP', 'RIGHT_CENTER', 'RIGHT_BOTTOM',
            'BOTTOM_LEFT', 'BOTTOM_CENTER', 'BOTTOM_RIGHT',
            'CENTER',
        ] as $member) {
            $this->assertArrayHasKey($member, GoogleConstants::VALUES['ControlPosition']);
        }
    }

    public function testControlPositionAlsoOffersTheBroadAliases(): void
    {
        // Convenience names that resolve onto a specific anchor.
        $positions = GoogleConstants::VALUES['ControlPosition'];

        $this->assertSame($positions['TOP_CENTER'], $positions['TOP']);
        $this->assertSame($positions['LEFT_TOP'], $positions['LEFT']);
        $this->assertSame($positions['RIGHT_TOP'], $positions['RIGHT']);
        $this->assertSame($positions['BOTTOM_CENTER'], $positions['BOTTOM']);
    }

    public function testEveryStyleEnumDefinesADefault(): void
    {
        foreach (['MapTypeControlStyle', 'ScaleControlStyle'] as $enum) {
            $this->assertArrayHasKey('DEFAULT', GoogleConstants::VALUES[$enum]);
            $this->assertSame(0, GoogleConstants::VALUES[$enum]['DEFAULT']);
        }
    }

    // ========================================================================= //
    // The translation actually happens
    // ========================================================================= //

    public function testDynamicMapPerformsTheReplacement(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/src/models/DynamicMap.php');

        $this->assertStringContainsString('_replaceGoogleConstants', $source);
        $this->assertStringContainsString('GoogleConstants::', $source);
    }

    public function testTheTokenPrefixIsDocumented(): void
    {
        // The docblock records the form a template writes, which is the only
        // place that contract is stated.
        $source = file_get_contents(dirname(__DIR__, 2) . '/src/enums/GoogleConstants.php');

        $this->assertStringContainsString('google.maps', $source);
    }

    public function testClassIsAbstractSoItIsNeverInstantiated(): void
    {
        $this->assertTrue((new \ReflectionClass(GoogleConstants::class))->isAbstract());
    }
}

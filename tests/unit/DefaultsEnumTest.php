<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\enums\Defaults;
use PHPUnit\Framework\TestCase;

/**
 * Pure-unit tests for the Address field's default configuration.
 *
 * Two constants live here and both are load-bearing well beyond their size.
 *
 * COORDINATES is the fallback a map centers on when a location has none. It
 * points at the Bermuda Triangle on purpose, so a map floating in the middle
 * of the Atlantic reads as "no coordinates" rather than as a geocoding result.
 * Replacing it with 0,0 or a real city would destroy that signal.
 *
 * SUBFIELDCONFIG is the canonical list of address subfields. It is consumed by
 * the field's settings screen, the Twig templates, the Vue store's binding
 * derivation, and the database schema, so its handles and their order are a
 * contract rather than a preference.
 */
class DefaultsEnumTest extends TestCase
{
    // ========================================================================= //
    // COORDINATES
    // ========================================================================= //

    public function testCoordinatesHaveLatLngAndZoom(): void
    {
        $this->assertArrayHasKey('lat', Defaults::COORDINATES);
        $this->assertArrayHasKey('lng', Defaults::COORDINATES);
        $this->assertArrayHasKey('zoom', Defaults::COORDINATES);
    }

    public function testCoordinatesPointAtTheBermudaTriangle(): void
    {
        // Deliberate. A map centered in the open Atlantic is the plugin's
        // visual signal that no coordinates were set.
        $this->assertSame(32.3113966, Defaults::COORDINATES['lat']);
        $this->assertSame(-64.7527469, Defaults::COORDINATES['lng']);
    }

    public function testCoordinatesAreNotNullIsland(): void
    {
        // 0,0 is a real place off the coast of Africa and is the classic
        // "unset coordinates" bug, so it must never become the default.
        $this->assertNotSame(0, Defaults::COORDINATES['lat']);
        $this->assertNotSame(0, Defaults::COORDINATES['lng']);
    }

    public function testDefaultZoomIsAWideView(): void
    {
        $this->assertSame(6, Defaults::COORDINATES['zoom']);
    }

    public function testCoordinatesAreFloats(): void
    {
        $this->assertIsFloat(Defaults::COORDINATES['lat']);
        $this->assertIsFloat(Defaults::COORDINATES['lng']);
        $this->assertIsInt(Defaults::COORDINATES['zoom']);
    }

    public function testCoordinatesAreWithinValidRanges(): void
    {
        $this->assertGreaterThanOrEqual(-90, Defaults::COORDINATES['lat']);
        $this->assertLessThanOrEqual(90, Defaults::COORDINATES['lat']);
        $this->assertGreaterThanOrEqual(-180, Defaults::COORDINATES['lng']);
        $this->assertLessThanOrEqual(180, Defaults::COORDINATES['lng']);
    }

    // ========================================================================= //
    // SUBFIELDCONFIG: the handle set
    // ========================================================================= //

    public function testSubfieldOrderIsExact(): void
    {
        // The order is the default rendering order of the field, so a
        // reshuffle is a visible change to every install that never
        // customized it.
        $this->assertSame(
            [
                'name', 'street1', 'street2', 'city', 'state', 'zip',
                'neighborhood', 'county', 'country', 'countryCode', 'placeId',
            ],
            array_column(Defaults::SUBFIELDCONFIG, 'handle')
        );
    }

    public function testEverySubfieldHandleIsUnique(): void
    {
        $handles = array_column(Defaults::SUBFIELDCONFIG, 'handle');

        $this->assertSame(count($handles), count(array_unique($handles)));
    }

    public function testEverySubfieldHandleHasAMatchingDatabaseColumn(): void
    {
        // FromScratch creates the columns these handles are stored in.
        $schema = file_get_contents(dirname(__DIR__, 2) . '/src/migrations/FromScratch.php');

        foreach (array_column(Defaults::SUBFIELDCONFIG, 'handle') as $handle) {
            $this->assertMatchesRegularExpression(
                "/'{$handle}'\s*=>/",
                $schema,
                "Subfield `{$handle}` has no column in the install schema."
            );
        }
    }

    // ========================================================================= //
    // SUBFIELDCONFIG: the shape of each entry
    // ========================================================================= //

    /**
     * @dataProvider subfieldProvider
     */
    public function testSubfieldHasEveryRequiredKey(array $subfield): void
    {
        foreach (['handle', 'label', 'width', 'enabled', 'autocomplete', 'required'] as $key) {
            $this->assertArrayHasKey($key, $subfield, "Subfield `{$subfield['handle']}` is missing `{$key}`.");
        }
    }

    /**
     * @dataProvider subfieldProvider
     */
    public function testSubfieldKeysAreCorrectlyTyped(array $subfield): void
    {
        $this->assertIsString($subfield['handle']);
        $this->assertIsString($subfield['label']);
        $this->assertIsInt($subfield['width']);
        $this->assertIsBool($subfield['enabled']);
        $this->assertIsBool($subfield['autocomplete']);
        $this->assertIsBool($subfield['required']);
    }

    /**
     * @dataProvider subfieldProvider
     */
    public function testSubfieldWidthIsAUsablePercentage(array $subfield): void
    {
        $this->assertGreaterThan(0, $subfield['width']);
        $this->assertLessThanOrEqual(100, $subfield['width']);
    }

    /**
     * @dataProvider subfieldProvider
     */
    public function testSubfieldHandleIsCamelCase(array $subfield): void
    {
        // The handle becomes a form input name, a database column, and a
        // JavaScript object key, so it has to survive all three.
        $this->assertMatchesRegularExpression('/^[a-z][A-Za-z0-9]*$/', $subfield['handle']);
    }

    public static function subfieldProvider(): array
    {
        $cases = [];
        foreach (Defaults::SUBFIELDCONFIG as $subfield) {
            $cases[$subfield['handle']] = [$subfield];
        }
        return $cases;
    }

    // ========================================================================= //
    // SUBFIELDCONFIG: the defaults a new field ships with
    // ========================================================================= //

    public function testTheExpectedSubfieldsAreEnabledByDefault(): void
    {
        $enabled = array_column(
            array_filter(Defaults::SUBFIELDCONFIG, static fn(array $s): bool => $s['enabled']),
            'handle'
        );

        $this->assertSame(['street1', 'street2', 'city', 'state', 'zip', 'country'], $enabled);
    }

    public function testOnlyStreet1OffersAutocomplete(): void
    {
        // Autocomplete attaches a Google Places widget to the input. More than
        // one widget on a field means competing place_changed listeners.
        $autocompleting = array_column(
            array_filter(Defaults::SUBFIELDCONFIG, static fn(array $s): bool => $s['autocomplete']),
            'handle'
        );

        $this->assertSame(['street1'], $autocompleting);
    }

    public function testNoSubfieldIsRequiredByDefault(): void
    {
        foreach (Defaults::SUBFIELDCONFIG as $subfield) {
            $this->assertFalse(
                $subfield['required'],
                "Subfield `{$subfield['handle']}` is required by default, which would "
                . 'break every existing install on upgrade.'
            );
        }
    }

    public function testAnyAutocompletingSubfieldIsAlsoEnabled(): void
    {
        // A disabled subfield renders no input, so autocomplete on one is dead
        // configuration that reads as a working feature.
        foreach (Defaults::SUBFIELDCONFIG as $subfield) {
            if ($subfield['autocomplete']) {
                $this->assertTrue(
                    $subfield['enabled'],
                    "Subfield `{$subfield['handle']}` autocompletes but is disabled."
                );
            }
        }
    }

    public function testEverySubfieldHasANonEmptyLabel(): void
    {
        foreach (Defaults::SUBFIELDCONFIG as $subfield) {
            $this->assertNotSame('', trim($subfield['label']));
        }
    }
}

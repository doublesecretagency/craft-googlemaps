<?php
namespace doublesecretagency\googlemaps\tests\unit;

use doublesecretagency\googlemaps\helpers\GeocodingHelper;
use PHPUnit\Framework\TestCase;

/**
 * Pure-unit tests for the Google API response parser.
 *
 * `restructureComponents()` turns one raw geocoding result into the flat
 * address shape the plugin stores. It touches no Craft service, so it can be
 * called directly with fixture payloads, which makes it one of the few places
 * in this plugin where real behavior is testable rather than merely its shape.
 *
 * It is also worth covering carefully because it sits on the path a user
 * blamed in issue #158. The parsing turned out to be correct, and a test here
 * is what lets the next such report be answered in seconds rather than by
 * re-probing the API.
 */
class GeocodingHelperRestructureTest extends TestCase
{
    /**
     * A representative US result, with every component the parser maps.
     */
    private function _usPayload(): array
    {
        return [
            'formatted_address' => '1 Test St, Testville, CA 90210, USA',
            'place_id' => 'abc123',
            'name' => 'Test Place',
            'address_components' => [
                ['types' => ['street_number'],                'long_name' => '1',             'short_name' => '1'],
                ['types' => ['route'],                        'long_name' => 'Test St',       'short_name' => 'Test St'],
                ['types' => ['locality'],                     'long_name' => 'Testville',     'short_name' => 'Testville'],
                ['types' => ['administrative_area_level_1'],  'long_name' => 'California',    'short_name' => 'CA'],
                ['types' => ['administrative_area_level_2'],  'long_name' => 'Test County',   'short_name' => 'TC'],
                ['types' => ['neighborhood'],                 'long_name' => 'Testside',      'short_name' => 'Testside'],
                ['types' => ['postal_code'],                  'long_name' => '90210',         'short_name' => '90210'],
                ['types' => ['country'],                      'long_name' => 'United States', 'short_name' => 'US'],
            ],
            'geometry' => ['location' => ['lat' => 34.05, 'lng' => -118.25]],
        ];
    }

    /**
     * A UK result, which Google shapes differently.
     */
    private function _ukPayload(): array
    {
        return [
            'formatted_address' => '10 Downing St, London SW1A 2AA, UK',
            'address_components' => [
                ['types' => ['street_number'],               'long_name' => '10',             'short_name' => '10'],
                ['types' => ['route'],                       'long_name' => 'Downing St',     'short_name' => 'Downing St'],
                ['types' => ['postal_town'],                 'long_name' => 'London',         'short_name' => 'London'],
                ['types' => ['administrative_area_level_1'], 'long_name' => 'England',        'short_name' => 'England'],
                ['types' => ['administrative_area_level_2'], 'long_name' => 'Greater London', 'short_name' => 'Greater London'],
                ['types' => ['postal_code'],                 'long_name' => 'SW1A 2AA',       'short_name' => 'SW1A 2AA'],
                ['types' => ['country'],                     'long_name' => 'United Kingdom', 'short_name' => 'GB'],
            ],
            'geometry' => ['location' => ['lat' => 51.5, 'lng' => -0.12]],
        ];
    }

    // ========================================================================= //
    // The formatted address, which issue #158 was about
    // ========================================================================= //

    public function testFormattedAddressIsCarriedThrough(): void
    {
        $result = GeocodingHelper::restructureComponents($this->_usPayload());

        $this->assertSame('1 Test St, Testville, CA 90210, USA', $result['formatted']);
    }

    public function testFormattedIsNullWhenGoogleOmitsIt(): void
    {
        $payload = $this->_usPayload();
        unset($payload['formatted_address']);

        $result = GeocodingHelper::restructureComponents($payload);

        // Null rather than an exception or an empty string, so a caller can
        // distinguish "not provided" from "provided as blank".
        $this->assertNull($result['formatted']);
    }

    public function testFormattedSurvivesAnOtherwiseEmptyPayload(): void
    {
        $result = GeocodingHelper::restructureComponents([
            'formatted_address' => 'Somewhere',
        ]);

        $this->assertSame('Somewhere', $result['formatted']);
    }

    // ========================================================================= //
    // The full returned shape
    // ========================================================================= //

    public function testReturnsEveryExpectedKey(): void
    {
        $result = GeocodingHelper::restructureComponents($this->_usPayload());

        $expected = [
            'name', 'street1', 'street2', 'city', 'state', 'zip',
            'neighborhood', 'county', 'country', 'countryCode',
            'placeId', 'lat', 'lng', 'raw', 'formatted',
        ];

        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, $result, "Missing key: {$key}");
        }
    }

    public function testEveryKeyIsStillPresentForAnEmptyPayload(): void
    {
        // A caller reads these keys unconditionally, so the shape must be
        // stable even when Google returns nothing useful.
        $result = GeocodingHelper::restructureComponents([]);

        foreach (['name', 'street1', 'city', 'state', 'zip', 'country', 'countryCode', 'placeId', 'lat', 'lng', 'formatted'] as $key) {
            $this->assertArrayHasKey($key, $result);
            $this->assertNull($result[$key], "Expected {$key} to be null for an empty payload.");
        }

        $this->assertSame([], $result['raw']);
    }

    // ========================================================================= //
    // US component mapping
    // ========================================================================= //

    /**
     * @dataProvider usMappingProvider
     */
    public function testUsComponentMapsToExpectedField(string $key, mixed $expected): void
    {
        $result = GeocodingHelper::restructureComponents($this->_usPayload());

        $this->assertSame($expected, $result[$key]);
    }

    public static function usMappingProvider(): array
    {
        return [
            'street number and route are joined' => ['street1', '1 Test St'],
            'locality becomes the city'          => ['city', 'Testville'],
            'admin level 1 uses the short name'  => ['state', 'CA'],
            'admin level 2 uses the short name'  => ['county', 'TC'],
            'neighborhood maps directly'         => ['neighborhood', 'Testside'],
            'postal code becomes the zip'        => ['zip', '90210'],
            'country uses the long name'         => ['country', 'United States'],
            'country code uses the short name'   => ['countryCode', 'US'],
            'place id is carried through'        => ['placeId', 'abc123'],
            'name is carried through'            => ['name', 'Test Place'],
        ];
    }

    public function testCoordinatesAreLifted(): void
    {
        $result = GeocodingHelper::restructureComponents($this->_usPayload());

        $this->assertSame(34.05, $result['lat']);
        $this->assertSame(-118.25, $result['lng']);
    }

    public function testStreet1IsNullWhenNeitherNumberNorRouteIsPresent(): void
    {
        $payload = $this->_usPayload();
        $payload['address_components'] = array_values(array_filter(
            $payload['address_components'],
            static fn(array $c): bool => !in_array($c['types'][0], ['street_number', 'route'], true)
        ));

        $result = GeocodingHelper::restructureComponents($payload);

        $this->assertNull($result['street1']);
    }

    // ========================================================================= //
    // The UK special case
    // ========================================================================= //

    public function testUkCityComesFromPostalTown(): void
    {
        // Google does not send a `locality` for most UK addresses.
        $result = GeocodingHelper::restructureComponents($this->_ukPayload());

        $this->assertSame('London', $result['city']);
    }

    public function testUkStateComesFromAdminLevel2(): void
    {
        // Without this the state would read "England" for every UK address.
        $result = GeocodingHelper::restructureComponents($this->_ukPayload());

        $this->assertSame('Greater London', $result['state']);
    }

    public function testUkCountryCodeIsGb(): void
    {
        $result = GeocodingHelper::restructureComponents($this->_ukPayload());

        $this->assertSame('GB', $result['countryCode']);
        $this->assertSame('United Kingdom', $result['country']);
    }

    public function testUkReorderingDoesNotApplyToOtherCountries(): void
    {
        // The US payload also carries an admin level 2, so a country-blind
        // implementation would put "TC" in the state field.
        $result = GeocodingHelper::restructureComponents($this->_usPayload());

        $this->assertSame('CA', $result['state']);
        $this->assertNotSame($result['county'], $result['state']);
    }

    // ========================================================================= //
    // The raw passthrough
    // ========================================================================= //

    public function testRawHoldsTheCompleteOriginalPayload(): void
    {
        $payload = $this->_usPayload();

        $result = GeocodingHelper::restructureComponents($payload);

        $this->assertSame($payload, $result['raw']);
    }

    public function testRawIsNotMutatedByTheParse(): void
    {
        $payload = $this->_usPayload();

        $result = GeocodingHelper::restructureComponents($payload);

        // Anything the parser derived must not have been written back.
        $this->assertArrayNotHasKey('street1', $result['raw']);
        $this->assertArrayNotHasKey('formatted', $result['raw']);
    }

    // ========================================================================= //
    // Malformed input
    // ========================================================================= //

    public function testMissingGeometryYieldsNullCoordinates(): void
    {
        $payload = $this->_usPayload();
        unset($payload['geometry']);

        $result = GeocodingHelper::restructureComponents($payload);

        $this->assertNull($result['lat']);
        $this->assertNull($result['lng']);
    }

    public function testUnrecognizedComponentTypesAreIgnored(): void
    {
        $payload = $this->_usPayload();
        $payload['address_components'][] = [
            'types' => ['some_future_google_type'],
            'long_name' => 'Whatever',
            'short_name' => 'W',
        ];

        $result = GeocodingHelper::restructureComponents($payload);

        // Still parses, and the known fields are unaffected.
        $this->assertSame('Testville', $result['city']);
        $this->assertNotContains('Whatever', array_filter($result, 'is_scalar'));
    }

    public function testComponentsWithoutTypesDoNotBreakTheParse(): void
    {
        $payload = $this->_usPayload();
        $payload['address_components'][] = ['long_name' => 'Orphan', 'short_name' => 'O'];

        $result = GeocodingHelper::restructureComponents($payload);

        $this->assertSame('Testville', $result['city']);
    }
}
